@extends('layouts.centre')

@section('title', 'Examination Results - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-square-poll-vertical text-success me-2"></i>Published Examination Results
        </h3>
        <p class="text-muted small mb-0">Candidate grades, percentage scores, and official statement of marksheets for centre candidates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.marks.index') }}" class="btn btn-primary rounded-2 px-3 fw-bold shadow-sm">
            <i class="fa-solid fa-pen-to-square me-1"></i>New Marks Entry
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-dark rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Evaluated Candidates</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total']) }}</div>
            <span class="small text-muted">All sessions</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Passed</div>
            <div class="fs-4 fw-bold text-success my-1">{{ number_format($stats['passed']) }}</div>
            <span class="small text-muted">Eligible for certification</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-danger rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Failed / Reappear</div>
            <div class="fs-4 fw-bold text-danger my-1">{{ number_format($stats['failed']) }}</div>
            <span class="small text-muted">Requires re-attempt</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Pass Rate</div>
            @php
                $passRate = $stats['total'] > 0 ? round(($stats['passed'] / $stats['total']) * 100, 1) : 0;
            @endphp
            <div class="fs-4 fw-bold text-info my-1">{{ $passRate }}%</div>
            <span class="small text-muted">Centre average</span>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.exams.results') }}" class="row g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-2" placeholder="Student name, Roll or Reg No..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="exam_id" class="form-select rounded-2">
                    <option value="">All Examination Cycles</option>
                    @foreach($exams as $ex)
                    <option value="{{ $ex->id }}" {{ request('exam_id') == $ex->id ? 'selected' : '' }}>
                        {{ $ex->exam_title }} ({{ $ex->session_name }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="course_id" class="form-select rounded-2">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->course_code }} - {{ $c->course_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="result_status" class="form-select rounded-2">
                    <option value="">All Statuses</option>
                    <option value="passed" {{ request('result_status') === 'passed' ? 'selected' : '' }}>Passed</option>
                    <option value="failed" {{ request('result_status') === 'failed' ? 'selected' : '' }}>Failed / Reappear</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-2 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('centre.exams.results') }}" class="btn btn-outline-secondary rounded-2" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-award me-2 text-primary"></i>Evaluated Candidate Roster
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $results->total() }} Records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete results table
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 820px;">
                <thead>
                    <tr>
                        <th class="ps-3">Roll & Registration No</th>
                        <th>Student Name</th>
                        <th>Exam Session</th>
                        <th>Course</th>
                        <th class="text-center">Total Score</th>
                        <th class="text-center">Percentage</th>
                        <th class="text-center">Grade</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $res)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-2">{{ $res->student?->roll_no }}</span>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $res->student?->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $res->student?->full_name }}</div>
                            <div class="small text-muted">Father: {{ $res->student?->father_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $res->exam?->exam_title ?? 'Session Exam' }}</div>
                            <div class="small text-muted">{{ $res->exam?->session_name ?? 'General Session' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-2">{{ $res->course?->course_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $res->course?->course_name }}</div>
                        </td>
                        <td class="text-center font-monospace fw-bold">
                            {{ number_format($res->total_marks_obtained, 1) }} / {{ number_format($res->total_max_marks, 1) }}
                        </td>
                        <td class="text-center fw-bold fs-6 {{ $res->percentage >= 40 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($res->percentage, 2) }}%
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $res->result_status === 'passed' ? 'bg-primary' : 'bg-secondary' }} rounded-2 font-monospace px-2 py-1 fs-6">
                                {{ $res->grade }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $res->result_status === 'passed' ? 'bg-success' : 'bg-danger' }} rounded-2">
                                <i class="fa-solid {{ $res->result_status === 'passed' ? 'fa-check' : 'fa-xmark' }} me-1"></i>{{ ucfirst($res->result_status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('centre.exams.results.marksheet', $res->id) }}" class="btn btn-outline-info rounded-start-2" target="_blank" title="View & Print Official Marksheet">
                                    <i class="fa-solid fa-file-invoice me-1"></i>Marksheet
                                </a>
                                @if($res->exam_id)
                                <a href="{{ route('centre.exams.marks', [$res->exam_id, 'student_id' => $res->student_id]) }}" class="btn btn-outline-secondary rounded-end-2" title="Edit Marks Evaluation">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-square-poll-horizontal fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Examination Results Found</h5>
                            <p class="small text-muted mb-3">No candidate results match your filter criteria or no evaluations have been recorded yet.</p>
                            <a href="{{ route('centre.exams.marks.index') }}" class="btn btn-sm btn-primary rounded-2 px-3 fw-semibold">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Enter Marks for Candidates
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="p-3 border-top">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
