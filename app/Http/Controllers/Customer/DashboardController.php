<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();

        $bookings = Booking::with(['homestay.location', 'room', 'review'])
            ->where('customer_id', $customer->id)
            ->latest('booked_at')
            ->paginate(10);

        $stats = [
            'total' => $customer->bookings()->count(),
            'upcoming' => $customer->bookings()->where('check_in', '>=', now())->count(),
            'completed' => $customer->bookings()->where('status', 'checked_out')->count(),
            'reviews' => $customer->reviews()->count(),
        ];

        return view('customer.dashboard', compact('customer', 'bookings', 'stats'));
    }
}
