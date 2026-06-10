@extends('layouts.admin')

@section('title', 'Add Location')
@section('page-title', 'Add Location')

@section('content')
<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.locations.store') }}" method="post">
            @csrf
            @include('admin.locations._form')
            <button type="submit" class="btn btn-primary">Create Location</button>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
