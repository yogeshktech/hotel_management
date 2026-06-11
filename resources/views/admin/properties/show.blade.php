@extends('layouts.admin')

@section('title', 'Property Review')
@section('page-title', 'Review Property: ' . $property->title)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel mb-3">
            <div class="card-header">Property Gallery ({{ $property->images->count() }})</div>
            <div class="card-body">
                @if($property->images->count())
                    <div class="row g-2">
                        @foreach($property->images as $image)
                        <div class="col-md-4">
                            <a href="{{ $image->url }}" target="_blank">
                                <img src="{{ $image->url }}" class="img-fluid rounded border w-100" style="height:180px;object-fit:cover;" alt="Property photo">
                            </a>
                            @if($image->is_primary)<span class="badge bg-primary mt-1">Primary</span>@endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Vendor has not uploaded property gallery images yet.</p>
                @endif
            </div>
        </div>

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
            <div class="card-body p-0">
                @foreach($property->rooms as $room)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $room->name }}</strong>
                            <span class="text-muted small">· {{ ucfirst($room->room_type) }} · {{ $room->capacity }} guests · {{ $room->total_units }} units · ₹{{ number_format($room->price_per_night) }}/night</span>
                        </div>
                        <span class="badge bg-secondary">{{ $room->status }}</span>
                    </div>
                    @if($room->images->count())
                        <div class="row g-2">
                            @foreach($room->images as $image)
                            <div class="col-4 col-md-3">
                                <a href="{{ $image->url }}" target="_blank">
                                    <img src="{{ $image->url }}" class="img-fluid rounded border w-100" style="height:100px;object-fit:cover;" alt="Room photo">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No room photos uploaded.</p>
                    @endif
                </div>
                @endforeach
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
                    <p class="small text-muted">Verify gallery images, location, and room setup before approving.</p>
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
                    <p class="mb-2 small text-muted">Vendor Status: {{ $property->owner->vendorProfile->status }}</p>
                    <a href="{{ route('admin.vendors.show', $property->owner->vendorProfile) }}" class="btn btn-sm btn-outline-primary">View Vendor & Documents</a>
                @else
                    <p class="text-muted small">No vendor profile linked.</p>
                @endif
            </div>
        </div>

        @can('properties.delete')
        <form action="{{ route('admin.properties.destroy', $property) }}" method="post" class="mt-3" onsubmit="return confirm('Permanently delete this property and all rooms?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger w-100">Delete Property</button>
        </form>
        @endcan

        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary w-100 mt-2">← Back to Properties</a>
    </div>
</div>
@endsection
