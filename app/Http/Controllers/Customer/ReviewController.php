<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Booking $booking)
    {
        $customer = auth('customer')->user();

        if ($booking->customer_id !== $customer->id) {
            abort(403);
        }

        if (! in_array($booking->status, ['checked_out', 'completed'])) {
            return redirect()->back()->with('error', 'You can review after checkout.');
        }

        if ($booking->review) {
            return redirect()->route('customer.dashboard')->with('info', 'Already reviewed.');
        }

        return view('customer.reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $customer = auth('customer')->user();

        if ($booking->customer_id !== $customer->id || $booking->review) {
            abort(403);
        }

        $validated = $request->validate([
            'service_rating' => 'required|integer|min:1|max:5',
            'food_rating' => 'required|integer|min:1|max:5',
            'overall_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        BookingReview::create([
            ...$validated,
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'homestay_id' => $booking->homestay_id,
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Thank you for your review!');
    }
}
