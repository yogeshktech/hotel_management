<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $vendors = VendorProfile::with(['staff', 'approver'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => VendorProfile::count(),
            'pending' => VendorProfile::pending()->count(),
            'approved' => VendorProfile::approved()->count(),
            'rejected' => VendorProfile::where('status', 'rejected')->count(),
        ];

        return view('admin.vendors.index', compact('vendors', 'status', 'counts'));
    }

    public function show(VendorProfile $vendor)
    {
        $vendor->load(['staff.homestays.location', 'staff.homestays.images', 'approver']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function approve(VendorProfile $vendor)
    {
        $vendor->update([
            'status' => 'approved',
            'approved_by' => auth('staff')->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $vendor->staff->update(['is_active' => true]);
        $vendor->staff->syncRoles('vendor');

        return redirect()->back()->with('success', 'Vendor approved successfully.');
    }

    public function reject(Request $request, VendorProfile $vendor)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $vendor->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth('staff')->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Vendor application rejected.');
    }

    public function suspend(VendorProfile $vendor)
    {
        $vendor->update(['status' => 'suspended']);
        $vendor->staff->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Vendor suspended.');
    }
}
