@php
    $img = $property->primaryImageUrl() ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';
    $rating = $property->reviews_avg_overall_rating ? round($property->reviews_avg_overall_rating, 1) : null;
@endphp
<div class="site-card">
    <a href="{{ route('properties.show', $property) }}" class="text-decoration-none text-dark">
        <div class="position-relative">
            <img src="{{ $img }}" alt="{{ $property->title }}" class="site-card-img">
            @if($rating)
                <span class="site-rating position-absolute top-0 end-0 m-3">★ {{ $rating }}</span>
            @endif
            <span class="site-badge position-absolute bottom-0 start-0 m-3">{{ $property->location->name ?? 'India' }}</span>
        </div>
        <div class="p-3">
            <h3 class="h6 fw-semibold mb-1">{{ $property->title }}</h3>
            <p class="text-muted small mb-2 text-truncate">{{ $property->address }}</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="site-price">₹{{ number_format($property->price_per_night, 0) }} <small>/ night</small></span>
                <span class="small text-muted">{{ $property->max_guests }} guests · {{ $property->bedrooms }} BR</span>
            </div>
        </div>
    </a>
</div>
