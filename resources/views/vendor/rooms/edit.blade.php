@extends('layouts.vendor')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room — ' . $room->name)

@section('content')
<div class="card-panel mb-3">
    <div class="card-header">Room Gallery ({{ $room->images->count() }})</div>
    <div class="card-body">
        <form action="{{ route('vendor.rooms.images.store', [$property, $room]) }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label small">Upload room photos (JPG, PNG, WEBP — max 5MB each)</label>
                    <input type="file" name="images[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Upload Images</button>
                </div>
            </div>
        </form>
        @if($room->images->count())
            <div class="row g-3">
                @foreach($room->images as $image)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="border rounded overflow-hidden">
                            <img src="{{ $image->url }}" alt="Room" class="w-100" style="height:140px;object-fit:cover;">
                            <div class="p-2 bg-light d-flex flex-wrap gap-1">
                                @if($image->is_primary)
                                    <span class="badge text-bg-primary">Primary</span>
                                @else
                                    <form action="{{ route('vendor.rooms.images.primary', [$property, $room, $image]) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-primary">Set Primary</button></form>
                                @endif
                                <form action="{{ route('vendor.rooms.images.destroy', [$property, $room, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small mb-0">No room photos yet. Upload images of this room.</p>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-header">Edit Room Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.rooms.update', [$property, $room]) }}">
                    @csrf @method('PUT')
                    @include('vendor.rooms._form')
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="refresh_pricing" value="1" class="form-check-input" id="refresh_pricing">
                        <label class="form-check-label" for="refresh_pricing">Recalculate package pricing from base price</label>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update Room</button>
                        <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-header">Package Pricing</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Package</th><th>Children</th><th>Price/Night</th></tr></thead>
                    <tbody>
                        @foreach($room->pricings as $pricing)
                            <tr>
                                <td>{{ ucfirst($pricing->package_type) }}</td>
                                <td>{{ $pricing->child_count }}</td>
                                <td>₹{{ number_format($pricing->price_per_night, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
