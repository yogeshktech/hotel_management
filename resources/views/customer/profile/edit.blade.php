@extends('layouts.customer')

@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold">Edit Profile</div>
            <div class="card-body">
                <form action="{{ route('customer.profile.update') }}" method="post">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile *</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
