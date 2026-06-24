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

    public function isOccupyingOnDate(Booking $booking, Carbon $date): bool
    {
        if (in_array($booking->status, ['cancelled', 'rejected'])) {
            return false;
        }

        $start = Carbon::parse($booking->check_in)->startOfDay();
        if ($date->lt($start)) {
            return false;
        }

        if ($booking->status === 'checked_out') {
            $vacantFrom = Carbon::parse($booking->vacant_from ?? $booking->check_out)->startOfDay();

            return $date->lt($vacantFrom);
        }

        return $date->lt(Carbon::parse($booking->check_out)->startOfDay());
    }

    public function occupiedUnitsOn(Room $room, ?Carbon $date = null): int
    {
        $date = ($date ?? now())->copy()->startOfDay();

        return Booking::where('room_id', $room->id)
            ->blocking()
            ->get()
            ->filter(fn (Booking $booking) => $this->isOccupyingOnDate($booking, $date))
            ->count();
    }

    public function availableFrom(Booking $booking): Carbon
    {
        if ($booking->status === 'checked_out') {
            return Carbon::parse($booking->vacant_from ?? $booking->check_out)->startOfDay();
        }

        return Carbon::parse($booking->check_out)->startOfDay();
    }

    public function roomSnapshot(Room $room): array
    {
        $today = now()->startOfDay();
        $total = (int) $room->total_units;
        $occupied = $this->occupiedUnitsOn($room, $today);

        $activeBookings = Booking::where('room_id', $room->id)
            ->blocking()
            ->with('customer')
            ->get()
            ->filter(fn (Booking $b) => $this->isOccupyingOnDate($b, $today) || $this->availableFrom($b)->gte($today));

        $timeline = $activeBookings
            ->sortBy(fn (Booking $b) => $this->availableFrom($b)->timestamp)
            ->map(function (Booking $booking) use ($today) {
                $availableFrom = $this->availableFrom($booking);
                $isOccupiedNow = $this->isOccupyingOnDate($booking, $today);

                return [
                    'booking_reference' => $booking->booking_reference,
                    'guest' => $booking->customer->name ?? 'Guest',
                    'status' => $booking->status,
                    'check_in' => $booking->check_in->format('d M Y'),
                    'check_out' => $booking->check_out->format('d M Y'),
                    'available_from' => $availableFrom->format('d M Y'),
                    'available_from_iso' => $availableFrom->toDateString(),
                    'available_in' => $availableFrom->isToday()
                        ? 'Available today'
                        : ($availableFrom->isPast() ? 'Available now' : $availableFrom->diffForHumans(now(), true) . ' remaining'),
                    'is_occupied_now' => $isOccupiedNow,
                ];
            })
            ->values()
            ->all();

        return [
            'room_id' => $room->id,
            'room_name' => $room->name,
            'property' => $room->homestay->title ?? '—',
            'property_id' => $room->homestay_id,
            'total_units' => $total,
            'occupied_now' => $occupied,
            'available_now' => max(0, $total - $occupied),
            'timeline' => $timeline,
        ];
    }

    /**
     * @param  iterable<int, Room>  $rooms
     */
    public function vendorAvailabilitySummary(iterable $rooms): array
    {
        $roomSnapshots = [];
        $totalUnits = 0;
        $occupiedNow = 0;
        $roomIds = [];

        foreach ($rooms as $room) {
            if ($room->status !== 'active') {
                continue;
            }

            $roomIds[] = $room->id;
            $snapshot = $this->roomSnapshot($room);
            $roomSnapshots[] = $snapshot;
            $totalUnits += $snapshot['total_units'];
            $occupiedNow += $snapshot['occupied_now'];
        }

        $checkingOutToday = Booking::whereIn('room_id', $roomIds)
            ->blocking()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereDate('check_out', now()->toDateString())
            ->with(['customer', 'room.homestay'])
            ->get()
            ->map(fn (Booking $booking) => [
                'booking_reference' => $booking->booking_reference,
                'guest' => $booking->customer->name ?? 'Guest',
                'room_name' => $booking->room->name ?? '—',
                'property' => $booking->room->homestay->title ?? '—',
                'available_from' => $booking->check_out->format('d M Y'),
                'available_in' => 'Available after checkout today',
            ])
            ->values()
            ->all();

        $freedToday = Booking::whereIn('room_id', $roomIds)
            ->where('status', 'checked_out')
            ->whereDate('vacant_from', now()->toDateString())
            ->with(['customer', 'room.homestay'])
            ->get()
            ->map(fn (Booking $booking) => [
                'booking_reference' => $booking->booking_reference,
                'guest' => $booking->customer->name ?? 'Guest',
                'room_name' => $booking->room->name ?? '—',
                'property' => $booking->room->homestay->title ?? '—',
                'available_from' => ($booking->vacant_from ?? $booking->check_out)->format('d M Y'),
                'available_in' => 'Available now (checked out)',
            ])
            ->values()
            ->all();

        return [
            'total_units' => $totalUnits,
            'occupied_now' => $occupiedNow,
            'available_now' => max(0, $totalUnits - $occupiedNow),
            'checking_out_today' => $checkingOutToday,
            'freed_today' => $freedToday,
            'rooms' => $roomSnapshots,
        ];
    }
}
