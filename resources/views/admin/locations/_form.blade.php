<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $location->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $location->slug ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Country *</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $location->country ?? 'India') }}" required placeholder="India">
    </div>
    <div class="col-md-4">
        <label class="form-label">State/Province</label>
        <input type="text" name="province" class="form-control" value="{{ old('province', $location->province ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $location->city ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Latitude</label>
        <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $location->latitude ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Longitude</label>
        <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $location->longitude ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description ?? '') }}</textarea>
    </div>
</div>
