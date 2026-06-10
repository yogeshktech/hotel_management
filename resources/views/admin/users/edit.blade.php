@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-header">User Details</div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="post">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roles *</label>
                            <select name="roles[]" class="form-select" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

  @can('roles.manage')
    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-header">Direct Permissions</div>
            <div class="card-body">
                <form action="{{ route('admin.users.permissions', $user) }}" method="post">
                    @csrf
                    <div style="max-height:400px;overflow-y:auto;">
                        @foreach($permissions as $perm)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                    value="{{ $perm->name }}" id="perm-{{ $perm->id }}"
                                    {{ $user->hasDirectPermission($perm->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $perm->id }}">{{ $perm->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-sm btn-secondary mt-2">Save Permissions</button>
                </form>
            </div>
        </div>
    </div>
  @endcan
</div>
@endsection
