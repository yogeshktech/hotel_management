@extends('layouts.vendor')

@section('title', 'Dashboard')
@section('page-title', 'Vendor Dashboard')

@section('content')
@if($profile && $profile->status !== 'approved')
    <div class="alert alert-warning">Your vendor account is <strong>{{ $profile->status }}</strong>. Property management is limited until approved.</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card-panel h-100">
            <div class="card-header">Setup Progress</div>
            <div class="card-body">
                @php
                    $steps = [
                        ['key' => 'approved', 'label' => 'Vendor Approved', 'route' => null],
                        ['key' => 'profile', 'label' => 'Complete Profile', 'route' => 'vendor.profile.edit'],
                        ['key' => 'documents', 'label' => 'Upload Documents (PAN + ID)', 'route' => 'vendor.documents.index'],
                        ['key' => 'locations', 'label' => 'Add Location / Destination', 'route' => 'vendor.locations.create'],
                        ['key' => 'properties', 'label' => 'Add Property', 'route' => 'vendor.properties.create'],
                        ['key' => 'rooms', 'label' => 'Add Rooms', 'route' => 'vendor.properties.index'],
                    ];
                @endphp
                @foreach($steps as $step)
                    <div class="step-item">
                        <span class="step-dot {{ ($onboarding[$step['key']] ?? false) ? 'done' : 'pending' }}">
                            {{ ($onboarding[$step['key']] ?? false) ? '✓' : '!' }}
                        </span>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $step['label'] }}</div>
                            @if(!($onboarding[$step['key']] ?? false) && $step['route'])
                                <a href="{{ route($step['route']) }}" class="small">Complete now →</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-md-4"><div class="stat-card"><div class="stat-value">{{ $stats['total_properties'] }}</div><div class="stat-label">Properties</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-value text-success">{{ $stats['active_properties'] }}</div><div class="stat-label">Active</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-value text-warning">{{ $stats['pending_properties'] }}</div><div class="stat-label">Pending Approval</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-value">{{ $stats['total_rooms'] }}</div><div class="stat-label">Total Rooms</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-value">{{ $stats['total_bookings'] }}</div><div class="stat-label">Bookings</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-value text-primary">{{ $stats['checked_in_now'] }}</div><div class="stat-label">Checked In Now</div></div></div>
            <div class="col-md-6"><div class="stat-card"><div class="stat-value">₹{{ number_format($stats['total_revenue'], 0) }}</div><div class="stat-label">Total Revenue (Paid)</div></div></div>
            <div class="col-md-6"><div class="stat-card"><div class="stat-value">₹{{ number_format($stats['month_revenue'], 0) }}</div><div class="stat-label">This Month Revenue</div></div></div>
            <div class="col-md-6"><div class="stat-card"><div class="stat-value">⭐ {{ $stats['avg_rating'] ?: '—' }}</div><div class="stat-label">Avg. Rating</div></div></div>
            <div class="col-md-6"><div class="stat-card"><div class="stat-value">{{ $stats['confirmed_bookings'] }}</div><div class="stat-label">Confirmed Bookings</div></div></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                Recent Bookings
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.bookings.create-offline') }}" class="btn btn-sm btn-primary">+ Offline Booking</a>
                    <a href="{{ route('vendor.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Ref</th><th>Guest</th><th>Property</th><th>Check-in</th><th>Status</th><th>Amount</th></tr></thead>
                    <tbody>
                        @forelse($stats['recent_bookings'] as $booking)
                            <tr>
                                <td>{{ $booking->booking_reference }}</td>
                                <td>{{ $booking->customer->name ?? 'Guest' }}</td>
                                <td>{{ $booking->homestay->title ?? '—' }}</td>
                                <td>{{ $booking->check_in?->format('d M Y') }}</td>
                                <td><span class="badge bg-secondary">{{ $booking->status }}</span></td>
                                <td>₹{{ number_format($booking->total_price, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                My Properties
                @if($profile?->canManageProperties())
                    <a href="{{ route('vendor.properties.create') }}" class="btn btn-sm btn-primary">+ Add Property</a>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($stats['properties'] as $property)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $property->title }}</div>
                            <small class="text-muted">{{ $property->location->name ?? '' }} · {{ $property->rooms->count() }} rooms · {{ $property->status }}</small>
                        </div>
                        <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-sm btn-outline-primary">Open</a>
                    </div>
                @empty
                    <div class="p-3 text-muted">No properties yet. Complete profile & documents first.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
