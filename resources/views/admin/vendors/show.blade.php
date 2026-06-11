@extends('layouts.admin')

@section('title', 'Vendor Review')
@section('page-title', 'Review Vendor: ' . $vendor->business_name)

@section('content')
@php
    $documentsByType = $vendor->documents->keyBy('document_type');
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel mb-3">
            <div class="card-header">Business Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Business Name</dt>
                    <dd class="col-sm-8">{{ $vendor->business_name }}</dd>
                    <dt class="col-sm-4">Owner</dt>
                    <dd class="col-sm-8">{{ $vendor->staff->name }} ({{ $vendor->staff->email }})</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $vendor->contact_phone }}</dd>
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $vendor->address }}, {{ $vendor->city }}{{ $vendor->state ? ', ' . $vendor->state : '' }} {{ $vendor->pincode }}</dd>
                    @if($vendor->gst_number)
                    <dt class="col-sm-4">GST Number</dt>
                    <dd class="col-sm-8">{{ $vendor->gst_number }}</dd>
                    @endif
                    @if($vendor->description)
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $vendor->description }}</dd>
                    @endif
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary">{{ ucfirst($vendor->status) }}</span></dd>
                    @if($vendor->rejection_reason)
                    <dt class="col-sm-4">Rejection Reason</dt>
                    <dd class="col-sm-8 text-danger">{{ $vendor->rejection_reason }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card-panel mb-3">
            <div class="card-header">KYC Documents — Review & Verify</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach(\App\Models\VendorDocument::TYPES as $type => $label)
                        @php $doc = $documentsByType->get($type); @endphp
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 {{ $doc ? '' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>{{ $label }}</strong>
                                    @if(in_array($type, ['pan_card', 'id_proof']))
                                        <span class="badge text-bg-danger">Required</span>
                                    @endif
                                </div>

                                @if($doc)
                                    @if($doc->isImage())
                                        <a href="{{ $doc->url }}" target="_blank">
                                            <img src="{{ $doc->url }}" alt="{{ $label }}" class="img-fluid rounded border mb-2" style="max-height:180px;object-fit:cover;width:100%;">
                                        </a>
                                    @else
                                        <div class="bg-white border rounded p-4 text-center mb-2">
                                            <div class="fs-1">📄</div>
                                            <div class="small text-muted">{{ $doc->original_name }}</div>
                                        </div>
                                    @endif

                                    <div class="small text-muted mb-2">
                                        Uploaded {{ $doc->created_at->format('d M Y, h:i A') }}
                                        · <span class="badge {{ $doc->status === 'approved' ? 'text-bg-success' : ($doc->status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($doc->status) }}</span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ $doc->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Open / Download</a>
                                        @can('vendors.approve')
                                            @if($doc->status !== 'approved')
                                            <form action="{{ route('admin.vendors.documents.approve', [$vendor, $doc]) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
                                            @endif
                                        @endcan
                                    </div>

                                    @if($doc->status === 'rejected' && $doc->rejection_reason)
                                        <div class="alert alert-danger small mt-2 mb-0 py-1">{{ $doc->rejection_reason }}</div>
                                    @endif

                                    @can('vendors.approve')
                                    @if($doc->status !== 'rejected')
                                    <form action="{{ route('admin.vendors.documents.reject', [$vendor, $doc]) }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="text" name="rejection_reason" class="form-control form-control-sm mb-1" placeholder="Rejection reason" required>
                                        <button class="btn btn-sm btn-outline-danger w-100">Reject Document</button>
                                    </form>
                                    @endif
                                    @endcan
                                @else
                                    <p class="text-muted small mb-0">Not uploaded by vendor yet.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-panel">
            <div class="card-header">Registered Properties ({{ $vendor->staff->homestays->count() }})</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Property</th><th>Location</th><th>Photos</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($vendor->staff->homestays as $property)
                        <tr>
                            <td>{{ $property->title }}</td>
                            <td>{{ $property->location->name ?? '—' }}</td>
                            <td>{{ $property->images->count() }}</td>
                            <td><span class="badge bg-secondary">{{ $property->status }}</span></td>
                            <td><a href="{{ route('admin.properties.show', $property) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">No properties registered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($vendor->status === 'pending')
        <div class="card-panel mb-3">
            <div class="card-header">Approval Actions</div>
            <div class="card-body">
                @can('vendors.approve')
                <form action="{{ route('admin.vendors.approve', $vendor) }}" method="post" class="mb-3">
                    @csrf
                    <button class="btn btn-success w-100">✓ Approve Vendor</button>
                </form>
                <form action="{{ route('admin.vendors.reject', $vendor) }}" method="post">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-danger w-100">✗ Reject Vendor</button>
                </form>
                @endcan
            </div>
        </div>
        @endif

        @if($vendor->status === 'approved')
        <div class="card-panel">
            <div class="card-body">
                <p class="text-muted small mb-2">Approved by {{ $vendor->approver->name ?? 'N/A' }} on {{ $vendor->approved_at?->format('d M Y') }}</p>
                @can('vendors.manage')
                <form action="{{ route('admin.vendors.suspend', $vendor) }}" method="post" onsubmit="return confirm('Suspend this vendor?')">
                    @csrf
                    <button class="btn btn-warning w-100">Suspend Vendor</button>
                </form>
                @endcan
            </div>
        </div>
        @endif

        <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary w-100 mt-3">← Back to Vendors</a>
    </div>
</div>
@endsection
