@extends('layouts.vendor')

@section('title', 'Bookings')
@section('page-title', 'Bookings Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <p class="text-muted small mb-0">Online aur offline dono bookings yahan dikhti hain. Offline booking save hote hi woh dates online ke liye block ho jati hain.</p>
    <a href="{{ route('vendor.bookings.create-offline') }}" class="btn btn-primary">+ Offline Booking</a>
</div>

<div class="card-panel mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Property</label>
                <select name="property_id" class="form-select form-select-sm">
                    <option value="">All properties</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" @selected(request('property_id') == $prop->id)>{{ $prop->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Channel</label>
                <select name="channel" class="form-select form-select-sm">
                    <option value="">Online + Offline</option>
                    <option value="online" @selected(request('channel') === 'online')>Online only</option>
                    <option value="offline" @selected(request('channel') === 'offline')>Offline only</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('vendor.bookings.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Channel</th>
                    <th>Guest</th>
                    <th>Property</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <a href="{{ route('vendor.bookings.show', $booking) }}">{{ $booking->booking_reference }}</a>
                        </td>
                        <td>
                            <span class="badge {{ $booking->booking_channel === 'offline' ? 'text-bg-dark' : 'text-bg-primary' }}">
                                {{ ucfirst($booking->booking_channel ?? 'online') }}
                            </span>
                        </td>
                        <td>{{ $booking->customer->name ?? '—' }}<br><small class="text-muted">{{ $booking->customer->email ?? '' }}</small></td>
                        <td>{{ $booking->homestay->title ?? '—' }}</td>
                        <td>{{ $booking->room->name ?? '—' }}</td>
                        <td>{{ $booking->check_in?->format('d M Y') }}</td>
                        <td>{{ $booking->check_out?->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $booking->status }}</span></td>
                        <td>{{ $booking->payment_status }}</td>
                        <td>₹{{ number_format($booking->total_price, 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
        <div class="p-3">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
