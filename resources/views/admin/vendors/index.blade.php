@extends('layouts.admin')

@section('title', 'Vendors')
@section('page-title', 'Vendor Management')

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap">
    @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
        <a href="{{ route('admin.vendors.index', ['status' => $key]) }}"
           class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }} ({{ $counts[$key] ?? 0 }})
        </a>
    @endforeach
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $vendor->business_name }}</div>
                        <small class="text-muted">{{ $vendor->staff->name }}</small>
                    </td>
                    <td>
                        <div>{{ $vendor->contact_phone }}</div>
                        <small class="text-muted">{{ $vendor->contact_email ?? $vendor->staff->email }}</small>
                    </td>
                    <td>{{ $vendor->city }}{{ $vendor->state ? ', ' . $vendor->state : '' }}</td>
                    <td>
                        @php
                            $badge = match($vendor->status) {
                                'pending' => 'badge-pending',
                                'approved' => 'badge-approved',
                                'rejected' => 'badge-rejected',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($vendor->status) }}</span>
                    </td>
                    <td>{{ $vendor->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-sm btn-primary">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No vendors found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $vendors->links() }}</div>
</div>
@endsection
