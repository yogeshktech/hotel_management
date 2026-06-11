@extends('layouts.vendor')

@section('title', 'Add Room')
@section('page-title', 'Add Room — ' . $property->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-header">Room Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.rooms.store', $property) }}">
                    @csrf
                    @include('vendor.rooms._form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Add Room</button>
                        <a href="{{ route('vendor.properties.show', $property) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
