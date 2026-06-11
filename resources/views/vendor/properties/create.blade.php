@extends('layouts.vendor')

@section('title', 'Add Property')
@section('page-title', 'Add New Property')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-panel">
            <div class="card-header">Property Details</div>
            <div class="card-body">
                <p class="text-muted small">Property will be submitted for admin approval.</p>
                <form method="POST" action="{{ route('vendor.properties.store') }}">
                    @csrf
                    @include('vendor.properties._form', ['property' => null])
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Submit Property</button>
                        <a href="{{ route('vendor.properties.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
