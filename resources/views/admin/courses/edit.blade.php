@extends('layouts.admin')

@section('title', 'Edit Course - ' . $course->course_name . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $course->course_code }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Edit Course Curriculum</h3>
        <p class="text-muted small mb-0">Modify course particulars, duration, pricing schedule and syllabus modules.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-outline-info btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-eye me-1"></i> View Prospectus
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <i class="fa-solid fa-book-bookmark fs-4 text-primary me-3"></i>
        <div>
            <div class="fw-bold text-dark">Course Curriculum Subjects & Marking Scheme</div>
            <div class="small text-muted">This course currently has <strong>{{ $course->subjects->count() }}</strong> configured subjects (Total Marks: {{ $course->subjects->sum('total_max') }}).</div>
        </div>
    </div>
    <a href="{{ route('admin.courses.show', $course->id) }}#subjects-section" class="btn btn-sm btn-primary shadow-sm text-nowrap">
        <i class="fa-solid fa-list-check me-1"></i> Manage Subjects & Marks
    </a>
</div>

<form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Course Details -->
        <div class="col-lg-8">
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-book me-2"></i>Course Details</h6>
                    <span class="badge bg-secondary font-monospace">{{ $course->course_code }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" name="course_code" class="form-control font-monospace text-uppercase @error('course_code') is-invalid @enderror" 
                                   value="{{ old('course_code', $course->course_code) }}" required>
                            @error('course_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" 
                                   value="{{ old('course_name', $course->course_name) }}" required>
                            @error('course_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- General Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duration <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                                   value="{{ old('duration', $course->duration) }}" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Minimum Eligibility <span class="text-danger">*</span></label>
                            <input type="text" name="eligibility" class="form-control @error('eligibility') is-invalid @enderror" 
                                   value="{{ old('eligibility', $course->eligibility) }}" required>
                            @error('eligibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Course Syllabus & Curriculum Modules</label>
                            <textarea name="syllabus" rows="8" class="form-control @error('syllabus') is-invalid @enderror">{{ old('syllabus', $course->syllabus) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing, Thumbnail & Settings -->
        <div class="col-lg-4">
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-indian-rupee-sign me-2"></i>Fee Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tuition / Base Fee (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="default_course_fee" id="baseFee" 
                               class="form-control @error('default_course_fee') is-invalid @enderror" 
                               value="{{ old('default_course_fee', $course->default_course_fee) }}" required>
                        @error('default_course_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registration Fee (₹)</label>
                        <input type="number" step="0.01" name="registration_fee" id="regFee" 
                               class="form-control" value="{{ old('registration_fee', $course->registration_fee) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Examination Fee (₹)</label>
                        <input type="number" step="0.01" name="exam_fee" id="examFee" 
                               class="form-control" value="{{ old('exam_fee', $course->exam_fee) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Certificate Fee (₹)</label>
                        <input type="number" step="0.01" name="certificate_fee" id="certFee" 
                               class="form-control" value="{{ old('certificate_fee', $course->certificate_fee) }}">
                    </div>

                    <div class="p-3 bg-light rounded border text-center">
                        <div class="text-muted small fw-semibold">Calculated Total Fee</div>
                        <div class="h4 fw-bold text-success mb-0" id="totalFeeDisplay">₹0.00</div>
                    </div>
                </div>
            </div>

            <!-- Media & Status Settings -->
            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sliders me-2"></i>Publishing & Media</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course Thumbnail Image</label>
                        @if($course->thumbnail)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail" class="rounded border" style="height: 60px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="thumbnail" accept="image/*" class="form-control @error('thumbnail') is-invalid @enderror">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_popular" id="isPopularCheck" value="1" {{ old('is_popular', $course->is_popular) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isPopularCheck">
                            Feature as Popular Course
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="statusCheck" value="1" {{ old('status', $course->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="statusCheck">
                            Active & Published
                        </label>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Update Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseFee = document.getElementById('baseFee');
    const regFee = document.getElementById('regFee');
    const examFee = document.getElementById('examFee');
    const certFee = document.getElementById('certFee');
    const totalDisplay = document.getElementById('totalFeeDisplay');

    function calculateTotal() {
        const b = parseFloat(baseFee.value) || 0;
        const r = parseFloat(regFee.value) || 0;
        const e = parseFloat(examFee.value) || 0;
        const c = parseFloat(certFee.value) || 0;
        const total = b + r + e + c;
        totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    [baseFee, regFee, examFee, certFee].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    calculateTotal();
});
</script>
@endpush
@endsection
