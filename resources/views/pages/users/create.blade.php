@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add New User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <!-- Admin role is intentionally excluded here so only sysadmin retains it, or we could add it back if needed -->
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save User</button>
        </form>
    </div>
</div>
@endsection
