@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Suppliers</h1>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Supplier</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->email ?? 'N/A' }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete supplier?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($suppliers->isEmpty())
                        <tr><td colspan="5" class="text-center py-4">No suppliers found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $suppliers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
