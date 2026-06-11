@extends('layouts.site')

@section('title', 'Payment')

@section('content')
<div class="container py-5 text-center">
    <div class="site-card p-5 col-lg-6 mx-auto">
        <div class="spinner-border text-success mb-3" role="status"></div>
        <h1 class="h5 fw-bold">Opening secure payment...</h1>
        <p class="text-muted small mb-0">Booking {{ $booking->booking_reference }} · ₹{{ number_format($booking->total_price, 0) }}</p>
    </div>
</div>

<form name="razorpayform" action="{{ route('payments.success') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order['id'] }}">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
</form>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const options = {
    key: "{{ config('services.razorpay.key', env('RAZORPAY_KEY')) }}",
    amount: "{{ $order['amount'] }}",
    currency: "INR",
    name: "{{ config('app.name') }}",
    description: "Booking {{ $booking->booking_reference }}",
    order_id: "{{ $order['id'] }}",
    prefill: {
        name: "{{ auth('customer')->user()->name }}",
        email: "{{ auth('customer')->user()->email }}",
        contact: "{{ auth('customer')->user()->phone }}"
    },
    handler: function (response) {
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.razorpayform.submit();
    },
    modal: {
        ondismiss: function() {
            window.location = "{{ route('bookings.show', $booking) }}";
        }
    }
};
const rzp = new Razorpay(options);
rzp.on('payment.failed', function() {
    window.location = "{{ route('bookings.show', $booking) }}?payment=failed";
});
document.addEventListener('DOMContentLoaded', () => rzp.open());
</script>
@endpush
