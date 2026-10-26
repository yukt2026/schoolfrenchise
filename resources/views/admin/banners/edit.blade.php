@extends('layouts.admin')

@section('title', 'Edit Hero Banner - TECH ERA COMPUTER ERP')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Hero Banners</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Banner #{{ $banner->id }}</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Hero Banner
            </h2>
            <p class="text-muted small mb-0">Modify banner graphics, destination redirect URL, sort ordering, and visibility status.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Banners
            </a>
            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Permanently delete this banner?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger shadow-sm">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please fix the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold text-dark mb-0 fs-6">Edit Banner Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Banner Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Banner Title / Caption <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subtitle -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Subtitle / Sub-Heading <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle', $banner->subtitle) }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Image Display -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Current Banner Graphic</label>
                            <div class="p-2 bg-light border rounded-3">
                                <div class="ratio ratio-21x9 bg-dark rounded-2 overflow-hidden mb-2" style="max-height: 240px;">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-100 h-100 object-fit-contain">
                                </div>
                                <div class="small text-muted font-monospace"><i class="fa-solid fa-file-image me-1"></i>Path: {{ $banner->image_path }}</div>
                            </div>
                        </div>

                        <!-- Replace Primary Image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Replace Graphic Image <span class="text-muted fw-normal">(Leave blank to keep existing)</span></label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small">Max file size: 10 MB. Recommended ratio: 2:1 (1920x800 or 1200x600 px).</div>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Optional Mobile Image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Mobile-Specific Image <span class="text-muted fw-normal">(Optional)</span></label>
                            @if($banner->mobile_image_path)
                                <div class="p-2 bg-light border rounded-3 mb-2" style="max-width: 260px;">
                                    <img src="{{ $banner->mobile_image_url }}" alt="Mobile Banner" class="w-100 h-auto rounded">
                                    <div class="small text-muted font-monospace mt-1">Mobile Image Set</div>
                                </div>
                            @endif
                            <input type="file" name="mobile_image" class="form-control @error('mobile_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text small">Upload only if you want a different layout specifically on smartphones.</div>
                            @error('mobile_image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Redirect Link -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark">Click Redirect URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-link text-muted"></i></span>
                                    <input type="text" name="link_url" class="form-control @error('link_url') is-invalid @enderror" value="{{ old('link_url', $banner->link_url) }}" placeholder="e.g. /admission-apply">
                                </div>
                                @error('link_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Target Window</label>
                                <select name="link_target" class="form-select">
                                    <option value="_self" {{ old('link_target', $banner->link_target) == '_self' ? 'selected' : '' }}>Same Window (_self)</option>
                                    <option value="_blank" {{ old('link_target', $banner->link_target) == '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Order & Visibility -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Display Priority / Sort Order</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-arrow-down-1-9 text-muted"></i></span>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Visibility Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" value="1" {{ old('status', $banner->status) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="statusSwitch">
                                        Active & Visible on Homepage Slider
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex align-items-center gap-2 pt-2">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Banner
                            </button>
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-light border text-muted">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-circle-info text-info me-2"></i>Banner Metadata</h6>
                </div>
                <div class="card-body p-3">
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Banner ID:</dt>
                        <dd class="col-7 font-monospace fw-bold text-dark">#{{ $banner->id }}</dd>

                        <dt class="col-5 text-muted">Created:</dt>
                        <dd class="col-7">{{ $banner->created_at->format('d M, Y h:i A') }}</dd>

                        <dt class="col-5 text-muted">Last Updated:</dt>
                        <dd class="col-7">{{ $banner->updated_at->format('d M, Y h:i A') }}</dd>

                        <dt class="col-5 text-muted">Status:</dt>
                        <dd class="col-7">
                            <span class="badge {{ $banner->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $banner->status ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
