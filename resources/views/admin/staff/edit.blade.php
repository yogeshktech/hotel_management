@extends('layouts.admin')

@section('page-title', 'Edit Staff: ' . $staff->name)

@section('content')
<div class="row g-3">
<div class="col-lg-7"><div class="card-panel"><div class="card-body">
<form action="{{ route('admin.staff.update', $staff) }}" method="post">@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $staff->name }}" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $staff->email }}" required></div>
    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ $staff->phone }}"></div>
    <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="{{ $staff->department }}"></div>
    <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Roles</label>
        <select name="roles[]" class="form-select" multiple required>@foreach($roles as $role)<option value="{{ $role->name }}" {{ $staff->hasRole($role->name)?'selected':'' }}>{{ $role->name }}</option>@endforeach</select>
    </div>
    <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $staff->is_active?'checked':'' }}><label class="form-check-label">Active</label></div></div>
</div>
<button class="btn btn-primary mt-3">Save</button>
</form></div></div></div>
<div class="col-lg-5"><div class="card-panel"><div class="card-header">Direct Permissions</div><div class="card-body">
<form action="{{ route('admin.staff.permissions', $staff) }}" method="post">@csrf
<div style="max-height:400px;overflow-y:auto">@foreach($permissions as $perm)
<div class="form-check"><input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="form-check-input" id="p{{ $perm->id }}" {{ $staff->hasDirectPermission($perm->name)?'checked':'' }}><label for="p{{ $perm->id }}">{{ $perm->name }}</label></div>
@endforeach</div>
<button class="btn btn-sm btn-secondary mt-2">Save Permissions</button>
</form></div></div></div>
</div>
@endsection
