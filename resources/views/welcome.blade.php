@extends('layouts.landing')

@section('title', config('app.name', 'Homestay Booking') . ' — Book Your Perfect Stay')

@section('content')
<div class="container py-5">
    {{-- Hero --}}
    <div class="text-center mb-5 pb-2">
        <span class="hero-badge badge rounded-pill px-3 py-2 mb-3 d-inline-block">Hotel & Homestay Management</span>
        <h1 class="display-5 fw-bold text-dark mb-3">
            Welcome to {{ config('app.name', 'Homestay Booking') }}
        </h1>
        <p class="lead text-secondary mx-auto mb-4" style="max-width: 620px;">
            Book beautiful homestays across India. Manage properties, vendors, and bookings — all in one platform.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <span class="feature-pill">🏔️ Manali & Goa</span>
            <span class="feature-pill">📅 Online Booking</span>
            <span class="feature-pill">⭐ Reviews & Ratings</span>
            <span class="feature-pill">🔐 Secure Login</span>
        </div>
    </div>

    {{-- Login Portals --}}
    <div class="row g-4 justify-content-center mb-5">
        {{-- Customer Portal --}}
        <div class="col-md-6 col-lg-5">
            <div class="card portal-card portal-customer shadow-sm">
                <div class="card-header">
                    <div class="portal-icon">👤</div>
                    <h4 class="fw-bold mb-1">Customer Portal</h4>
                    <p class="text-secondary small mb-0">Book homestays, track bookings & write reviews</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <ul class="list-unstyled small text-secondary mb-4">
                        <li class="mb-2">✓ Browse properties in Manali, Goa & more</li>
                        <li class="mb-2">✓ Online booking with package pricing</li>
                        <li class="mb-2">✓ Manage profile & booking history</li>
                    </ul>
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-brand w-100">Go to Dashboard</a>
                    @else
                        <a href="{{ route('customer.login') }}" class="btn btn-brand w-100 mb-2">Customer Login</a>
                        <a href="{{ route('customer.register') }}" class="btn btn-outline-brand w-100">Create Account</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Staff Portal --}}
        <div class="col-md-6 col-lg-5">
            <div class="card portal-card portal-staff shadow-sm">
                <div class="card-header">
                    <div class="portal-icon">🛡️</div>
                    <h4 class="fw-bold mb-1">Staff & Admin Portal</h4>
                    <p class="text-secondary small mb-0">Super Admin · Team · Vendor management</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <ul class="list-unstyled small text-secondary mb-4">
                        <li class="mb-2">✓ Super Admin & Admin Staff dashboards</li>
                        <li class="mb-2">✓ Vendor property & booking management</li>
                        <li class="mb-2">✓ Offline bookings & check-in/out</li>
                    </ul>
                    @auth('staff')
                        <a href="{{ auth('staff')->user()->hasRole('vendor') ? route('vendor.dashboard') : route('admin.dashboard') }}"
                           class="btn btn-dark w-100">Go to Panel</a>
                    @else
                        <a href="{{ route('staff.login') }}" class="btn btn-dark w-100">Staff / Admin Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Demo Credentials --}}
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="credentials-box p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Demo Login Credentials</h5>
                        <p class="text-secondary small mb-0">Seeded test accounts — password for all: <code>password123</code></p>
                    </div>
                    <span class="badge text-bg-warning">Development / Demo</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Login URL</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td><span class="badge text-bg-danger">Super Admin</span></td>
                                <td><code>superadmin@hotel.com</code></td>
                                <td>/staff/login</td>
                                <td><a href="{{ route('staff.login') }}" class="btn btn-sm btn-outline-dark">Login</a></td>
                            </tr>
                            <tr>
                                <td><span class="badge text-bg-dark">Admin Staff</span></td>
                                <td><code>staff@hotel.com</code></td>
                                <td>/staff/login</td>
                                <td><a href="{{ route('staff.login') }}" class="btn btn-sm btn-outline-dark">Login</a></td>
                            </tr>
                            <tr>
                                <td><span class="badge text-bg-warning text-dark">Vendor</span></td>
                                <td><code>vendor1@hotel.com</code></td>
                                <td>/staff/login</td>
                                <td><a href="{{ route('staff.login') }}" class="btn btn-sm btn-outline-dark">Login</a></td>
                            </tr>
                            <tr>
                                <td><span class="badge text-bg-primary">Customer</span></td>
                                <td><code>amit@customer.com</code></td>
                                <td>/customer/login</td>
                                <td><a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-primary">Login</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
