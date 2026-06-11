@extends('layouts.admin')

@section('page-title', 'Booking ' . $booking->booking_reference)

@section('content')
<div class="row g-3">
<div class="col-lg-8">
    <div class="card-panel mb-3"><div class="card-header">Booking Timeline</div><div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between"><span>📅 Booked</span><strong>{{ $booking->booked_at?->format('d M Y, H:i') ?? $booking->created_at->format('d M Y, H:i') }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>📥 Check-in Date</span><strong>{{ $booking->check_in?->format('d M Y') }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>✅ Checked In At</span><strong>{{ $booking->checked_in_at?->format('d M Y, H:i') ?? '—' }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>📤 Check-out Date</span><strong>{{ $booking->check_out?->format('d M Y') }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>🏁 Checked Out At</span><strong>{{ $booking->checked_out_at?->format('d M Y, H:i') ?? '—' }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>🔓 Room Vacant From</span><strong class="text-success">{{ $booking->vacant_on }}</strong></li>
        </ul>
    </div></div>

    <div class="card-panel"><div class="card-header">Details</div><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->customer->name }} · {{ $booking->customer->phone }} · {{ $booking->customer->email }}</dd>
            <dt class="col-sm-4">Property</dt><dd class="col-sm-8">{{ $booking->homestay->title }} — {{ $booking->homestay->location->name ?? '' }}</dd>
            <dt class="col-sm-4">Room</dt><dd class="col-sm-8">{{ $booking->room->name ?? 'N/A' }}</dd>
            <dt class="col-sm-4">Package</dt><dd class="col-sm-8">{{ ucfirst($booking->guest_package) }} ({{ $booking->adults_count }} adults, {{ $booking->children_count }} children)</dd>
            <dt class="col-sm-4">Channel</dt><dd class="col-sm-8"><span class="badge bg-{{ $booking->booking_channel==='online'?'primary':'secondary' }}">{{ ucfirst($booking->booking_channel) }}</span></dd>
            <dt class="col-sm-4">Total</dt><dd class="col-sm-8">₹{{ number_format($booking->total_price) }}</dd>
            @if($booking->createdByStaff)<dt class="col-sm-4">Booked By Staff</dt><dd class="col-sm-8">{{ $booking->createdByStaff->name }}</dd>@endif
        </dl>
    </div></div>

    @if($booking->review)
    <div class="card-panel mt-3"><div class="card-header">Customer Review</div><div class="card-body">
        <p>Service: {{ $booking->review->service_rating }}/5 · Food: {{ $booking->review->food_rating }}/5 · Overall: {{ $booking->review->overall_rating }}/5</p>
        <p class="mb-0">{{ $booking->review->comment }}</p>
    </div></div>
    @endif
</div>

<div class="col-lg-4">
    <div class="card-panel mb-3"><div class="card-header">Actions</div><div class="card-body">
        @if(in_array($booking->status, ['confirmed','pending']))
        <form action="{{ route('admin.bookings.check-in', $booking) }}" method="post" class="mb-2">@csrf<button class="btn btn-success w-100">Check In Guest</button></form>
        @endif
        @if($booking->status === 'checked_in')
        <form action="{{ route('admin.bookings.check-out', $booking) }}" method="post">@csrf<button class="btn btn-warning w-100">Check Out Guest</button></form>
        @endif
        @can('bookings.delete')
        @if($booking->status !== 'checked_in')
        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="post" class="mt-2" onsubmit="return confirm('Delete this booking and release dates?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger w-100">Delete Booking</button>
        </form>
        @endif
        @endcan
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary w-100 mt-2">← Back</a>
    </div></div>
    <div class="card-panel"><div class="card-body text-center">
        <div class="h5 mb-1">{{ $booking->is_occupied ? '🟢 Occupied' : '⚪ Not Occupied' }}</div>
        <small class="text-muted">Status: {{ $booking->status }}</small>
    </div></div>
</div>
</div>
@endsection
