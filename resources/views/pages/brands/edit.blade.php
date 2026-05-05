@extends('layouts.app')

@section('title', 'Edit Brand')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Brand: {{ $brand->name }}</h1>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $brand->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country of Origin</label>
                    <input type="text" name="country_of_origin" class="form-control"
                           value="{{ old('country_of_origin', $brand->country_of_origin) }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Brand Logo</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if($brand->logo)
                        <img src="{{ Storage::url($brand->logo) }}" width="60" height="60"
                             style="object-fit:contain;" class="rounded border p-1 bg-white">
                        <span class="text-muted small">Current logo</span>
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white"
                             style="width:60px;height:60px;">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <span class="text-muted small">No logo uploaded</span>
                    @endif
                </div>
                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                <div class="form-text">Upload a new logo to replace the current one.</div>
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Brand</button>
        </form>
    </div>
</div>
@endsection
