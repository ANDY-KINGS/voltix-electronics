@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Inventory Status</h1>
    <div>
        <a href="{{ route('reports.export', 'inventory') }}?format=pdf" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Download PDF</a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Reports</a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="inventoryTable">
                <thead class="bg-info text-white">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Reorder Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                        <td class="text-center font-weight-bold {{ $product->stock_quantity <= $product->reorder_level ? 'text-danger' : 'text-success' }}">
                            {{ $product->stock_quantity }}
                        </td>
                        <td class="text-center">{{ $product->reorder_level }}</td>
                        <td>
                            @if($product->stock_quantity <= $product->reorder_level)
                                <span class="badge bg-danger">Low Stock</span>
                            @else
                                <span class="badge bg-success">Adequate</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($products->isEmpty())
                        <tr><td colspan="7" class="text-center py-4">No products found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
