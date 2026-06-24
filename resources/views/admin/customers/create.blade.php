@extends('layouts.admin')

@section('title', 'Register Customer')
@section('page-title', 'Register New Customer')

@section('content')
<div class="card-panel">
    <div class="card-body">
        <p class="text-muted small">Walk-in guest ko yahan register karo. Default password: <code>password123</code> (guest change kar sakta hai baad mein).</p>
        <form action="{{ route('admin.customers.store') }}" method="post">
            @csrf
            @if(request('redirect'))<input type="hidden" name="redirect" value="{{ request('redirect') }}">@endif
            @include('admin.customers._form')
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Register Customer</button>
                <a href="{{ request('redirect', route('admin.customers.index')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
