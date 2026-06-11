@extends('layouts.vendor')

@section('title', 'Offline Booking')
@section('page-title', 'Create Offline Booking')

@section('content')
<div class="alert alert-info small">
    Walk-in / phone booking yahan add karo. Jaise hi save hogi, woh dates <strong>online booking ke liye automatically block</strong> ho jayengi — doosre customers woh room us period mein book nahi kar payenge.
</div>

<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('vendor.bookings.store-offline') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Property & Room *</label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">Select room</option>
                        @foreach($properties as $property)
                            <optgroup label="{{ $property->title }}">
                                @foreach($property->rooms as $room)
                                    <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                        {{ $room->name }} ({{ $room->total_units }} units) — ₹{{ number_format($room->price_per_night, 0) }}/night
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Returning guest (optional)</label>
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">— New walk-in guest —</option>
                        @foreach($pastGuests as $guest)
                            <option value="{{ $guest->id }}" @selected(old('customer_id') == $guest->id)>
                                {{ $guest->name }} — {{ $guest->phone }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Select to skip guest details below.</div>
                </div>

                <div class="col-md-4 guest-fields">
                    <label class="form-label">Guest Name *</label>
                    <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}">
                </div>
                <div class="col-md-4 guest-fields">
                    <label class="form-label">Guest Phone *</label>
                    <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" placeholder="+919900000001">
                </div>
                <div class="col-md-4 guest-fields">
                    <label class="form-label">Guest Email</label>
                    <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Package *</label>
                    <select name="guest_package" class="form-select" required>
                        <option value="adult">Adult (Single)</option>
                        <option value="couple">Couple</option>
                        <option value="family">Family</option>
                        <option value="child">Child Only</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Children (0-4)</label>
                    <select name="child_count" class="form-select">
                        @foreach(range(0, 4) as $i)
                            <option value="{{ $i }}" @selected(old('child_count', 0) == $i)>{{ $i }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Check-in *</label>
                    <input type="date" name="check_in" class="form-control" value="{{ old('check_in', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Check-out *</label>
                    <input type="date" name="check_out" class="form-control" value="{{ old('check_out') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="guest_notes" class="form-control" rows="2" placeholder="Special requests, ID reference, etc.">{{ old('guest_notes') }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Offline Booking & Block Dates</button>
                <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const customerSelect = document.getElementById('customer_id');
    const guestFields = document.querySelectorAll('.guest-fields input');
    function toggleGuestFields() {
        const hide = customerSelect.value !== '';
        guestFields.forEach(el => {
            el.closest('.guest-fields').style.display = hide ? 'none' : '';
            if (hide) el.removeAttribute('required'); else if (el.name !== 'guest_email') el.setAttribute('required', 'required');
        });
    }
    customerSelect.addEventListener('change', toggleGuestFields);
    toggleGuestFields();
</script>
@endpush
@endsection
