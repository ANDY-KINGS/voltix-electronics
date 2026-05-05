@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Users</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->roles->first()->name ?? 'N/A') }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('admin.users.toggleActive', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}">
                                    <i class="fas fa-power-off"></i> {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user completely?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($users->isEmpty())
                        <tr><td colspan="6" class="text-center py-4">No users found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
