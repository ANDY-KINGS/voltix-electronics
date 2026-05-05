@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sales Report</h1>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Reports</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
        <form action="{{ route('reports.sales') }}" method="GET" class="row gx-2 gy-2 align-items-center">
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
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="salesTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Payment Method</th>
                        <th>Discount</th>
                        <th>Total Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumTotal = 0; $sumDiscount = 0; @endphp
                    @foreach($sales as $order)
                    @php 
                        $sumTotal += $order->total_amount; 
                        $sumDiscount += $order->discount;
                    @endphp
                    <tr>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ strtoupper($order->payment->method ?? 'N/A') }}</td>
                        <td class="text-danger">{{ $order->discount > 0 ? number_format($order->discount, 2) : '-' }}</td>
                        <td class="font-weight-bold">{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($sales->isEmpty())
                        <tr><td colspan="6" class="text-center py-4">No completed sales found.</td></tr>
                    @endif
                </tbody>
                @if($sales->isNotEmpty())
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-end h5 border-top border-dark pt-3">Grand Total:</td>
                        <td class="text-danger h5 border-top border-dark pt-3">{{ number_format($sumDiscount, 2) }}</td>
                        <td class="text-primary h5 border-top border-dark pt-3">KES {{ number_format($sumTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
