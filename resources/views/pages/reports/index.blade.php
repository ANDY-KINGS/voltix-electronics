@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Business Reports</h1>
</div>

<div class="row">
    <!-- Sales Report -->
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary text-white p-3 rounded mr-3" style="margin-right: 15px;">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h5 class="mb-0 font-weight-bold">Sales Report</h5>
            </div>
            <p class="text-muted">View all completed sales, filter by date range, and track daily total revenue generated.</p>
            <div class="mt-auto">
                <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary w-100">View Sales Report</a>
            </div>
        </div>
    </div>

    <!-- Inventory Report -->
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-info text-white p-3 rounded mr-3" style="margin-right: 15px;">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <h5 class="mb-0 font-weight-bold">Inventory Report</h5>
            </div>
            <p class="text-muted">Check current stock levels, identify out-of-stock items, and see which products require reordering.</p>
            <div class="mt-auto">
                <a href="{{ route('reports.inventory') }}" class="btn btn-outline-info w-100">View Inventory Report</a>
            </div>
        </div>
    </div>

    <!-- Revenue & Profit Report -->
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success text-white p-3 rounded mr-3" style="margin-right: 15px;">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
                <h5 class="mb-0 font-weight-bold">Revenue & Profit</h5>
            </div>
            <p class="text-muted">Analyze item-level profitability, comparing cost prices against actual selling prices.</p>
            <div class="mt-auto">
                <a href="{{ route('reports.revenue') }}" class="btn btn-outline-success w-100">View Profit Report</a>
            </div>
        </div>
    </div>
</div>
@endsection
