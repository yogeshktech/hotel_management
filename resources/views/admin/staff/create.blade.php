@extends('layouts.admin')

@section('page-title', 'Add Staff Member')

@section('content')
<div class="card-panel"><div class="card-body">
<form action="{{ route('admin.staff.store') }}" method="post">@csrf
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control" placeholder="e.g. Front Desk"></div>
    <div class="col-md-6"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Confirm *</label><input type="password" name="password_confirmation" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Roles *</label>
        <select name="roles[]" class="form-select" multiple required>@foreach($roles as $role)<option value="{{ $role->name }}">{{ $role->name }}</option>@endforeach</select>
    </div>
</div>
<button class="btn btn-primary mt-3">Create</button>
<a href="{{ route('admin.staff.index') }}" class="btn btn-secondary mt-3">Cancel</a>
</form></div></div>
@endsection
