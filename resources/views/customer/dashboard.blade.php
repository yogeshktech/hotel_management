@extends('layouts.site')

@section('title', 'My Bookings')

@section('content')
<section class="py-4 bg-white border-bottom">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="site-section-title h4 mb-0">Hello, {{ $customer->name }}</h1>
            <p class="text-muted small mb-0">Manage your reservations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('properties.index') }}" class="btn btn-site-gold btn-sm">Book a Stay</a>
            <a href="{{ route('customer.profile.edit') }}" class="btn btn-site-outline btn-sm">Profile</a>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="site-card p-3 text-center"><div class="h3 mb-0 fw-bold">{{ $stats['total'] }}</div><small class="text-muted">Total</small></div></div>
        <div class="col-6 col-md-3"><div class="site-card p-3 text-center"><div class="h3 mb-0 fw-bold text-primary">{{ $stats['upcoming'] }}</div><small class="text-muted">Upcoming</small></div></div>
        <div class="col-6 col-md-3"><div class="site-card p-3 text-center"><div class="h3 mb-0 fw-bold text-success">{{ $stats['completed'] }}</div><small class="text-muted">Completed</small></div></div>
        <div class="col-6 col-md-3"><div class="site-card p-3 text-center"><div class="h3 mb-0 fw-bold">{{ $stats['reviews'] }}</div><small class="text-muted">Reviews</small></div></div>
    </div>

    <div class="site-card overflow-hidden">
        <div class="p-3 border-bottom fw-bold">My Bookings</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Property</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td><a href="{{ route('bookings.show', $booking) }}" class="fw-semibold text-decoration-none">{{ $booking->booking_reference }}</a></td>
                        <td>
                            <div class="fw-medium">{{ $booking->homestay->title ?? '—' }}</div>
                            <small class="text-muted">{{ ucfirst($booking->guest_package) }}</small>
                        </td>
                        <td class="small">{{ $booking->check_in?->format('d M') }} → {{ $booking->check_out?->format('d M Y') }}</td>
                        <td class="fw-semibold">₹{{ number_format($booking->total_price, 0) }}</td>
                        <td>
                            <span class="badge {{ $booking->status === 'confirmed' ? 'text-bg-success' : ($booking->status === 'cancelled' ? 'text-bg-secondary' : 'text-bg-warning') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            @if($booking->payment_status === 'pending')
                                <span class="badge text-bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @if($booking->payment_status === 'pending' && $booking->status !== 'cancelled')
                                <a href="{{ route('payments.create', $booking) }}" class="btn btn-sm btn-site-gold">Pay</a>
                            @endif
                            @if(in_array($booking->status, ['checked_out','completed']) && !$booking->review)
                                <a href="{{ route('customer.reviews.create', $booking) }}" class="btn btn-sm btn-warning">Review</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No bookings yet. <a href="{{ route('properties.index') }}">Find a stay</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())<div class="p-3">{{ $bookings->links() }}</div>@endif
    </div>
</div>
@endsection
