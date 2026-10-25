@extends('layouts.centre')

@section('title', 'Marks Evaluation Hub - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-pen-to-square text-success me-2"></i>Marks Entry & Evaluation
        </h3>
        <p class="text-muted small mb-0">Record internal assessment scores, theory, practical, and viva marks for your centre candidates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.results') }}" class="btn btn-outline-success rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-square-poll-vertical me-1"></i>View Published Results
        </a>
    </div>
</div>

<!-- Quick Exam Selector Banner -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-4 bg-light rounded-3">
        <form method="GET" action="{{ route('centre.exams.marks.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <label class="form-label fw-bold text-dark mb-1">Select Examination Session to Enter Marks:</label>
                <select name="exam_id" class="form-select rounded-2" required>
                    <option value="">-- Choose Examination Session --</option>
                    @foreach($exams as $ex)
                    <option value="{{ $ex->id }}">
                        {{ $ex->exam_title }} ({{ $ex->session_name }}) - {{ $ex->course?->course_name }} [Code: {{ $ex->exam_code }}] &bull; {{ $ex->results_count }} Evaluated
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-2">
                    <i class="fa-solid fa-arrow-right me-1"></i>Open Marks Sheet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Examination Cycles Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-list-check me-2 text-primary"></i>All Examination Sessions for Assessment
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Total Sessions: {{ $exams->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete table
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 780px;">
                <thead>
                    <tr>
                        <th class="ps-3">Exam Code & Title</th>
                        <th>Course & Session</th>
                        <th>Exam Date</th>
                        <th class="text-center">Centre Candidates</th>
                        <th class="text-center">Evaluated / Recorded</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $ex)
                    @php
                        $candCount = $ex->centre_candidates_count ?? 0;
                        $resCount = $ex->results_count ?? 0;
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-secondary-subtle text-dark font-monospace rounded-2">{{ $ex->exam_code }}</span>
                            <div class="fw-bold text-dark mt-1">{{ $ex->exam_title }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-2">{{ $ex->course?->course_code }}</span>
                            <div class="small text-muted">{{ $ex->course?->course_name }}</div>
                            <div class="small text-muted">{{ $ex->session_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $ex->exam_date ? $ex->exam_date->format('d M, Y') : 'Announced' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">{{ $candCount }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $resCount >= $candCount && $candCount > 0 ? 'bg-success' : 'bg-warning text-dark' }} font-monospace px-2 py-1 fs-6">
                                {{ $resCount }} / {{ $candCount }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $ex->status === 'published' ? 'bg-success' : ($ex->status === 'evaluated' ? 'bg-info' : 'bg-secondary') }} rounded-2 text-capitalize">
                                {{ $ex->status }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('centre.exams.marks', $ex->id) }}" class="btn btn-success rounded-start-2 fw-semibold" title="Enter Marks">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Enter Marks
                                </a>
                                <a href="{{ route('centre.exams.hall-tickets', $ex->id) }}" target="_blank" class="btn btn-outline-primary rounded-end-2" title="View Hall Tickets">
                                    <i class="fa-solid fa-ticket"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-question fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Active Examination Cycles</h5>
                            <p class="small text-muted mb-0">Central Board examination sessions will be listed here once scheduled.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
