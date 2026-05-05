@extends('layouts.app')

@section('title', 'Awaiting M-Pesa Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 mt-5 text-center">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-success text-white py-4">
                <h3><i class="fas fa-mobile-alt"></i> M-Pesa Payment</h3>
                <p class="mb-0">Waiting for customer confirmation...</p>
            </div>
            <div class="card-body p-5">
                <div class="spinner-border text-success mb-4" role="status" style="width: 4rem; height: 4rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                
                <h4 class="mb-3">Order Number: <strong>{{ $order->order_number }}</strong></h4>
                <p class="lead">Amount to pay: KES {{ number_format($totalAmount, 2) }}</p>
                <p class="text-muted mb-4">Phone Number sent to: {{ $request->phone_number }}</p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Please ask the customer to check their phone and enter their M-Pesa PIN to complete the transaction.
                </div>

                <div class="mt-4">
                    <button class="btn btn-outline-secondary" onclick="checkStatusManual()">
                        <i class="fas fa-sync"></i> Check Status Manually
                    </button>
                    <!-- Fallback URL to simulate callback or redirect to generic receipt after checking via Daraja Query endpoint -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Listen for real-time payment success using Pusher (Laravel Echo logic structure)
    // Since we use raw Pusher, we subscribe to the event
    var channel = pusher.subscribe('private-orders.{{ $order->id }}');
    channel.bind('App\\Events\\PaymentReceived', function(data) {
        if(data.payment_status === 'success') {
            window.location.href = "{{ route('pos.receipt', $order->id) }}";
        } else {
            alert("Payment failed or cancelled!");
            // Redirect back to pos or order details
        }
    });

    // Manual Polling Fallback
    function checkStatusManual() {
        fetch('/api/mpesa/checkStatus/{{ $order->id }}')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = "{{ route('pos.receipt', $order->id) }}";
                } else if (data.status === 'failed') {
                    alert("Payment has failed.");
                } else {
                    alert("Still pending...");
                }
            });
    }

    // Auto poll every 10 seconds as backup to Pusher
    setInterval(checkStatusManual, 10000);
</script>
@endpush
