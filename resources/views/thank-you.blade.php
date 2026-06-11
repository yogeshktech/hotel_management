@extends('layouts.site')

@section('title', 'Booking Confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="site-card p-5">
                <div class="display-3 mb-3">🎉</div>
                <h1 class="site-section-title">Payment successful!</h1>
                <p class="text-muted mb-4">Your booking is confirmed. A confirmation has been recorded and your dates are reserved.</p>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-site-gold me-2">View My Bookings</a>
                <a href="{{ route('home') }}" class="btn btn-site-outline">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection
