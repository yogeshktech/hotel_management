@extends('layouts.site')

@section('title', 'Destinations')

@section('content')
<section class="py-5 bg-white">
    <div class="container text-center">
        <span class="site-badge">Explore India</span>
        <h1 class="site-section-title mt-2">Destinations</h1>
        <p class="text-muted">Find homestays and boutique hotels in your favourite places</p>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        @forelse($locations as $location)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('locations.show', $location) }}" class="site-dest-card d-block" style="min-height:240px">
                <img src="https://images.unsplash.com/photo-{{ ['1506905925346-21bda4d32df4','1469854523086-cc02fe5d8800','1476514525535-07fb3b4ae5f1'][$loop->index % 3] }}?w=600&q=80" alt="{{ $location->name }}">
                <div class="dest-label">
                    <h3>{{ $location->name }}</h3>
                    <span>{{ $location->homestays_count }} {{ Str::plural('stay', $location->homestays_count) }} · {{ $location->city ?? $location->country }}</span>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">No destinations yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $locations->links() }}</div>
</div>
@endsection
