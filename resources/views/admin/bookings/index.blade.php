@extends('layouts.admin')

@section('page-title', 'Booking Tracking')

@section('content')
<div class="row g-2 mb-3">
    <div class="col"><div class="stat-card"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-primary">{{ $stats['online'] }}</div><div class="stat-label">Online</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-secondary">{{ $stats['offline'] }}</div><div class="stat-label">Offline</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-success">{{ $stats['checked_in'] }}</div><div class="stat-label">Checked In</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-warning">{{ $stats['upcoming'] }}</div><div class="stat-label">Upcoming</div></div></div>
</div>

<div class="d-flex justify-content-between mb-3">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm {{ !$status && !$channel ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
        <a href="{{ route('admin.bookings.index', ['channel' => 'online']) }}" class="btn btn-sm {{ $channel==='online' ? 'btn-primary' : 'btn-outline-secondary' }}">Online</a>
        <a href="{{ route('admin.bookings.index', ['channel' => 'offline']) }}" class="btn btn-sm {{ $channel==='offline' ? 'btn-primary' : 'btn-outline-secondary' }}">Offline</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'checked_in']) }}" class="btn btn-sm {{ $status==='checked_in' ? 'btn-primary' : 'btn-outline-secondary' }}">Checked In</a>
    </div>
    @can('bookings.manage')
    <a href="{{ route('admin.bookings.create-offline') }}" class="btn btn-sm btn-dark">+ Offline Booking</a>
    @endcan
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead>
                <tr>
                    <th>Ref</th><th>Customer</th><th>Property / Room</th><th>Package</th>
                    <th>Booked On</th><th>Check-in</th><th>Check-out</th><th>Vacant From</th>
                    <th>Channel</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr>
                    <td>{{ $b->booking_reference }}</td>
                    <td>{{ $b->customer->name ?? '—' }}<br><small class="text-muted">{{ $b->customer->phone ?? '' }}</small></td>
                    <td>{{ $b->homestay->title ?? '' }}<br><small>{{ $b->room->name ?? '—' }}</small></td>
                    <td>{{ ucfirst($b->guest_package) }} @if($b->children_count)({{ $b->children_count }} kids)@endif</td>
                    <td>{{ $b->booked_at?->format('d M Y H:i') }}</td>
                    <td>{{ $b->check_in?->format('d M Y') }}</td>
                    <td>{{ $b->check_out?->format('d M Y') }}</td>
                    <td>{{ $b->vacant_on }}</td>
                    <td><span class="badge bg-{{ $b->booking_channel==='online'?'primary':'secondary' }}">{{ ucfirst($b->booking_channel) }}</span></td>
                    <td><span class="badge bg-info">{{ $b->status }}</span></td>
                    <td><a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-sm btn-primary">Track</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $bookings->links() }}</div>
</div>
@endsection
