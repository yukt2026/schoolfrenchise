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

    <!-- Official Dossier Advisory Banner -->
    <div class="alert alert-info border-0 rounded-3 shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; min-width: 42px;">
            <i class="fa-solid fa-user-lock fs-5"></i>
        </div>
        <div>
            <div class="fw-bold text-dark mb-0">Official Student Dossier (Managed by Study Centre)</div>
            <div class="small text-muted">To maintain academic data integrity, your profile particulars and credentials are maintained by your Authorized Study Centre (<strong>{{ $student->centre?->centre_name ?? 'Study Centre' }}</strong>). If any corrections or updates are required, please contact your franchise centre administrator.</div>
        </div>
    </div>

    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Photo & Official Enrolment Badges -->
            <div class="col-12 col-lg-4">
                <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 text-center mb-4">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div class="rounded-circle bg-light border border-3 border-primary shadow-sm overflow-hidden d-flex align-items-center justify-content-center mx-auto" style="width: 130px; height: 130px;">
                            @if($student->photo_url)
                                <img id="studentAvatarPreview" src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.classList.add('d-none');document.getElementById('studentAvatarPlaceholder').classList.remove('d-none');">
                                <i id="studentAvatarPlaceholder" class="fa-solid fa-user fa-4x text-secondary opacity-50 d-none"></i>
                            @else
                                <img id="studentAvatarPreview" src="" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover d-none">
                                <i id="studentAvatarPlaceholder" class="fa-solid fa-user fa-4x text-secondary opacity-50"></i>
                            @endif
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $student->full_name }}</h5>
                    <div class="text-muted small mb-2">{{ $student->email }}</div>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 rounded-1">
                            ROLL: {{ $student->roll_no ?? 'Pending' }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-1 rounded-1">
                            REG: {{ $student->registration_no }}
                        </span>
                    </div>

                    <div class="p-2 bg-light rounded-2 text-muted small mt-2">
                        <i class="fa-solid fa-shield me-1 text-primary"></i> Photo & Profile verified by Centre
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
                        <i class="fa-solid fa-address-card text-primary me-2"></i> Personal Particulars
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Full Name (Official Record)</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->full_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Father's Name</label>
                            <input type="text" name="father_name" class="form-control bg-light" value="{{ old('father_name', $student->father_name) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control bg-light" value="{{ old('mother_name', $student->mother_name) }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Date of Birth</label>
                            <input type="text" name="date_of_birth" class="form-control bg-light" value="{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : ($student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : 'N/A') }}" readonly>
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
                            <label class="form-label small fw-semibold text-muted">Primary Phone / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone', $student->phone ?? $student->mobile) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $student->email) }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Permanent / Postal Address</label>
                            <textarea name="address" rows="2" class="form-control bg-light" readonly>{{ old('address', $student->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">State</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->state?->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">District</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->district?->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">PIN Code</label>
                            <input type="text" name="pincode" class="form-control bg-light" value="{{ old('pincode', $student->pincode) }}" readonly>
                        </div>
                    </div>

                    <div class="alert alert-light border rounded-3 mt-4 mb-0 d-flex align-items-center justify-content-between p-3">
                        <div class="small text-muted">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i> Need to update your address, mobile number or particulars?
                        </div>
                        <span class="badge bg-secondary-subtle text-dark border px-3 py-2">
                            <i class="fa-solid fa-hand-holding-hand me-1 text-primary"></i> Contact Your Study Centre Coordinator
                        </span>
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
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('studentAvatarPreview');
            const placeholder = document.getElementById('studentAvatarPlaceholder');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('stateSelect')?.addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('districtSelect');
    if (!stateId) return;

    fetch(`/api/districts/${stateId}`)
        .then(res => res.json())
        .then(data => {
            districtSelect.innerHTML = '<option value="">Select District</option>';
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.name;
                districtSelect.appendChild(opt);
            });
        });
});
</script>
@endpush
