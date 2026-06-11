@extends('layouts.site')

@section('title', $property->title)

@section('content')
@php
    $images = $property->images;
    $mainImg = $images->first()?->url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80';
    $rating = $property->reviews_avg_overall_rating ? round($property->reviews_avg_overall_rating, 1) : null;
    $activeRooms = $property->rooms->where('status', 'active');
@endphp

<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb" class="small mb-2">
            <a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a>
            <span class="text-muted mx-1">/</span>
            <a href="{{ route('properties.index') }}" class="text-muted text-decoration-none">Stays</a>
            <span class="text-muted mx-1">/</span>
            <span>{{ $property->title }}</span>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h1 class="site-section-title mb-1">{{ $property->title }}</h1>
                <p class="text-muted mb-0">📍 {{ $property->location->name ?? '' }} · {{ $property->address }}</p>
            </div>
            @if($rating)<span class="site-rating fs-6">★ {{ $rating }} rating</span>@endif
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-3">
                <img src="{{ $mainImg }}" id="galleryMain" class="site-gallery-main" alt="{{ $property->title }}">
            </div>
            @if($images->count() > 1)
            <div class="row g-2 mb-4">
                @foreach($images->take(5) as $image)
                <div class="col">
                    <img src="{{ $image->url }}" class="site-gallery-thumb {{ $loop->first ? 'active' : '' }}"
                         onclick="document.getElementById('galleryMain').src=this.src; document.querySelectorAll('.site-gallery-thumb').forEach(t=>t.classList.remove('active')); this.classList.add('active');"
                         alt="Gallery">
                </div>
                @endforeach
            </div>
            @endif

            <div class="site-card p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">About this property</h2>
                <p class="text-muted mb-3">{{ $property->description }}</p>
                <div class="row g-2 text-center">
                    <div class="col-3"><div class="site-amenity"><strong>{{ $property->max_guests }}</strong><br><small>Guests</small></div></div>
                    <div class="col-3"><div class="site-amenity"><strong>{{ $property->bedrooms }}</strong><br><small>Bedrooms</small></div></div>
                    <div class="col-3"><div class="site-amenity"><strong>{{ $property->beds }}</strong><br><small>Beds</small></div></div>
                    <div class="col-3"><div class="site-amenity"><strong>{{ $property->bathrooms }}</strong><br><small>Baths</small></div></div>
                </div>
                @if($property->amenities)
                <h3 class="h6 fw-bold mt-4 mb-2">Amenities</h3>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($property->amenities as $amenity)
                        <span class="site-amenity">{{ $amenity }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="site-card p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Rooms & packages</h2>
                @forelse($activeRooms as $room)
                @php $roomImg = $room->images->first()?->url ?? $mainImg; @endphp
                <div class="site-room-card mb-3" data-room-id="{{ $room->id }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <img src="{{ $roomImg }}" class="rounded w-100" style="height:100px;object-fit:cover" alt="{{ $room->name }}">
                        </div>
                        <div class="col-md-6">
                            <h3 class="h6 fw-bold mb-1">{{ $room->name }}</h3>
                            <p class="small text-muted mb-1">{{ ucfirst($room->room_type) }} · {{ $room->capacity }} guests · {{ $room->total_units }} unit(s)</p>
                            <p class="small mb-0">{{ \Illuminate\Support\Str::limit($room->description, 100) }}</p>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <div class="site-price mb-2">₹{{ number_format($room->price_per_night, 0) }}<small>/night</small></div>
                            <button type="button" class="btn btn-site-outline btn-sm select-room-btn" data-room-id="{{ $room->id }}">Select</button>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No rooms available at the moment.</p>
                @endforelse
            </div>

            @if($reviews->count())
            <div class="site-card p-4">
                <h2 class="h5 fw-bold mb-3">Guest reviews</h2>
                @foreach($reviews as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->customer->name ?? 'Guest' }}</strong>
                        <span class="site-rating">★ {{ $review->overall_rating }}</span>
                    </div>
                    <p class="small text-muted mb-0 mt-1">{{ $review->comment }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="site-booking-panel" id="bookingPanel"
                 data-calculate-url="{{ route('bookings.calculate-price') }}"
                 data-property-slug="{{ $property->slug }}">
                <div class="site-price mb-3">₹{{ number_format($property->price_per_night, 0) }} <small>from / night</small></div>

                <input type="hidden" id="room_id" value="{{ $activeRooms->first()?->id }}">

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Check-in</label>
                    <input type="date" id="check_in" class="form-control" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Check-out</label>
                    <input type="date" id="check_out" class="form-control" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Package</label>
                    <select id="guest_package" class="form-select">
                        <option value="adult">Adult (Single)</option>
                        <option value="couple" selected>Couple</option>
                        <option value="family">Family</option>
                        <option value="child">Child Only</option>
                    </select>
                </div>
                <div class="mb-3" id="childCountWrap" style="display:none">
                    <label class="form-label small fw-semibold">Children (0-4)</label>
                    <select id="child_count" class="form-select">
                        @foreach(range(0, 4) as $i)<option value="{{ $i }}">{{ $i }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Promo code</label>
                    <input type="text" id="promo_code" class="form-control" placeholder="Optional">
                </div>

                <div id="pricePreview" class="border rounded p-3 mb-3 bg-light small" style="display:none">
                    <div class="d-flex justify-content-between"><span>Nights</span><span id="pv_nights">—</span></div>
                    <div class="d-flex justify-content-between"><span>Base</span><span id="pv_base">—</span></div>
                    <div class="d-flex justify-content-between"><span>Fees</span><span id="pv_fees">—</span></div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span id="pv_total">—</span></div>
                    <div id="pv_avail" class="mt-2"></div>
                </div>

                @auth('customer')
                    <a href="#" id="bookNowBtn" class="btn btn-site-gold w-100">Book Now</a>
                @else
                    <a href="{{ route('customer.login', ['redirect' => url()->current()]) }}" class="btn btn-site-gold w-100">Sign in to Book</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/booking.js'])
<script>
document.querySelectorAll('.select-room-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('room_id').value = this.dataset.roomId;
        document.querySelectorAll('.site-room-card').forEach(c => c.classList.remove('selected'));
        this.closest('.site-room-card').classList.add('selected');
        if (window.refreshBookingPrice) window.refreshBookingPrice();
    });
});
document.getElementById('guest_package')?.addEventListener('change', function() {
    document.getElementById('childCountWrap').style.display = this.value === 'family' ? '' : 'none';
});
@auth('customer')
document.getElementById('bookNowBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    const params = new URLSearchParams({
        room_id: document.getElementById('room_id').value,
        check_in: document.getElementById('check_in').value,
        check_out: document.getElementById('check_out').value,
        guest_package: document.getElementById('guest_package').value,
        child_count: document.getElementById('child_count').value,
    });
    window.location = '{{ route('bookings.create', $property) }}?' + params.toString();
});
@endauth
</script>
@endpush
