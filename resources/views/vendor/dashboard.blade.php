@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Vendor Dashboard</h2>
    <p class="text-muted">Welcome, {{ $staff->name }}. Register your resort and rooms here (coming soon).</p>

    @if($profile)
        <div class="alert alert-{{ $profile->status === 'approved' ? 'success' : ($profile->status === 'pending' ? 'warning' : 'danger') }}">
            Vendor Status: <strong>{{ ucfirst($profile->status) }}</strong>
            @if($profile->status === 'pending')
                — Waiting for Super Admin approval.
            @endif
        </div>
    @else
        <div class="alert alert-info">Complete your vendor registration to list properties.</div>
    @endif

    <h5 class="mt-4">Your Properties ({{ $properties->count() }})</h5>
    @forelse($properties as $property)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <strong>{{ $property->title }}</strong>
                    <div class="text-muted small">{{ $property->location->name ?? '' }} — Status: {{ $property->status }}</div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">No properties yet.</p>
    @endforelse
</div>
@endsection
