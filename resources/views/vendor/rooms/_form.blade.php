<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Room Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $room->name ?? '') }}" required placeholder="e.g. Deluxe Room">
    </div>
    <div class="col-md-4">
        <label class="form-label">Room Type *</label>
        <select name="room_type" class="form-select" required>
            @foreach(['standard', 'deluxe', 'suite', 'family', 'dorm'] as $type)
                <option value="{{ $type }}" @selected(old('room_type', $room->room_type ?? '') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $room->description ?? '') }}</textarea>
    </div>
    <div class="col-md-3">
        <label class="form-label">Capacity *</label>
        <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity', $room->capacity ?? 2) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Beds *</label>
        <input type="number" name="bed_count" class="form-control" min="1" value="{{ old('bed_count', $room->bed_count ?? 1) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Total Units *</label>
        <input type="number" name="total_units" class="form-control" min="1" value="{{ old('total_units', $room->total_units ?? 1) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Price/Night (₹) *</label>
        <input type="number" name="price_per_night" class="form-control" min="0" step="0.01" value="{{ old('price_per_night', $room->price_per_night ?? '') }}" required>
    </div>
    @if(isset($room))
    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            @foreach(['active', 'inactive', 'draft'] as $status)
                <option value="{{ $status }}" @selected(old('status', $room->status) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    @endif
</div>
