<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account') — Hotel Booking</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #f8fafc; }
        .customer-nav { background: #1e40af; }
        .customer-nav a { color: rgba(255,255,255,.85); text-decoration: none; padding: .5rem 1rem; }
        .customer-nav a:hover, .customer-nav a.active { color: #fff; }
    </style>
</head>
<body>
<nav class="customer-nav py-2 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('customer.dashboard') }}" class="text-white fw-bold text-decoration-none">🏨 Hotel Booking</a>
        <div class="d-flex gap-1 align-items-center">
            <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('customer.profile.edit') }}" class="{{ request()->routeIs('customer.profile.*') ? 'active' : '' }}">Profile</a>
            <span class="text-white-50 small ms-2">{{ auth('customer')->user()->name }}</span>
            <form action="{{ route('customer.logout') }}" method="post" class="d-inline ms-2">
                @csrf
                <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div>
    </div>
</nav>
<div class="container pb-5">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif
    @yield('content')
</div>
</body>
</html>
