@extends('layouts.vendor')

@section('title', 'Vendor Profile')
@section('page-title', 'Business Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-panel">
            <div class="card-header">Complete Your Vendor Profile</div>
            <div class="card-body">
                <p class="text-muted small">Fill all details accurately. Required before adding properties.</p>
                <form method="POST" action="{{ route('vendor.profile.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
                                   value="{{ old('business_name', $profile->business_name) }}" required>
                            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $profile->gst_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone *</label>
                            <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
                                   value="{{ old('contact_phone', $profile->contact_phone) }}" required>
                            @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email *</label>
                            <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                                   value="{{ old('contact_email', $profile->contact_email) }}" required>
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address *</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $profile->address) }}" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $profile->city) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $profile->state) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $profile->pincode) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Business Description *</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $profile->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                        <a href="{{ route('vendor.documents.index') }}" class="btn btn-outline-secondary">Next: Upload Documents →</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
