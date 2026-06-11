@extends('layouts.vendor')

@section('title', 'Locations')
@section('page-title', 'Destinations / Locations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0">Add cities and destinations for your properties.</p>
    <a href="{{ route('vendor.locations.create') }}" class="btn btn-primary">+ Add Location</a>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                    <th>Properties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td class="fw-semibold">{{ $location->name }}</td>
                        <td>{{ $location->city ?? '—' }}</td>
                        <td>{{ $location->province ?? '—' }}</td>
                        <td>{{ $location->country }}</td>
                        <td>{{ $location->homestays_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No locations yet. Add your first destination.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locations->hasPages())
        <div class="p-3">{{ $locations->links() }}</div>
    @endif
</div>
@endsection
