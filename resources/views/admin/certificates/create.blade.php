@extends('layouts.admin')

@section('title', 'Issue Certificate & Marksheet - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Issue Official Certificate & Marksheet</h1>
            <p class="text-muted small mb-0">Generate verified completion certificates and official marksheets with automatic duration-based issue date calibration.</p>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Please correct the following errors:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fa-solid fa-file-shield me-2"></i>Official Certification Particulars
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border px-2 py-1 font-monospace">
                        Board Controller Office
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.certificates.store') }}" method="POST" id="certificateForm">
                        @csrf

                        <!-- Hidden fields bound dynamically via JavaScript -->
                        <input type="hidden" name="exam_result_id" id="hidden_exam_result_id" value="{{ old('exam_result_id', $selectedResult?->id) }}">
                        <input type="hidden" name="student_id" id="hidden_student_id" value="{{ old('student_id', $selectedStudent?->id) }}">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="candidate_select" class="form-label fw-semibold">Select Candidate <span class="text-danger">*</span></label>
                                <select class="form-select @error('exam_result_id') is-invalid @enderror @error('student_id') is-invalid @enderror" id="candidate_select" required>
                                    <option value="">-- Choose Candidate to Issue Certificate & Marksheet --</option>
                                    
                                    @if($eligibleStudents->count() > 0)
                                        <optgroup label="Enrolled Students (Direct Board Issuance - {{ $eligibleStudents->count() }})">
                                            @foreach($eligibleStudents as $stu)
                                                @php
                                                    $adm = $stu->admissions->first() ?? $stu->admissions()->latest('admission_date')->first();
                                                    $crs = $adm?->course;
                                                    $admDate = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('Y-m-d') : '';
                                                    $admDateHuman = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('d M Y') : 'N/A';
                                                    $durMonths = $crs?->duration_months ?? 6;
                                                    $durText = $crs?->duration ?? ($durMonths . ' Months');
                                                    $calcIssueDate = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->addMonths($durMonths)->format('Y-m-d') : date('Y-m-d');
                                                    $calcIssueDateHuman = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->addMonths($durMonths)->format('d M Y') : date('d M Y');
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
                                                    data-admission-date="{{ $admDate }}"
                                                    data-admission-date-human="{{ $admDateHuman }}"
                                                    data-duration-months="{{ $durMonths }}"
                                                    data-duration-text="{{ $durText }}"
                                                    data-calc-issue-date="{{ $calcIssueDate }}"
                                                    data-calc-issue-date-human="{{ $calcIssueDateHuman }}"
                                                    data-pct="75.0"
                                                    data-grade="A"
                                                    {{ (old('student_id') == $stu->id || ($selectedStudent && $selectedStudent->id == $stu->id)) ? 'selected' : '' }}>
                                                    [Direct] {{ $stu->full_name }} (Roll: {{ $stu->roll_no }}) - {{ $crs?->course_code ?? 'Course N/A' }} | Adm: {{ $admDateHuman }} (Dur: {{ $durText }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if($eligibleResults->count() > 0)
                                        <optgroup label="Candidates with Passed Exam Results ({{ $eligibleResults->count() }})">
                                            @foreach($eligibleResults as $res)
                                                @php
                                                    $adm = $res->student->admissions->where('course_id', $res->course_id)->first() ?? $res->student->admissions->first();
                                                    $admDate = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('Y-m-d') : '';
                                                    $admDateHuman = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('d M Y') : 'N/A';
                                                    $durMonths = $res->course?->duration_months ?? 6;
                                                    $durText = $res->course?->duration ?? ($durMonths . ' Months');
                                                    $calcIssueDate = $adm?->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->addMonths($durMonths)->format('Y-m-d') : ($res->exam?->exam_date ? \Carbon\Carbon::parse($res->exam->exam_date)->format('Y-m-d') : date('Y-m-d'));
                                                    $calcIssueDateHuman = \Carbon\Carbon::parse($calcIssueDate)->format('d M Y');
                                                @endphp
                                                <option value="result:{{ $res->id }}"
                                                    data-type="result"
                                                    data-id="{{ $res->id }}"
                                                    data-student="{{ $res->student->full_name }}"
                                                    data-roll="{{ $res->student->roll_no }}"
                                                    data-reg="{{ $res->student->registration_no }}"
                                                    data-course-id="{{ $res->course_id }}"
                                                    data-course="{{ $res->course->course_name }}"
                                                    data-centre="{{ $res->student->centre->centre_name ?? 'Main Centre' }}"
                                                    data-admission-date="{{ $admDate }}"
                                                    data-admission-date-human="{{ $admDateHuman }}"
                                                    data-duration-months="{{ $durMonths }}"
                                                    data-duration-text="{{ $durText }}"
                                                    data-calc-issue-date="{{ $calcIssueDate }}"
                                                    data-calc-issue-date-human="{{ $calcIssueDateHuman }}"
                                                    data-pct="{{ $res->percentage }}"
                                                    data-grade="{{ $res->grade }}"
                                                    {{ (old('exam_result_id') == $res->id || ($selectedResult && $selectedResult->id == $res->id)) ? 'selected' : '' }}>
                                                    [Exam Result] {{ $res->student->full_name }} (Roll: {{ $res->student->roll_no }}) - {{ $res->course->course_code }} - {{ $res->percentage }}% (Grade {{ $res->grade }})
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
                                            <div class="mt-2" id="previewTimeline" style="display: none;">
                                                <span class="badge bg-white text-dark border me-1"><i class="fa-solid fa-calendar-check text-info me-1"></i>Admission: <strong id="previewAdmDate">-</strong></span>
                                                <span class="badge bg-white text-dark border me-1"><i class="fa-solid fa-clock text-warning me-1"></i>Duration: <strong id="previewDuration">-</strong></span>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-certificate me-1"></i>Auto Issue Date: <strong id="previewCalcDate">-</strong></span>
                                            </div>
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
                                                    <option value="{{ $course->id }}" 
                                                            data-duration="{{ $course->duration }}"
                                                            data-duration-months="{{ $course->duration_months }}"
                                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->course_name }} ({{ $course->course_code }}) - [{{ $course->duration }}]
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
                                <label for="issue_date" class="form-label fw-semibold">
                                    Official Issuance Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-day text-primary"></i></span>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror font-monospace fw-bold" id="issue_date" name="issue_date" value="{{ old('issue_date') }}" required>
                                    <button type="button" class="btn btn-outline-secondary" id="resetCalcDateBtn" title="Re-calculate date based on Admission Date + Course Duration">
                                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Set
                                    </button>
                                </div>
                                <div id="issueDateCalcHint" class="form-text text-success fw-semibold mt-1" style="display: none;">
                                    <i class="fa-solid fa-circle-check me-1"></i> <span id="issueDateCalcText"></span>
                                </div>
                                <div class="form-text small text-muted">
                                    Automatically calibrated: <strong>Admission Date + Course Duration</strong>.
                                </div>
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
    const issueDateInput = document.getElementById('issue_date');
    const resetCalcBtn = document.getElementById('resetCalcDateBtn');
    const issueDateCalcHint = document.getElementById('issueDateCalcHint');
    const issueDateCalcText = document.getElementById('issueDateCalcText');

    const previewStudent = document.getElementById('previewStudent');
    const previewRollReg = document.getElementById('previewRollReg');
    const previewCentre = document.getElementById('previewCentre');
    const previewCourse = document.getElementById('previewCourse');
    const previewBadge = document.getElementById('previewBadge');
    const previewTimeline = document.getElementById('previewTimeline');
    const previewAdmDate = document.getElementById('previewAdmDate');
    const previewDuration = document.getElementById('previewDuration');
    const previewCalcDate = document.getElementById('previewCalcDate');

    let currentAdmDate = '';
    let currentDurationMonths = 6;
    let currentCalculatedDate = '';

    function calculateGrade(pct) {
        pct = parseFloat(pct);
        if (isNaN(pct)) return 'N/A';
        if (pct >= 85) return 'A+';
        if (pct >= 70) return 'A';
        if (pct >= 60) return 'B';
        if (pct >= 50) return 'C';
        if (pct >= 40) return 'D';
        return 'F';
    }

    function addMonthsToDate(dateStr, months) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return '';
        let year = parseInt(parts[0], 10);
        let month = parseInt(parts[1], 10) - 1; // 0-based
        let day = parseInt(parts[2], 10);

        const d = new Date(year, month, day);
        d.setMonth(d.getMonth() + parseInt(months, 10));

        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dayFormatted = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${dayFormatted}`;
    }

    function formatDateHuman(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function applyCalculatedDate(dateStr, reasonText) {
        if (dateStr) {
            issueDateInput.value = dateStr;
            currentCalculatedDate = dateStr;
            if (issueDateCalcHint && issueDateCalcText) {
                issueDateCalcText.textContent = reasonText || `Auto-set to ${formatDateHuman(dateStr)} (Admission + Duration)`;
                issueDateCalcHint.style.display = 'block';
            }
        }
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
            if (previewTimeline) previewTimeline.style.display = 'none';
            if (issueDateCalcHint) issueDateCalcHint.style.display = 'none';
            currentAdmDate = '';
            return;
        }

        const type = opt.dataset.type;
        const id = opt.dataset.id;

        currentAdmDate = opt.dataset.admissionDate || '';
        const admHuman = opt.dataset.admissionDateHuman || 'N/A';
        currentDurationMonths = parseInt(opt.dataset.durationMonths, 10) || 6;
        const durText = opt.dataset.durationText || `${currentDurationMonths} Months`;
        const calcDate = opt.dataset.calcIssueDate || '';
        const calcHuman = opt.dataset.calcIssueDateHuman || '';

        previewStudent.textContent = 'Candidate: ' + (opt.dataset.student || 'Unknown');
        previewRollReg.textContent = `Roll No: ${opt.dataset.roll || 'N/A'} | Reg No: ${opt.dataset.reg || 'N/A'}`;
        previewCentre.textContent = 'Study Centre: ' + (opt.dataset.centre || 'Main Centre');
        previewCourse.textContent = 'Course Curriculum: ' + (opt.dataset.course || 'Unassigned');

        if (previewTimeline) {
            previewAdmDate.textContent = admHuman;
            previewDuration.textContent = durText;
            previewCalcDate.textContent = calcHuman || formatDateHuman(calcDate);
            previewTimeline.style.display = 'block';
        }

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

        // Apply automatic issue date
        if (calcDate) {
            applyCalculatedDate(calcDate, `Admission (${admHuman}) + ${durText} = ${calcHuman || formatDateHuman(calcDate)}`);
        }
    }

    candidateSelect.addEventListener('change', updateSelection);

    // If Course dropdown changes inside Direct Certification, recalculate issue date dynamically
    if (courseSelect) {
        courseSelect.addEventListener('change', function () {
            const opt = courseSelect.options[courseSelect.selectedIndex];
            if (opt && opt.dataset.durationMonths && currentAdmDate) {
                const newMonths = parseInt(opt.dataset.durationMonths, 10);
                const durName = opt.dataset.duration || `${newMonths} Months`;
                const newDate = addMonthsToDate(currentAdmDate, newMonths);
                applyCalculatedDate(newDate, `Recalibrated: Admission + ${durName} = ${formatDateHuman(newDate)}`);
                if (previewDuration) previewDuration.textContent = durName;
                if (previewCalcDate) previewCalcDate.textContent = formatDateHuman(newDate);
            }
        });
    }

    if (resetCalcBtn) {
        resetCalcBtn.addEventListener('click', function () {
            if (currentCalculatedDate) {
                issueDateInput.value = currentCalculatedDate;
                if (issueDateCalcHint) issueDateCalcHint.style.display = 'block';
            } else if (currentAdmDate) {
                const date = addMonthsToDate(currentAdmDate, currentDurationMonths);
                applyCalculatedDate(date);
            }
        });
    }

    // Initial trigger if prepopulated
    if (candidateSelect.value) {
        updateSelection();
    }
});
</script>
@endpush
@endsection
