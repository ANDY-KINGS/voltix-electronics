@extends('layouts.app')

@section('title', 'Serial Numbers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Serial Numbers</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSerialsModal">
        <i class="fas fa-plus"></i> Import Serials
    </button>
</div>

{{-- Filter Bar --}}
<div class="card shadow mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label mb-1 small fw-semibold">Filter by Product</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Filter by Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="sold"      {{ request('status') == 'sold'      ? 'selected' : '' }}>Sold</option>
                    <option value="returned"  {{ request('status') == 'returned'  ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.serial-numbers.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Serial Number</th>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Status</th>
                        <th>Sold In Order</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serialNumbers as $sn)
                    <tr>
                        <td><code>{{ $sn->serial_number }}</code></td>
                        <td>{{ $sn->product->name ?? '—' }}</td>
                        <td>{{ $sn->product->brand->name ?? '—' }}</td>
                        <td>
                            @if($sn->status === 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($sn->status === 'sold')
                                <span class="badge bg-danger">Sold</span>
                            @else
                                <span class="badge bg-warning text-dark">Returned</span>
                            @endif
                        </td>
                        <td>
                            @if($sn->orderItem && $sn->orderItem->order_id)
                                <a href="{{ route('orders.show', $sn->orderItem->order_id) }}" class="small">Order #{{ $sn->orderItem->order_id }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $sn->created_at->format('d M Y') }}</td>
                        <td>
                            @if($sn->status === 'available')
                                <form action="{{ route('admin.serial-numbers.destroy', $sn->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete serial {{ $sn->serial_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @else
                                <span class="text-muted small">Protected</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($serialNumbers->isEmpty())
                        <tr><td colspan="7" class="text-center text-muted py-4">
                            No serial numbers found. <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#addSerialsModal">Import now</button>
                        </td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $serialNumbers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Bulk Import Modal --}}
<div class="modal fade" id="addSerialsModal" tabindex="-1" aria-labelledby="addSerialsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addSerialsModalLabel"><i class="fas fa-barcode"></i> Import Serial Numbers</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.serial-numbers.store') }}">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} — {{ $p->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Serial Numbers <span class="text-danger">*</span></label>
                        <textarea name="bulk_serials" class="form-control font-monospace" rows="8"
                            placeholder="Enter one serial number per line:&#10;ELC-001-SN-A1B2C3D4&#10;ELC-001-SN-E5F6G7H8&#10;ELC-001-SN-I9J0K1L2">{{ old('bulk_serials') }}</textarea>
                        <div class="form-text">Enter one serial number per line. Duplicates are silently skipped.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import Serials</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-open modal if there are validation errors
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('addSerialsModal'));
        modal.show();
    @endif
</script>
@endpush
@endsection
