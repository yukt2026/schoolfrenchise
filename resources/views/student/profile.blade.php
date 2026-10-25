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

    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Photo & Official Enrolment Badges -->
            <div class="col-12 col-lg-4">
                <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 text-center mb-4">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div class="rounded-circle bg-light border border-3 border-primary shadow-sm overflow-hidden d-flex align-items-center justify-content-center mx-auto" style="width: 130px; height: 130px;">
                            @if($student->photo_path && file_exists(public_path('storage/' . $student->photo_path)))
                                <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover">
                            @else
                                <i class="fa-solid fa-user fa-4x text-secondary opacity-50"></i>
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

                    <!-- Upload New Avatar -->
                    <div class="text-start mt-2">
                        <label class="form-label small fw-semibold text-muted">Update Passport Photo</label>
                        <input type="file" name="photo" class="form-control form-control-sm @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text" style="font-size: 0.72rem;">JPG/PNG, Max 2MB. Square headshot recommended.</div>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <i class="fa-solid fa-address-card text-primary me-2"></i> Personal Information
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Full Name (Official Record)</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->full_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Father's Name</label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $student->father_name) }}">
                            @error('father_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $student->mother_name) }}">
                            @error('mother_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone) }}" maxlength="10">
                            </div>
                            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Permanent / Postal Address</label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $student->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">State</label>
                            <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $student->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">District</label>
                            <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror">
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id', $student->district_id) == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('district_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">PIN Code</label>
                            <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $student->pincode) }}" maxlength="6">
                            @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-2">
                        <button type="submit" class="btn btn-primary rounded-2 px-4 fw-semibold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
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
