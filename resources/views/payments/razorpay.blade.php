<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<form name='razorpayform' action="{{ route('payments.success') }}" method="POST">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
</form>

<script>
var options = {
    "key": "{{ env('RAZORPAY_KEY') }}",
    "amount": "{{ $order['amount'] }}",
    "currency": "INR",
    "name": "Homestay Booking",
    "order_id": "{{ $order['id'] }}",
    "handler": function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.razorpayform.submit();
    }
};
var rzp1 = new Razorpay(options);
rzp1.open();
</script>