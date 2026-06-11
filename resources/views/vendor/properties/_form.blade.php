@php
    $amenityOptions = ['wifi', 'parking', 'restaurant', 'room-service', 'pool', 'gym', 'ac', 'breakfast'];
    $selectedAmenities = old('amenities', $property->amenities ?? []);
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Property Title *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $property->title ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label d-flex justify-content-between align-items-center">
            <span>Location *</span>
            <a href="{{ route('vendor.locations.create', ['redirect' => 'property']) }}" class="small fw-normal">+ Add new</a>
        </label>
        <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
            <option value="">Select location</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @selected(old('location_id', session('new_location_id', $property->location_id ?? '')) == $loc->id)>
                    {{ $loc->name }}{{ $loc->city ? ' — ' . $loc->city : '' }}
                </option>
            @endforeach
        </select>
        @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($locations->isEmpty())
            <div class="form-text text-warning">No locations found. <a href="{{ route('vendor.locations.create', ['redirect' => 'property']) }}">Add a location first</a>.</div>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label">Description *</label>
        <textarea name="description" class="form-control" rows="4" required>{{ old('description', $property->description ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Address *</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $property->address ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Max Guests *</label>
        <input type="number" name="max_guests" class="form-control" min="1" value="{{ old('max_guests', $property->max_guests ?? 2) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Bedrooms *</label>
        <input type="number" name="bedrooms" class="form-control" min="0" value="{{ old('bedrooms', $property->bedrooms ?? 1) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Beds *</label>
        <input type="number" name="beds" class="form-control" min="0" value="{{ old('beds', $property->beds ?? 1) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Bathrooms *</label>
        <input type="number" name="bathrooms" class="form-control" min="0" value="{{ old('bathrooms', $property->bathrooms ?? 1) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Base Price / Night (₹) *</label>
        <input type="number" name="price_per_night" class="form-control" min="0" step="0.01" value="{{ old('price_per_night', $property->price_per_night ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Cleaning Fee (₹)</label>
        <input type="number" name="cleaning_fee" class="form-control" min="0" step="0.01" value="{{ old('cleaning_fee', $property->cleaning_fee ?? 0) }}">
    </div>
    <div class="col-12">
        <label class="form-label d-block">Amenities</label>
        <div class="d-flex flex-wrap gap-2">
            @foreach($amenityOptions as $amenity)
                <label class="form-check-label border rounded px-3 py-1">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity }}" class="form-check-input me-1"
                        @checked(in_array($amenity, $selectedAmenities))> {{ ucfirst(str_replace('-', ' ', $amenity)) }}
                </label>
            @endforeach
        </div>
    </div>
</div>
