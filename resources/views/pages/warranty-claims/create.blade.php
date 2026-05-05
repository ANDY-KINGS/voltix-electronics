@extends('layouts.app')

@section('title', 'New Warranty Claim')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Raise Warranty Claim</h1>
    <a href="{{ route('warranty-claims.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('warranty-claims.store') }}" method="POST" id="warrantyClaimForm">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customerSelect" class="form-select @error('customer_id') is-invalid @enderror" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                {{ old('customer_id', $selectedItem?->order?->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone ?? $customer->email ?? 'No contact' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Order Item <span class="text-danger">*</span></label>
                    <select name="order_item_id" id="orderItemSelect" class="form-select @error('order_item_id') is-invalid @enderror" required>
                        <option value="">-- Select customer first --</option>
                        @if($selectedItem)
                            <option value="{{ $selectedItem->id }}" selected>
                                {{ $selectedItem->product->name ?? 'Item #'.$selectedItem->id }}
                                @if($selectedItem->serialNumber) (S/N: {{ $selectedItem->serialNumber->serial_number }}) @endif
                            </option>
                        @endif
                    </select>
                    @error('order_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Auto-populated product details --}}
            <div id="itemDetails" class="alert alert-info d-none mb-3">
                <strong>Product:</strong> <span id="detailProduct">—</span> &nbsp;|&nbsp;
                <strong>Serial #:</strong> <span id="detailSerial">—</span> &nbsp;|&nbsp;
                <strong>Warranty Expiry:</strong> <span id="detailWarranty">—</span>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Issue Description <span class="text-danger">*</span></label>
                <textarea name="issue_description" class="form-control @error('issue_description') is-invalid @enderror"
                    rows="5" required placeholder="Describe the problem in detail (min 10 characters)...">{{ old('issue_description') }}</textarea>
                @error('issue_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-shield-alt"></i> Submit Claim</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('customerSelect').addEventListener('change', function() {
    const customerId = this.value;
    const itemSelect = document.getElementById('orderItemSelect');
    itemSelect.innerHTML = '<option value="">Loading...</option>';
    document.getElementById('itemDetails').classList.add('d-none');

    if (!customerId) {
        itemSelect.innerHTML = '<option value="">-- Select customer first --</option>';
        return;
    }

    // Fetch order items for this customer via simple AJAX
    fetch(`/api/customers/${customerId}/order-items`)
        .then(r => r.json())
        .then(items => {
            itemSelect.innerHTML = '<option value="">-- Select Order Item --</option>';
            items.forEach(item => {
                const sn = item.serial_number ? ` (S/N: ${item.serial_number})` : '';
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.text = `${item.product_name}${sn} — Order #${item.order_number}`;
                opt.dataset.product = item.product_name;
                opt.dataset.serial  = item.serial_number || 'N/A';
                opt.dataset.expiry  = item.warranty_expiry || 'N/A';
                itemSelect.appendChild(opt);
            });
        })
        .catch(() => {
            itemSelect.innerHTML = '<option value="">-- Could not load items --</option>';
        });
});

document.getElementById('orderItemSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) { document.getElementById('itemDetails').classList.add('d-none'); return; }
    document.getElementById('detailProduct').textContent  = opt.dataset.product  || '—';
    document.getElementById('detailSerial').textContent   = opt.dataset.serial   || '—';
    document.getElementById('detailWarranty').textContent = opt.dataset.expiry   || '—';
    document.getElementById('itemDetails').classList.remove('d-none');
});
</script>
@endpush
@endsection
