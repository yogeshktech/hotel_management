@extends('layouts.admin')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location')

@section('content')
<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.locations.update', $location) }}" method="post">
            @csrf @method('PUT')
            @include('admin.locations._form')
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
