<?php

namespace App\Http\Controllers;

use App\Models\Homestay;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class HomestayController extends Controller
{
    public function index()
    {
        $homestays = Homestay::with(['location', 'owner'])
            ->latest()
            ->paginate(10);
            
        return view('admin.homestays.index', compact('homestays'));
    }

    public function create()
    {
        $locations = Location::pluck('name', 'id');
        return view('admin.homestays.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id'            => 'required|exists:locations,id',
            'title'                  => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_night'        => 'required|numeric|min:0',
            'max_guests'             => 'required|integer|min:1',
            'bedrooms'               => 'required|integer|min:0',
            'beds'                   => 'required|integer|min:0',
            'bathrooms'              => 'required|integer|min:0',
            'cleaning_fee'           => 'nullable|numeric|min:0',
            'service_fee_percentage' => 'required|numeric|min:0|max:30',
            'amenities'              => 'nullable|array',
            'house_rules'            => 'nullable|array',
            'address'                => 'nullable|string',
            'latitude'               => 'nullable|numeric',
            'longitude'              => 'nullable|numeric',
            'status'                 => 'required|in:draft,pending,active,inactive,rejected',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['staff_id'] = auth('staff')->id();

        Homestay::create($validated);

        return redirect()->route('homestays.index')
            ->with('success', 'Homestay created successfully.');
    }

    public function show(Homestay $homestay)
    {
        $homestay->load(['location', 'owner', 'bookings']);
        return view('admin.homestays.show', compact('homestay'));
    }

    public function edit(Homestay $homestay)
    {
        $locations = Location::pluck('name', 'id');
        return view('admin.homestays.edit', compact('homestay', 'locations'));
    }

    public function update(Request $request, Homestay $homestay)
    {
        $validated = $request->validate([
            'location_id'            => 'required|exists:locations,id',
            'title'                  => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_night'        => 'required|numeric|min:0',
            'max_guests'             => 'required|integer|min:1',
            'bedrooms'               => 'required|integer|min:0',
            'beds'                   => 'required|integer|min:0',
            'bathrooms'              => 'required|integer|min:0',
            'cleaning_fee'           => 'nullable|numeric|min:0',
            'service_fee_percentage' => 'required|numeric|min:0|max:30',
            'amenities'              => 'nullable|array',
            'house_rules'            => 'nullable|array',
            'address'                => 'nullable|string',
            'latitude'               => 'nullable|numeric',
            'longitude'              => 'nullable|numeric',
            'status'                 => 'required|in:draft,pending,active,inactive,rejected',
        ]);

        if ($request->filled('title') && $homestay->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        $homestay->update($validated);

        return redirect()->route('homestays.index')
            ->with('success', 'Homestay updated successfully.');
    }

    public function destroy(Homestay $homestay)
    {
        $homestay->delete();
        return redirect()->route('homestays.index')
            ->with('success', 'Homestay deleted successfully.');
    }
}