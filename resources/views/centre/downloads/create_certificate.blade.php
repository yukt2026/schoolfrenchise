@extends('layouts.centre')

@section('title', 'Issue Student Certificate - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('centre.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('centre.downloads.student-certificates') }}" class="text-decoration-none">Certificates</a></li>
                <li class="breadcrumb-item active" aria-current="page">Issue Certificate</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-stamp text-primary me-2"></i>Issue Student Certificate & Marksheet
        </h3>
        <p class="text-muted small mb-0">Generate official completion diplomas and statements of marks for your centre's enrolled students with automatic duration-based issue date calibration.</p>
    </div>
    <div>
        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-secondary btn-sm rounded-2 px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Please correct the following errors:</div>
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-file-shield text-primary me-2"></i>Certification Credentials Particulars
                </h6>
                <span class="badge bg-primary-subtle text-primary border px-2 py-1 font-monospace">
                    Centre: {{ $centre->centre_code }}
                </span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.downloads.student-certificates.store') }}" id="certificateIssueForm">
                    @csrf

                    <!-- Hidden fields bound dynamically via JavaScript -->
                    <input type="hidden" name="exam_result_id" id="hidden_exam_result_id" value="{{ old('exam_result_id', $selectedResult?->id) }}">
                    <input type="hidden" name="student_id" id="hidden_student_id" value="{{ old('student_id', $selectedStudent?->id) }}">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="candidate_select" class="form-label fw-semibold">
                                Select Candidate <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('exam_result_id') is-invalid @enderror @error('student_id') is-invalid @enderror" id="candidate_select" required>
                                <option value="">-- Choose Candidate to Issue Certificate & Marksheet --</option>
                                
                                @if($eligibleStudents->count() > 0)
                                    <optgroup label="Enrolled Students (Direct Certification - {{ $eligibleStudents->count() }})">
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
                                                data-centre="{{ $centre->centre_name }}"
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
                                                data-centre="{{ $centre->centre_name }}"
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
                            <div class="form-text small">Select any enrolled candidate. Issue Date will automatically calibrate based on Admission Date and Course Duration.</div>
                        </div>

                        <!-- Selected Candidate Synopsis Card -->
                        <div class="col-12">
                            <div class="card bg-light border p-3 rounded-2" id="candidatePreviewCard">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark fs-6" id="previewStudent">Candidate: None Selected</div>
                                        <div class="small text-muted mt-1" id="previewRollReg">Roll No: - | Reg No: -</div>
                                        <div class="small text-muted" id="previewCentre">Centre: {{ $centre->centre_name }} ({{ $centre->centre_code }})</div>
                                        <div class="small text-primary fw-semibold mt-1" id="previewCourse">Course: -</div>
                                        <div class="mt-2" id="previewTimeline" style="display: none;">
                                            <span class="badge bg-white text-dark border me-1"><i class="fa-solid fa-calendar-check text-info me-1"></i>Admission: <strong id="previewAdmDate">-</strong></span>
                                            <span class="badge bg-white text-dark border me-1"><i class="fa-solid fa-clock text-warning me-1"></i>Duration: <strong id="previewDuration">-</strong></span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-certificate me-1"></i>Auto Issue Date: <strong id="previewCalcDate">-</strong></span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary px-3 py-2 fs-6" id="previewBadge">Standing: -</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Direct Student Evaluation Details (Shown when direct student selected) -->
                        <div class="col-12" id="directEvaluationBlock" style="display: none;">
                            <div class="p-3 border rounded-3 bg-white">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fa-solid fa-graduation-cap me-2"></i>Assessment & Performance Marks
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
                                Automatically calculated: <strong>Admission Date + Course Duration</strong>. If admission was 1 year ago for a 1-year course, the issue date is automatically set to 1 year ago.
                            </div>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-xmark me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-certificate me-1"></i> Issue Official Certificate & Marksheet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const candidateSelect = document.getElementById('candidate_select');
    const hiddenResultId = document.getElementById('hidden_exam_result_id');
    const hiddenStudentId = document.getElementById('hidden_student_id');
    const directEvalBlock = document.getElementById('directEvaluationBlock');
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
        pct = parseFloat(pct) || 0;
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
        });
    }

    function syncCandidateSelection() {
        const selectedOpt = candidateSelect.options[candidateSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            hiddenResultId.value = '';
            hiddenStudentId.value = '';
            directEvalBlock.style.display = 'none';
            previewStudent.textContent = 'Candidate: None Selected';
            previewRollReg.textContent = 'Roll No: - | Reg No: -';
            previewCourse.textContent = 'Course: -';
            previewBadge.textContent = 'Standing: -';
            previewBadge.className = 'badge bg-secondary px-3 py-2 fs-6';
            if (previewTimeline) previewTimeline.style.display = 'none';
            if (issueDateCalcHint) issueDateCalcHint.style.display = 'none';
            currentAdmDate = '';
            return;
        }

        const type = selectedOpt.dataset.type;
        const id = selectedOpt.dataset.id;
        const student = selectedOpt.dataset.student;
        const roll = selectedOpt.dataset.roll;
        const reg = selectedOpt.dataset.reg;
        const courseId = selectedOpt.dataset.courseId;
        const course = selectedOpt.dataset.course;
        const centre = selectedOpt.dataset.centre;
        const pct = selectedOpt.dataset.pct;
        const grade = selectedOpt.dataset.grade;

        currentAdmDate = selectedOpt.dataset.admissionDate || '';
        const admHuman = selectedOpt.dataset.admissionDateHuman || 'N/A';
        currentDurationMonths = parseInt(selectedOpt.dataset.durationMonths, 10) || 6;
        const durText = selectedOpt.dataset.durationText || `${currentDurationMonths} Months`;
        const calcDate = selectedOpt.dataset.calcIssueDate || '';
        const calcHuman = selectedOpt.dataset.calcIssueDateHuman || '';

        previewStudent.textContent = 'Candidate: ' + student;
        previewRollReg.textContent = 'Roll No: ' + roll + ' | Reg No: ' + reg;
        previewCentre.textContent = 'Centre: ' + centre;
        previewCourse.textContent = 'Course: ' + course;

        if (previewTimeline) {
            previewAdmDate.textContent = admHuman;
            previewDuration.textContent = durText;
            previewCalcDate.textContent = calcHuman || formatDateHuman(calcDate);
            previewTimeline.style.display = 'block';
        }

        if (type === 'result') {
            hiddenResultId.value = id;
            hiddenStudentId.value = '';
            directEvalBlock.style.display = 'none';
            previewBadge.textContent = 'Passed Result: ' + pct + '% (Grade ' + grade + ')';
            previewBadge.className = 'badge bg-success px-3 py-2 fs-6';
        } else {
            hiddenStudentId.value = id;
            hiddenResultId.value = '';
            directEvalBlock.style.display = 'block';
            if (courseId && courseSelect) {
                courseSelect.value = courseId;
            }
            if (pctInput && !pctInput.value) {
                pctInput.value = '75.0';
            }
            if (gradeInput) {
                gradeInput.value = calculateGrade(pctInput ? pctInput.value : 75);
            }
            previewBadge.textContent = 'Direct Enrolled Candidate';
            previewBadge.className = 'badge bg-primary px-3 py-2 fs-6';
        }

        // Apply automatic issue date
        if (calcDate) {
            applyCalculatedDate(calcDate, `Admission (${admHuman}) + ${durText} = ${calcHuman || formatDateHuman(calcDate)}`);
        }
    }

    candidateSelect.addEventListener('change', syncCandidateSelection);

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

    // Initial trigger if something is pre-selected
    if (candidateSelect.value) {
        syncCandidateSelection();
    }
});
</script>
@endpush
