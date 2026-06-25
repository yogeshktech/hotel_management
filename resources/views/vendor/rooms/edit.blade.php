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
                                @can('properties.delete')
                                <form action="{{ route('vendor.rooms.images.destroy', [$property, $room, $image]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                @endcan
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
        <div class="card-panel mb-4">
            <div class="card-header">Edit Room Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.rooms.update', [$property, $room]) }}">
                    @csrf @method('PUT')
                    @include('vendor.rooms._form')
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Update Room</button>
                        <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-outline-secondary">Back</a>
                        @can('properties.delete')
                        <form action="{{ route('vendor.rooms.destroy', [$property, $room]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room permanently?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Delete Room</button>
                        </form>
                        @endcan
                    </div>
                </form>
            </div>
        </div>

        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Seasonal / Festival Pricing</span>
                <span class="badge bg-secondary">{{ $room->seasons->count() }}</span>
            </div>
            <div class="card-body">
                <p class="small text-muted">Raise prices during festivals (e.g. Diwali ×1.5 = +50%) or lower on off-season days (e.g. ×0.8 = −20%). Applies per night in the date range.</p>

                <form method="POST" action="{{ route('vendor.rooms.seasons.store', [$property, $room]) }}" class="border rounded p-3 mb-4 bg-light">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small">Season name *</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Diwali, Summer Peak…" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Start date *</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">End date *</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Multiplier *</label>
                            <input type="number" name="price_multiplier" class="form-control form-control-sm" min="0.1" max="5" step="0.05" value="1.5" required>
                            <div class="form-text">1.0 = normal</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional note for your reference">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-primary">Add Season</button>
                        </div>
                    </div>
                </form>

                @if($room->seasons->count())
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Dates</th>
                                    <th>Rate</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($room->seasons as $season)
                                <tr>
                                    <td>
                                        <strong>{{ $season->name }}</strong>
                                        @if($season->notes)<br><span class="text-muted small">{{ $season->notes }}</span>@endif
                                    </td>
                                    <td class="small">{{ $season->start_date->format('d M Y') }} → {{ $season->end_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $season->price_multiplier > 1 ? 'text-bg-warning' : ($season->price_multiplier < 1 ? 'text-bg-success' : 'text-bg-secondary') }}">
                                            ×{{ $season->price_multiplier }} {{ $season->multiplier_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($season->is_active)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Paused</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#season-edit-{{ $season->id }}">Edit</button>
                                        <form action="{{ route('vendor.rooms.seasons.destroy', [$property, $room, $season]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this season?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="season-edit-{{ $season->id }}">
                                    <td colspan="5" class="bg-light">
                                        <form method="POST" action="{{ route('vendor.rooms.seasons.update', [$property, $room, $season]) }}" class="p-2">
                                            @csrf @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $season->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $season->start_date->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $season->end_date->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="price_multiplier" class="form-control form-control-sm" value="{{ $season->price_multiplier }}" min="0.1" max="5" step="0.05" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check mt-1">
                                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="season-active-{{ $season->id }}" @checked($season->is_active)>
                                                        <label class="form-check-label small" for="season-active-{{ $season->id }}">Active</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button class="btn btn-sm btn-primary w-100">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted small mb-0">No seasonal pricing yet. Add Diwali, New Year, or off-season discounts above.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Package Pricing (editable)</span>
                <form action="{{ route('vendor.rooms.pricings.reset', [$property, $room]) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset all packages from room base price?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Reset defaults</button>
                </form>
            </div>
            <div class="card-body">
                <p class="small text-muted">Set prices for each guest package. <strong>Couple</strong> price is shown on the website as the room rate.</p>
                <form method="POST" action="{{ route('vendor.rooms.pricings.update', [$property, $room]) }}">
                    @csrf @method('PUT')
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th style="width:140px">₹ / Night</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($room->pricings->sortBy(fn ($p) => [$p->package_type, $p->child_count]) as $pricing)
                                <tr>
                                    <td class="small">{{ \App\Models\RoomPricing::packageLabel($pricing->package_type, $pricing->child_count) }}</td>
                                    <td>
                                        <input type="hidden" name="pricings[{{ $loop->index }}][id]" value="{{ $pricing->id }}">
                                        <input type="number" name="pricings[{{ $loop->index }}][price_per_night]" class="form-control form-control-sm"
                                            min="0" step="1" value="{{ (int) $pricing->price_per_night }}" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Package Prices</button>
                </form>
                <hr>
                <p class="small text-muted mb-0">
                    Property fees (set under <a href="{{ route('vendor.properties.edit', $property) }}">Edit Property</a>):<br>
                    Cleaning ₹{{ number_format($property->cleaning_fee, 0) }} · Service {{ $property->service_fee_percentage }}%
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card-panel mt-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Facilities &amp; Add-ons (customer selectable)</span>
        <form action="{{ route('vendor.rooms.addons.reset', [$property, $room]) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset add-ons to defaults?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Reset defaults</button>
        </form>
    </div>
    <div class="card-body">
        <p class="small text-muted">Set price for WiFi, AC, meals, etc. Customers see every charge before payment. Toggle <strong>In package</strong> for the “All facilities package” bundle. Price ₹0 = shown as complimentary.</p>

        <form method="POST" action="{{ route('vendor.rooms.addons.update', [$property, $room]) }}" class="mb-4">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Price (₹)</th>
                            <th>Charge type</th>
                            <th>In package</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($room->addons as $addon)
                        <tr>
                            <td>
                                <input type="hidden" name="addons[{{ $loop->index }}][id]" value="{{ $addon->id }}">
                                <input type="text" name="addons[{{ $loop->index }}][name]" class="form-control form-control-sm" value="{{ $addon->name }}" required>
                            </td>
                            <td><input type="number" name="addons[{{ $loop->index }}][price]" class="form-control form-control-sm" min="0" step="1" value="{{ (int) $addon->price }}" required></td>
                            <td>
                                <select name="addons[{{ $loop->index }}][charge_type]" class="form-select form-select-sm">
                                    @foreach(\App\Models\RoomAddon::CHARGE_TYPES as $k => $label)
                                        <option value="{{ $k }}" @selected($addon->charge_type === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="addons[{{ $loop->index }}][is_included_in_package]" value="1" class="form-check-input" @checked($addon->is_included_in_package)>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="addons[{{ $loop->index }}][is_active]" value="1" class="form-check-input" @checked($addon->is_active)>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary">Save Add-ons</button>
        </form>

        <form method="POST" action="{{ route('vendor.rooms.addons.store', [$property, $room]) }}" class="border rounded p-3 bg-light">
            @csrf
            <p class="small fw-semibold mb-2">Add custom facility</p>
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Bonfire, Guide" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" class="form-control form-control-sm" min="0" step="1" value="0" required>
                </div>
                <div class="col-md-3">
                    <select name="charge_type" class="form-select form-select-sm">
                        @foreach(\App\Models\RoomAddon::CHARGE_TYPES as $k => $label)
                            <option value="{{ $k }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input type="checkbox" name="is_included_in_package" value="1" class="form-check-input" id="new_addon_pkg">
                        <label class="form-check-label small" for="new_addon_pkg">In package</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-outline-primary w-100">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
