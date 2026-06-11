<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        @auth('customer')
                            <li class="nav-item dropdown">
                                <a id="customerDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ auth('customer')->user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerDropdown">
                                    <a class="dropdown-item" href="{{ route('customer.dashboard') }}">My Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('customer.profile.edit') }}">Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('customer.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="customer-logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth

                        @auth('staff')
                            <li class="nav-item dropdown">
                                <a id="staffDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ auth('staff')->user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="staffDropdown">
                                    @if(auth('staff')->user()->hasRole('vendor'))
                                        <a class="dropdown-item" href="{{ route('vendor.dashboard') }}">Vendor Panel</a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('staff.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('staff-logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="staff-logout-form" action="{{ route('staff.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth

                        @guest('customer')
                            @guest('staff')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('customer.login') }}">Customer Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('staff.login') }}">Staff Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('customer.register') }}">{{ __('Register') }}</a>
                                </li>
                            @endguest
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
