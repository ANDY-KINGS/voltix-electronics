@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Customers</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Customer</a>
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
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>{{ $customer->email ?? 'N/A' }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            @role('admin')
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete customer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endrole
                        </td>
                    </tr>
                    @endforeach
                    @if($customers->isEmpty())
                        <tr><td colspan="5" class="text-center py-4">No customers found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
