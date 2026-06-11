<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Homestay;
use App\Models\PromoCode;
use App\Models\Room;
use App\Services\BookingPricingService;
use App\Services\RoomAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request, Homestay $property)
    {
        if ($property->status !== 'active') {
            abort(404);
        }

        $property->load(['location', 'images', 'rooms.pricings', 'rooms.images']);

        $selectedRoom = $request->room_id
            ? $property->rooms->firstWhere('id', (int) $request->room_id)
            : $property->rooms->first();

        return view('site.bookings.create', [
            'property' => $property,
            'selectedRoom' => $selectedRoom,
            'checkIn' => $request->get('check_in', now()->addDay()->format('Y-m-d')),
            'checkOut' => $request->get('check_out', now()->addDays(2)->format('Y-m-d')),
            'guestPackage' => $request->get('guest_package', 'couple'),
            'childCount' => (int) $request->get('child_count', 0),
        ]);
    }

    public function calculatePrice(Request $request, BookingPricingService $pricingService, RoomAvailabilityService $availabilityService)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'promo_code' => 'nullable|string',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);

        try {
            $pricing = $pricingService->calculate(
                $room,
                $validated['guest_package'],
                $childCount,
                Carbon::parse($validated['check_in']),
                Carbon::parse($validated['check_out'])
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $promoDiscount = 0;
        if (! empty($validated['promo_code'])) {
            $promo = $this->resolvePromo($validated['promo_code'], $pricing['total_price']);
            if ($promo) {
                $promoDiscount = $promo['discount'];
            }
        }

        $pricing['promo_discount'] = $promoDiscount;
        $pricing['total_price'] = max(0, $pricing['total_price'] - $promoDiscount);
        $pricing['available'] = $availabilityService->isAvailable($room, $validated['check_in'], $validated['check_out']);
        $pricing['units_available'] = $availabilityService->availableUnits($room, $validated['check_in'], $validated['check_out']);

        return response()->json(['success' => true, 'data' => $pricing]);
    }

    public function store(Request $request, BookingPricingService $pricingService, RoomAvailabilityService $availabilityService)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_package' => 'required|in:adult,couple,family,child',
            'child_count' => 'nullable|integer|min:0|max:4',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'promo_code' => 'nullable|string',
            'guest_notes' => 'nullable|string|max:500',
        ]);

        $room = Room::with('homestay')->findOrFail($validated['room_id']);
        $childCount = (int) ($validated['child_count'] ?? 0);

        if ($room->homestay->status !== 'active') {
            return back()->withInput()->with('error', 'Property not available.');
        }

        if (! $availabilityService->isAvailable($room, $validated['check_in'], $validated['check_out'])) {
            return back()->withInput()->with('error', 'Room not available for selected dates.');
        }

        try {
            $pricing = $pricingService->calculate(
                $room,
                $validated['guest_package'],
                $childCount,
                Carbon::parse($validated['check_in']),
                Carbon::parse($validated['check_out'])
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $promoDiscount = 0;
        $promoCode = null;
        if (! empty($validated['promo_code'])) {
            $promo = $this->resolvePromo($validated['promo_code'], $pricing['total_price']);
            if ($promo) {
                $promoDiscount = $promo['discount'];
                $promoCode = $validated['promo_code'];
                PromoCode::where('code', $promoCode)->first()?->increment('used_count');
            }
        }

        $total = max(0, $pricing['total_price'] - $promoDiscount);

        $booking = DB::transaction(function () use ($validated, $room, $pricing, $promoDiscount, $promoCode, $total, $childCount) {
            return Booking::create([
                'homestay_id' => $room->homestay_id,
                'customer_id' => auth('customer')->id(),
                'room_id' => $room->id,
                'booking_channel' => 'online',
                'guest_package' => $validated['guest_package'],
                'adults_count' => $pricing['adults_count'],
                'children_count' => $pricing['children_count'],
                'guests' => $pricing['adults_count'] + $pricing['children_count'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'base_price' => $pricing['base_price'],
                'cleaning_fee' => $pricing['cleaning_fee'],
                'service_fee' => $pricing['service_fee'],
                'promo_discount' => $promoDiscount,
                'promo_code' => $promoCode,
                'total_price' => $total,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'payment_status' => 'pending',
                'status' => 'pending',
                'guest_notes' => $validated['guest_notes'] ?? null,
                'booked_at' => now(),
                'vacant_from' => $validated['check_out'],
            ]);
        });

        return redirect()->route('payments.create', $booking)
            ->with('success', 'Booking created! Complete payment to confirm.');
    }

    public function show(Booking $booking)
    {
        if ($booking->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        $booking->load(['homestay.location', 'homestay.images', 'room']);

        return view('site.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        if (in_array($booking->status, ['checked_in', 'checked_out', 'cancelled'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    private function resolvePromo(string $code, float $amount): ?array
    {
        $promo = PromoCode::where('code', $code)->where('active', true)
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->first();

        if (! $promo) {
            return null;
        }

        $discount = $promo->type === 'percentage'
            ? round($amount * ($promo->value / 100), 2)
            : (float) $promo->value;

        return ['discount' => $discount, 'promo' => $promo];
    }
}
