<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OfflineBookingService
{
    public function __construct(
        private BookingPricingService $pricingService,
        private RoomAvailabilityService $availabilityService,
    ) {}

    public function resolveCustomer(array $data): Customer
    {
        if (! empty($data['customer_id'])) {
            return Customer::findOrFail($data['customer_id']);
        }

        $phone = $data['guest_phone'];
        $email = $data['guest_email'] ?? 'walkin.' . preg_replace('/\D/', '', $phone) . '@guest.local';

        return Customer::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => $data['guest_name'],
                'email' => Customer::where('email', $email)->exists()
                    ? 'walkin.' . Str::lower(Str::random(8)) . '@guest.local'
                    : $email,
                'password' => bcrypt(Str::random(32)),
                'is_active' => true,
            ]
        );
    }

    public function create(array $validated, int $staffId): Booking
    {
        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        if (! $this->availabilityService->isAvailable($room, $checkIn, $checkOut)) {
            throw new \InvalidArgumentException('Room is not available for the selected dates. Dates are blocked by an existing booking.');
        }

        $pricing = $this->pricingService->calculate(
            $room,
            $validated['guest_package'],
            $childCount,
            $checkIn,
            $checkOut
        );

        $customer = $this->resolveCustomer($validated);

        return Booking::create([
            'homestay_id' => $room->homestay_id,
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'booking_channel' => 'offline',
            'guest_package' => $validated['guest_package'],
            'adults_count' => $pricing['adults_count'],
            'children_count' => $pricing['children_count'],
            'guests' => $pricing['adults_count'] + $pricing['children_count'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'base_price' => $pricing['base_price'],
            'addons_total' => $pricing['addons_total'] ?? 0,
            'full_package_addons' => $pricing['full_package_addons'] ?? false,
            'addons_snapshot' => $pricing['addons_snapshot'] ?? [],
            'cleaning_fee' => $pricing['cleaning_fee'],
            'service_fee' => $pricing['service_fee'],
            'total_price' => $pricing['total_price'],
            'currency' => 'INR',
            'payment_method' => $validated['payment_method'] ?? 'cash',
            'payment_status' => $validated['payment_status'] ?? 'paid',
            'status' => $validated['status'] ?? 'confirmed',
            'guest_notes' => $validated['guest_notes'] ?? null,
            'booked_at' => now(),
            'vacant_from' => $validated['check_out'],
            'created_by_staff_id' => $staffId,
        ]);
    }
}
