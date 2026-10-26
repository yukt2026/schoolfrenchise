@extends('layouts.admin')

@section('title', 'Edit Student - ' . $student->full_name . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" class="text-decoration-none">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Edit Student Dossier</h3>
        <p class="text-muted small mb-0">Modify information for <strong class="text-primary">{{ $student->full_name }}</strong> (Roll: {{ $student->roll_no }})</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-info btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-eye me-1"></i> View Profile
        </a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Roster
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Centre & Enrollment Status -->
        <div class="col-12">
            <div class="card dce-table-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-building-columns me-2"></i>Centre Allocation & Academic Status</h6>
                    <span class="badge bg-secondary font-monospace">{{ $student->roll_no }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Allocated Study Centre <span class="text-danger">*</span></label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror" required>
                                @foreach($centres as $c)
                                    <option value="{{ $c->id }}" {{ old('centre_id', $student->centre_id) == $c->id ? 'selected' : '' }}>
                                        [{{ $c->centre_code }}] {{ $c->centre_name }} - {{ $c->city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Student Academic Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="enrolled" {{ old('status', $student->status) == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                <option value="ongoing" {{ old('status', $student->status) == 'ongoing' ? 'selected' : '' }}>Ongoing Course</option>
                                <option value="completed" {{ old('status', $student->status) == 'completed' ? 'selected' : '' }}>Course Completed</option>
                                <option value="dropped" {{ old('status', $student->status) == 'dropped' ? 'selected' : '' }}>Dropped / Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Registration Number</label>
                            <input type="text" class="form-control font-monospace bg-light" value="{{ $student->registration_no }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="col-lg-7">
            <div class="card dce-table-card h-100">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-user me-2"></i>Personal Particulars</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name', $student->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father's / Guardian's Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" 
                                   value="{{ old('father_name', $student->father_name) }}" required>
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" 
                                   value="{{ old('mother_name', $student->mother_name) }}">
                            @error('mother_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" 
                                   value="{{ old('dob', $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" name="mobile" maxlength="10" class="form-control @error('mobile') is-invalid @enderror" 
                                       value="{{ old('mobile', $student->mobile) }}" required>
                            </div>
                            @error('mobile')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternate Contact</label>
                            <input type="tel" name="alternate_mobile" maxlength="20" class="form-control @error('alternate_mobile') is-invalid @enderror" 
                                   value="{{ old('alternate_mobile', $student->alternate_mobile) }}">
                            @error('alternate_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $student->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Highest Prior Qualification</label>
                            <input type="text" name="highest_qualification" class="form-control @error('highest_qualification') is-invalid @enderror" 
                                   value="{{ old('highest_qualification', $student->highest_qualification) }}">
                            @error('highest_qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address & File Uploads -->
        <div class="col-lg-5">
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-map-location-dot me-2"></i>Residential Address</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror" required>
                                @foreach($states as $s)
                                    <option value="{{ $s->id }}" {{ old('state_id', $student->state_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                                @foreach($districts as $d)
                                    <option value="{{ $d->id }}" {{ old('district_id', $student->district_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                   value="{{ old('city', $student->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" maxlength="6" class="form-control @error('pincode') is-invalid @enderror" 
                                   value="{{ old('pincode', $student->pincode) }}" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-camera me-2"></i>Update Documents</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Replace Passport Photo</label>
                        @if($student->photo_path)
                            <div class="mb-2">
                                <img src="{{ $student->photo_url ?? asset('storage/' . $student->photo_path) }}" alt="Current Photo" class="rounded border" style="height: 60px; width: 60px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/120x120?text=Photo';">
                            </div>
                        @endif
                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Replace Signature</label>
                        @if($student->signature_path)
                            <div class="mb-2">
                                <img src="{{ $student->signature_url ?? asset('storage/' . $student->signature_path) }}" alt="Current Signature" class="rounded border" style="height: 35px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/200x80?text=Sign';">
                            </div>
                        @endif
                        <input type="file" name="signature" accept="image/*" class="form-control @error('signature') is-invalid @enderror">
                    </div>

                    <div>
                        <label class="form-label fw-semibold">Replace ID Proof / KYC Document</label>
                        @if($student->id_proof_doc)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $student->id_proof_doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-paperclip me-1"></i> View Existing Document
                                </a>
                            </div>
                        @endif
                        <input type="file" name="id_proof" accept=".pdf,image/*" class="form-control @error('id_proof') is-invalid @enderror">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Controls -->
        <div class="col-12 text-end">
            <hr class="my-3">
            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                <i class="fa-solid fa-save me-2"></i>Update Student Record
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');

    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
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
});
</script>
@endpush
@endsection
