<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Homestay;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount(['homestays' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->paginate(12);

        return view('site.locations.index', compact('locations'));
    }

    public function show(Location $location)
    {
        $properties = Homestay::active()
            ->where('location_id', $location->id)
            ->with(['location', 'images'])
            ->withAvg('reviews', 'overall_rating')
            ->latest()
            ->paginate(12);

        return view('site.locations.show', compact('location', 'properties'));
    }
}
