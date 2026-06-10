@extends('layouts.admin')

@section('title', 'Property Review')
@section('page-title', 'Review Property: ' . $property->title)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        @if($property->images->count())
        <div class="card-panel mb-3">
            <div class="card-header">Images ({{ $property->images->count() }})</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($property->images as $image)
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid rounded border" alt="{{ $image->caption }}" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                        @if($image->is_primary)<span class="badge bg-primary mt-1">Primary</span>@endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card-panel mb-3">
            <div class="card-header">Property Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Title</dt>
                    <dd class="col-sm-8">{{ $property->title }}</dd>
                    <dt class="col-sm-4">Vendor</dt>
                    <dd class="col-sm-8">{{ $property->owner->name ?? 'N/A' }} · {{ $property->owner->phone ?? '' }}</dd>
                    <dt class="col-sm-4">Location</dt>
                    <dd class="col-sm-8">{{ $property->location->name ?? '—' }} — {{ $property->address }}</dd>
                    @if($property->latitude)
                    <dt class="col-sm-4">Coordinates</dt>
                    <dd class="col-sm-8">
                        {{ $property->latitude }}, {{ $property->longitude }}
                        <a href="https://maps.google.com/?q={{ $property->latitude }},{{ $property->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">View on Map</a>
                    </dd>
                    @endif
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $property->description }}</dd>
                    <dt class="col-sm-4">Capacity</dt>
                    <dd class="col-sm-8">{{ $property->max_guests }} guests · {{ $property->bedrooms }} BR · {{ $property->beds }} beds · {{ $property->bathrooms }} bath</dd>
                    <dt class="col-sm-4">Price</dt>
                    <dd class="col-sm-8">₹{{ number_format($property->price_per_night) }}/night</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary">{{ $property->status }}</span></dd>
                </dl>
            </div>
        </div>

        @if($property->rooms->count())
        <div class="card-panel">
            <div class="card-header">Rooms ({{ $property->rooms->count() }})</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Room</th><th>Type</th><th>Capacity</th><th>Units</th><th>Price</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($property->rooms as $room)
                        <tr>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->room_type }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>{{ $room->total_units }}</td>
                            <td>₹{{ number_format($room->price_per_night) }}</td>
                            <td>{{ $room->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($property->status === 'pending')
        <div class="card-panel mb-3">
            <div class="card-header">Approval Actions</div>
            <div class="card-body">
                @can('properties.approve')
                <form action="{{ route('admin.properties.approve', $property) }}" method="post" class="mb-3">
                    @csrf
                    <p class="small text-muted">Verify images, location on map, and room setup before approving.</p>
                    <button class="btn btn-success w-100">✓ Approve & Publish</button>
                </form>
                <form action="{{ route('admin.properties.reject', $property) }}" method="post">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Note</label>
                        <textarea name="rejection_note" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-danger w-100">✗ Reject Property</button>
                </form>
                @endcan
            </div>
        </div>
        @endif

        <div class="card-panel">
            <div class="card-header">Vendor Info</div>
            <div class="card-body">
                @if($property->owner?->vendorProfile)
                    <p class="mb-1"><strong>{{ $property->owner->vendorProfile->business_name }}</strong></p>
                    <p class="mb-1 small">{{ $property->owner->vendorProfile->contact_phone }}</p>
                    <p class="mb-0 small text-muted">Vendor Status: {{ $property->owner->vendorProfile->status }}</p>
                @else
                    <p class="text-muted small">No vendor profile linked.</p>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary w-100 mt-3">← Back to Properties</a>
    </div>
</div>
@endsection
