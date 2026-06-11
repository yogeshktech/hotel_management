<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homestay;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $properties = Homestay::with(['location', 'owner', 'images', 'rooms'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Homestay::count(),
            'pending' => Homestay::pending()->count(),
            'active' => Homestay::active()->count(),
            'rejected' => Homestay::where('status', 'rejected')->count(),
        ];

        return view('admin.properties.index', compact('properties', 'status', 'counts'));
    }

    public function show(Homestay $property)
    {
        $property->load(['location', 'owner.vendorProfile', 'images', 'rooms.images', 'bookings']);

        return view('admin.properties.show', compact('property'));
    }

    public function approve(Homestay $property)
    {
        $property->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Property approved and published.');
    }

    public function reject(Request $request, Homestay $property)
    {
        $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $property->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Property rejected.');
    }
}
