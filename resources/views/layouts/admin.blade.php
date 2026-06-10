<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name', 'Hotel Booking') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --accent: #3b82f6;
        }
        body { background: #f1f5f9; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #cbd5e1;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 1000;
        }
        .admin-sidebar .brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
        }
        .admin-sidebar .brand small { display: block; font-size: .7rem; font-weight: 400; color: #94a3b8; }
        .admin-nav { padding: .75rem 0; }
        .admin-nav .nav-section {
            padding: .5rem 1.5rem .25rem;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
        }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .6rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: .9rem;
            transition: background .15s;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .admin-nav a.active { border-left: 3px solid var(--accent); }
        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-content { padding: 1.5rem; flex: 1; }
        .stat-card {
            background: #fff;
            border-radius: .5rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            height: 100%;
        }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
        .stat-card .stat-label { color: #64748b; font-size: .85rem; }
        .card-panel {
            background: #fff;
            border-radius: .5rem;
            border: 1px solid #e2e8f0;
        }
        .card-panel .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .card-panel .card-body { padding: 1.25rem; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-wrapper">
  @include('admin.partials.sidebar')

  <div class="admin-main">
    @include('admin.partials.topbar')

    <div class="admin-content">
      @include('admin.partials.alerts')
      @yield('content')
    </div>
  </div>
</div>
@stack('scripts')
</body>
</html>
