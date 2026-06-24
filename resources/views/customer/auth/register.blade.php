@extends('layouts.site')

@section('title', 'Create Account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="site-card p-4 p-md-5">
                <h1 class="site-section-title h4 text-center mb-1">Join us</h1>
                <p class="text-muted text-center small mb-4">Create an account to book premium stays</p>
                <form method="POST" action="{{ route('customer.register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mobile *</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+919900000001" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('partials.password-field', ['name' => 'password', 'label' => 'Password *'])
                    @include('partials.password-field', [
                        'name' => 'password_confirmation',
                        'label' => 'Confirm Password *',
                        'wrapperClass' => 'mb-4',
                    ])
                    <button type="submit" class="btn btn-site-gold w-100 mb-3">Create Account</button>
                    <p class="text-center small mb-0">Already registered? <a href="{{ route('customer.login') }}">Sign in</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
