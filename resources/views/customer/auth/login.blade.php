@extends('layouts.site')

@section('title', 'Sign In')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="site-card p-4 p-md-5">
                <h1 class="site-section-title h4 text-center mb-1">Welcome back</h1>
                <p class="text-muted text-center small mb-3">Sign in to book stays and manage reservations</p>

                <div class="alert alert-light border small mb-4 py-2">
                    <strong>Customer login:</strong> <code>user@customer.com</code> / <code>password123</code><br>
                    <span class="text-muted">Staff & admin use <a href="{{ route('staff.login') }}">Staff Login</a> — not this page.</span>
                </div>

                <form method="POST" action="{{ route('customer.login') }}">
                    @csrf
                    @if(request('redirect'))<input type="hidden" name="redirect" value="{{ request('redirect') }}">@endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('partials.password-field', ['name' => 'password', 'label' => 'Password'])
                    <div class="mb-4 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-site-gold w-100 mb-3">Sign In</button>
                    <p class="text-center small mb-0">No account? <a href="{{ route('customer.register') }}">Register</a>
                        · <a href="{{ route('staff.login') }}">Staff / Admin login</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
