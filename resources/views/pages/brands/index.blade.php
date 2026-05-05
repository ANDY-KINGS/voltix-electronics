@extends('layouts.app')

@section('title', 'Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Brands</h1>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Brand</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Logo</th>
                        <th>Brand Name</th>
                        <th>Country of Origin</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td class="text-center">
                            @if($brand->logo)
                                <img src="{{ Storage::url($brand->logo) }}" width="40" height="40" style="object-fit:contain;" class="rounded border">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;margin:auto;">
                                    <i class="fas fa-tags"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $brand->name }}</td>
                        <td>{{ $brand->country_of_origin ?? '—' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $brand->products_count }} product(s)</span>
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete brand {{ addslashes($brand->name) }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($brands->isEmpty())
                        <tr><td colspan="5" class="text-center text-muted py-3">No brands found. <a href="{{ route('admin.brands.create') }}">Add one now</a>.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $brands->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
