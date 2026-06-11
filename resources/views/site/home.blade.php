@extends('layouts.site')

@section('title', 'Premium Homestays & Hotels')

@section('content')
@php
    $heroImg = $banners->first()?->image ? asset('storage/'.$banners->first()->image) : 'https://images.unsplash.com/photo-1582719508461-905c778771fd?w=1600&q=80';
@endphp
<section class="site-hero d-flex align-items-center">
    <img src="{{ $heroImg }}" alt="" class="site-hero-bg">
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 mb-3 px-3 py-2">Luxury Stays · Homestays · Resorts</span>
                <h1 class="site-serif display-4 fw-bold mb-3">Discover extraordinary stays across India</h1>
                <p class="lead mb-0 opacity-90">Curated properties with seamless online booking — same experience on web & mobile API.</p>
            </div>
            <div class="col-lg-5">
                <form action="{{ route('properties.index') }}" method="GET" class="site-search-box">
                    <h2 class="h6 fw-bold mb-3 text-dark">Find your perfect stay</h2>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Destination</label>
                        <select name="location_id" class="form-select">
                            <option value="">All destinations</option>
                            @foreach($allLocations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}{{ $loc->city ? ', '.$loc->city : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Check-in</label>
                            <input type="date" name="check_in" class="form-control" value="{{ now()->addDay()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Check-out</label>
                            <input type="date" name="check_out" class="form-control" value="{{ now()->addDays(2)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Guests</label>
                        <select name="guests" class="form-select">
                            @foreach(range(1, 8) as $g)
                                <option value="{{ $g }}">{{ $g }} guest{{ $g > 1 ? 's' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-site-gold w-100">Search Stays</button>
                </form>
            </div>
        </div>
    </div>
</section>

@if($locations->count())
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="site-badge">Destinations</span>
                <h2 class="site-section-title mt-2 mb-0">Popular places</h2>
            </div>
            <a href="{{ route('locations.index') }}" class="btn btn-site-outline btn-sm">View all</a>
        </div>
        <div class="row g-3">
            @foreach($locations as $location)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('locations.show', $location) }}" class="site-dest-card">
                    <img src="https://images.unsplash.com/photo-{{ ['1506905925346-21bda4d32df4','1469854523086-cc02fe5d8800','1476514525535-07fb3b4ae5f1','1501785888941-7e702e7fd2f3','1518548419970-44786e03528f','1520250497591-112f2f40a3f4'][$loop->index % 6] }}?w=400&q=80" alt="{{ $location->name }}">
                    <div class="dest-label">
                        <h3>{{ $location->name }}</h3>
                        <span>{{ $location->homestays_count }} stays</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="site-badge">Handpicked</span>
            <h2 class="site-section-title mt-2">Featured properties</h2>
            <p class="text-muted">Verified homestays with premium amenities and instant booking</p>
        </div>
        <div class="row g-4">
            @forelse($featured as $property)
                <div class="col-md-6 col-lg-3">@include('site.partials.property-card', ['property' => $property])</div>
            @empty
                <div class="col-12 text-center text-muted py-5">No properties available yet. Check back soon!</div>
            @endforelse
        </div>
        @if($featured->count())
        <div class="text-center mt-4">
            <a href="{{ route('properties.index') }}" class="btn btn-site-primary">Browse all stays</a>
        </div>
        @endif
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['🏨', 'Verified Properties', 'Every stay is reviewed by our team before going live.'],
                ['📱', 'Web & API Booking', 'Book from website or mobile app — same real-time availability.'],
                ['🔒', 'Secure Payments', 'Razorpay-powered checkout with instant confirmation.'],
                ['⭐', 'Guest Reviews', 'Real ratings from verified guests after checkout.'],
            ] as [$icon, $title, $desc])
            <div class="col-md-6 col-lg-3">
                <div class="site-card p-4 h-100">
                    <div class="site-feature-icon mb-3">{{ $icon }}</div>
                    <h3 class="h6 fw-bold">{{ $title }}</h3>
                    <p class="text-muted small mb-0">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
