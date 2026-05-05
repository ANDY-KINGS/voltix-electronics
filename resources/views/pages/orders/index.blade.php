@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Orders</h1>
    <a href="{{ route('pos.index') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Order (POS)</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
        <form action="{{ route('orders.index') }}" method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="order_number" class="form-control mb-2 mb-sm-0" placeholder="Order Number" value="{{ request('order_number') }}">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select mb-2 mb-sm-0">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="date" name="date_from" class="form-control mb-2 mb-sm-0" value="{{ request('date_from') }}">
            </div>
            <div class="col-auto">
                <span class="text-muted mx-2">to</span>
            </div>
            <div class="col-auto">
                <input type="date" name="date_to" class="form-control mb-2 mb-sm-0" value="{{ request('date_to') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total (KES)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $order->user->name ?? 'Unknown' }}</td>
                        <td>
                            <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            {{ strtoupper($order->payment->method ?? 'N/A') }}
                        </td>
                        <td class="font-weight-bold">{{ number_format($order->total_amount, 2) }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('pos.receipt', $order->id) }}" class="btn btn-sm btn-secondary"><i class="fas fa-receipt"></i> Receipt</a>
                        </td>
                    </tr>
                    @endforeach
                    @if($orders->isEmpty())
                        <tr><td colspan="8" class="text-center py-4">No orders found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
