@extends('layouts.admin')

@section('title', 'Register New Centre / Franchise')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.index') }}">Centres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">New Centre</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i>Register New Affiliated Centre
            </h2>
            <p class="text-muted small mb-0">Enroll a direct authorized learning centre into the national {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} network.</p>
        </div>
        <div>
            <a href="{{ route('admin.centres.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Roster
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.centres.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Section 1: Centre Identity & Affiliation -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-building-columns text-primary me-2"></i>1. Institute Identity & Accreditation
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Centre Code <span class="text-danger">*</span></label>
                        <input type="text" name="centre_code" class="form-control font-monospace @error('centre_code') is-invalid @enderror" value="{{ old('centre_code', $suggestedCode) }}" required placeholder="e.g. DCE-26-001">
                        <div class="form-text small">Unique institutional franchise code.</div>
                        @error('centre_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Centre / Institute Name <span class="text-danger">*</span></label>
                        <input type="text" name="centre_name" class="form-control @error('centre_name') is-invalid @enderror" value="{{ old('centre_name') }}" required placeholder="e.g. Tech Era Skill Centre - Branch Name">
                        @error('centre_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Affiliation Date <span class="text-danger">*</span></label>
                        <input type="date" name="affiliation_date" class="form-control @error('affiliation_date') is-invalid @enderror" value="{{ old('affiliation_date', now()->toDateString()) }}" required>
                        @error('affiliation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Valid Upto</label>
                        <input type="date" name="valid_upto" class="form-control @error('valid_upto') is-invalid @enderror" value="{{ old('valid_upto', now()->addYear()->toDateString()) }}">
                        @error('valid_upto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Operational)</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Director & Portal Login Credentials -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user-tie text-primary me-2"></i>2. Centre Director & Login Credentials
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Centre Director / Head Name <span class="text-danger">*</span></label>
                        <input type="text" name="director_name" class="form-control @error('director_name') is-invalid @enderror" value="{{ old('director_name') }}" required placeholder="e.g. Rajesh Kumar Sharma">
                        @error('director_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Official Email Address (Login ID) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. branch@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Portal Password (Optional)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Default: Director's Mobile No">
                        <div class="form-text small">Leave blank to use mobile number as default password.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Primary Mobile No <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required placeholder="10-digit mobile number">
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Alternate Mobile / Helpline</label>
                        <input type="text" name="alternate_mobile" class="form-control @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile') }}" placeholder="e.g. 011-2756XXXX">
                        @error('alternate_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Lab Capacity (Workstations)</label>
                        <input type="number" name="lab_capacity" class="form-control @error('lab_capacity') is-invalid @enderror" value="{{ old('lab_capacity', 15) }}" min="0">
                        @error('lab_capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Location & Postal Address -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-location-dot text-primary me-2"></i>3. Location & Postal Address
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">State <span class="text-danger">*</span></label>
                        <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror" required>
                            <option value="">Select State</option>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">District <span class="text-danger">*</span></label>
                        <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                            <option value="">Select State First</option>
                        </select>
                        @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">City / Town <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required placeholder="e.g. Rohini, Sector 7">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-9">
                        <label class="form-label fw-semibold small">Complete Physical Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required placeholder="Plot no, building name, landmark, road...">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">PIN Code <span class="text-danger">*</span></label>
                        <input type="text" name="pincode" class="form-control font-monospace @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}" required placeholder="e.g. 110085">
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Branding & Images -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-camera text-primary me-2"></i>4. Centre Logo & Photos
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Centre Logo / Signboard</label>
                        <div class="mb-2 text-center p-2 bg-light rounded border" style="min-height: 85px; display: flex; align-items: center; justify-content: center;">
                            <img id="newCentreLogoPreview" src="https://placehold.co/150x80?text=Logo+Preview" alt="Logo Preview" class="img-fluid rounded" style="max-height: 70px; object-fit: contain;">
                        </div>
                        <input type="file" name="centre_logo" class="form-control" accept="image/*" onchange="previewUpload(this, 'newCentreLogoPreview')">
                        <div class="form-text small">JPEG/PNG/WEBP up to 2MB.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Director Passport Photo</label>
                        <div class="mb-2 text-center p-2 bg-light rounded border" style="min-height: 85px; display: flex; align-items: center; justify-content: center;">
                            <img id="newDirectorPhotoPreview" src="https://placehold.co/80x80?text=Photo" alt="Photo Preview" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                        </div>
                        <input type="file" name="director_photo" class="form-control" accept="image/*" onchange="previewUpload(this, 'newDirectorPhotoPreview')">
                        <div class="form-text small">JPEG/PNG/WEBP up to 2MB.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Director Signature</label>
                        <div class="mb-2 text-center p-2 bg-light rounded border" style="min-height: 85px; display: flex; align-items: center; justify-content: center;">
                            <img id="newDirectorSignPreview" src="https://placehold.co/160x50?text=Sign+Preview" alt="Sign Preview" class="img-fluid" style="max-height: 50px; object-fit: contain;">
                        </div>
                        <input type="file" name="director_signature" class="form-control" accept="image/*" onchange="previewUpload(this, 'newDirectorSignPreview')">
                        <div class="form-text small">For certificates & documents.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Controls -->
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.centres.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                <i class="fa-solid fa-check me-1"></i> Register Centre
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');

    function loadDistricts(stateId, selectedDistrictId = null) {
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">Select State First</option>';
            return;
        }

        districtSelect.innerHTML = '<option value="">Loading districts...</option>';

        fetch(`/api/districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select District</option>';
                data.forEach(d => {
                    const isSelected = selectedDistrictId == d.id ? 'selected' : '';
                    options += `<option value="${d.id}" ${isSelected}>${d.name}</option>`;
                });
                districtSelect.innerHTML = options;
            })
            .catch(() => {
                districtSelect.innerHTML = '<option value="">Failed to load districts</option>';
            });
    }

    stateSelect.addEventListener('change', function() {
        loadDistricts(this.value);
    });

    // If preselected on validation failure
    if (stateSelect.value) {
        loadDistricts(stateSelect.value, "{{ old('district_id') }}");
    }
});

function previewUpload(input, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const el = document.getElementById(imgId);
            if (el) el.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
