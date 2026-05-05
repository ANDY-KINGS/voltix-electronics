@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($category->description, 50) }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($categories->isEmpty())
                        <tr><td colspan="4" class="text-center py-4">No categories found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
