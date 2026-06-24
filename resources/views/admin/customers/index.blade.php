@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customers / Guests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, email, phone..." value="{{ $search }}">
        <button type="submit" class="btn btn-sm btn-primary">Search</button>
        @if($search)<a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>@endif
    </form>
    @can('customers.create')
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">+ Register Customer</a>
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
                    <th>City</th>
                    <th>Bookings</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td class="fw-semibold">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->city ?? '—' }}</td>
                    <td>{{ $customer->bookings_count }}</td>
                    <td>
                        <span class="badge {{ $customer->is_active ? 'badge-approved' : 'bg-secondary' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @can('customers.edit')
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @endcan
                        @can('customers.delete')
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $customers->links() }}</div>
</div>
@endsection
