@extends('layouts.admin')

@section('title', 'Team & Staff')
@section('page-title', 'Team & Staff Management')

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
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">+ Add Staff</a>
    @endcan
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Department</th><th>Roles</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($staffMembers as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td class="fw-semibold">{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td>{{ $member->department ?? '—' }}</td>
                    <td>@foreach($member->roles as $role)<span class="badge bg-primary">{{ $role->name }}</span> @endforeach</td>
                    <td><span class="badge {{ $member->is_active ? 'badge-approved' : 'bg-secondary' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-nowrap">
                        @can('users.edit')<a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-sm btn-outline-primary">Edit</a>@endcan
                        <form action="{{ route('admin.staff.toggleActive', $member) }}" method="post" class="d-inline">@csrf<button class="btn btn-sm btn-outline-warning">Toggle</button></form>
                        @can('users.delete')
                            @if(!$member->hasRole('super_admin') && $member->id !== auth('staff')->id())
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this staff member?')">
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
    <div class="p-3">{{ $staffMembers->links() }}</div>
</div>
@endsection
