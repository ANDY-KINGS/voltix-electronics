@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add New Customer</h1>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>

            <div class="mb-4">
                <label class="form-label">National ID Number <small class="text-muted">(for warranty verification)</small></label>
                <input type="text" name="id_number" class="form-control" placeholder="e.g. 12345678"
                    value="{{ old('id_number') }}">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Customer</button>
        </form>
    </div>
</div>
@endsection
