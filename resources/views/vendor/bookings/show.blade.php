@extends('layouts.vendor')

@section('title', 'Booking ' . $booking->booking_reference)
@section('page-title', 'Booking Details')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $booking->booking_reference }}</span>
                <span class="badge {{ $booking->booking_channel === 'offline' ? 'text-bg-dark' : 'text-bg-primary' }}">
                    {{ ucfirst($booking->booking_channel) }}
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Guest</dt>
                    <dd class="col-sm-8">{{ $booking->customer->name }} · {{ $booking->customer->phone }}</dd>
                    <dt class="col-sm-4">Property</dt>
                    <dd class="col-sm-8">{{ $booking->homestay->title }}</dd>
                    <dt class="col-sm-4">Room</dt>
                    <dd class="col-sm-8">{{ $booking->room->name ?? '—' }}</dd>
                    <dt class="col-sm-4">Package</dt>
                    <dd class="col-sm-8">{{ ucfirst($booking->guest_package) }} ({{ $booking->guests }} guests)</dd>
                    <dt class="col-sm-4">Check-in / Check-out</dt>
                    <dd class="col-sm-8">{{ $booking->check_in->format('d M Y') }} → {{ $booking->check_out->format('d M Y') }} ({{ $booking->nights }} nights)</dd>
                    <dt class="col-sm-4">Total</dt>
                    <dd class="col-sm-8">₹{{ number_format($booking->total_price, 0) }} · {{ $booking->payment_status }} ({{ $booking->payment_method }})</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><span class="badge text-bg-secondary">{{ $booking->status }}</span></dd>
                    <dt class="col-sm-4">Room vacant from</dt>
                    <dd class="col-sm-8">{{ $booking->vacant_on }}</dd>
                    @if($booking->guest_notes)
                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8">{{ $booking->guest_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-panel mb-3">
            <div class="card-header">Actions</div>
            <div class="card-body d-grid gap-2">
                @if($booking->status === 'confirmed')
                    <form action="{{ route('vendor.bookings.check-in', $booking) }}" method="POST">@csrf<button class="btn btn-success w-100">Check In Guest</button></form>
                @endif
                @if($booking->status === 'checked_in')
                    <form action="{{ route('vendor.bookings.check-out', $booking) }}" method="POST">@csrf<button class="btn btn-primary w-100">Check Out Guest</button></form>
                @endif
                @if(!in_array($booking->status, ['checked_out', 'cancelled']))
                    <form action="{{ route('vendor.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel booking and release dates for online booking?')">
                        @csrf
                        <button class="btn btn-outline-danger w-100">Cancel Booking</button>
                    </form>
                @endif
            </div>
        </div>

        @if($booking->booking_channel === 'offline')
        <div class="alert alert-success small">
            Ye offline booking online calendar mein block hai. Cancel ya check-out ke baad dates dubara available ho sakti hain.
        </div>
        @endif

        <a href="{{ route('vendor.bookings.index') }}" class="btn btn-secondary w-100">← All Bookings</a>
    </div>
</div>
@endsection
