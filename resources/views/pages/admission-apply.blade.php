@extends('layouts.app')

@section('title', 'Online Student Admission Application | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="dce-page-banner">
    <div class="container">
        <h1>Online Student Admission Form</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('enquiry.admission') }}">Admission Enquiry</a></li>
                <li class="breadcrumb-item active" aria-current="page">Apply Online</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">
                            Academic Enrolment {{ date('Y') }}-{{ date('y', strtotime('+1 year')) }}
                        </span>
                        <h2 class="fw-bold text-dark mb-1">Student Admission Application</h2>
                        <p class="text-muted small">Please fill out this official application accurately. All documents will be verified before roll number assignment.</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</strong>
                            <ul class="mb-0 small ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admission.apply.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- 1. Centre & Course Selection -->
                        <div class="p-3 rounded-3 mb-4" style="background-color: #eff6ff; border-left: 4px solid #2563eb;">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-graduation-cap me-2"></i>1. Study Centre & Course Selection</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Target Study Centre <span class="text-danger">*</span></label>
                                    <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror" required>
                                        <option value="">-- Select Nearest Study Centre --</option>
                                        @foreach($centres as $cnt)
                                            <option value="{{ $cnt->id }}" {{ old('centre_id') == $cnt->id ? 'selected' : '' }}>
                                                [{{ $cnt->centre_code }}] {{ $cnt->centre_name }} ({{ $cnt->city ?? $cnt->district?->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('centre_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Desired Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                        <option value="">-- Select Course --</option>
                                        @foreach($courses as $crs)
                                            <option value="{{ $crs->id }}" {{ old('course_id') == $crs->id ? 'selected' : '' }}>
                                                {{ $crs->course_name }} ({{ $crs->course_code }} - {{ $crs->duration }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 2. Personal Particulars -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-user me-2 text-primary"></i>2. Candidate Personal Particulars
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Name (As per 10th Certificate) <span class="text-danger">*</span></label>
                                <input type="text" name="applicant_name" class="form-control @error('applicant_name') is-invalid @enderror" 
                                       placeholder="Enter your full name" value="{{ old('applicant_name') }}" required>
                                @error('applicant_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Father's / Guardian's Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" 
                                       placeholder="Enter father's name" value="{{ old('father_name') }}" required>
                                @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Mother's Name</label>
                                <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" 
                                       placeholder="Enter mother's name" value="{{ old('mother_name') }}">
                                @error('mother_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" 
                                       value="{{ old('dob') }}" required>
                                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                                <input type="tel" name="mobile" class="form-control @error('mobile') is-invalid @enderror" 
                                       placeholder="10-digit mobile number" value="{{ old('mobile') }}" maxlength="15" required>
                                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="name@example.com" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Highest Educational Qualification</label>
                                <input type="text" name="highest_qualification" class="form-control @error('highest_qualification') is-invalid @enderror" 
                                       placeholder="e.g. 10th Pass, 12th Pass, BCA, B.Sc" value="{{ old('highest_qualification') }}">
                                @error('highest_qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- 3. Address Details -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-map-location-dot me-2 text-primary"></i>3. Residential / Postal Address
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Permanent Address <span class="text-danger">*</span></label>
                                <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" 
                                          placeholder="House / Street / Village, Post Office" required>{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">State <span class="text-danger">*</span></label>
                                <select name="state_id" id="stateSelect" class="form-select dce-state-select @error('state_id') is-invalid @enderror" data-district-target="districtSelect" required>
                                    <option value="">Select State</option>
                                    @foreach($states as $st)
                                        <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">District <span class="text-danger">*</span></label>
                                <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                                    <option value="">Select District</option>
                                </select>
                                @error('district_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">City / Tehsil <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                       placeholder="City" value="{{ old('city') }}" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">PIN Code <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" 
                                       placeholder="6-digit PIN" value="{{ old('pincode') }}" maxlength="10" required>
                                @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- 4. Document Uploads -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-file-arrow-up me-2 text-primary"></i>4. Document Verification Uploads
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-light">
                                    <label class="form-label small fw-semibold">Passport Sized Photograph</label>
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text small">JPEG/PNG, Max 2MB. Clear frontal headshot.</div>
                                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-light">
                                    <label class="form-label small fw-semibold">Identity Proof / Educational Marksheet</label>
                                    <input type="file" name="id_proof" class="form-control @error('id_proof') is-invalid @enderror" accept="application/pdf,image/jpeg,image/png,image/webp">
                                    <div class="form-text small">PDF/JPEG/PNG, Max 5MB. Aadhaar, Voter ID, or 10th marksheet.</div>
                                    @error('id_proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Declaration -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="declarationCheck" required>
                            <label class="form-check-label small text-muted" for="declarationCheck">
                                I hereby declare that the particulars provided in this admission form are true, complete and authentic. I agree to abide by all academic standards, fee schedules and disciplinary regulations of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}.
                            </label>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-dce-accent px-5 py-3 fw-bold fs-6 shadow">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Official Application
                            </button>
                            <div class="mt-3 small text-muted">
                                Already submitted? <a href="{{ route('admission.status') }}" class="fw-semibold text-primary">Track your admission status here</a>.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const oldDistrictId = "{{ old('district_id') }}";

    function loadDistricts(stateId, selectedDistrict = '') {
        if (!districtSelect) return;
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">Select District</option>';
            return;
        }

        districtSelect.innerHTML = '<option value="">Loading districts...</option>';

        fetch(`/api/districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Select District</option>';
                if (data && data.length > 0) {
                    data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.name;
                        if (selectedDistrict && selectedDistrict == d.id) {
                            opt.selected = true;
                        }
                        districtSelect.appendChild(opt);
                    });
                } else {
                    districtSelect.innerHTML = '<option value="">No districts found</option>';
                }
            })
            .catch(() => {
                districtSelect.innerHTML = '<option value="">Failed to load districts</option>';
            });
    }

    if (stateSelect) {
        stateSelect.addEventListener('change', function() {
            loadDistricts(this.value);
        });

        if (stateSelect.value) {
            loadDistricts(stateSelect.value, oldDistrictId);
        }
    }
});
</script>
@endpush
