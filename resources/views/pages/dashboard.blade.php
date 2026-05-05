@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <span class="text-muted" style="font-size:0.85rem;"><i class="fas fa-circle text-success"></i> System Online</span>
</div>

{{-- Row 1: Core Metrics --}}
<div class="row">
    <!-- Revenue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue (Today)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">KES {{ number_format($totalRevenueToday, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Orders (Today)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrdersToday }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Electronics-Specific Metrics --}}
<div class="row mb-4">
    <!-- Open Warranty Claims Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2" style="border-left: 4px solid #f6c23e;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Open Warranty Claims</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openWarrantyClaims }}</div>
                        @if($openWarrantyClaims > 0)
                            <a href="{{ route('warranty-claims.index', ['status' => 'open']) }}" class="small text-warning">View open claims →</a>
                        @else
                            <span class="small text-success">All clear ✓</span>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Serials Sold Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2" style="border-left: 4px solid #36b9cc;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Serial Numbers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $serialsSold }} <small class="text-muted fs-6">sold</small></div>
                        <div class="small text-success">{{ $availableSerials }} available in stock</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-barcode fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-xl-4 col-md-12 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-2" style="color:#6c757d;">Quick Actions</div>
                <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary w-100 mb-2"><i class="fas fa-calculator"></i> Open POS</a>
                <a href="{{ route('warranty-claims.create') }}" class="btn btn-sm btn-outline-warning w-100 mb-2"><i class="fas fa-shield-alt"></i> New Warranty Claim</a>
                <a href="{{ route('admin.serial-numbers.index') }}" class="btn btn-sm btn-outline-info w-100"><i class="fas fa-barcode"></i> Manage Serials</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sales Overview (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="position: relative; height:320px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Products (This Month)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2" style="position: relative; height:305px;">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered sm-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount (KES)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->customer->name ?? 'Walk-in Customer' }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            @if($recentOrders->isEmpty())
                                <tr><td colspan="4" class="text-center">No recent orders</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        @if($lowStockItems->count() > 0)
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">Items Requiring Re-order</h6>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($lowStockItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->name }} ({{ $item->sku }})
                            <span class="badge bg-danger rounded-pill">{{ $item->stock_quantity }} / {{ $item->reorder_level }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById("myAreaChart");
    var salesData = @json($last7Days);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(d => d.date),
            datasets: [{
                label: "Sales (KES)",
                lineTension: 0.3,
                backgroundColor: "rgba(31, 58, 110, 0.05)",
                borderColor: "rgba(31, 58, 110, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(31, 58, 110, 1)",
                data: salesData.map(d => d.sales),
            }],
        },
        options: { maintainAspectRatio: false, layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } } }
    });

    var ctxPie = document.getElementById("myPieChart");
    var topProducts = @json($topProducts);
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: topProducts.map(d => d.product ? d.product.name : 'Unknown'),
            datasets: [{
                data: topProducts.map(d => d.total_sold),
                backgroundColor: ['#1F3A6E', '#27AE60', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: { maintainAspectRatio: false },
    });
</script>
@endpush
