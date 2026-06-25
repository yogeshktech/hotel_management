<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomAddon;
use App\Models\RoomPricingSeason;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingPricingService
{
    /**
     * @param  array<int>  $selectedAddonIds
     */
    public function calculate(
        Room $room,
        string $packageType,
        int $childCount,
        Carbon $checkIn,
        Carbon $checkOut,
        array $selectedAddonIds = [],
        bool $fullPackageAddons = false,
    ): array {
        $room->loadMissing('homestay', 'seasons', 'addons');

        $pricing = $room->pricings()
            ->where('package_type', $packageType)
            ->where('child_count', $packageType === 'child' || $packageType === 'family' ? $childCount : 0)
            ->first();

        if (! $pricing) {
            throw new \InvalidArgumentException("No pricing found for {$packageType} package.");
        }

        $nights = max(1, $checkIn->diffInDays($checkOut));
        $nightlyRate = (float) $pricing->price_per_night;
        $activeSeasons = $room->seasons->where('is_active', true);

        $basePrice = 0.0;
        $seasonalNights = 0;
        $peakMultiplier = 1.0;
        $date = $checkIn->copy();

        while ($date->lt($checkOut)) {
            $multiplier = $this->seasonMultiplier($activeSeasons, $date);
            $basePrice += round($nightlyRate * $multiplier, 2);

            if ($multiplier !== 1.0) {
                $seasonalNights++;
                $peakMultiplier = max($peakMultiplier, $multiplier);
            }

            $date->addDay();
        }

        $adults = match ($packageType) {
            'adult' => 1,
            'couple' => 2,
            'family' => 2,
            'child' => 0,
            default => 1,
        };

        $children = match ($packageType) {
            'child' => $childCount,
            'family' => $childCount,
            default => 0,
        };

        $guests = max(1, $adults + $children);
        $addonResult = $this->calculateAddons($room, $selectedAddonIds, $fullPackageAddons, $nights, $guests);

        $roomSubtotal = $basePrice;
        $addonsTotal = $addonResult['total'];
        $subtotal = $roomSubtotal + $addonsTotal;

        $cleaningFee = (float) ($room->homestay->cleaning_fee ?? 0);
        $serviceFeePct = (float) ($room->homestay->service_fee_percentage ?? 0);
        $serviceFee = round($subtotal * ($serviceFeePct / 100), 2);
        $total = $subtotal + $cleaningFee + $serviceFee;

        $lineItems = [
            ['key' => 'room', 'label' => 'Room stay (' . $this->packageLabel($packageType) . ')', 'amount' => $roomSubtotal],
        ];

        foreach ($addonResult['lines'] as $line) {
            $lineItems[] = $line;
        }

        if ($cleaningFee > 0) {
            $lineItems[] = ['key' => 'cleaning', 'label' => 'Cleaning fee', 'amount' => $cleaningFee];
        }

        if ($serviceFee > 0) {
            $lineItems[] = ['key' => 'service', 'label' => "Platform service fee ({$serviceFeePct}%)", 'amount' => $serviceFee];
        }

        return [
            'adults_count' => $adults,
            'children_count' => $children,
            'guests' => $guests,
            'guest_package' => $packageType,
            'guest_package_label' => $this->packageLabel($packageType),
            'base_price' => $roomSubtotal,
            'addons_total' => $addonsTotal,
            'addons' => $addonResult['lines'],
            'addons_snapshot' => $addonResult['snapshot'],
            'full_package_addons' => $fullPackageAddons,
            'subtotal' => $subtotal,
            'cleaning_fee' => $cleaningFee,
            'service_fee' => $serviceFee,
            'service_fee_percentage' => $serviceFeePct,
            'line_items' => $lineItems,
            'total_price' => $total,
            'nights' => $nights,
            'price_per_night' => round($roomSubtotal / $nights, 2),
            'standard_price_per_night' => $nightlyRate,
            'seasonal_nights' => $seasonalNights,
            'seasonal_multiplier' => $seasonalNights > 0 ? $peakMultiplier : null,
            'has_seasonal_pricing' => $seasonalNights > 0,
            'available_addons' => $room->addons->where('is_active', true)->values()->map(fn (RoomAddon $a) => [
                'id' => $a->id,
                'slug' => $a->slug,
                'name' => $a->name,
                'price' => (float) $a->price,
                'charge_type' => $a->charge_type,
                'charge_label' => $a->chargeLabel(),
                'is_included_in_package' => $a->is_included_in_package,
                'is_free' => $a->isFree(),
            ]),
        ];
    }

    /**
     * @param  array<int>  $selectedAddonIds
     * @return array{total: float, lines: list<array>, snapshot: list<array>}
     */
    private function calculateAddons(Room $room, array $selectedAddonIds, bool $fullPackage, int $nights, int $guests): array
    {
        $activeAddons = $room->addons->where('is_active', true)->sortBy('sort_order');

        if ($fullPackage) {
            $selected = $activeAddons->where('is_included_in_package', true);
        } else {
            $selected = $activeAddons->whereIn('id', $selectedAddonIds);
        }

        $total = 0.0;
        $lines = [];
        $snapshot = [];

        foreach ($selected as $addon) {
            $amount = $this->addonAmount($addon, $nights, $guests);
            $total += $amount;

            $detail = $this->addonDetailLabel($addon, $nights, $guests);
            $lines[] = [
                'key' => 'addon_' . $addon->id,
                'label' => $addon->name . ($detail ? " ({$detail})" : ''),
                'amount' => $amount,
                'is_free' => $addon->isFree(),
            ];

            $snapshot[] = [
                'id' => $addon->id,
                'slug' => $addon->slug,
                'name' => $addon->name,
                'unit_price' => (float) $addon->price,
                'charge_type' => $addon->charge_type,
                'total' => $amount,
                'detail' => $detail,
            ];
        }

        return [
            'total' => round($total, 2),
            'lines' => $lines,
            'snapshot' => $snapshot,
        ];
    }

    private function addonAmount(RoomAddon $addon, int $nights, int $guests): float
    {
        $price = (float) $addon->price;

        return match ($addon->charge_type) {
            'per_stay' => $price,
            'per_night' => $price * $nights,
            'per_guest_per_night' => $price * $nights * $guests,
            default => $price,
        };
    }

    private function addonDetailLabel(RoomAddon $addon, int $nights, int $guests): string
    {
        if ($addon->isFree()) {
            return 'Complimentary';
        }

        return match ($addon->charge_type) {
            'per_stay' => 'once per stay',
            'per_night' => "₹{$addon->price} × {$nights} night(s)",
            'per_guest_per_night' => "₹{$addon->price} × {$guests} guest(s) × {$nights} night(s)",
            default => $addon->chargeLabel(),
        };
    }

    private function packageLabel(string $packageType): string
    {
        return match ($packageType) {
            'adult' => 'Adult (single)',
            'couple' => 'Couple (2 guests)',
            'family' => 'Family',
            'child' => 'Child only',
            default => ucfirst($packageType),
        };
    }

    /**
     * @param  Collection<int, RoomPricingSeason>  $seasons
     */
    private function seasonMultiplier(Collection $seasons, Carbon $date): float
    {
        $multiplier = 1.0;

        foreach ($seasons as $season) {
            if ($season->covers($date)) {
                $multiplier = max($multiplier, (float) $season->price_multiplier);
            }
        }

        return $multiplier;
    }
}
