@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Login</h5>
                    <small>Book hotels & resorts online</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <hr>
                    <p class="text-center small mb-0">
                        New customer? <a href="{{ route('customer.register') }}">Register</a> ·
                        Staff? <a href="{{ route('staff.login') }}">Staff Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
