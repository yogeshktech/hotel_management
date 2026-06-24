@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer) }}" method="post">
            @csrf @method('PUT')
            @include('admin.customers._form', ['customer' => $customer])
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
