@extends('layouts.app')

@section('title', 'Warranty Claim #' . $warrantyClaim->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Warranty Claim #{{ $warrantyClaim->id }}</h1>
    <a href="{{ route('warranty-claims.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="row">
    {{-- Claim Details --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Claim Details</h6>
                @if($warrantyClaim->status === 'open')
                    <span class="badge bg-danger fs-6">Open</span>
                @elseif($warrantyClaim->status === 'in_review')
                    <span class="badge bg-warning text-dark fs-6">In Review</span>
                @elseif($warrantyClaim->status === 'resolved')
                    <span class="badge bg-success fs-6">Resolved</span>
                @else
                    <span class="badge bg-secondary fs-6">Rejected</span>
                @endif
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th width="35%">Customer</th><td>{{ $warrantyClaim->customer->name ?? '—' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $warrantyClaim->customer->phone ?? '—' }}</td></tr>
                    <tr><th>National ID</th><td>{{ $warrantyClaim->customer->id_number ?? '—' }}</td></tr>
                    <tr><th>Product</th><td>{{ $warrantyClaim->orderItem->product->name ?? '—' }}</td></tr>
                    <tr><th>Brand</th><td>{{ $warrantyClaim->orderItem->product->brand->name ?? '—' }}</td></tr>
                    <tr><th>Serial Number</th>
                        <td>
                            @if($warrantyClaim->orderItem->serialNumber)
                                <code>{{ $warrantyClaim->orderItem->serialNumber->serial_number }}</code>
                            @else
                                <span class="text-muted">Not tracked</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Warranty Expiry</th>
                        <td>
                            @if($warrantyClaim->orderItem->warranty_expiry)
                                {{ \Carbon\Carbon::parse($warrantyClaim->orderItem->warranty_expiry)->format('d M Y') }}
                                @if(\Carbon\Carbon::parse($warrantyClaim->orderItem->warranty_expiry)->isPast())
                                    <span class="badge bg-danger ms-1">Expired</span>
                                @else
                                    <span class="badge bg-success ms-1">Active</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Date Raised</th><td>{{ $warrantyClaim->created_at->format('d M Y, H:i') }}</td></tr>
                    @if($warrantyClaim->resolved_at)
                    <tr><th>Resolved On</th><td>{{ $warrantyClaim->resolved_at->format('d M Y, H:i') }}</td></tr>
                    @endif
                </table>

                <hr>
                <h6 class="fw-bold">Issue Description</h6>
                <p class="bg-light p-3 rounded">{{ $warrantyClaim->issue_description }}</p>

                @if($warrantyClaim->notes)
                    <h6 class="fw-bold">Resolution Notes</h6>
                    <p class="bg-light p-3 rounded border-start border-success border-3">{{ $warrantyClaim->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Admin Resolution Panel --}}
    <div class="col-lg-4">
        @can('manage-warranty-claims')
        <div class="card shadow mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 fw-bold"><i class="fas fa-user-shield"></i> Admin: Update Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('warranty-claims.update-status', $warrantyClaim->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="open"      {{ $warrantyClaim->status == 'open'      ? 'selected' : '' }}>Open</option>
                            <option value="in_review" {{ $warrantyClaim->status == 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="resolved"  {{ $warrantyClaim->status == 'resolved'  ? 'selected' : '' }}>Resolved</option>
                            <option value="rejected"  {{ $warrantyClaim->status == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resolution Notes</label>
                        <textarea name="notes" class="form-control" rows="4"
                            placeholder="Describe the resolution or reason for rejection...">{{ old('notes', $warrantyClaim->notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Update Status</button>
                </form>
            </div>
        </div>
        @endcan

        {{-- Order Context --}}
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">Order Context</h6>
            </div>
            <div class="card-body">
                @if($warrantyClaim->orderItem->order ?? null)
                    <p class="mb-1"><strong>Order:</strong>
                        <a href="{{ route('orders.show', $warrantyClaim->orderItem->order_id) }}">
                            #{{ $warrantyClaim->orderItem->order->order_number ?? $warrantyClaim->orderItem->order_id }}
                        </a>
                    </p>
                    <p class="mb-1"><strong>Qty:</strong> {{ $warrantyClaim->orderItem->quantity }}</p>
                    <p class="mb-0"><strong>Unit Price:</strong> KES {{ number_format($warrantyClaim->orderItem->unit_price, 2) }}</p>
                @else
                    <p class="text-muted">Order context unavailable.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
