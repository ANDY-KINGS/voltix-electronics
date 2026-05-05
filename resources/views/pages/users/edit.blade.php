@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit User: {{ $user->name }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="alert alert-info py-2">
                <i class="fas fa-info-circle"></i> Leave password fields empty if you don't want to change the password.
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    @php $currentRole = $user->roles->first()->name ?? ''; @endphp
                    <option value="cashier" {{ old('role', $currentRole) == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="owner" {{ old('role', $currentRole) == 'owner' ? 'selected' : '' }}>Owner</option>
                    @if($currentRole == 'admin')
                    <option value="admin" selected>System Admin</option>
                    @endif
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
        </form>
    </div>
</div>
@endsection
