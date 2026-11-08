@extends('layouts.student')

@section('title', 'My Profile - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-user-graduate text-primary me-2"></i> Student Profile
            </h4>
            <div class="text-muted small">Manage your personal records, contact details and identity information</div>
        </div>
        <div>
            <a href="{{ route('student.id-card') }}" class="btn btn-outline-primary btn-sm rounded-2 px-3 fw-semibold">
                <i class="fa-solid fa-id-card me-1"></i> View ID Card
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
                <strong class="fs-6">Please correct the following:</strong>
            </div>
            <ul class="mb-0 small ps-4 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Photo & Official Enrolment Badges -->
            <div class="col-12 col-lg-4">
                <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 text-center mb-4">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div class="rounded-circle bg-light border border-3 border-primary shadow-sm overflow-hidden d-flex align-items-center justify-content-center mx-auto" style="width: 140px; height: 140px;">
                            @if($student->photo_url)
                                <img id="studentAvatarPreview" src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.classList.add('d-none');document.getElementById('studentAvatarPlaceholder').classList.remove('d-none');">
                                <i id="studentAvatarPlaceholder" class="fa-solid fa-user fa-4x text-secondary opacity-50 d-none"></i>
                            @else
                                <img id="studentAvatarPreview" src="" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover d-none">
                                <i id="studentAvatarPlaceholder" class="fa-solid fa-user fa-4x text-secondary opacity-50"></i>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                            <i class="fa-solid fa-lock me-1"></i> Profile Photo Locked
                        </span>
                        <div class="text-muted small mt-2" style="font-size: 0.72rem;">
                            Profile photo is locked for official identity verification. To change your photo, please contact your study centre.
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $student->full_name }}</h5>
                    <div class="text-muted small mb-2">{{ $student->email ?? 'No email on record' }}</div>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 rounded-1">
                            ROLL: {{ $student->roll_no ?? 'Pending' }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-1 rounded-1">
                            REG: {{ $student->registration_no }}
                        </span>
                    </div>

                    <div class="p-2 bg-light rounded-2 text-muted small mt-2">
                        <i class="fa-solid fa-shield-halved me-1 text-primary"></i> Affiliated with {{ $student->centre?->centre_name ?? 'TECH ERA COMPUTER' }}
                    </div>
                </div>

                <!-- Academic Affiliation Card -->
                <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-building-columns text-primary me-2"></i>Institutional Record</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="py-2 border-bottom border-light d-flex justify-content-between">
                            <span class="text-muted">Centre / Branch:</span>
                            <span class="fw-semibold text-dark">{{ $student->centre?->centre_name ?? 'Head Office' }}</span>
                        </li>
                        <li class="py-2 border-bottom border-light d-flex justify-content-between">
                            <span class="text-muted">Centre Code:</span>
                            <span class="font-monospace text-primary fw-bold">{{ $student->centre?->centre_code ?? 'MAIN' }}</span>
                        </li>
                        <li class="py-2 border-bottom border-light d-flex justify-content-between">
                            <span class="text-muted">Current Course:</span>
                            <span class="fw-semibold text-dark">{{ $admission->course?->course_name ?? 'N/A' }}</span>
                        </li>
                        <li class="py-2 border-bottom border-light d-flex justify-content-between">
                            <span class="text-muted">Batch:</span>
                            <span class="fw-semibold text-dark">{{ $admission->batch?->batch_name ?? 'Standard' }}</span>
                        </li>
                        <li class="py-2 d-flex justify-content-between">
                            <span class="text-muted">Admission Date:</span>
                            <span class="fw-semibold text-dark">{{ $admission?->admission_date ? $admission->admission_date->format('d M, Y') : 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Column: Personal & Contact Information -->
            <div class="col-12 col-lg-8">
                <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-address-card text-primary me-2"></i> Certified Personal Particulars
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Full Legal Name</label>
                            <input type="text" class="form-control bg-light font-monospace" value="{{ $student->full_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Father's Name</label>
                            <input type="text" class="form-control bg-light font-monospace" value="{{ $student->father_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Mother's Name</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->mother_name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Date of Birth</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Gender</label>
                            <input type="text" class="form-control bg-light text-uppercase" value="{{ $student->gender ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-phone text-primary me-2"></i> Contact & Residential Details
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Primary Mobile / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" name="mobile" maxlength="10" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $student->mobile) }}" required>
                            </div>
                            @error('mobile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Alternate Mobile</label>
                            <input type="tel" name="alternate_mobile" maxlength="20" class="form-control @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile', $student->alternate_mobile) }}">
                            @error('alternate_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="{{ $student->email ?? 'N/A' }}" readonly>
                            <div class="form-text small">Email login credential managed via study centre.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $student->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Permanent / Postal Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror" required>
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}" {{ old('state_id', $student->state_id) == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                                @foreach($districts as $dt)
                                    <option value="{{ $dt->id }}" {{ old('district_id', $student->district_id) == $dt->id ? 'selected' : '' }}>
                                        {{ $dt->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" maxlength="6" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $student->pincode) }}" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="small text-muted">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i> Updates will sync with your digital ID card & records.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-2 shadow-sm">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Save Profile Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewStudentPhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('studentAvatarPreview');
            const placeholder = document.getElementById('studentAvatarPlaceholder');
            const helpText = document.getElementById('photoHelpText');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
            if (helpText) {
                helpText.textContent = 'Selected: ' + file.name;
                helpText.className = 'small text-success fw-bold mt-1';
            }
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');

    if (stateSelect && districtSelect) {
        stateSelect.addEventListener('change', function() {
            const stateId = this.value;
            if (!stateId) return;

            districtSelect.innerHTML = '<option value="">Loading districts...</option>';

            fetch('/api/districts/' + stateId)
                .then(res => res.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">Select District</option>';
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.name;
                        districtSelect.appendChild(opt);
                    });
                })
                .catch(err => {
                    console.error('Failed to load districts:', err);
                    districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                });
        });
    }
});
</script>
@endpush
