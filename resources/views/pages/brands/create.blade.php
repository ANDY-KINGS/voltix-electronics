@extends('layouts.app')

@section('title', 'Add Brand')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Add New Brand</h1>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required placeholder="e.g. Samsung">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country of Origin <small class="text-muted">(optional)</small></label>
                    <input type="text" name="country_of_origin" class="form-control"
                           value="{{ old('country_of_origin') }}" placeholder="e.g. South Korea">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Brand Logo <small class="text-muted">(optional, max 2MB)</small></label>
                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Brand</button>
        </form>
    </div>
</div>
@endsection
