@extends('layouts.site')

@section('title', 'Complete Booking')

@section('content')
@php $img = $property->primaryImageUrl() ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&q=80'; @endphp
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <h1 class="site-section-title mb-0">Complete your booking</h1>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <form action="{{ route('bookings.store') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="site-card p-4 mb-4">
                    <h2 class="h6 fw-bold mb-3">Stay details</h2>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Room *</label>
                        <select name="room_id" id="room_id" class="form-select" required>
                            @foreach($property->rooms->where('status', 'active') as $room)
                                <option value="{{ $room->id }}" @selected($selectedRoom?->id == $room->id)>
                                    {{ $room->name }} — ₹{{ number_format($room->price_per_night, 0) }}/night
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check-in *</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Check-out *</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" value="{{ $checkOut }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Package *</label>
                            <select name="guest_package" id="guest_package" class="form-select" required>
                                @foreach(['adult'=>'Adult (Single)','couple'=>'Couple','family'=>'Family','child'=>'Child Only'] as $v=>$l)
                                    <option value="{{ $v }}" @selected($guestPackage === $v)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="childCountWrap" style="{{ $guestPackage === 'family' ? '' : 'display:none' }}">
                            <label class="form-label fw-semibold">Children</label>
                            <select name="child_count" id="child_count" class="form-select">
                                @foreach(range(0,4) as $i)<option value="{{ $i }}" @selected($childCount == $i)>{{ $i }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Promo code</label>
                        <input type="text" name="promo_code" id="promo_code" class="form-control" placeholder="Optional">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Special requests</label>
                        <textarea name="guest_notes" class="form-control" rows="2" placeholder="Early check-in, dietary needs..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-site-gold btn-lg w-100" id="submitBtn">Proceed to Payment</button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="site-booking-panel" id="bookingPanel" data-calculate-url="{{ route('bookings.calculate-price') }}">
                <img src="{{ $img }}" class="rounded mb-3 w-100" style="height:160px;object-fit:cover" alt="">
                <h2 class="h6 fw-bold">{{ $property->title }}</h2>
                <p class="small text-muted mb-3">{{ $property->location->name }} · {{ $property->address }}</p>
                <div id="pricePreview" class="border rounded p-3 bg-light small">
                    <p class="text-muted mb-0">Calculating price...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/booking.js'])
<script>
document.getElementById('guest_package')?.addEventListener('change', function() {
    document.getElementById('childCountWrap').style.display = this.value === 'family' ? '' : 'none';
});
['room_id','check_in','check_out','guest_package','child_count','promo_code'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => window.refreshBookingPrice?.());
});
</script>
@endpush
