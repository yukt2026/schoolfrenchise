@extends('layouts.admin')

@section('title', 'Add New Hero Banner - TECH ERA COMPUTER ERP')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Hero Banners</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload Banner</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Upload Hero Slider Banner
            </h2>
            <p class="text-muted small mb-0">Add a new graphic promotional banner for display in the homepage hero slider.</p>
        </div>
        <div>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Banners
            </a>
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
                    <h5 class="card-title fw-bold text-dark mb-0 fs-6">Banner Details & Media</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" id="bannerForm">
                        @csrf

                        <!-- Banner Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Banner Title / Caption <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. TECH ERA COMPUTER - Admission Open 2026-27" required>
                            <div class="form-text small">Used for identification, screen readers, and SEO image alt-text.</div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subtitle / Tagline -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Subtitle / Sub-Heading <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle') }}" placeholder="e.g. Empowering Digital India with Quality Computer Education">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Primary Banner Image Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                Primary Banner Graphic Image <span class="text-danger">*</span>
                            </label>
                            <div class="input-group mb-2">
                                <input type="file" name="image" id="bannerImageInput" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" required onchange="previewBanner(this, 'bannerPreviewBox', 'bannerPreviewImg')">
                            </div>
                            <div class="form-text small">
                                Supported formats: <strong>JPG, JPEG, PNG, WEBP</strong>. Max size: <strong>10 MB</strong>. Recommended resolution: <strong>1920 &times; 800 px</strong> or <strong>1200 &times; 600 px</strong> (2:1 aspect ratio).
                            </div>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Area -->
                            <div id="bannerPreviewBox" class="mt-3 p-2 bg-light border rounded-3 d-none">
                                <div class="small fw-semibold text-muted mb-1"><i class="fa-solid fa-eye me-1"></i>Image Live Preview:</div>
                                <div class="ratio ratio-21x9 bg-dark rounded-2 overflow-hidden">
                                    <img id="bannerPreviewImg" src="" alt="Banner Preview" class="w-100 h-100 object-fit-contain">
                                </div>
                            </div>
                        </div>

                        <!-- Optional Mobile Banner Image -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                Dedicated Mobile Banner Image <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <input type="file" name="mobile_image" id="mobileImageInput" class="form-control @error('mobile_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewBanner(this, 'mobilePreviewBox', 'mobilePreviewImg')">
                            <div class="form-text small">
                                If left blank, the primary banner graphic will automatically scale responsively on mobile phones.
                            </div>
                            @error('mobile_image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <!-- Mobile Preview Area -->
                            <div id="mobilePreviewBox" class="mt-3 p-2 bg-light border rounded-3 d-none" style="max-width: 320px;">
                                <div class="small fw-semibold text-muted mb-1"><i class="fa-solid fa-mobile-screen me-1"></i>Mobile Preview:</div>
                                <div class="bg-dark rounded-2 overflow-hidden" style="max-height: 240px;">
                                    <img id="mobilePreviewImg" src="" alt="Mobile Banner Preview" class="w-100 h-auto object-fit-contain">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Link & Destination URL -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark">Click Redirect URL <span class="text-muted fw-normal">(Optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-link text-muted"></i></span>
                                    <input type="text" name="link_url" class="form-control @error('link_url') is-invalid @enderror" value="{{ old('link_url') }}" placeholder="e.g. /admission-apply or https://wa.me/919162384660">
                                </div>
                                <div class="form-text small">When visitors tap or click on the banner, they will be taken to this link.</div>
                                @error('link_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Open In</label>
                                <select name="link_target" class="form-select">
                                    <option value="_self" {{ old('link_target') == '_self' ? 'selected' : '' }}>Same Window (_self)</option>
                                    <option value="_blank" {{ old('link_target') == '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Ordering & Status -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Display Priority / Sort Order</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-arrow-down-1-9 text-muted"></i></span>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $nextOrder) }}" min="0" required>
                                </div>
                                <div class="form-text small">Lower numbers appear first on the carousel (e.g. 1, 2, 3).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Visibility Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="statusSwitch">
                                        Publish Immediately (Active on homepage)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center gap-2 pt-2">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Publish Banner
                            </button>
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-light border text-muted">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side Help & Best Practices -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Banner Tips & Best Practices</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-muted">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div><strong>Graphic Design:</strong> You can upload banners designed in Canva or Photoshop containing institute logo, contact details, and course names.</div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div><strong>Recommended Ratio:</strong> 2:1 or 16:9 aspect ratio (e.g., 1920x960, 1600x800, or 1200x600 px) ensures flawless look across mobile and desktop.</div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div><strong>Link Action:</strong> Link to <code>/admission-apply</code> for direct registrations or WhatsApp link for instant queries.</div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div><strong>Display Ordering:</strong> Give the most important announcement (e.g. Admission Open) sort order <strong>1</strong>.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewBanner(input, boxId, imgId) {
    const box = document.getElementById(boxId);
    const img = document.getElementById(imgId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            box.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        box.classList.add('d-none');
    }
}
</script>
@endpush
@endsection
