<?php

namespace App\Services;

use App\Models\Room;
use Carbon\Carbon;

class BookingPricingService
{
    public function calculate(Room $room, string $packageType, int $childCount, Carbon $checkIn, Carbon $checkOut): array
    {
        $pricing = $room->pricings()
            ->where('package_type', $packageType)
            ->where('child_count', $packageType === 'child' || $packageType === 'family' ? $childCount : 0)
            ->first();

        if (! $pricing) {
            throw new \InvalidArgumentException("No pricing found for {$packageType} package.");
        }

        $nights = max(1, $checkIn->diffInDays($checkOut));
        $basePrice = (float) $pricing->price_per_night * $nights;
        $cleaningFee = (float) ($room->homestay->cleaning_fee ?? 0);
        $serviceFee = round($basePrice * ((float) ($room->homestay->service_fee_percentage ?? 0) / 100), 2);
        $total = $basePrice + $cleaningFee + $serviceFee;

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

        return [
            'adults_count' => $adults,
            'children_count' => $children,
            'guest_package' => $packageType,
            'base_price' => $basePrice,
            'cleaning_fee' => $cleaningFee,
            'service_fee' => $serviceFee,
            'total_price' => $total,
            'nights' => $nights,
            'price_per_night' => (float) $pricing->price_per_night,
        ];
    }
}
