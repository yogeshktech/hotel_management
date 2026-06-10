@extends('layouts.customer')

@section('title', 'Write Review')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header fw-semibold">Review Your Stay — {{ $booking->homestay->title }}</div>
            <div class="card-body">
                <p class="text-muted small">Booking: {{ $booking->booking_reference }} · {{ $booking->check_in?->format('d M') }} – {{ $booking->check_out?->format('d M Y') }}</p>
                <form action="{{ route('customer.reviews.store', $booking) }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Service Quality (1-5) *</label>
                        <select name="service_rating" class="form-select" required>
                            @for($i=1;$i<=5;$i++)<option value="{{ $i }}">{{ $i }} Star{{ $i>1?'s':'' }}</option>@endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Food Quality (1-5) *</label>
                        <select name="food_rating" class="form-select" required>
                            @for($i=1;$i<=5;$i++)<option value="{{ $i }}">{{ $i }} Star{{ $i>1?'s':'' }}</option>@endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Overall Experience (1-5) *</label>
                        <select name="overall_rating" class="form-select" required>
                            @for($i=1;$i<=5;$i++)<option value="{{ $i }}">{{ $i }} Star{{ $i>1?'s':'' }}</option>@endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comments</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="How was the service? How was the food?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
