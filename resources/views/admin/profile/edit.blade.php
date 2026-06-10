@extends('layouts.admin')

@section('page-title', 'My Profile')

@section('content')
<div class="row justify-content-center"><div class="col-md-8"><div class="card-panel"><div class="card-body">
<form action="{{ route('admin.profile.update') }}" method="post">@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $staff->name }}" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $staff->email }}" required></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ $staff->phone }}"></div>
    <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="{{ $staff->department }}"></div>
    <div class="col-md-6"><label class="form-label">Role</label><input type="text" class="form-control" value="{{ $staff->roles->pluck('name')->join(', ') }}" disabled></div>
    <div class="col-md-6"><label class="form-label">Last Login</label><input type="text" class="form-control" value="{{ $staff->last_login_at?->format('d M Y H:i') ?? 'N/A' }}" disabled></div>
    <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
</div>
<button class="btn btn-primary mt-3">Update Profile</button>
</form></div></div></div></div>
@endsection
