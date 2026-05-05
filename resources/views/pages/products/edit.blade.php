@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Product: {{ $product->name }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            {{-- Name + SKU --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                </div>
            </div>

            {{-- Model Number --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Model Number <small class="text-muted fw-normal">(optional)</small></label>
                <input type="text" name="model_number" class="form-control" placeholder="e.g. SM-A556E"
                    value="{{ old('model_number', $product->model_number) }}">
            </div>

            {{-- Category + Brand --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">-- Select Brand --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Supplier --}}
            <div class="mb-3">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cost + Selling Price --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Cost Price (KES) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Selling Price (KES) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>
            </div>

            {{-- Warranty --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Warranty (Months)</label>
                <input type="number" name="warranty_months" class="form-control" value="{{ old('warranty_months', $product->warranty_months ?? 12) }}" min="0" style="max-width:200px;">
            </div>

            {{-- Stock + Reorder --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                    <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $product->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $product->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Serial Tracking Toggle --}}
            <div class="mb-3 p-3 bg-light rounded border">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="serial_tracking" value="1" id="serialTracking"
                        {{ old('serial_tracking', $product->serial_tracking) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="serialTracking">
                        Enable Serial Number Tracking
                        <small class="text-muted d-block fw-normal">Turn on for high-value items (phones, laptops).</small>
                    </label>
                </div>
                @if($product->serial_tracking)
                    <div class="mt-2 small">
                        <span class="badge bg-info">{{ $product->serialNumbers()->available()->count() }} available</span>
                        <span class="badge bg-danger ms-1">{{ $product->serialNumbers()->sold()->count() }} sold</span>
                        <a href="{{ route('admin.serial-numbers.index', ['product_id' => $product->id]) }}" class="ms-2 small">Manage serials →</a>
                    </div>
                @endif
            </div>

            {{-- Image --}}
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" width="50" height="50" class="rounded me-3 border" style="object-fit:cover;">
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <label class="form-label mb-0">Update Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Product</button>
        </form>
    </div>
</div>
@endsection
