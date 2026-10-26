@extends('layouts.centre')

@section('title', 'Centre Profile & Settings - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-3 mb-md-4">
    <h3 class="fw-bold text-dark mb-1 fs-5 fs-md-4">
        <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Study Centre Profile & Settings
    </h3>
    <p class="text-muted small mb-0">Manage franchise branch credentials, director profile, contact points, and login password.</p>
</div>

<div class="row g-3 g-md-4">
    <!-- Left Column: Affiliation & Details Form -->
    <div class="col-lg-8">
        <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
            <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-building text-primary me-2"></i>Branch Profile & Lab Infrastructure</h6>
                <span class="badge bg-primary font-monospace rounded-2">{{ $centre->centre_code }}</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('centre.profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Study Centre Official Name</label>
                            <input type="text" class="form-control bg-light rounded-2" value="{{ $centre->centre_name }}" readonly>
                            <div class="form-text" style="font-size: 0.72rem;">Central board authorized name. Contact head office for changes.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Director / Head Person Name <span class="text-danger">*</span></label>
                            <input type="text" name="director_name" class="form-control rounded-2" value="{{ old('director_name', $centre->director_name) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Contact Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control rounded-2" maxlength="10" value="{{ old('mobile', $centre->mobile) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternate Mobile</label>
                            <input type="text" name="alternate_mobile" class="form-control rounded-2" value="{{ old('alternate_mobile', $centre->alternate_mobile) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Official Contact Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-2" value="{{ old('email', $centre->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Computer Lab Workstations Capacity <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="1" name="lab_capacity" class="form-control rounded-start-2" value="{{ old('lab_capacity', $centre->lab_capacity) }}" required>
                                <span class="input-group-text bg-light rounded-end-2">PCs</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Registered Campus Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control rounded-2" value="{{ old('address', $centre->address) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Postal Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" class="form-control rounded-2" value="{{ old('pincode', $centre->pincode) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control rounded-2" value="{{ old('city', $centre->city) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">District</label>
                            <input type="text" class="form-control bg-light rounded-2" value="{{ $centre->district?->name }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" class="form-control bg-light rounded-2" value="{{ $centre->state?->name }}" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Centre Official Logo / Signboard</label>
                            @if($centre->centre_logo_url)
                                <div class="mb-2" id="centreLogoPreviewBox">
                                    <img id="centreLogoPreview" src="{{ $centre->centre_logo_url }}" alt="Centre Logo" class="rounded border p-1 bg-white" style="height: 70px; max-width: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/150x80?text=Logo';">
                                </div>
                            @else
                                <div class="mb-2 d-none" id="centreLogoPreviewBox">
                                    <img id="centreLogoPreview" src="" alt="Centre Logo" class="rounded border p-1 bg-white" style="height: 70px; max-width: 140px; object-fit: contain;">
                                </div>
                            @endif
                            <input type="file" name="centre_logo" class="form-control form-control-sm rounded-2" accept="image/*" onchange="previewImageDirect(this, 'centreLogoPreview', 'centreLogoPreviewBox')">
                            <div class="form-text" style="font-size: 0.72rem;">JPG/PNG/WebP, Max 2MB. Instant preview on select.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Director Passport Photo</label>
                            @if($centre->director_photo_url)
                                <div class="mb-2" id="directorPhotoPreviewBox">
                                    <img id="directorPhotoPreview" src="{{ $centre->director_photo_url }}" alt="Director" class="rounded border p-1" style="height: 70px; width: 70px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/120x120?text=Photo';">
                                </div>
                            @else
                                <div class="mb-2 d-none" id="directorPhotoPreviewBox">
                                    <img id="directorPhotoPreview" src="" alt="Director" class="rounded border p-1" style="height: 70px; width: 70px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="director_photo" class="form-control form-control-sm rounded-2" accept="image/*" onchange="previewImageDirect(this, 'directorPhotoPreview', 'directorPhotoPreviewBox')">
                            <div class="form-text" style="font-size: 0.72rem;">JPG/PNG/WebP, Max 2MB. Instant preview on select.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Director Specimen Signature</label>
                            @if($centre->director_signature_url)
                                <div class="mb-2" id="directorSigPreviewBox">
                                    <img id="directorSigPreview" src="{{ $centre->director_signature_url }}" alt="Signature" class="rounded border p-1 bg-white" style="height: 40px; max-width: 150px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/200x80?text=Signature';">
                                </div>
                            @else
                                <div class="mb-2 d-none" id="directorSigPreviewBox">
                                    <img id="directorSigPreview" src="" alt="Signature" class="rounded border p-1 bg-white" style="height: 40px; max-width: 150px; object-fit: contain;">
                                </div>
                            @endif
                            <input type="file" name="director_signature" class="form-control form-control-sm rounded-2" accept="image/*" onchange="previewImageDirect(this, 'directorSigPreview', 'directorSigPreviewBox')">
                            <div class="form-text" style="font-size: 0.72rem;">Official specimen signature stamp or digital sign.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-2 px-4 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Save Profile Updates
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-0">
            <div class="card-header bg-white py-2 py-md-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-lock text-warning me-2"></i>Change Centre Login Password</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('centre.profile.change-password') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control rounded-2" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-2" minlength="6" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control rounded-2" minlength="6" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning text-dark rounded-2 px-4 fw-bold mt-3 mt-md-4">
                        <i class="fa-solid fa-key me-1"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Affiliation Credentials Summary -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-2 py-md-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-award text-warning me-2"></i>Affiliation Standing</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="text-center mb-3">
                    @if($centre->centre_logo_url)
                        <div class="mb-2">
                            <img src="{{ $centre->centre_logo_url }}" alt="{{ $centre->centre_name }}" class="rounded border p-1 bg-white shadow-sm" style="max-height: 55px; max-width: 140px; object-fit: contain;">
                        </div>
                    @endif
                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 fw-bold rounded-2">
                        <i class="fa-solid fa-circle-check me-1"></i>AUTHORIZED ACTIVE
                    </span>
                </div>

                <ul class="list-unstyled mb-0 small">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Centre Code:</span>
                        <span class="fw-bold font-monospace text-primary">{{ $centre->centre_code }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Affiliation Date:</span>
                        <span class="fw-semibold">{{ $centre->affiliation_date ? $centre->affiliation_date->format('d M, Y') : 'N/A' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Validity Term:</span>
                        <span class="fw-semibold">{{ $centre->valid_upto ? $centre->valid_upto->format('d M, Y') : 'Annual Regular' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Director:</span>
                        <span class="fw-bold text-dark">{{ $centre->director_name }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Prepaid Wallet:</span>
                        <span class="fw-bold text-success font-monospace">₹{{ number_format($centre->wallet?->balance ?? 0, 2) }}</span>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-outline-primary rounded-2 w-100 fw-semibold" target="_blank">
                        <i class="fa-solid fa-certificate me-1"></i>Affiliation Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImageDirect(input, imgId, boxId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            if (img) img.src = e.target.result;
            const box = document.getElementById(boxId);
            if (box) box.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
