@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel">
            <div class="card-header">All Roles</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $role->name }}</span>
                                @if($role->name === 'super_admin')
                                    <span class="badge bg-danger ms-1">System</span>
                                @endif
                            </td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                            </td>
                            <td>
                                @if($role->name !== 'super_admin')
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @if(!in_array($role->name, ['vendor', 'customer']))
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                    @endif
                                @else
                                    <span class="text-muted small">Protected</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('roles.manage')
    <div class="col-lg-4">
        <div class="card-panel">
            <div class="card-header">Create New Role</div>
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Role Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. content_manager" required>
                    </div>
                    <div class="mb-3" style="max-height:300px;overflow-y:auto;">
                        <label class="form-label">Permissions</label>
                        @foreach($permissions as $group => $perms)
                            <div class="fw-semibold small text-muted mt-2 text-uppercase">{{ $group }}</div>
                            @foreach($perms as $perm)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="new-{{ $perm->id }}">
                                    <label class="form-check-label" for="new-{{ $perm->id }}">{{ $perm->name }}</label>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Role</button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
