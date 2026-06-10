<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Homestay;
use App\Models\Room;
use App\Services\BookingPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $channel = $request->get('channel');

        $bookings = Booking::with(['homestay', 'customer', 'room', 'createdByStaff'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($channel, fn ($q) => $q->where('booking_channel', $channel))
            ->latest('booked_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Booking::count(),
            'online' => Booking::online()->count(),
            'offline' => Booking::offline()->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'upcoming' => Booking::where('check_in', '>', now())->whereIn('status', ['confirmed', 'pending'])->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'status', 'channel', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['homestay.location', 'customer', 'room', 'createdByStaff', 'review']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking)
    {
        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Guest checked in.');
    }

    public function checkOut(Booking $booking)
    {
        $booking->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
            'vacant_from' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Guest checked out. Room vacant from today.');
    }

    public function createOffline()
    {
        $properties = Homestay::active()->with('rooms.pricings')->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.bookings.create-offline', compact('properties', 'customers'));
    }

    public function storeOffline(Request $request, BookingPricingService $pricingService)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'payment_method' => 'nullable|string',
            'guest_notes' => 'nullable|string',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);

        $pricing = $pricingService->calculate(
            $room,
            $validated['guest_package'],
            $childCount,
            Carbon::parse($validated['check_in']),
            Carbon::parse($validated['check_out'])
        );

        Booking::create([
            'homestay_id' => $room->homestay_id,
            'customer_id' => $validated['customer_id'],
            'room_id' => $room->id,
            'booking_channel' => 'offline',
            'guest_package' => $validated['guest_package'],
            'adults_count' => $pricing['adults_count'],
            'children_count' => $pricing['children_count'],
            'guests' => $pricing['adults_count'] + $pricing['children_count'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'base_price' => $pricing['base_price'],
            'cleaning_fee' => $pricing['cleaning_fee'],
            'service_fee' => $pricing['service_fee'],
            'total_price' => $pricing['total_price'],
            'currency' => 'INR',
            'payment_method' => $validated['payment_method'] ?? 'cash',
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'guest_notes' => $validated['guest_notes'] ?? null,
            'booked_at' => now(),
            'vacant_from' => $validated['check_out'],
            'created_by_staff_id' => auth('staff')->id(),
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Offline booking created.');
    }
}
