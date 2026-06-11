<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luxury Stays') — {{ config('app.name', 'Homestay Booking') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="site-body d-flex flex-column min-vh-100">
    <nav class="site-nav navbar navbar-expand-lg py-3 sticky-top" id="siteNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
                <span class="site-brand-icon">✦</span>
                <span class="fw-bold text-dark site-serif">{{ config('app.name', 'Homestay Booking') }}</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="siteNavMenu">
                <ul class="navbar-nav mx-auto gap-lg-1">
                    <li class="nav-item"><a class="nav-link fw-medium {{ request()->routeIs('home') ? 'text-success' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium {{ request()->routeIs('properties.*') ? 'text-success' : '' }}" href="{{ route('properties.index') }}">Stays</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium {{ request()->routeIs('locations.*') ? 'text-success' : '' }}" href="{{ route('locations.index') }}">Destinations</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-site-outline btn-sm">My Bookings</a>
                        <form action="{{ route('customer.logout') }}" method="post" class="d-inline">@csrf
                            <button class="btn btn-link btn-sm text-muted">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('customer.login') }}" class="btn btn-site-outline btn-sm">Sign In</a>
                        <a href="{{ route('customer.register') }}" class="btn btn-site-primary btn-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3"><div class="alert alert-success mb-0">{{ session('success') }}</div></div>
    @endif
    @if(session('error'))
        <div class="container mt-3"><div class="alert alert-danger mb-0">{{ session('error') }}</div></div>
    @endif
    @if($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger mb-0">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <main class="flex-grow-1">@yield('content')</main>

    <footer class="site-footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="site-brand-icon">✦</span>
                        <span class="site-serif fs-5 text-white fw-semibold">{{ config('app.name') }}</span>
                    </div>
                    <p class="small mb-0">Premium homestays & boutique hotels across India. Book online or via our mobile API.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">Explore</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('properties.index') }}">All Stays</a></li>
                        <li class="mb-2"><a href="{{ route('locations.index') }}">Destinations</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">Account</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('customer.login') }}">Sign In</a></li>
                        <li class="mb-2"><a href="{{ route('customer.register') }}">Register</a></li>
                        <li class="mb-2"><a href="{{ route('customer.dashboard') }}">My Bookings</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Partners</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('staff.login') }}">Hotel / Vendor Login</a></li>
                        @if(Route::has('l5-swagger.default.api'))
                        <li class="mb-2"><a href="{{ route('l5-swagger.default.api') }}">API Documentation</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4 opacity-25">
            <p class="small text-center mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>

    <script>window.addEventListener('scroll',()=>document.getElementById('siteNav')?.classList.toggle('scrolled',scrollY>20));</script>
    @stack('scripts')
</body>
</html>
