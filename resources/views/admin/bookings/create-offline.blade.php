@extends('layouts.admin')

@section('page-title', 'Create Offline Booking')

@section('content')
<div class="alert alert-info small">
    Walk-in guest ke liye neeche naam/phone bharein — customer auto-register ho jayega.
    Ya pehle se registered customer select karein.
    Dates save hote hi <strong>online booking block</strong> ho jati hain.
</div>

<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.bookings.store-offline') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span>Returning customer (optional)</span>
                        @can('customers.create')
                        <a href="{{ route('admin.customers.create', ['redirect' => route('admin.bookings.create-offline')]) }}" class="small">+ Register new</a>
                        @endcan
                    </label>
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">— New walk-in guest —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id', request('customer_id')) == $c->id)>
                                {{ $c->name }} — {{ $c->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room *</label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">Select room</option>
                        @foreach($properties as $p)
                            <optgroup label="{{ $p->title }}">
                                @foreach($p->rooms as $r)
                                    <option value="{{ $r->id }}" @selected(old('room_id') == $r->id)>{{ $r->name }} — ₹{{ number_format($r->price_per_night, 0) }}/night</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
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
                    <textarea name="guest_notes" class="form-control" rows="2">{{ old('guest_notes') }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create Offline Booking</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const customerSelect = document.getElementById('customer_id');
    const guestFields = document.querySelectorAll('.guest-fields input');
    function toggleGuestFields() {
        const hide = customerSelect.value !== '';
        document.querySelectorAll('.guest-fields').forEach(el => el.style.display = hide ? 'none' : '');
        guestFields.forEach(el => {
            if (hide) el.removeAttribute('required'); else if (el.name !== 'guest_email') el.setAttribute('required', 'required');
        });
    }
    customerSelect.addEventListener('change', toggleGuestFields);
    toggleGuestFields();
</script>
@endpush
@endsection
