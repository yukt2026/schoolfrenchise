@extends('layouts.admin')

@section('title', 'Direct Admission - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}" class="text-decoration-none">Admissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Direct Admission</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Direct Course Admission</h3>
        <p class="text-muted small mb-0">Enroll a registered student into a course, assign fee schedule and allocate training batch.</p>
    </div>
    <div>
        <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Admissions
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.admissions.store') }}">
    @csrf

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-file-signature me-2"></i>Admission Particulars</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Choose Registered Student --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}" data-centre-id="{{ $st->centre_id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                        [{{ $st->roll_no }}] {{ $st->full_name }} ({{ $st->mobile }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Student must be registered first in <a href="{{ route('admin.students.create') }}">Students directory</a>.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Study Centre <span class="text-danger">*</span></label>
                            <select name="centre_id" id="centreSelect" class="form-select @error('centre_id') is-invalid @enderror" required>
                                <option value="">-- Select Study Centre --</option>
                                @foreach($centres as $c)
                                    <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
                                        [{{ $c->centre_code }}] {{ $c->centre_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Course <span class="text-danger">*</span></label>
                            <select name="course_id" id="courseSelect" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">-- Select Course ({{ $courses->count() }} Available) --</option>
                                @foreach($courses as $crs)
                                    <option value="{{ $crs->id }}" data-fee="{{ $crs->course_fee ?: $crs->total_fee }}" {{ old('course_id') == $crs->id ? 'selected' : '' }}>
                                        [{{ $crs->course_code }}] {{ $crs->course_name }} ({{ $crs->duration }}) - ₹{{ number_format($crs->course_fee ?: $crs->total_fee) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Training Batch (Optional)</label>
                            <select name="batch_id" class="form-select">
                                <option value="">-- Assign Later --</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control @error('admission_date') is-invalid @enderror" 
                                   value="{{ old('admission_date', date('Y-m-d')) }}" required>
                            @error('admission_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="course_fee" id="courseFeeInput" class="form-control @error('course_fee') is-invalid @enderror" 
                                   value="{{ old('course_fee', 0) }}" required>
                            @error('course_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Discount / Concession (₹)</label>
                            <input type="number" step="0.01" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" 
                                   value="{{ old('discount_amount', 0) }}">
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Administrative Remarks / Notes</label>
                            <textarea name="remarks" rows="2" class="form-control" placeholder="Optional internal admission notes...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="fa-solid fa-check me-2"></i>Complete Admission
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentSelect');
    const centreSelect = document.getElementById('centreSelect');
    const courseSelect = document.getElementById('courseSelect');
    const courseFeeInput = document.getElementById('courseFeeInput');

    if (studentSelect && centreSelect) {
        studentSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const centreId = selectedOpt.getAttribute('data-centre-id');
            if (centreId && (!centreSelect.value || centreSelect.value == '')) {
                centreSelect.value = centreId;
            }
        });
    }

    if (courseSelect && courseFeeInput) {
        function updateCourseFee() {
            const selectedOption = courseSelect.options[courseSelect.selectedIndex];
            if (selectedOption) {
                const fee = selectedOption.getAttribute('data-fee');
                if (fee && (!courseFeeInput.value || parseFloat(courseFeeInput.value) <= 0)) {
                    courseFeeInput.value = fee;
                }
            }
        }

        courseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const fee = selectedOption ? selectedOption.getAttribute('data-fee') : null;
            if (fee) {
                courseFeeInput.value = fee;
            }
        });

        // Initial check if preselected
        if (courseSelect.value) {
            updateCourseFee();
        }
    }
});
</script>
@endpush
@endsection
