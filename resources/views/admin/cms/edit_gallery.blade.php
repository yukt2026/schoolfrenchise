@extends('layouts.admin')

@section('title', 'Edit Gallery Photograph')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.gallery') }}">Gallery</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Photo</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Photo Details
            </h2>
            <p class="text-muted small mb-0">Modify caption, display category, sort priority, or replace image file.</p>
        </div>
        <div>
            <a href="{{ route('admin.cms.gallery') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Gallery
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image me-2 text-primary"></i>Photo #{{ $gallery->id }} Particulars</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.cms.gallery.update', $gallery) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label small fw-semibold">Photo Title / Caption <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $gallery->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label small fw-semibold">Album Category <span class="text-danger">*</span></label>
                                <input list="categoryList" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $gallery->category) }}" required>
                                <datalist id="categoryList">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="sort_order" class="form-label small fw-semibold">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $gallery->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold d-block">Visibility</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', $gallery->status) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="status">Published</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label small fw-semibold">Replace Image File (Optional)</label>
                            @if($gallery->image_path)
                                <div class="mb-2">
                                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" style="max-height: 120px; border-radius: 6px;" class="border p-1 bg-light">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text small">Leave blank to keep existing photograph.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.cms.gallery') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-check me-1"></i> Update Photo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
