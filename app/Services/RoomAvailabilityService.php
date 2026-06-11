<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class RoomAvailabilityService
{
    public function overlappingCount(Room $room, Carbon|string $checkIn, Carbon|string $checkOut, ?int $excludeBookingId = null): int
    {
        $checkIn = $checkIn instanceof Carbon ? $checkIn->toDateString() : $checkIn;
        $checkOut = $checkOut instanceof Carbon ? $checkOut->toDateString() : $checkOut;

        return Booking::where('room_id', $room->id)
            ->blocking()
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();
    }

    public function availableUnits(Room $room, Carbon|string $checkIn, Carbon|string $checkOut, ?int $excludeBookingId = null): int
    {
        $booked = $this->overlappingCount($room, $checkIn, $checkOut, $excludeBookingId);

        return max(0, (int) $room->total_units - $booked);
    }

    public function isAvailable(Room $room, Carbon|string $checkIn, Carbon|string $checkOut, ?int $excludeBookingId = null): bool
    {
        return $this->availableUnits($room, $checkIn, $checkOut, $excludeBookingId) > 0;
    }
}
