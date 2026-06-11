@extends('layouts.vendor')

@section('title', $property->title)
@section('page-title', $property->title)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <span class="badge {{ $property->status === 'active' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($property->status) }}</span>
        <span class="text-muted ms-2">{{ $property->location->name ?? '' }} · {{ $property->address }}</span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('vendor.properties.edit', $property) }}" class="btn btn-sm btn-outline-secondary">Edit Property</a>
        <a href="{{ route('vendor.rooms.create', $property) }}" class="btn btn-sm btn-primary">+ Add Room</a>
        @can('properties.delete')
        <form action="{{ route('vendor.properties.destroy', $property) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this property and all rooms?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete Property</button>
        </form>
        @endcan
    </div>
</div>

<div class="card-panel mb-3">
    <div class="card-header">Property Gallery ({{ $property->images->count() }})</div>
    <div class="card-body">
        <form action="{{ route('vendor.properties.images.store', $property) }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label small">Upload property photos (JPG, PNG, WEBP — max 5MB each)</label>
                    <input type="file" name="images[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Upload Images</button>
                </div>
            </div>
        </form>
        @if($property->images->count())
            <div class="row g-3">
                @foreach($property->images as $image)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="border rounded overflow-hidden">
                            <img src="{{ $image->url }}" alt="Property" class="w-100" style="height:140px;object-fit:cover;">
                            <div class="p-2 bg-light d-flex flex-wrap gap-1">
                                @if($image->is_primary)
                                    <span class="badge text-bg-primary">Primary</span>
                                @else
                                    <form action="{{ route('vendor.properties.images.primary', [$property, $image]) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-primary">Set Primary</button></form>
                                @endif
                                @can('properties.delete')
                                <form action="{{ route('vendor.properties.images.destroy', [$property, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small mb-0">No gallery images yet. Upload photos of your property.</p>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">{{ $property->rooms->count() }}</div><div class="stat-label">Rooms</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">₹{{ number_format($property->price_per_night, 0) }}</div><div class="stat-label">Base Price</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">{{ $property->max_guests }}</div><div class="stat-label">Max Guests</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">{{ $property->bookings()->count() }}</div><div class="stat-label">Bookings</div></div></div>
</div>

<div class="card-panel">
    <div class="card-header">Rooms</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Room</th><th>Photos</th><th>Type</th><th>Capacity</th><th>Units</th><th>Price/Night</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($property->rooms as $room)
                    <tr>
                        <td class="fw-semibold">{{ $room->name }}</td>
                        <td>{{ $room->images->count() }}</td>
                        <td>{{ ucfirst($room->room_type) }}</td>
                        <td>{{ $room->capacity }}</td>
                        <td>{{ $room->total_units }}</td>
                        <td>₹{{ number_format($room->price_per_night, 0) }}</td>
                        <td><span class="badge text-bg-secondary">{{ $room->status }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('vendor.rooms.edit', [$property, $room]) }}" class="btn btn-sm btn-outline-primary">Edit & Photos</a>
                            @can('properties.delete')
                            <form action="{{ route('vendor.rooms.destroy', [$property, $room]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No rooms yet. <a href="{{ route('vendor.rooms.create', $property) }}">Add your first room</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($property->description)
<div class="card-panel mt-3">
    <div class="card-header">Description</div>
    <div class="card-body">{{ $property->description }}</div>
</div>
@endif
@endsection
