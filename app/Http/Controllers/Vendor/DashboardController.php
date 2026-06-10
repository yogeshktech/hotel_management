<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $staff = auth('staff')->user();
        $profile = $staff->vendorProfile;
        $properties = $staff->homestays()->with('location')->latest()->get();

        return view('vendor.dashboard', compact('staff', 'profile', 'properties'));
    }
}
