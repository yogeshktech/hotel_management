@extends('layouts.admin')

@section('title', $customer->name)
@section('page-title', 'Customer: ' . $customer->name)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel mb-3">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $customer->name }}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $customer->email }}</dd>
                    <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $customer->phone }}</dd>
                    <dt class="col-sm-4">City</dt><dd class="col-sm-8">{{ $customer->city ?? '—' }}</dd>
                    <dt class="col-sm-4">Address</dt><dd class="col-sm-8">{{ $customer->address ?? '—' }}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $customer->is_active ? 'Active' : 'Inactive' }}</dd>
                    <dt class="col-sm-4">Total Bookings</dt><dd class="col-sm-8">{{ $customer->bookings_count }}</dd>
                </dl>
            </div>
        </div>

        <div class="card-panel">
            <div class="card-header">Recent Bookings</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Ref</th><th>Property</th><th>Check-in</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($customer->bookings as $booking)
                        <tr>
                            <td><a href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->booking_reference }}</a></td>
                            <td>{{ $booking->homestay->title ?? '—' }}</td>
                            <td>{{ $booking->check_in?->format('d M Y') }}</td>
                            <td>{{ $booking->status }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="d-grid gap-2">
            @can('customers.edit')
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">Edit Customer</a>
            @endcan
            @can('bookings.manage')
            <a href="{{ route('admin.bookings.create-offline') }}?customer_id={{ $customer->id }}" class="btn btn-dark">Offline Booking</a>
            @endcan
            @can('customers.delete')
            <form action="{{ route('admin.customers.destroy', $customer) }}" method="post" onsubmit="return confirm('Delete customer?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger w-100">Delete Customer</button>
            </form>
            @endcan
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">← All Customers</a>
        </div>
    </div>
</div>
@endsection
