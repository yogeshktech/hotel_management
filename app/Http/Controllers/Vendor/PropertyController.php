<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends VendorController
{
    public function index()
    {
        $profile = $this->ensureProfile();
        $properties = $this->staff()->homestays()->with(['location', 'rooms'])->latest()->paginate(10);

        return view('vendor.properties.index', compact('profile', 'properties'));
    }

    public function create()
    {
        $this->ensureCanManageProperties();
        $locations = Location::orderBy('name')->get();

        return view('vendor.properties.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $this->ensureCanManageProperties();

        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'max_guests' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'beds' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'price_per_night' => 'required|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'address' => 'required|string|max:500',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
        ]);

        $property = Homestay::create([
            ...$validated,
            'slug' => Str::slug($validated['title']) . '-' . time(),
            'staff_id' => $this->staff()->id,
            'currency' => 'INR',
            'cleaning_fee' => $validated['cleaning_fee'] ?? 0,
            'amenities' => $validated['amenities'] ?? [],
            'status' => 'pending',
        ]);

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Property submitted for admin approval. Upload gallery photos, then add rooms.');
    }

    public function show(Homestay $property)
    {
        $this->ensureOwnProperty($property);
        $property->load(['location', 'rooms.pricings', 'rooms.images', 'images']);

        return view('vendor.properties.show', compact('property'));
    }

    public function edit(Homestay $property)
    {
        $this->ensureOwnProperty($property);
        $locations = Location::orderBy('name')->get();

        return view('vendor.properties.edit', compact('property', 'locations'));
    }

    public function update(Request $request, Homestay $property)
    {
        $this->ensureOwnProperty($property);

        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'max_guests' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'beds' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'price_per_night' => 'required|numeric|min:0',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'address' => 'required|string|max:500',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
        ]);

        $property->update([
            ...$validated,
            'cleaning_fee' => $validated['cleaning_fee'] ?? 0,
            'amenities' => $validated['amenities'] ?? [],
            'status' => $property->status === 'active' ? 'active' : 'pending',
        ]);

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Property updated successfully.');
    }
}
