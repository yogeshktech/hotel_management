@extends('layouts.admin')

@section('title', 'Locations')
@section('page-title', 'Location Management')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <span class="text-muted">{{ $locations->total() }} locations</span>
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary btn-sm">+ Add Location</a>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Properties</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($locations as $location)
                <tr>
                    <td class="fw-semibold">{{ $location->name }}</td>
                    <td>{{ $location->city }}</td>
                    <td>{{ $location->country }}</td>
                    <td>{{ $location->homestays_count ?? $location->homestays()->count() }}</td>
                    <td>
                        <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @can('locations.delete')
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this location?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $locations->links() }}</div>
</div>
@endsection
