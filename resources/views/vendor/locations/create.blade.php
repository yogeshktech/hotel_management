@extends('layouts.vendor')

@section('title', 'Add Location')
@section('page-title', 'Add New Location')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-panel">
            <div class="card-header">Destination Details</div>
            <div class="card-body">
                <p class="text-muted small">Add a city or area where your property is located. It will appear in the location dropdown when adding properties.</p>
                <form action="{{ route('vendor.locations.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ request('redirect', 'locations') }}">
                    @include('admin.locations._form', ['location' => null])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Location</button>
                        @if(request('redirect') === 'property')
                            <a href="{{ route('vendor.properties.create') }}" class="btn btn-outline-secondary">Back to Property</a>
                        @else
                            <a href="{{ route('vendor.locations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
