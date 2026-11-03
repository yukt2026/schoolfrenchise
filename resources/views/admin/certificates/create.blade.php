@extends('layouts.admin')

@section('title', 'Issue Certificate & Marksheet')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-stamp me-2" style="color: #0891b2;"></i>Issue Official Certificate & Marksheet
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Issue Certificate</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-file-shield me-2 text-primary"></i>Issuance Credentials Particulars
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.certificates.store') }}" id="certificateIssueForm">
                        @csrf

                        <!-- Hidden fields bound dynamically via JavaScript -->
                        <input type="hidden" name="exam_result_id" id="hidden_exam_result_id" value="{{ old('exam_result_id', $selectedResult?->id) }}">
                        <input type="hidden" name="student_id" id="hidden_student_id" value="{{ old('student_id', $selectedStudent?->id) }}">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="candidate_select" class="form-label fw-semibold">
                                    Select Candidate <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('exam_result_id') is-invalid @enderror @error('student_id') is-invalid @enderror" id="candidate_select" required>
                                    <option value="">-- Choose Candidate to Issue Certificate --</option>
                                    
                                    @if($eligibleResults->count() > 0)
                                        <optgroup label="Candidates with Passed Exam Results ({{ $eligibleResults->count() }})">
                                            @foreach($eligibleResults as $res)
                                                <option value="result:{{ $res->id }}"
                                                    data-type="result"
                                                    data-id="{{ $res->id }}"
                                                    data-student="{{ $res->student->full_name }}"
                                                    data-roll="{{ $res->student->roll_no }}"
                                                    data-reg="{{ $res->student->registration_no }}"
                                                    data-course-id="{{ $res->course_id }}"
                                                    data-course="{{ $res->course->course_name }}"
                                                    data-centre="{{ $res->centre->centre_name ?? 'Head Office' }}"
                                                    data-pct="{{ $res->percentage }}"
                                                    data-grade="{{ $res->grade }}"
                                                    {{ (old('exam_result_id') == $res->id || ($selectedResult && $selectedResult->id == $res->id)) ? 'selected' : '' }}>
                                                    [Passed Result] {{ $res->student->full_name }} (Roll: {{ $res->student->roll_no }}) - {{ $res->course->course_code }} - {{ $res->percentage }}% (Grade {{ $res->grade }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($eligibleStudents->count() > 0)
                                        <optgroup label="Enrolled Students (Direct Board Issuance - {{ $eligibleStudents->count() }})">
                                            @foreach($eligibleStudents as $stu)
                                                @php
                                                    $adm = $stu->admissions->first();
                                                    $crs = $adm?->course;
                                                @endphp
                                                <option value="student:{{ $stu->id }}"
                                                    data-type="student"
                                                    data-id="{{ $stu->id }}"
                                                    data-student="{{ $stu->full_name }}"
                                                    data-roll="{{ $stu->roll_no }}"
                                                    data-reg="{{ $stu->registration_no }}"
                                                    data-course-id="{{ $crs?->id }}"
                                                    data-course="{{ $crs?->course_name ?? 'Unassigned Course' }}"
                                                    data-centre="{{ $stu->centre->centre_name ?? 'Head Office' }}"
                                                    data-pct="75.0"
                                                    data-grade="A"
                                                    {{ (old('student_id') == $stu->id || ($selectedStudent && $selectedStudent->id == $stu->id)) ? 'selected' : '' }}>
                                                    [Enrolled] {{ $stu->full_name }} (Roll: {{ $stu->roll_no }}) - {{ $crs?->course_code ?? 'Course N/A' }} - {{ $stu->centre->centre_name ?? 'Main Centre' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('exam_result_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('student_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selected Candidate Synopsis -->
                            <div class="col-md-12">
                                <div class="card bg-light border-0 p-3 rounded-2" id="candidatePreviewCard">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold text-dark fs-6" id="previewStudent">Candidate: None Selected</div>
                                            <div class="small text-muted mt-1" id="previewRollReg">Roll No: - | Reg No: -</div>
                                            <div class="small text-muted" id="previewCentre">Centre: -</div>
                                            <div class="small text-primary fw-semibold mt-1" id="previewCourse">Course: -</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success px-3 py-2 fs-6" id="previewBadge">Standing: -</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Direct Student Evaluation Details (Shown when direct student selected) -->
                            <div class="col-md-12" id="directEvaluationBlock" style="display: none;">
                                <div class="p-3 border rounded-3 bg-white">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="fa-solid fa-graduation-cap me-2"></i>Direct Assessment & Performance Details
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="course_id" class="form-label fw-semibold">Enrolled Course Curriculum <span class="text-danger">*</span></label>
                                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id">
                                                <option value="">Select Course</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->course_name }} ({{ $course->course_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('course_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="percentage" class="form-label fw-semibold">Percentage (%)</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" min="35" max="100" class="form-control @error('percentage') is-invalid @enderror" id="percentage" name="percentage" value="{{ old('percentage', '75.0') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            @error('percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="grade" class="form-label fw-semibold">Awarded Grade</label>
                                            <input type="text" class="form-control @error('grade') is-invalid @enderror text-uppercase fw-bold text-primary" id="grade" name="grade" value="{{ old('grade', 'A') }}" readonly>
                                            @error('grade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-text small mt-2">
                                        <i class="fa-solid fa-circle-info text-info me-1"></i> Subjects and marksheet statement rows will be automatically calibrated to this percentage across the course curriculum.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="certificate_no" class="form-label fw-semibold">Certificate Serial Number</label>
                                <input type="text" class="form-control @error('certificate_no') is-invalid @enderror font-monospace" id="certificate_no" name="certificate_no" value="{{ old('certificate_no') }}" placeholder="Leave blank to auto-generate (TEC/CERT/YYYY/XXXX)">
                                <div class="form-text small">Unique tamper-proof board registration identifier.</div>
                                @error('certificate_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="marksheet_no" class="form-label fw-semibold">Marksheet Serial Number</label>
                                <input type="text" class="form-control @error('marksheet_no') is-invalid @enderror font-monospace" id="marksheet_no" name="marksheet_no" value="{{ old('marksheet_no') }}" placeholder="Leave blank to auto-generate (TEC/MS/YYYY/XXXX)">
                                <div class="form-text small">Unique marks statement registration serial.</div>
                                @error('marksheet_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="issue_date" class="form-label fw-semibold">Official Issuance Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-stamp me-1"></i> Issue Certificate & Marksheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const candidateSelect = document.getElementById('candidate_select');
    const hiddenExamResultId = document.getElementById('hidden_exam_result_id');
    const hiddenStudentId = document.getElementById('hidden_student_id');
    const directBlock = document.getElementById('directEvaluationBlock');
    const courseSelect = document.getElementById('course_id');
    const pctInput = document.getElementById('percentage');
    const gradeInput = document.getElementById('grade');

    const previewStudent = document.getElementById('previewStudent');
    const previewRollReg = document.getElementById('previewRollReg');
    const previewCentre = document.getElementById('previewCentre');
    const previewCourse = document.getElementById('previewCourse');
    const previewBadge = document.getElementById('previewBadge');

    function calculateGrade(pct) {
        pct = parseFloat(pct);
        if (isNaN(pct)) return 'N/A';
        if (pct >= 85) return 'S';
        if (pct >= 75) return 'A';
        if (pct >= 60) return 'B';
        if (pct >= 50) return 'C';
        if (pct >= 40) return 'D';
        return 'F';
    }

    if (pctInput && gradeInput) {
        pctInput.addEventListener('input', function () {
            gradeInput.value = calculateGrade(this.value);
            if (candidateSelect.selectedOptions[0] && candidateSelect.selectedOptions[0].dataset.type === 'student') {
                previewBadge.textContent = `${this.value}% (Grade ${gradeInput.value})`;
            }
        });
    }

    function updateSelection() {
        const opt = candidateSelect.options[candidateSelect.selectedIndex];
        if (!opt || !opt.value) {
            hiddenExamResultId.value = '';
            hiddenStudentId.value = '';
            directBlock.style.display = 'none';
            previewStudent.textContent = 'Candidate: None Selected';
            previewRollReg.textContent = 'Roll No: - | Reg No: -';
            previewCentre.textContent = 'Centre: -';
            previewCourse.textContent = 'Course: -';
            previewBadge.textContent = 'Standing: -';
            previewBadge.className = 'badge bg-secondary px-3 py-2 fs-6';
            return;
        }

        const type = opt.dataset.type;
        const id = opt.dataset.id;

        previewStudent.textContent = 'Candidate: ' + (opt.dataset.student || 'Unknown');
        previewRollReg.textContent = `Roll No: ${opt.dataset.roll || 'N/A'} | Reg No: ${opt.dataset.reg || 'N/A'}`;
        previewCentre.textContent = 'Study Centre: ' + (opt.dataset.centre || 'Main Centre');
        previewCourse.textContent = 'Course Curriculum: ' + (opt.dataset.course || 'Unassigned');

        if (type === 'result') {
            hiddenExamResultId.value = id;
            hiddenStudentId.value = '';
            directBlock.style.display = 'none';
            if (courseSelect) courseSelect.removeAttribute('required');
            previewBadge.textContent = `Passed Exam: ${opt.dataset.pct}% (Grade ${opt.dataset.grade})`;
            previewBadge.className = 'badge bg-success px-3 py-2 fs-6';
        } else if (type === 'student') {
            hiddenExamResultId.value = '';
            hiddenStudentId.value = id;
            directBlock.style.display = 'block';
            if (courseSelect) {
                courseSelect.setAttribute('required', 'required');
                if (opt.dataset.courseId) {
                    courseSelect.value = opt.dataset.courseId;
                }
            }
            const currentPct = pctInput.value || 75;
            gradeInput.value = calculateGrade(currentPct);
            previewBadge.textContent = `Direct Issuance: ${currentPct}% (Grade ${gradeInput.value})`;
            previewBadge.className = 'badge bg-primary px-3 py-2 fs-6';
        }
    }

    candidateSelect.addEventListener('change', updateSelection);

    // Initial trigger if prepopulated
    if (candidateSelect.value) {
        updateSelection();
    }
});
</script>
@endpush
@endsection
