@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name / Model</th>
                        <th>SKU</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Price (KES)</th>
                        <th>Stock</th>
                        <th>Serials</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="text-center" style="width: 60px;">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $product->name }}</span>
                            @if($product->model_number)
                                <small class="text-muted d-block">{{ $product->model_number }}</small>
                            @endif
                            @if($product->serial_tracking)
                                <span class="badge bg-info" style="font-size:0.65rem;">Serial Tracked</span>
                            @endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->brand->name ?? '—' }}</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock_quantity <= $product->reorder_level)
                                <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                            @else
                                <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->serial_tracking)
                                <span class="badge bg-light text-dark border">
                                    {{ $product->serialNumbers()->available()->count() }} avail.
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
