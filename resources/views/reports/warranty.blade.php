@extends('layouts.app')

@section('title', 'Warranty Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="fas fa-shield-alt text-warning"></i> Warranty Report</h1>
    <div>
        <a href="{{ route('reports.warranty.export', 'pdf') }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

{{-- Row 1: Stat Cards --}}
@php
    $total      = $claimsByStatus['total'] ?? 0;
    $open       = $claimsByStatus['open'] ?? 0;
    $resolved   = $claimsByStatus['resolved'] ?? 0;
    $rejected   = $claimsByStatus['rejected'] ?? 0;
    $rejRate    = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
@endphp
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow text-center py-3">
            <div class="card-body">
                <div class="h3 fw-bold">{{ $total }}</div>
                <div class="text-muted small text-uppercase">Total Claims</div>
                <i class="fas fa-shield-alt fa-2x text-primary mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow text-center py-3 border-danger">
            <div class="card-body">
                <div class="h3 fw-bold text-danger">{{ $open }}</div>
                <div class="text-muted small text-uppercase">Open Claims</div>
                <i class="fas fa-exclamation-circle fa-2x text-danger mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow text-center py-3 border-success">
            <div class="card-body">
                <div class="h3 fw-bold text-success">{{ $resolved }}</div>
                <div class="text-muted small text-uppercase">Resolved</div>
                <i class="fas fa-check-circle fa-2x text-success mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow text-center py-3 border-warning">
            <div class="card-body">
                <div class="h3 fw-bold text-warning">{{ $rejRate }}%</div>
                <div class="text-muted small text-uppercase">Rejection Rate</div>
                <i class="fas fa-times-circle fa-2x text-warning mt-2"></i>
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Expiring Warranties --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-clock text-warning"></i> Warranties Expiring in Next 30 Days</h6>
        <span class="badge bg-warning text-dark">{{ $expiringSoon->count() }} items</span>
    </div>
    <div class="card-body">
        @if($expiringSoon->isEmpty())
            <p class="text-center text-muted py-3">No warranties expiring in the next 30 days.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Serial #</th>
                        <th>Customer</th>
                        <th>Expiry Date</th>
                        <th>Days Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringSoon as $item)
                    @php
                        $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->warranty_expiry), false);
                        $rowClass = $daysLeft < 7 ? 'table-danger' : ($daysLeft < 14 ? 'table-warning' : '');
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $item->product->name ?? '—' }}</td>
                        <td>{{ $item->product->brand->name ?? '—' }}</td>
                        <td>
                            @if($item->serialNumber)
                                <code class="small">{{ $item->serialNumber->serial_number }}</code>
                            @else <span class="text-muted">N/A</span> @endif
                        </td>
                        <td>{{ $item->order->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->warranty_expiry)->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $daysLeft < 7 ? 'bg-danger' : ($daysLeft < 14 ? 'bg-warning text-dark' : 'bg-info') }}">
                                {{ $daysLeft }} day(s)
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Row 3: Top Claimed Products --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-trophy text-warning"></i> Top 5 Most Claimed Products</h6>
    </div>
    <div class="card-body">
        @if($topClaimed->isEmpty())
            <p class="text-center text-muted py-3">No warranty claims data available yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Total Claims</th>
                        <th>Resolved</th>
                        <th>Open</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topClaimed as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->orderItem->product->name ?? 'Unknown' }}</td>
                        <td>{{ $row->orderItem->product->brand->name ?? '—' }}</td>
                        <td><span class="badge bg-primary">{{ $row->claim_count }}</span></td>
                        <td><span class="badge bg-success">{{ $row->resolved_count }}</span></td>
                        <td><span class="badge bg-danger">{{ $row->open_count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
