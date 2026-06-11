<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Panel') — {{ config('app.name', 'Homestay Booking') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #134e4a;
            --sidebar-hover: #0f766e;
            --accent: #14b8a6;
        }
        body { background: #f0fdfa; }
        .vendor-wrapper { display: flex; min-height: 100vh; }
        .vendor-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #ccfbf1;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 1000;
        }
        .vendor-sidebar .brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .vendor-sidebar .brand small { display: block; font-size: .7rem; font-weight: 400; color: #99f6e4; }
        .vendor-nav { padding: .75rem 0; }
        .vendor-nav .nav-section {
            padding: .5rem 1.5rem .25rem;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #5eead4;
        }
        .vendor-nav a {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .6rem 1.5rem;
            color: #ccfbf1;
            text-decoration: none;
            font-size: .9rem;
        }
        .vendor-nav a:hover, .vendor-nav a.active {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .vendor-nav a.active { border-left: 3px solid var(--accent); }
        .vendor-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .vendor-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .vendor-content { padding: 1.5rem; flex: 1; }
        .stat-card, .card-panel {
            background: #fff;
            border-radius: .5rem;
            border: 1px solid #e2e8f0;
        }
        .stat-card { padding: 1.25rem; height: 100%; }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: #134e4a; }
        .stat-card .stat-label { color: #64748b; font-size: .85rem; }
        .card-panel .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .card-panel .card-body { padding: 1.25rem; }
        .step-item { display: flex; align-items: center; gap: .75rem; padding: .5rem 0; }
        .step-dot {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; flex-shrink: 0;
        }
        .step-dot.done { background: #d1fae5; color: #065f46; }
        .step-dot.pending { background: #fef3c7; color: #92400e; }
        @media (max-width: 768px) {
            .vendor-sidebar { transform: translateX(-100%); }
            .vendor-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="vendor-wrapper">
    @include('vendor.partials.sidebar')

    <div class="vendor-main">
        @include('vendor.partials.topbar')

        <div class="vendor-content">
            @include('admin.partials.alerts')
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
