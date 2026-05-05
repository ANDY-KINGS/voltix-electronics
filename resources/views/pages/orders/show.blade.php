@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Order: {{ $order->order_number }}</h1>
    <div>
        <a href="{{ route('pos.receipt', $order->id) }}" class="btn btn-primary"><i class="fas fa-print"></i> Print Receipt</a>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Qty</th>
                            <th>Serial #</th>
                            <th>Warranty Expiry</th>
                            <th>Warranty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name ?? 'Unknown Product' }}
                                @if($item->product->brand ?? null)
                                    <small class="text-muted d-block">{{ $item->product->brand->name }}</small>
                                @endif
                            </td>
                            <td>{{ $item->product->sku ?? 'N/A' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td>
                                @if($item->serialNumber)
                                    <code class="small">{{ $item->serialNumber->serial_number }}</code>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($item->warranty_expiry)
                                    {{ \Carbon\Carbon::parse($item->warranty_expiry)->format('d M Y') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($item->warrantyClaim)
                                    <span class="badge bg-warning text-dark">Claim: {{ ucfirst(str_replace('_',' ',$item->warrantyClaim->status)) }}</span>
                                @elseif($item->warranty_expiry && !\Carbon\Carbon::parse($item->warranty_expiry)->isPast())
                                    <span class="badge bg-success">Active</span>
                                    <a href="{{ route('warranty-claims.create', ['order_item_id' => $item->id]) }}"
                                       class="btn btn-sm btn-outline-warning ms-1" title="Raise Claim" style="font-size:0.7rem; padding:1px 5px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </a>
                                @elseif($item->warranty_expiry)
                                    <span class="badge bg-secondary">Expired</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end font-weight-bold">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end"><strong>Gross Total:</strong></td>
                            <td class="text-end"><strong>{{ number_format($order->total_amount + $order->discount, 2) }}</strong></td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="4" class="text-end text-danger"><strong>Discount:</strong></td>
                            <td class="text-end text-danger"><strong>-{{ number_format($order->discount, 2) }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end text-primary h5 mt-2"><strong>Net Total Amount:</strong></td>
                            <td class="text-end text-primary h5 mt-2"><strong>KES {{ number_format($order->total_amount, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Order Status</small>
                    <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning' : 'bg-danger') }} p-2" style="font-size: 0.9rem;">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Order Date</small>
                    <strong>{{ $order->created_at->format('M d, Y - h:i A') }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Cashier</small>
                    <strong>{{ $order->user->name ?? 'System' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Customer</small>
                    @if($order->customer)
                        <strong>{{ $order->customer->name }}</strong><br>
                        <i class="fas fa-phone text-muted mt-1"></i> {{ $order->customer->phone ?? 'No Phone' }}
                    @else
                        <strong>Walk-in Customer</strong>
                    @endif
                </div>

                <hr>

                <h6 class="font-weight-bold mb-3">Payment Details</h6>
                @if($order->payment)
                    <div class="row text-sm mb-1">
                        <div class="col-6 text-muted">Method:</div>
                        <div class="col-6 text-end"><strong>{{ strtoupper($order->payment->method) }}</strong></div>
                    </div>
                    <div class="row text-sm mb-1">
                        <div class="col-6 text-muted">Status:</div>
                        <div class="col-6 text-end">
                            <span class="text-{{ $order->payment->status == 'success' ? 'success' : 'warning' }} font-weight-bold">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </div>
                    </div>
                    @if($order->payment->method == 'mpesa')
                    <div class="row text-sm mb-1">
                        <div class="col-6 text-muted">M-Pesa Code:</div>
                        <div class="col-6 text-end"><strong>{{ $order->payment->mpesa_code ?? 'N/A' }}</strong></div>
                    </div>
                    @endif
                @else
                    <div class="alert alert-warning py-2 mb-0">No payment record found.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
