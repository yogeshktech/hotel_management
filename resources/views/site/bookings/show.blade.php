@extends('layouts.site')

@section('title', 'Booking '.$booking->booking_reference)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="site-card p-4 p-md-5 text-center mb-4">
                <div class="display-4 mb-3">{{ in_array($booking->status, ['confirmed','checked_in']) ? '✅' : ($booking->status === 'cancelled' ? '❌' : '⏳') }}</div>
                <h1 class="site-section-title">Booking {{ $booking->booking_reference }}</h1>
                <span class="site-status-badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }} text-white">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>

            <div class="site-card p-4 mb-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Property</small>
                        <strong>{{ $booking->homestay->title }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Room</small>
                        <strong>{{ $booking->room->name ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Check-in</small>
                        <strong>{{ $booking->check_in->format('d M Y') }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Check-out</small>
                        <strong>{{ $booking->check_out->format('d M Y') }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Package</small>
                        <strong>{{ ucfirst($booking->guest_package) }} ({{ $booking->guests }} guests)</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Total paid</small>
                        <strong class="site-price">₹{{ number_format($booking->total_price, 0) }}</strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Payment</small>
                        <strong>{{ ucfirst($booking->payment_status) }} via {{ $booking->payment_method }}</strong>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('customer.dashboard') }}" class="btn btn-site-primary">My Bookings</a>
                <a href="{{ route('properties.index') }}" class="btn btn-site-outline">Browse more stays</a>
                @if(!in_array($booking->status, ['checked_in','checked_out','cancelled']))
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel this booking?')">
                    @csrf
                    <button class="btn btn-outline-danger">Cancel Booking</button>
                </form>
                @endif
                @if($booking->payment_status === 'pending' && $booking->status !== 'cancelled')
                <a href="{{ route('payments.create', $booking) }}" class="btn btn-site-gold">Complete Payment</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
