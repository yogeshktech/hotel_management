@extends('layouts.admin')

@section('page-title', 'Create Offline Booking')

@section('content')
<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.bookings.store-offline') }}" method="post">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room *</label>
                    <select name="room_id" class="form-select" required>
                        @foreach($properties as $p)
                            @foreach($p->rooms as $r)
                                <option value="{{ $r->id }}">{{ $p->title }} — {{ $r->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
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
                    <label class="form-label">Children Count (1-4)</label>
                    <select name="child_count" class="form-select">
                        <option value="0">0</option>
                        @foreach(range(1, 4) as $i)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment</label>
                    <input type="text" name="payment_method" class="form-control" value="cash">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Check-in *</label>
                    <input type="date" name="check_in" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Check-out *</label>
                    <input type="date" name="check_out" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="guest_notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create Offline Booking</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
@endsection
