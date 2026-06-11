@extends('layouts.vendor')

@section('title', 'Properties')
@section('page-title', 'My Properties')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage your homestays and resorts.</p>
    @if($profile->canManageProperties())
        <a href="{{ route('vendor.properties.create') }}" class="btn btn-primary">+ Add Property</a>
    @else
        <span class="badge text-bg-warning">Complete profile & documents to add properties</span>
    @endif
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Location</th>
                    <th>Rooms</th>
                    <th>Price/Night</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                    <tr>
                        <td class="fw-semibold">{{ $property->title }}</td>
                        <td>{{ $property->location->name ?? '—' }}</td>
                        <td>{{ $property->rooms->count() }}</td>
                        <td>₹{{ number_format($property->price_per_night, 0) }}</td>
                        <td><span class="badge {{ $property->status === 'active' ? 'text-bg-success' : ($property->status === 'pending' ? 'text-bg-warning' : 'text-bg-secondary') }}">{{ ucfirst($property->status) }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                            <a href="{{ route('vendor.properties.edit', $property) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No properties yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($properties->hasPages())
        <div class="p-3">{{ $properties->links() }}</div>
    @endif
</div>
@endsection
