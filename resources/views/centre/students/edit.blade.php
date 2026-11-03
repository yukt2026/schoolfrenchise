@extends('layouts.centre')

@section('title', 'Edit Student - ' . $student->full_name . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.students.show', $student->id) }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Student Profile
        </a>
        <h3 class="fw-bold text-dark mb-0">Edit Student Profile</h3>
        <p class="text-muted small mb-0">Update profile particulars and login access for <strong class="text-primary">{{ $student->full_name }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary font-monospace px-3 py-2 fs-6 rounded-2">ROLL: {{ $student->roll_no }}</span>
        <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-2 fs-6 rounded-2">REG: {{ $student->registration_no }}</span>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center mb-1">
            <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
            <strong class="fs-6">Please resolve the following issues:</strong>
        </div>
        <ul class="mb-0 small ps-4 mt-2">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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

<form method="POST" action="{{ route('centre.students.update', $student->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Academic Status Strip -->
        <div class="col-12">
            <div class="card border-0 rounded-3 shadow-sm">
                <div class="card-body p-3 bg-light rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-user-graduate fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $student->full_name }}</div>
                            <div class="small text-muted font-monospace">Course: {{ $student->admissions->first()?->course?->course_name ?? 'Computer Course' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <label class="form-label fw-bold mb-0 small text-nowrap">Academic Status <span class="text-danger">*</span>:</label>
                        <select name="status" class="form-select form-select-sm rounded-2 fw-semibold @error('status') is-invalid @enderror" style="width: 180px;" required>
                            <option value="enrolled" {{ old('status', $student->status) === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                            <option value="ongoing" {{ old('status', $student->status) === 'ongoing' ? 'selected' : '' }}>Ongoing Course</option>
                            <option value="completed" {{ old('status', $student->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="dropped" {{ old('status', $student->status) === 'dropped' ? 'selected' : '' }}>Dropped / Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column: Personal Particulars & Portal Login Credentials -->
        <div class="col-lg-7">
            <!-- 1. Personal Info -->
            <div class="card border-0 rounded-3 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-id-card text-primary me-2"></i>Personal Particulars
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control rounded-2 @error('full_name') is-invalid @enderror" value="{{ old('full_name', $student->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control rounded-2 @error('father_name') is-invalid @enderror" value="{{ old('father_name', $student->father_name) }}" required>
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control rounded-2 @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $student->mother_name) }}">
                            @error('mother_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control rounded-2 @error('dob') is-invalid @enderror" value="{{ old('dob', $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select rounded-2 @error('gender') is-invalid @enderror" required>
                                <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Mobile <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-2">+91</span>
                                <input type="tel" name="mobile" maxlength="10" class="form-control rounded-end-2 @error('mobile') is-invalid @enderror" value="{{ old('mobile', $student->mobile) }}" required>
                            </div>
                            @error('mobile')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternate Mobile</label>
                            <input type="tel" name="alternate_mobile" maxlength="20" class="form-control rounded-2 @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile', $student->alternate_mobile) }}">
                            @error('alternate_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Highest Prior Qualification</label>
                            <input type="text" name="highest_qualification" class="form-control rounded-2 @error('highest_qualification') is-invalid @enderror" value="{{ old('highest_qualification', $student->highest_qualification) }}" placeholder="e.g. 10th Standard / 12th / Graduate">
                            @error('highest_qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Class Batch Schedule / Shift</label>
                            <select name="batch_id" class="form-select rounded-2 @error('batch_id') is-invalid @enderror">
                                <option value="">-- No Batch Assigned --</option>
                                @if(isset($batches))
                                    @foreach($batches as $b)
                                        <option value="{{ $b->id }}" {{ (old('batch_id', $student->admissions->first()?->batch_id) == $b->id) ? 'selected' : '' }}>
                                            {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text small">Select batch for daily attendance marking.</div>
                            @error('batch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Student Portal Login Credentials (Email & Password) -->
            <div class="card border-0 rounded-3 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-key text-warning me-2"></i>Student Portal Login Access
                    </h6>
                    <span class="badge bg-info-subtle text-info border rounded-2 small">Self-Service Access</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border small text-muted mb-3 p-2">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                        The student uses their Roll Number, Mobile, or Email to sign in. Update password here if candidate requested a reset.
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Login Email Address</label>
                            <input type="email" name="email" class="form-control rounded-2 @error('email') is-invalid @enderror" value="{{ old('email', $student->email ?? $student->user?->email) }}" placeholder="student@example.com">
                            <div class="form-text small">Used for receiving receipts, notices, and portal sign-in.</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Set New Password</label>
                            <input type="password" name="password" class="form-control rounded-2 @error('password') is-invalid @enderror" minlength="6" placeholder="Leave blank to keep current">
                            <div class="form-text small">Minimum 6 characters.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-2" minlength="6" placeholder="Repeat new password">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Address & Photo/Sign Uploads -->
        <div class="col-lg-5">
            <!-- Residential Address with Dynamic State & District -->
            <div class="card border-0 rounded-3 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-location-dot text-danger me-2"></i>Residential Address
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="stateSelect" class="form-select rounded-2 @error('state_id') is-invalid @enderror" required>
                                <option value="">-- Choose State --</option>
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

                        <div class="col-12">
                            <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="districtSelect" class="form-select rounded-2 @error('district_id') is-invalid @enderror" required>
                                <option value="">-- Select District --</option>
                                @foreach($districts as $dst)
                                    <option value="{{ $dst->id }}" {{ old('district_id', $student->district_id) == $dst->id ? 'selected' : '' }}>
                                        {{ $dst->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control rounded-2 @error('city') is-invalid @enderror" value="{{ old('city', $student->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" maxlength="6" class="form-control rounded-2 @error('pincode') is-invalid @enderror" value="{{ old('pincode', $student->pincode) }}" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Street Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control rounded-2 @error('address') is-invalid @enderror" required>{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploads: Passport Photo, Signature & KYC ID Proof -->
            <div class="card border-0 rounded-3 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-camera text-info me-2"></i>Photo & Documents
                    </h6>
                </div>
                <div class="card-body p-4">
                    <!-- Photo -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Passport Photo</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="studentPhotoPreview" src="{{ $student->photo_url ?? 'https://placehold.co/100x100?text=Photo' }}" alt="Student Photo" class="rounded border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/100x100?text=Photo';">
                            <span class="small text-muted" id="studentPhotoText">{{ $student->photo_url ? 'Current Photo' : 'No photo uploaded' }}</span>
                        </div>
                        <input type="file" name="photo" id="studentPhotoInput" accept="image/*" class="form-control form-control-sm rounded-2 @error('photo') is-invalid @enderror" onchange="previewStudentFile(this, 'studentPhotoPreview', 'studentPhotoText')">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Signature -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Student Signature</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img id="studentSignPreview" src="{{ $student->signature_url ?? 'https://placehold.co/160x60?text=Sign' }}" alt="Student Signature" class="rounded border p-1 bg-white shadow-sm" style="height: 40px; max-width: 130px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/160x60?text=Sign';">
                            <span class="small text-muted" id="studentSignText">{{ $student->signature_url ? 'Current Signature' : 'No signature uploaded' }}</span>
                        </div>
                        <input type="file" name="signature" id="studentSignInput" accept="image/*" class="form-control form-control-sm rounded-2 @error('signature') is-invalid @enderror" onchange="previewStudentFile(this, 'studentSignPreview', 'studentSignText')">
                        @error('signature')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ID Proof -->
                    <div>
                        <label class="form-label fw-semibold">Replace ID Proof / KYC Document</label>
                        @if($student->id_proof_doc_url)
                            <div class="mb-2">
                                <a href="{{ $student->id_proof_doc_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-2">
                                    <i class="fa-solid fa-paperclip me-1"></i>View Current Document
                                </a>
                            </div>
                        @endif
                        <input type="file" name="id_proof" accept=".pdf,image/*" class="form-control form-control-sm rounded-2 @error('id_proof') is-invalid @enderror">
                        @error('id_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="col-12 text-end">
            <hr class="my-3">
            <a href="{{ route('centre.students.show', $student->id) }}" class="btn btn-outline-secondary rounded-2 px-4 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-2 px-5 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-1"></i>Save Profile Changes
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');

    if (stateSelect && districtSelect) {
        stateSelect.addEventListener('change', function() {
            const stateId = this.value;
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';

            if (!stateId) {
                return;
            }

            districtSelect.innerHTML = '<option value="">Loading districts...</option>';

            fetch('/api/districts/' + stateId)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Select District --</option>';
                    data.forEach(d => {
                        options += `<option value="${d.id}">${d.name}</option>`;
                    });
                    districtSelect.innerHTML = options;
                })
                .catch(err => {
                    console.error('Error fetching districts:', err);
                    districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                });
        });
    }
});

function previewStudentFile(input, imgId, textId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const targetImg = document.getElementById(imgId);
            if (targetImg) {
                targetImg.src = e.target.result;
            }
            if (textId) {
                const targetText = document.getElementById(textId);
                if (targetText) {
                    targetText.textContent = 'Selected: ' + input.files[0].name;
                    targetText.className = 'small text-success fw-semibold';
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
