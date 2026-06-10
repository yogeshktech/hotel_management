@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="get" class="d-flex gap-2">
        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
    </form>
    @can('users.create')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Add User</a>
    @endcan
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-approved">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        @can('users.edit')
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @endcan
                        <form action="{{ route('admin.users.toggleActive', $user) }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning">Toggle</button>
                        </form>
                        @can('users.delete')
                        @if(!$user->hasRole('super_admin'))
                        <form action="{{ route('admin.users.destroy', $user) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $users->links() }}</div>
</div>
@endsection
