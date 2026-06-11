<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends VendorController
{
    public function index()
    {
        $locations = Location::withCount('homestays')
            ->orderBy('name')
            ->paginate(15);

        return view('vendor.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('vendor.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|unique:locations,slug',
            'country' => 'required|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Location::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter++;
        }

        $location = Location::create($validated);

        $redirect = $request->input('redirect');

        if ($redirect === 'property') {
            return redirect()->route('vendor.properties.create')
                ->with('success', 'Location "' . $validated['name'] . '" added and selected below.')
                ->with('new_location_id', $location->id);
        }

        return redirect()->route('vendor.locations.index')
            ->with('success', 'Location created successfully.');
    }
}
