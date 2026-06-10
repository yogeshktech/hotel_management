@extends('layouts.customer')

@section('title', 'Dashboard')

@section('content')
<h4 class="mb-3">Welcome, {{ $customer->name }}</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3"><div class="h4 mb-0">{{ $stats['total'] }}</div><small class="text-muted">Total Bookings</small></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="h4 mb-0 text-primary">{{ $stats['upcoming'] }}</div><small class="text-muted">Upcoming</small></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="h4 mb-0 text-success">{{ $stats['completed'] }}</div><small class="text-muted">Completed</small></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="h4 mb-0">{{ $stats['reviews'] }}</div><small class="text-muted">Reviews Given</small></div></div>
</div>

<div class="card">
    <div class="card-header fw-semibold">My Bookings</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Property</th>
                    <th>Package</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Vacant From</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td><small>{{ $booking->booking_reference }}</small></td>
                    <td>{{ $booking->homestay->title ?? 'N/A' }}</td>
                    <td>{{ ucfirst($booking->guest_package) }} @if($booking->children_count)({{ $booking->children_count }} kids)@endif</td>
                    <td>{{ $booking->check_in?->format('d M Y') }}</td>
                    <td>{{ $booking->check_out?->format('d M Y') }}</td>
                    <td>{{ $booking->vacant_on }}</td>
                    <td><span class="badge bg-{{ $booking->booking_channel === 'online' ? 'primary' : 'secondary' }}">{{ ucfirst($booking->booking_channel) }}</span></td>
                    <td><span class="badge bg-info">{{ $booking->status }}</span></td>
                    <td>
                        @if(in_array($booking->status, ['checked_out','completed']) && !$booking->review)
                            <a href="{{ route('customer.reviews.create', $booking) }}" class="btn btn-sm btn-warning">Review</a>
                        @elseif($booking->review)
                            <span class="text-success small">✓ Reviewed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $bookings->links() }}</div>
</div>
@endsection
