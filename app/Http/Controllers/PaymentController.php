<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Exception;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    public function create(Booking $booking)
    {
        if ($booking->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('bookings.show', $booking)
                ->with('info', 'This booking is already paid.');
        }

        $api = new Api(config('services.razorpay.key', env('RAZORPAY_KEY')), config('services.razorpay.secret', env('RAZORPAY_SECRET')));
        $order = $api->order->create([
            'receipt' => $booking->booking_reference,
            'amount' => (int) round($booking->total_price * 100),
            'currency' => 'INR',
        ]);

        return view('payments.razorpay', compact('order', 'booking'));
    }

    public function success(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $api = new Api(config('services.razorpay.key', env('RAZORPAY_KEY')), config('services.razorpay.secret', env('RAZORPAY_SECRET')));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);
        } catch (Exception $e) {
            return redirect()->route('customer.dashboard')->with('error', 'Payment verification failed.');
        }

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
        ]);

        return view('thank-you', compact('booking'));
    }
}
