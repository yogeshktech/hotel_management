<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Homestay;
use App\Models\Location;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->orderBy('order')
            ->take(3)
            ->get();

        $locations = Location::withCount(['homestays' => fn ($q) => $q->active()])
            ->having('homestays_count', '>', 0)
            ->orderByDesc('homestays_count')
            ->take(6)
            ->get();

        $featured = Homestay::active()
            ->with(['location', 'images'])
            ->withAvg('reviews', 'overall_rating')
            ->latest()
            ->take(8)
            ->get();

        $allLocations = Location::orderBy('name')->get();

        return view('site.home', compact('banners', 'locations', 'featured', 'allLocations'));
    }
}
