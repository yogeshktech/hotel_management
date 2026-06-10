<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Homestay;
use App\Models\PromoCode;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    
    public function create($homestayId)
    {
        $homestay = Homestay::with('location')->findOrFail($homestayId);

        // Optional: Check if user already has active booking for this homestay
        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('homestay_id', $homestay->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        return view('bookings.create', compact('homestay', 'existingBooking'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'homestay_id'    => 'required|exists:homestays,id',
            'check_in'       => 'required|date|after_or_equal:today',
            'check_out'      => 'required|date|after:check_in',
            'guests'         => 'required|integer|min:1|max:8',
            'extra_beds'     => 'nullable|integer|min:0|max:4',
            'breakfast'      => 'boolean',
            'promo_code'     => 'nullable|string|exists:promo_codes,code',
            'special_request'=> 'nullable|string|max:500',
        ]);

        $homestay = Homestay::findOrFail($validated['homestay_id']);

        // 1. Prevent booking if already booked by this user (simple protection)
        if (Booking::where('user_id', Auth::id())
            ->where('homestay_id', $homestay->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists()) {
            return back()->withErrors(['error' => 'You already have an active booking for this homestay.']);
        }

        // 2. Check date overlap (proper availability check)
        $overlapping = Booking::where('homestay_id', $homestay->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                      ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                      ->orWhereRaw('? between check_in and check_out', [$validated['check_in']]);
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['check_in' => 'Sorry, these dates are not available.']);
        }

        // 3. Calculate stay duration
        $checkIn  = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights   = $checkIn->diffInDays($checkOut);

        // 4. Price calculation
        $basePrice          = $homestay->price_per_night * $nights;
        $extraBedsPrice     = ($validated['extra_beds'] ?? 0) * 500 * $nights;
        $breakfastPrice     = ($validated['breakfast'] ?? false)
                                ? 200 * $validated['guests'] * $nights
                                : 0;
        $serviceFee         = $basePrice * ($homestay->service_fee_percentage / 100);
        $cleaningFee        = $homestay->cleaning_fee ?? 0;

        $subTotal           = $basePrice + $extraBedsPrice + $breakfastPrice + $serviceFee + $cleaningFee;

        // 5. Apply promo code (percentage or fixed - depending on your promo model)
        $promoDiscount = 0;
        if ($validated['promo_code']) {
            $promo = PromoCode::where('code', $validated['promo_code'])
                ->where('active', true)
                ->where(function ($q) use ($checkIn) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', $checkIn);
                })
                ->where(function ($q) use ($checkIn) {
                    $q->whereNull('valid_until')->orWhere('valid_until', '>=', $checkIn);
                })
                ->first();

            if ($promo) {
                if ($promo->type === 'percentage') {
                    $promoDiscount = $subTotal * ($promo->value / 100);
                } else {
                    $promoDiscount = $promo->value;
                }
                // Optional: increment used_count
                $promo->increment('used_count');
            }
        }

        $totalPrice = max(0, $subTotal - $promoDiscount);

        // 6. Create booking (transaction for safety)
        return DB::transaction(function () use (
            $request,
            $homestay,
            $validated,
            $checkIn,
            $checkOut,
            $nights,
            $totalPrice,
            $basePrice,
            $serviceFee,
            $cleaningFee,
            $promoDiscount
        ) {
            $booking = Booking::create([
                'user_id'           => Auth::id(),
                'homestay_id'       => $homestay->id,
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'nights'            => $nights,
                'guests'            => $validated['guests'],
                'extra_beds'        => $validated['extra_beds'] ?? 0,
                'breakfast'         => $validated['breakfast'] ?? false,
                'special_request'   => $validated['special_request'],
                'base_price'        => $basePrice,
                'service_fee'       => $serviceFee,
                'cleaning_fee'      => $cleaningFee,
                'promo_discount'    => $promoDiscount,
                'promo_code'        => $validated['promo_code'] ?? null,
                'total_price'       => $totalPrice,
                'currency'          => $homestay->currency ?? 'IDR',
                'status'            => 'pending',
                'payment_status'    => 'pending',
            ]);

            // Optional: send notification to host/user
            // event(new BookingCreated($booking));

            return redirect()
                ->route('payments.create', $booking->id)
                ->with('success', 'Booking request created successfully! Please complete payment.');
        });
    }

    
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking); // Using policy if you have one

        $booking->load(['homestay.location', 'user']);

        return view('bookings.show', compact('booking'));
    }

    
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        if ($booking->status === 'confirmed') {
            // Refund logic / notification if needed
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}