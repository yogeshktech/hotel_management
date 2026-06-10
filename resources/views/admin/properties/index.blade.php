@extends('layouts.admin')

@section('title', 'Properties')
@section('page-title', 'Property Management')

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap">
    @foreach(['all' => 'All', 'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected'] as $key => $label)
        <a href="{{ route('admin.properties.index', ['status' => $key]) }}"
           class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }} ({{ $counts[$key] ?? 0 }})
        </a>
    @endforeach
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Vendor</th>
                    <th>Location</th>
                    <th>Rooms</th>
                    <th>Price/Night</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $property->title }}</div>
                        <small class="text-muted">{{ $property->images->count() }} images</small>
                    </td>
                    <td>{{ $property->owner->name ?? 'N/A' }}</td>
                    <td>{{ $property->location->name ?? '—' }}</td>
                    <td>{{ $property->rooms->count() }}</td>
                    <td>₹{{ number_format($property->price_per_night) }}</td>
                    <td><span class="badge bg-secondary">{{ $property->status }}</span></td>
                    <td>
                        <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-sm btn-primary">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No properties found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $properties->links() }}</div>
</div>
@endsection
