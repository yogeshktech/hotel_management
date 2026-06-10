<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\Customer;
use App\Models\Homestay;
use App\Models\Staff;
use App\Models\VendorProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'total_staff' => Staff::count(),
            'total_vendors' => Staff::role('vendor')->count(),
            'pending_vendors' => VendorProfile::pending()->count(),
            'total_properties' => Homestay::count(),
            'pending_properties' => Homestay::pending()->count(),
            'active_properties' => Homestay::active()->count(),
            'total_bookings' => Booking::count(),
            'online_bookings' => Booking::online()->count(),
            'offline_bookings' => Booking::offline()->count(),
            'checked_in_now' => Booking::where('status', 'checked_in')->count(),
            'recent_reviews' => BookingReview::with(['customer', 'homestay'])->latest()->take(5)->get(),
            'recent_bookings' => Booking::with(['homestay', 'customer', 'room'])
                ->latest('booked_at')
                ->take(5)
                ->get(),
            'pending_vendors_list' => VendorProfile::with('staff')->pending()->latest()->take(5)->get(),
            'pending_properties_list' => Homestay::with(['owner', 'location'])->pending()->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
