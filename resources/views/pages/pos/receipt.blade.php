@extends('layouts.app')

@section('title', 'Receipt ' . $order->order_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Transaction Complete</h5>
                <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> New Sale</a>
            </div>
            <div class="card-body p-4" id="receiptArea">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">VOLTIX ELECTRONIX</h3>
                    <p class="text-muted mb-1" style="font-size:0.85rem;">Powering Your Digital Life</p>
                    <p class="mb-1">123 Business Street, Nairobi</p>
                    <p class="mb-1">Phone: 0700000000</p>
                    <hr>
                    <h5 class="mt-3">RECEIPT</h5>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <strong>Order #:</strong> {{ $order->order_number }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="text-end">
                        <strong>Cashier:</strong> {{ $order->user->name }}<br>
                        <strong>Customer:</strong> {{ $order->customer->name ?? 'Walk-in' }}
                    </div>
                </div>

                <table class="table table-sm border-bottom">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name }}
                                @if($item->serial_number_id && $item->serialNumber)
                                    <small class="text-muted d-block">S/N: {{ $item->serialNumber->serial_number }}</small>
                                @endif
                                @if($item->warranty_expiry)
                                    <small class="text-success d-block"><i class="fas fa-shield-alt"></i> Warranty until: {{ \Carbon\Carbon::parse($item->warranty_expiry)->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mb-2">
                    <div class="col-6 text-end">
                        <p class="mb-1">Subtotal:</p>
                        @if($order->discount > 0)
                        <p class="mb-1 text-danger">Discount:</p>
                        @endif
                        <h4 class="mt-2">Grand Total:</h4>
                    </div>
                    <div class="col-4 text-end">
                        <p class="mb-1">{{ number_format($order->total_amount + $order->discount, 2) }}</p>
                        @if($order->discount > 0)
                        <p class="mb-1 text-danger">-{{ number_format($order->discount, 2) }}</p>
                        @endif
                        <h4 class="mt-2 text-primary font-weight-bold">KES {{ number_format($order->total_amount, 2) }}</h4>
                    </div>
                </div>
                
                <hr>

                <div class="text-center">
                    @php $allPayments = $order->payments; @endphp
                    @if($allPayments->count() > 1)
                        <p class="mb-2"><strong>Payment Breakdown:</strong></p>
                        @foreach($allPayments as $pay)
                            <div class="d-flex justify-content-between px-3 mb-1">
                                <span>
                                    @if($pay->method === 'mpesa')
                                        <i class="fas fa-mobile-alt text-success"></i> M-Pesa
                                        @if($pay->mpesa_code)
                                            <small class="text-muted">({{ $pay->mpesa_code }})</small>
                                        @endif
                                    @else
                                        <i class="fas fa-money-bill-wave text-success"></i> Cash
                                    @endif
                                </span>
                                <strong>KES {{ number_format($pay->amount, 2) }}</strong>
                            </div>
                        @endforeach
                    @else
                        @php $singlePay = $allPayments->first(); @endphp
                        <p class="mb-1"><strong>Payment Method:</strong> {{ strtoupper($singlePay->method ?? 'UNKNOWN') }}</p>
                        @if($singlePay && $singlePay->method === 'mpesa' && $singlePay->mpesa_code)
                            <p class="mb-0"><strong>M-Pesa Code:</strong> {{ $singlePay->mpesa_code }}</p>
                        @endif
                    @endif
                </div>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="mb-1">Thank you for your business!</p>
                    <p class="mb-0 small text-muted">All products come with manufacturer warranty. Keep this receipt.</p>
                </div>
            </div>
            <div class="card-footer bg-light text-center">
                <button class="btn btn-outline-secondary" onclick="printReceipt()"><i class="fas fa-print"></i> Print Receipt</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printReceipt() {
        var printContents = document.getElementById("receiptArea").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endpush
