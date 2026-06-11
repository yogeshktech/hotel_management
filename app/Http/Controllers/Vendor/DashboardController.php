<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\Homestay;

class DashboardController extends VendorController
{
    public function index()
    {
        $staff = $this->staff();
        $profile = $staff->vendorProfile;
        $homestayIds = $staff->homestays()->pluck('id');

        $stats = [
            'total_properties' => $staff->homestays()->count(),
            'active_properties' => $staff->homestays()->active()->count(),
            'pending_properties' => $staff->homestays()->pending()->count(),
            'total_rooms' => Homestay::whereIn('id', $homestayIds)->withCount('rooms')->get()->sum('rooms_count'),
            'total_bookings' => Booking::whereIn('homestay_id', $homestayIds)->count(),
            'pending_bookings' => Booking::whereIn('homestay_id', $homestayIds)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::whereIn('homestay_id', $homestayIds)->where('status', 'confirmed')->count(),
            'checked_in_now' => Booking::whereIn('homestay_id', $homestayIds)->where('status', 'checked_in')->count(),
            'total_revenue' => Booking::whereIn('homestay_id', $homestayIds)
                ->where('payment_status', 'paid')
                ->sum('total_price'),
            'month_revenue' => Booking::whereIn('homestay_id', $homestayIds)
                ->where('payment_status', 'paid')
                ->whereMonth('booked_at', now()->month)
                ->whereYear('booked_at', now()->year)
                ->sum('total_price'),
            'avg_rating' => round(
                BookingReview::whereIn('homestay_id', $homestayIds)->avg('overall_rating') ?? 0,
                1
            ),
            'recent_bookings' => Booking::with(['homestay', 'customer', 'room'])
                ->whereIn('homestay_id', $homestayIds)
                ->latest('booked_at')
                ->take(8)
                ->get(),
            'properties' => $staff->homestays()->with(['location', 'rooms'])->latest()->get(),
        ];

        $onboarding = $profile?->onboardingSteps() ?? [];

        return view('vendor.dashboard', compact('staff', 'profile', 'stats', 'onboarding'));
    }
}
