@extends('layouts.site')

@section('title', 'Browse Stays')

@section('content')
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <h1 class="site-section-title mb-1">Find your stay</h1>
        <p class="text-muted mb-0">{{ $properties->total() }} properties available</p>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">
            <form method="GET" class="site-filter-bar sticky-top" style="top:90px">
                <h2 class="h6 fw-bold mb-3">Filters</h2>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Destination</label>
                    <select name="location_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Property name...">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Min ₹</label>
                        <input type="number" name="min_price" class="form-control form-control-sm" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Max ₹</label>
                        <input type="number" name="max_price" class="form-control form-control-sm" value="{{ request('max_price') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Min guests</label>
                    <select name="guests" class="form-select form-select-sm">
                        <option value="">Any</option>
                        @foreach(range(1, 8) as $g)
                            <option value="{{ $g }}" @selected(request('guests') == $g)>{{ $g }}+</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-site-primary btn-sm w-100">Apply</button>
                <a href="{{ route('properties.index') }}" class="btn btn-link btn-sm w-100 mt-1">Clear</a>
            </form>
        </div>
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($properties as $property)
                    <div class="col-md-6 col-xl-4">@include('site.partials.property-card', ['property' => $property])</div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted mb-3">No properties match your filters.</p>
                        <a href="{{ route('properties.index') }}" class="btn btn-site-outline">Clear filters</a>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $properties->links() }}</div>
        </div>
    </div>
</div>
@endsection
