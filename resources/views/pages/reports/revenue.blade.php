@extends('layouts.app')

@section('title', 'Revenue & Profit Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Revenue & Profit generated</h1>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Reports</a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow border-left-info py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">KES {{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow border-left-warning py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total COGS (Cost of goods)</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">KES {{ number_format($totalCost, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow border-left-success py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gross Profit</div>
                <div class="h4 mb-0 font-weight-bold text-gray-800">KES {{ number_format($profit, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
        <form action="{{ route('reports.revenue') }}" method="GET" class="row gx-2 gy-2 justify-content-end">
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
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="revenueTable">
                <thead class="bg-success text-white">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Total Cost Price</th>
                        <th>Total Selling Price (Rev)</th>
                        <th>Profit Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    @php
                        $itemCost = $item->quantity * ($item->product->cost_price ?? 0);
                        $itemRev = $item->subtotal;
                        $itemProfit = $itemRev - $itemCost;
                    @endphp
                    <tr>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->product->name ?? 'Unknown' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($itemCost, 2) }}</td>
                        <td>{{ number_format($itemRev, 2) }}</td>
                        <td class="{{ $itemProfit > 0 ? 'text-success' : 'text-danger' }}">
                            <strong>{{ number_format($itemProfit, 2) }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
