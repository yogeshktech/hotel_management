@extends('layouts.vendor')

@section('title', 'Documents')
@section('page-title', 'Vendor Documents')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-header">Upload Document</div>
            <div class="card-body">
                <p class="text-muted small">Required: <strong>PAN Card</strong> and <strong>ID Proof</strong>. PDF, JPG, PNG — max 5MB.</p>
                <form method="POST" action="{{ route('vendor.documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Document Type *</label>
                        <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                            <option value="">Select type</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('document_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File *</label>
                        <input type="file" name="document" class="form-control @error('document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Upload</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-header">Uploaded Documents</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Type</th><th>File</th><th>Status</th><th>Uploaded</th><th></th></tr></thead>
                    <tbody>
                        @forelse($documentTypes as $type => $label)
                            @php $doc = $documents->get($type); @endphp
                            <tr>
                                <td>{{ $label }} @if(in_array($type, ['pan_card', 'id_proof']))<span class="text-danger">*</span>@endif</td>
                                <td>
                                    @if($doc)
                                        <a href="{{ $doc->url }}" target="_blank">{{ $doc->original_name ?? 'View' }}</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                                <td>
                                    @if($doc)
                                        <span class="badge {{ $doc->status === 'approved' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($doc->status) }}</span>
                                    @else — @endif
                                </td>
                                <td>{{ $doc?->created_at?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    @if($doc)
                                        @can('documents.delete')
                                        <form action="{{ route('vendor.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this document?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($profile->hasRequiredDocuments())
            <div class="alert alert-success mt-3">Required documents uploaded. You can now <a href="{{ route('vendor.properties.create') }}">add properties</a>.</div>
        @else
            <div class="alert alert-info mt-3">Upload PAN Card and ID Proof to unlock property management.</div>
        @endif
    </div>
</div>
@endsection
