@extends('layouts.centre')

@section('title', 'Examination Sessions - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i>Examination Sessions & Schedules
        </h3>
        <p class="text-muted small mb-0">Central Board examination sessions, hall tickets / admit cards, and internal marks evaluation.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.exams.hall-tickets.index') }}" class="btn btn-outline-primary rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-ticket me-1"></i>Hall Tickets
        </a>
        <a href="{{ route('centre.exams.marks.index') }}" class="btn btn-outline-success rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-pen-to-square me-1"></i>Marks Entry
        </a>
        <a href="{{ route('centre.exams.results') }}" class="btn btn-outline-info rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-square-poll-vertical me-1"></i>Exam Results
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.exams.index') }}" class="row g-2">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-2" placeholder="Search by Exam Title, Exam Code, Session..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select rounded-2">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="evaluated" {{ request('status') === 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-2 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('centre.exams.index') }}" class="btn btn-outline-secondary rounded-2" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Exams Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Active Board Examination Cycles
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $exams->total() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete table
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 720px;">
                <thead>
                    <tr>
                        <th class="ps-3">Exam Code</th>
                        <th>Exam Session Title</th>
                        <th>Target Course</th>
                        <th>Date of Examination</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-secondary-subtle text-dark font-monospace rounded-2">{{ $exam->exam_code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $exam->exam_title }}</div>
                            <div class="small text-muted">{{ $exam->session_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-2">{{ $exam->course?->course_code }}</span>
                            <div class="small text-muted">{{ $exam->course?->course_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'Announced' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $exam->status === 'published' ? 'bg-success' : ($exam->status === 'evaluated' ? 'bg-info' : 'bg-warning text-dark') }} rounded-2 text-capitalize">
                                {{ $exam->status }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('centre.exams.hall-tickets', $exam->id) }}" class="btn btn-outline-primary rounded-start-2" target="_blank" title="Print Hall Tickets">
                                    <i class="fa-solid fa-ticket me-1"></i>Hall Tickets
                                </a>
                                <a href="{{ route('centre.exams.marks', $exam->id) }}" class="btn btn-outline-success rounded-end-2" title="Enter Marks">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Marks Entry
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-question fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Examination Sessions Active</h5>
                            <p class="small text-muted mb-0">Upcoming examination sessions scheduled by the Head Office will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
        <div class="p-3 border-top">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
