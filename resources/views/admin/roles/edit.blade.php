@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role: ' . $role->name)

@section('content')
<div class="card-panel">
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role) }}" method="post">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Role Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                <div class="row">
                    @foreach($permissions as $group => $perms)
                        <div class="col-md-4 mb-3">
                            <div class="fw-semibold small text-muted text-uppercase mb-2">{{ $group }}</div>
                            @foreach($perms as $perm)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $perm->name }}" id="perm-{{ $perm->id }}"
                                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm-{{ $perm->id }}">{{ $perm->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Role</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
