@extends('layouts.centre')

@section('title', 'Exam Marks Evaluation - ' . $exam->exam_title . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <a href="{{ route('centre.exams.marks.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Marks Entry Hub
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-pen-to-square text-success me-2"></i>Internal Marks Evaluation Sheet
        </h3>
        <p class="text-muted small mb-0">
            <strong>{{ $exam->exam_title }}</strong> &bull; Course: {{ $exam->course?->course_name }} ({{ $exam->course?->course_code }}) &bull; Session: {{ $exam->session_name }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.results', ['exam_id' => $exam->id]) }}" class="btn btn-outline-primary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-square-poll-vertical me-1"></i>View Session Results
        </a>
    </div>
</div>

<!-- Select Student Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-4 bg-light rounded-3">
        <label class="form-label fw-bold text-dark mb-2">Select Centre Candidate for Marks Entry:</label>
        <form method="GET" action="{{ route('centre.exams.marks', $exam->id) }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <select name="student_id" class="form-select rounded-2" onchange="this.form.submit()" required>
                        <option value="">-- Choose Candidate from This Centre --</option>
                        @foreach($students as $s)
                        @php
                            $hasResult = $s->results->where('exam_id', $exam->id)->first();
                        @endphp
                        <option value="{{ $s->id }}" {{ ($selectedStudent && $selectedStudent->id == $s->id) ? 'selected' : '' }}>
                            {{ $s->roll_no }} - {{ $s->full_name }} (Father: {{ $s->father_name }})
                            @if($hasResult) [Evaluated: {{ $hasResult->grade }} - {{ number_format($hasResult->percentage, 1) }}% - {{ strtoupper($hasResult->result_status) }}] @else [PENDING EVALUATION] @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-2">
                        <i class="fa-solid fa-arrows-rotate me-1"></i>Load Candidate Subjects
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedStudent)
<form method="POST" action="{{ route('centre.exams.marks.store', $exam->id) }}" id="marksEntryForm">
    @csrf
    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

    <!-- Candidate Header Card -->
    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle overflow-hidden bg-light border d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    @if($selectedStudent->photo_path)
                    <img src="{{ $selectedStudent->photo_url ?? asset('storage/' . $selectedStudent->photo_path) }}" alt="{{ $selectedStudent->full_name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/96x96?text=Photo';">
                    @else
                    <i class="fa-solid fa-user text-muted fs-4"></i>
                    @endif
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark fs-5">{{ $selectedStudent->full_name }}</h6>
                    <div class="small text-muted font-monospace">
                        ROLL: <span class="fw-bold text-primary">{{ $selectedStudent->roll_no }}</span> | 
                        REG: <span class="fw-bold">{{ $selectedStudent->registration_no }}</span> | 
                        Father: {{ $selectedStudent->father_name }}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(isset($existingResult) && $existingResult)
                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-2">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>Previously Evaluated ({{ $existingResult->grade }})
                </span>
                @else
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-2">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>New Evaluation
                </span>
                @endif
                <span class="badge bg-primary-subtle text-primary font-monospace rounded-2 px-2 py-1">{{ $exam->course?->course_code }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                    <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to enter theory, practical & viva marks
                </div>
                <table class="table table-custom align-middle mb-0" id="subjectsTable" style="min-width: 760px;">
                    <thead>
                        <tr>
                            <th class="ps-3">Subject Details</th>
                            <th class="text-center" style="width: 150px;">Theory Marks</th>
                            <th class="text-center" style="width: 150px;">Practical Marks</th>
                            <th class="text-center" style="width: 150px;">Viva / Project</th>
                            <th class="text-center" style="width: 110px;">Pass Marks</th>
                            <th class="text-center" style="width: 110px;">Total Max</th>
                            <th class="text-center pe-3" style="width: 110px;">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exam->course?->subjects ?? [] as $sub)
                        @php
                            $subMax = (float)($sub->total_max ?: 100);
                            $passM = (float)($sub->pass_marks ?: 40);
                            $existingSub = $existingSubjects[$sub->id] ?? null;
                            $defaultTheory = old('subjects.' . $sub->id . '.theory_obtained', $existingSub?->theory_obtained ?? 0);
                            $defaultPractical = old('subjects.' . $sub->id . '.practical_obtained', $existingSub?->practical_obtained ?? 0);
                            $defaultViva = old('subjects.' . $sub->id . '.viva_obtained', $existingSub?->viva_obtained ?? 0);
                        @endphp
                        <tr class="subject-row" data-sub-id="{{ $sub->id }}" data-pass-marks="{{ $passM }}" data-max-marks="{{ $subMax }}">
                            <td class="ps-3">
                                <input type="hidden" name="subjects[{{ $sub->id }}][id]" value="{{ $sub->id }}">
                                <div class="fw-bold text-dark">{{ $sub->subject_name }}</div>
                                <div class="small text-muted font-monospace">{{ $sub->subject_code }}</div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->theory_max }}" name="subjects[{{ $sub->id }}][theory_obtained]" class="form-control text-center fw-bold marks-input theory-input rounded-start-2" placeholder="0" value="{{ $defaultTheory }}" required>
                                    <span class="input-group-text bg-light text-muted rounded-end-2">/ {{ $sub->theory_max }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->practical_max }}" name="subjects[{{ $sub->id }}][practical_obtained]" class="form-control text-center fw-bold marks-input practical-input rounded-start-2" placeholder="0" value="{{ $defaultPractical }}">
                                    <span class="input-group-text bg-light text-muted rounded-end-2">/ {{ $sub->practical_max }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->viva_max }}" name="subjects[{{ $sub->id }}][viva_obtained]" class="form-control text-center fw-bold marks-input viva-input rounded-start-2" placeholder="0" value="{{ $defaultViva }}">
                                    <span class="input-group-text bg-light text-muted rounded-end-2">/ {{ $sub->viva_max }}</span>
                                </div>
                            </td>
                            <td class="text-center text-muted font-monospace fw-semibold">{{ $passM }}</td>
                            <td class="text-center fw-bold text-primary font-monospace">{{ $subMax }}</td>
                            <td class="text-center pe-3">
                                <span class="badge bg-secondary-subtle text-dark font-monospace subtotal-badge fs-6 rounded-2">0.0</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-triangle-exclamation fs-1 text-warning mb-2 d-block"></i>
                                <h6>No Course Subjects Configured</h6>
                                <p class="small text-muted mb-0">Course curriculum subjects must be added by Admin before entering marks.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Real-time Live Score Preview Banner -->
            <div class="p-3 bg-light border-top">
                <div class="row g-3 align-items-center text-center">
                    <div class="col-6 col-md-3 border-end">
                        <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Marks Obtained</div>
                        <div class="fs-4 fw-bold text-primary font-monospace" id="liveTotalObtained">0.00</div>
                    </div>
                    <div class="col-6 col-md-3 border-end">
                        <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Maximum</div>
                        <div class="fs-4 fw-bold text-dark font-monospace" id="liveTotalMax">0.00</div>
                    </div>
                    <div class="col-6 col-md-3 border-end">
                        <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Percentage Score</div>
                        <div class="fs-4 fw-bold font-monospace" id="livePercentage">0.00%</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Grade & Status</div>
                        <div class="mt-1" id="liveGradeBadge">
                            <span class="badge bg-secondary rounded-2 px-3 py-1 fs-6">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>Minimum 40% aggregate required for passing grade.
                </div>
                <button type="submit" class="btn btn-success rounded-2 px-4 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>Save Marks & Generate Marksheet
                </button>
            </div>
        </div>
    </div>
</form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('subjectsTable');
    if (!table) return;

    function calculateLiveScore() {
        let totalObtained = 0;
        let totalMax = 0;
        let allPassed = true;

        const rows = table.querySelectorAll('.subject-row');
        rows.forEach(function (row) {
            const th = parseFloat(row.querySelector('.theory-input')?.value) || 0;
            const pr = parseFloat(row.querySelector('.practical-input')?.value) || 0;
            const va = parseFloat(row.querySelector('.viva-input')?.value) || 0;
            const subTot = th + pr + va;
            const maxM = parseFloat(row.getAttribute('data-max-marks')) || 100;
            const passM = parseFloat(row.getAttribute('data-pass-marks')) || 40;

            const subBadge = row.querySelector('.subtotal-badge');
            if (subBadge) {
                subBadge.textContent = subTot.toFixed(1);
                if (subTot < passM) {
                    subBadge.className = 'badge bg-danger-subtle text-danger font-monospace subtotal-badge fs-6 rounded-2';
                    allPassed = false;
                } else {
                    subBadge.className = 'badge bg-success-subtle text-success font-monospace subtotal-badge fs-6 rounded-2';
                }
            }

            totalObtained += subTot;
            totalMax += maxM;
        });

        const pct = totalMax > 0 ? ((totalObtained / totalMax) * 100) : 0;
        let grade = 'F';
        if (pct >= 85) grade = 'A+';
        else if (pct >= 70) grade = 'A';
        else if (pct >= 60) grade = 'B';
        else if (pct >= 50) grade = 'C';
        else if (pct >= 40) grade = 'D';

        const isPassed = allPassed && pct >= 40;

        document.getElementById('liveTotalObtained').textContent = totalObtained.toFixed(2);
        document.getElementById('liveTotalMax').textContent = totalMax.toFixed(2);
        document.getElementById('livePercentage').textContent = pct.toFixed(2) + '%';

        const gradeBox = document.getElementById('liveGradeBadge');
        if (isPassed) {
            gradeBox.innerHTML = '<span class="badge bg-success rounded-2 px-3 py-1 fs-6"><i class="fa-solid fa-check me-1"></i>' + grade + ' (PASSED)</span>';
        } else {
            gradeBox.innerHTML = '<span class="badge bg-danger rounded-2 px-3 py-1 fs-6"><i class="fa-solid fa-xmark me-1"></i>FAILED / REAPPEAR</span>';
        }
    }

    table.querySelectorAll('.marks-input').forEach(function (input) {
        input.addEventListener('input', calculateLiveScore);
    });

    calculateLiveScore();
});
</script>
@endpush
@endsection
