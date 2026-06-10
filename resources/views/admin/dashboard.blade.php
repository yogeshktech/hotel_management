@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_customers'] }}</div>
            <div class="stat-label">Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value text-warning">{{ $stats['pending_vendors'] }}</div>
            <div class="stat-label">Pending Vendors</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value text-warning">{{ $stats['pending_properties'] }}</div>
            <div class="stat-label">Pending Properties</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value text-success">{{ $stats['total_bookings'] }}</div>
            <div class="stat-label">Total Bookings</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_vendors'] }}</div>
            <div class="stat-label">Registered Vendors</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_properties'] }}</div>
            <div class="stat-label">Total Properties</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value text-success">{{ $stats['active_properties'] }}</div>
            <div class="stat-label">Active Properties</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-primary">{{ $stats['online_bookings'] }}</div><div class="stat-label">Online Bookings</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-secondary">{{ $stats['offline_bookings'] }}</div><div class="stat-label">Offline Bookings</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-success">{{ $stats['checked_in_now'] }}</div><div class="stat-label">Currently Checked In</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">{{ $stats['total_staff'] }}</div><div class="stat-label">Team & Staff</div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                Pending Vendor Approvals
                <a href="{{ route('admin.vendors.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['pending_vendors_list'] as $vendor)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $vendor->business_name }}</div>
                            <small class="text-muted">{{ $vendor->staff->email }} · {{ $vendor->contact_phone }}</small>
                        </div>
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-sm btn-primary">Review</a>
                    </div>
                @empty
                    <div class="p-3 text-muted">No pending vendor applications.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                Pending Property Approvals
                <a href="{{ route('admin.properties.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['pending_properties_list'] as $property)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $property->title }}</div>
                            <small class="text-muted">{{ $property->owner->name ?? 'N/A' }} · {{ $property->location->name ?? '' }}</small>
                        </div>
                        <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-sm btn-primary">Review</a>
                    </div>
                @empty
                    <div class="p-3 text-muted">No pending properties.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card-panel mt-3">
    <div class="card-header">Recent Bookings</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Property</th>
                    <th>Check-in</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recent_bookings'] as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->customer->name ?? 'Guest' }}</td>
                        <td>{{ $booking->homestay->title ?? 'N/A' }}</td>
                        <td>{{ $booking->check_in?->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $booking->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted text-center py-3">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
