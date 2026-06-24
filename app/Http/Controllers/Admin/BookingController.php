<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Homestay;
use App\Models\Room;
use App\Services\OfflineBookingService;
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

    public function storeOffline(Request $request, OfflineBookingService $offlineBookingService)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'guest_name' => 'required_without:customer_id|string|max:255',
            'guest_phone' => 'required_without:customer_id|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'payment_method' => 'nullable|string',
            'guest_notes' => 'nullable|string',
        ]);

        try {
            $offlineBookingService->create($validated, auth('staff')->id());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Offline booking created. Dates blocked for online booking.');
    }

    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in'])) {
            return back()->with('error', 'Check out the guest before deleting this booking.');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted. Dates are open for online booking again.');
    }
}
