@extends('layouts.centre')

@section('title', 'Direct Student Admission Registration - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.students.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Directory
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-user-plus text-primary me-2"></i>New Candidate Admission Registration
    </h3>
    <p class="text-muted small mb-0">Direct admission enrollment for Study Centre {{ $centre->centre_name }} ({{ $centre->centre_code }}). Roll Number and Registration Number will be generated automatically.</p>
</div>

<form method="POST" action="{{ route('centre.students.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Personal & Academic Details -->
        <div class="col-lg-8">
            <!-- 1. Personal Identity Details -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user text-primary me-2"></i>1. Candidate Personal Details</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Candidate Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="As per matriculation certificate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="Father's full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="Mother's full name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span> <small class="text-muted">(Default Password)</small></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="text" name="mobile" class="form-control" maxlength="10" value="{{ old('mobile') }}" placeholder="10-digit mobile" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <small class="text-muted">(Optional)</small></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="candidate@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternate Mobile</label>
                            <input type="text" name="alternate_mobile" class="form-control" value="{{ old('alternate_mobile') }}" placeholder="Parent/Guardian contact">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Highest Educational Qualification</label>
                            <input type="text" name="highest_qualification" class="form-control" value="{{ old('highest_qualification') }}" placeholder="e.g. 10th, 12th, Graduate">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Address & Geography -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-location-dot text-danger me-2"></i>2. Residential Address</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="state_select" class="form-select" required>
                                <option value="">Select State</option>
                                @foreach($states as $st)
                                <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="district_select" class="form-select" required>
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="City" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Street Address / House No <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Full address" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Postal Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" class="form-control" maxlength="10" value="{{ old('pincode') }}" placeholder="6-digit pincode" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Course Enrollment & Batch -->
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-graduation-cap text-success me-2"></i>3. Course Enrollment & Batch Allocation</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Enrolled Course <span class="text-danger">*</span></label>
                            <select name="course_id" id="course_select" class="form-select" required>
                                <option value="">Select Computer Course</option>
                                @foreach($courses as $c)
                                <option value="{{ $c->id }}" data-fee="{{ $c->default_course_fee }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->course_code }} - {{ $c->course_name }} ({{ $c->duration_months }} Months)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Centre Batch / Lab Shift</label>
                            <select name="batch_id" class="form-select">
                                <option value="">Assign Batch Later / Open Shift</option>
                                @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Standard Course Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="course_fee" id="course_fee_input" class="form-control" value="{{ old('course_fee', 0) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Approved Concession / Discount (₹)</label>
                            <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', 0) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Documents Upload & Submission -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-paperclip text-info me-2"></i>4. Mandatory Documents</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Candidate Passport Photo</label>
                        <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text" style="font-size: 0.72rem;">JPG, PNG up to 2MB. Clear frontal view.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Candidate Signature</label>
                        <input type="file" name="signature" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text" style="font-size: 0.72rem;">Signature on plain white paper.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">ID Proof / Aadhaar Copy</label>
                        <input type="file" name="id_proof" class="form-control form-control-sm" accept=".pdf,image/*">
                        <div class="form-text" style="font-size: 0.72rem;">PDF or image up to 5MB.</div>
                    </div>
                </div>
            </div>

            <!-- Centre Affiliation Badge & Submission -->
            <div class="card border-0 rounded-4 shadow-sm bg-light">
                <div class="card-body p-4">
                    <div class="small text-muted mb-2">Admitting Study Centre:</div>
                    <div class="fw-bold text-dark">{{ $centre->centre_name }}</div>
                    <div class="font-monospace text-primary small mb-3">CODE: {{ $centre->centre_code }}</div>

                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                        <i class="fa-solid fa-shield-halved me-1"></i>
                        The student login credentials will be generated immediately. Default password is the mobile number.
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-check-circle me-1"></i>Confirm & Register Candidate
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stateSelect = document.getElementById('state_select');
        const districtSelect = document.getElementById('district_select');
        const courseSelect = document.getElementById('course_select');
        const courseFeeInput = document.getElementById('course_fee_input');
        const oldDistrictId = "{{ old('district_id') }}";

        function loadDistricts(stateId, selectedDistrictId = '') {
            districtSelect.innerHTML = '<option value="">Loading districts...</option>';
            if (!stateId) {
                districtSelect.innerHTML = '<option value="">Select District</option>';
                return;
            }

            fetch('/api/districts/' + stateId)
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="">Select District</option>';
                    data.forEach(d => {
                        const isSel = (selectedDistrictId && selectedDistrictId == d.id) ? 'selected' : '';
                        html += `<option value="${d.id}" ${isSel}>${d.name}</option>`;
                    });
                    districtSelect.innerHTML = html;
                })
                .catch(() => {
                    districtSelect.innerHTML = '<option value="">Failed to load districts</option>';
                });
        }

        // State change -> load districts
        stateSelect.addEventListener('change', function () {
            loadDistricts(this.value);
        });

        // Trigger on page load if state is already selected (e.g. on validation error)
        if (stateSelect.value) {
            loadDistricts(stateSelect.value, oldDistrictId);
        }

        // Course change -> autofill fee
        courseSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const defaultFee = selected.getAttribute('data-fee');
            if (defaultFee && parseFloat(defaultFee) > 0) {
                courseFeeInput.value = defaultFee;
            }
        });
    });
</script>
@endpush
@endsection
