<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Homestay Booking'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --brand-primary: #0f766e;
            --brand-dark: #134e4a;
            --brand-accent: #f59e0b;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: linear-gradient(160deg, #f0fdfa 0%, #ecfeff 40%, #f8fafc 100%);
            min-height: 100vh;
        }
        .landing-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(15, 118, 110, 0.1);
        }
        .brand-mark {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-dark));
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .hero-badge {
            background: rgba(15, 118, 110, 0.1);
            color: var(--brand-dark);
            border: 1px solid rgba(15, 118, 110, 0.15);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .portal-card {
            border: none;
            border-radius: 1.25rem;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .portal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.12) !important;
        }
        .portal-card .card-header {
            border: none;
            padding: 1.5rem 1.5rem 0;
            background: transparent;
        }
        .portal-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .portal-customer .portal-icon { background: #dbeafe; }
        .portal-staff .portal-icon { background: #fef3c7; }
        .btn-brand {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
            border-radius: 0.65rem;
        }
        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }
        .btn-outline-brand {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            font-weight: 600;
            border-radius: 0.65rem;
        }
        .btn-outline-brand:hover {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
        }
        .feature-pill {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 0.4rem 0.9rem;
            font-size: 0.85rem;
            color: #475569;
        }
        .credentials-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
        }
        .credentials-box code {
            font-size: 0.8rem;
            color: var(--brand-dark);
        }
        .landing-footer {
            color: #64748b;
            font-size: 0.875rem;
        }
        .landing-offcanvas {
            --bs-offcanvas-width: 50%;
        }
        .landing-offcanvas .nav-link {
            padding: 0.75rem 0;
            font-weight: 500;
            color: #1e293b;
        }
        .landing-offcanvas .nav-link:hover {
            color: var(--brand-primary);
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="landing-nav navbar navbar-expand-lg py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="{{ url('/') }}">
                <span class="brand-mark">🏡</span>
                <span>{{ config('app.name', 'Homestay Booking') }}</span>
            </a>

            {{-- Desktop navigation --}}
            <div class="d-none d-lg-flex ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    @auth('customer')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('customer.dashboard') }}">My Dashboard</a>
                        </li>
                    @elseauth('staff')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ auth('staff')->user()->hasRole('vendor') ? route('vendor.dashboard') : route('admin.dashboard') }}">My Panel</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('customer.login') }}">Customer Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('staff.login') }}">Staff Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-brand btn-sm" href="{{ route('customer.register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- Mobile: right offcanvas --}}
            <button class="navbar-toggler d-lg-none border-0 shadow-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#landingOffcanvas"
                    aria-controls="landingOffcanvas" aria-label="Open menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="offcanvas offcanvas-end landing-offcanvas" tabindex="-1" id="landingOffcanvas" aria-labelledby="landingOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="landingOffcanvasLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                @auth('customer')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}" data-bs-dismiss="offcanvas">My Dashboard</a>
                    </li>
                @elseauth('staff')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ auth('staff')->user()->hasRole('vendor') ? route('vendor.dashboard') : route('admin.dashboard') }}" data-bs-dismiss="offcanvas">My Panel</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.login') }}" data-bs-dismiss="offcanvas">Customer Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('staff.login') }}" data-bs-dismiss="offcanvas">Staff Login</a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="btn btn-brand w-100" href="{{ route('customer.register') }}" data-bs-dismiss="offcanvas">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>

    @yield('content')

    <footer class="landing-footer text-center py-4 mt-auto">
        <div class="container">
            <p class="mb-1">&copy; {{ date('Y') }} {{ config('app.name', 'Homestay Booking') }}. All rights reserved.</p>
            @if (Route::has('l5-swagger.default.api'))
                <a href="{{ route('l5-swagger.default.api') }}" class="text-decoration-none" style="color: var(--brand-primary);">API Documentation</a>
            @endif
        </div>
    </footer>
</body>
</html>
