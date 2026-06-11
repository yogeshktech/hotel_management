<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use App\Services\OfflineBookingService;
use Illuminate\Http\Request;

class BookingController extends VendorController
{
    public function index(Request $request)
    {
        $homestayIds = $this->staff()->homestays()->pluck('id');

        $query = Booking::with(['homestay', 'customer', 'room', 'createdByStaff'])
            ->whereIn('homestay_id', $homestayIds)
            ->latest('booked_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->where('homestay_id', $request->property_id);
        }

        if ($request->filled('channel')) {
            $query->where('booking_channel', $request->channel);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $properties = $this->staff()->homestays()->orderBy('title')->get();

        return view('vendor.bookings.index', compact('bookings', 'properties'));
    }

    public function createOffline()
    {
        $properties = $this->staff()->homestays()->with('rooms.pricings')->orderBy('title')->get();

        if ($properties->isEmpty()) {
            return redirect()->route('vendor.properties.index')
                ->with('error', 'Add a property and rooms before creating offline bookings.');
        }

        $pastGuests = Customer::whereHas('bookings', fn ($q) => $q->whereIn('homestay_id', $properties->pluck('id')))
            ->orderBy('name')
            ->get();

        return view('vendor.bookings.create-offline', compact('properties', 'pastGuests'));
    }

    public function storeOffline(Request $request, OfflineBookingService $offlineBookingService)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'nullable|exists:customers,id',
            'guest_name' => 'required_without:customer_id|string|max:255',
            'guest_phone' => 'required_without:customer_id|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'payment_method' => 'nullable|string|max:50',
            'guest_notes' => 'nullable|string|max:500',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);

        if ($room->homestay->staff_id !== $this->staff()->id) {
            abort(403);
        }

        try {
            $booking = $offlineBookingService->create($validated, $this->staff()->id);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('vendor.bookings.show', $booking)
            ->with('success', 'Offline booking created. These dates are now blocked for online booking.');
    }

    public function show(Booking $booking)
    {
        $this->ensureOwnBooking($booking);
        $booking->load(['homestay.location', 'customer', 'room', 'createdByStaff']);

        return view('vendor.bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking)
    {
        $this->ensureOwnBooking($booking);

        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Guest checked in.');
    }

    public function checkOut(Booking $booking)
    {
        $this->ensureOwnBooking($booking);

        $booking->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
            'vacant_from' => now()->toDateString(),
        ]);

        return back()->with('success', 'Guest checked out. Room is now available online from today.');
    }

    public function cancel(Booking $booking)
    {
        $this->ensureOwnBooking($booking);

        if (in_array($booking->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled. Dates are open for online booking again.');
    }

    public function destroy(Booking $booking)
    {
        $this->ensureOwnBooking($booking);

        if ($booking->status === 'checked_in') {
            return back()->with('error', 'Check out the guest before deleting this booking.');
        }

        if (! in_array($booking->status, ['checked_out', 'cancelled'])) {
            $booking->update(['status' => 'cancelled']);
        }

        $booking->delete();

        return redirect()->route('vendor.bookings.index')
            ->with('success', 'Booking deleted. Dates are open for online booking again.');
    }

    private function ensureOwnBooking(Booking $booking): void
    {
        if ($booking->homestay->staff_id !== $this->staff()->id) {
            abort(403);
        }
    }
}
