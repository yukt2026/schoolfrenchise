@extends('layouts.centre')

@section('title', 'Admit Cards & Hall Tickets - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-ticket text-primary me-2"></i>Hall Tickets & Admit Cards
        </h3>
        <p class="text-muted small mb-0">Generate, view, and print official examination hall tickets for your centre candidates.</p>
    </div>
    @if($selectedExam && $students->isNotEmpty())
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.hall-tickets', $selectedExam->id) }}" target="_blank" class="btn btn-primary rounded-2 px-3 fw-bold shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print All Hall Tickets ({{ $students->count() }})
        </a>
    </div>
    @endif
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Active Exam Cycles</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total_exams']) }}</div>
            <span class="small text-muted">Central board sessions</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-primary rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Eligible Candidates</div>
            <div class="fs-4 fw-bold text-primary my-1">{{ number_format($stats['eligible_candidates']) }}</div>
            <span class="small text-muted">Ready for hall ticket</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Centre Code</div>
            <div class="fs-4 fw-bold text-info my-1 font-monospace">{{ $centre->centre_code }}</div>
            <span class="small text-muted text-truncate d-block">{{ $centre->centre_name }}</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Quick Action</div>
            <a href="{{ route('centre.exams.marks.index') }}" class="btn btn-outline-success btn-sm w-100 mt-2 fw-semibold rounded-2">
                <i class="fa-solid fa-pen-to-square me-1"></i>Go to Marks Entry
            </a>
        </div>
    </div>
</div>

<!-- Filter & Selection Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.exams.hall-tickets.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <label class="small text-muted fw-bold mb-1">Select Examination Session:</label>
                <select name="exam_id" class="form-select rounded-2" onchange="this.form.submit()">
                    @forelse($exams as $ex)
                    <option value="{{ $ex->id }}" {{ ($selectedExam && $selectedExam->id == $ex->id) ? 'selected' : '' }}>
                        {{ $ex->exam_title }} ({{ $ex->session_name }}) - {{ $ex->course?->course_name ?? 'All Courses' }} [{{ $ex->exam_code }}]
                    </option>
                    @empty
                    <option value="">No Active Exams Available</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-5">
                <label class="small text-muted fw-bold mb-1">Search Candidate:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-2" placeholder="Student name, Roll No or Reg No..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end" style="padding-top: 22px;">
                <button type="submit" class="btn btn-primary w-100 rounded-2 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('centre.exams.hall-tickets.index') }}" class="btn btn-outline-secondary rounded-2" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

@if($selectedExam)
<!-- Selected Exam Summary Banner -->
<div class="alert alert-info border-0 rounded-3 mb-4 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="fw-bold fs-6 text-dark">
            <i class="fa-solid fa-circle-info text-info me-2"></i>{{ $selectedExam->exam_title }}
            <span class="badge bg-primary-subtle text-primary font-monospace ms-2">{{ $selectedExam->exam_code }}</span>
            <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $selectedExam->session_name }}</span>
        </div>
        <div class="small text-muted mt-1">
            <strong>Target Course:</strong> {{ $selectedExam->course?->course_name }} ({{ $selectedExam->course?->course_code }}) &bull; 
            <strong>Exam Date:</strong> {{ $selectedExam->exam_date ? $selectedExam->exam_date->format('d M, Y') : 'Announced by Board' }} &bull; 
            <strong>Status:</strong> <span class="badge bg-info text-capitalize">{{ $selectedExam->status }}</span>
        </div>
    </div>
    @if($students->isNotEmpty())
    <div>
        <a href="{{ route('centre.exams.hall-tickets', $selectedExam->id) }}" target="_blank" class="btn btn-primary btn-sm rounded-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print All ({{ $students->count() }})
        </a>
    </div>
    @endif
</div>

<!-- Candidate Roster Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-users me-2 text-primary"></i>Eligible Centre Candidates Roster
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $students->count() }} Candidates</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete candidate roster
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 760px;">
                <thead>
                    <tr>
                        <th class="ps-3">Candidate Particulars</th>
                        <th>Roll & Registration No</th>
                        <th>Course & Batch</th>
                        <th>Exam Schedule</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                    @php
                        $stAdmission = $st->admissions->first();
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle overflow-hidden bg-light border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                                    @if($st->photo_path)
                                    <img src="{{ $st->photo_url ?? asset('storage/' . $st->photo_path) }}" alt="{{ $st->full_name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/80x80?text=Photo';">
                                    @else
                                    <i class="fa-solid fa-user text-muted fs-5"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $st->full_name }}</div>
                                    <div class="small text-muted">Father: {{ $st->father_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold font-monospace text-primary">{{ $st->roll_no }}</div>
                            <div class="small text-muted font-monospace" style="font-size: 0.75rem;">Reg: {{ $st->registration_no }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-2 font-monospace">{{ $selectedExam->course?->course_code }}</span>
                            <div class="small text-muted">{{ $stAdmission?->batch?->batch_name ?? 'Standard Batch' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $selectedExam->exam_date ? $selectedExam->exam_date->format('d M, Y') : 'Announced' }}</div>
                            <div class="small text-muted">{{ $centre->centre_name }}</div>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('centre.exams.hall-tickets.single', [$selectedExam->id, $st->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2 fw-semibold shadow-sm" title="Print Admit Card">
                                <i class="fa-solid fa-print me-1"></i>Print Admit Card
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-id-badge fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Eligible Candidates Found</h5>
                            <p class="small text-muted mb-0">No active students enrolled in <strong>{{ $selectedExam->course?->course_name }}</strong> were found at your centre.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card border-0 rounded-3 shadow-sm text-center py-5">
    <div class="card-body">
        <i class="fa-solid fa-clipboard-question fs-1 text-black-50 mb-3 d-block"></i>
        <h5>No Examination Cycle Selected</h5>
        <p class="text-muted small">Please select an active examination cycle above to generate and print hall tickets.</p>
    </div>
</div>
@endif
@endsection
