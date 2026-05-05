@extends('layouts.app')

@section('title', 'Warranty Claims')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Warranty Claims</h1>
    <a href="{{ route('warranty-claims.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Claim</a>
</div>

{{-- Status Filter Tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('warranty-claims.index') }}">
            All <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'open' ? 'active' : '' }}" href="{{ route('warranty-claims.index', ['status' => 'open']) }}">
            Open <span class="badge bg-danger ms-1">{{ $counts['open'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'in_review' ? 'active' : '' }}" href="{{ route('warranty-claims.index', ['status' => 'in_review']) }}">
            In Review <span class="badge bg-warning text-dark ms-1">{{ $counts['in_review'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'resolved' ? 'active' : '' }}" href="{{ route('warranty-claims.index', ['status' => 'resolved']) }}">
            Resolved <span class="badge bg-success ms-1">{{ $counts['resolved'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('warranty-claims.index', ['status' => 'rejected']) }}">
            Rejected <span class="badge bg-secondary ms-1">{{ $counts['rejected'] }}</span>
        </a>
    </li>
</ul>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Claim #</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Serial #</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims as $claim)
                    <tr>
                        <td class="fw-semibold">#{{ $claim->id }}</td>
                        <td>{{ $claim->customer->name ?? '—' }}</td>
                        <td>{{ $claim->orderItem->product->name ?? '—' }}</td>
                        <td>
                            @if($claim->orderItem->serialNumber)
                                <code class="small">{{ $claim->orderItem->serialNumber->serial_number }}</code>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="small">{{ Str::limit($claim->issue_description, 50) }}</td>
                        <td>
                            @if($claim->status === 'open')
                                <span class="badge bg-danger">Open</span>
                            @elseif($claim->status === 'in_review')
                                <span class="badge bg-warning text-dark">In Review</span>
                            @elseif($claim->status === 'resolved')
                                <span class="badge bg-success">Resolved</span>
                            @else
                                <span class="badge bg-secondary">Rejected</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $claim->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('warranty-claims.show', $claim->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @if($claims->isEmpty())
                        <tr><td colspan="8" class="text-center text-muted py-4">No warranty claims found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $claims->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
