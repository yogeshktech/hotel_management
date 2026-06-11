@extends('layouts.vendor')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-panel">
            <div class="card-header">Edit: {{ $property->title }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.properties.update', $property) }}">
                    @csrf @method('PUT')
                    @include('vendor.properties._form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Property</button>
                        <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
