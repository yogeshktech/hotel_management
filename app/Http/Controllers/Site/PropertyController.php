<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Homestay;
use App\Models\Location;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::orderBy('name')->get();

        $properties = Homestay::active()
            ->with(['location', 'images'])
            ->withAvg('reviews', 'overall_rating')
            ->when($request->location_id, fn ($q, $id) => $q->where('location_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('address', 'like', "%{$s}%");
            }))
            ->when($request->min_price, fn ($q, $p) => $q->where('price_per_night', '>=', $p))
            ->when($request->max_price, fn ($q, $p) => $q->where('price_per_night', '<=', $p))
            ->when($request->guests, fn ($q, $g) => $q->where('max_guests', '>=', $g))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('site.properties.index', compact('properties', 'locations'));
    }

    public function show(Homestay $property)
    {
        if ($property->status !== 'active') {
            abort(404);
        }

        $property->load(['location', 'images', 'rooms.pricings', 'rooms.addons', 'rooms.images']);
        $property->loadAvg('reviews', 'overall_rating');
        $property->increment('view_count');

        $reviews = $property->reviews()
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('site.properties.show', compact('property', 'reviews'));
    }
}
