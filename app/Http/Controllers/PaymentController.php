<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Booking;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Notification;    
use Exception;

class PaymentController extends Controller
{
    public function create($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $order = $api->order->create([
            'receipt' => 'booking_' . $booking->id,
            'amount' => $booking->total_price * 100,  // In paise
            'currency' => 'INR',
        ]);

        return view('payments.razorpay', compact('order', 'booking'));
    }

    public function success(Request $request)
    {
        // Verify payment
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];
            $api->utility->verifyPaymentSignature($attributes);
        } catch (Exception $e) {
            // Handle failure
            return redirect()->route('bookings.create')->with('error', 'Payment failed');
        }

        // Update booking status
        $booking = Booking::where('id', $request->booking_id)->first();  // Assume passed
        $booking->update(['status' => 'confirmed']);

        // Send notifications
        $this->sendNotifications($booking);

        return view('thank-you');
    }
}
