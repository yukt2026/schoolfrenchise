@extends('layouts.student')

@section('title', 'Examinations & Hall Tickets - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Examinations</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-pen-nib text-primary me-2"></i>Examinations & Hall Tickets
        </h2>
        <p class="text-muted small mb-0">Check upcoming exam schedules, download official admit cards, and review conducted test papers.</p>
    </div>
    @if($admission && $admission->course)
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-1 fs-7">
            <i class="fa-solid fa-graduation-cap me-1"></i> {{ $admission->course->course_name }}
        </span>
    @endif
</div>

<!-- Exam Table Card -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 bg-white">
    <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold fs-6 mb-0 text-dark">
                <i class="fa-solid fa-calendar-check text-primary me-2"></i>Exam Schedule & Hall Tickets
            </h5>
            <span class="badge bg-primary-subtle text-primary border px-2 py-1 small rounded-1">
                Total Exams: {{ $exams->total() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($exams->count() > 0)
            <div class="table-scroll-hint d-md-none">
                <i class="fa-solid fa-arrows-left-right text-primary"></i>
                <span>Scroll horizontally to view exam dates & admit cards</span>
            </div>
            <div class="table-responsive">
                <table class="table-custom table-hover align-middle mb-0" style="min-width: 680px;">
                    <thead>
                        <tr>
                            <th class="ps-4">Exam Name & Code</th>
                            <th>Course / Subject</th>
                            <th>Date & Time</th>
                            <th class="text-center">Marks</th>
                            <th class="text-center">Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            @php
                                $examDate = \Carbon\Carbon::parse($exam->exam_date);
                                $isPast = $examDate->isPast();
                                $hasAdmitCard = isset($admitCards[$exam->id]);
                                $hasResult = isset($results[$exam->id]);
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $exam->title ?? $exam->exam_title }}</div>
                                    <div class="small text-muted font-monospace">{{ $exam->exam_code ?? 'EXAM-'.$exam->id }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-1">{{ $exam->course->course_name ?? 'Universal' }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark text-nowrap">
                                        <i class="fa-regular fa-calendar me-1 text-primary"></i> {{ $examDate->format('d M, Y') }}
                                    </div>
                                    <div class="small text-muted text-nowrap">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('h:i A') : 'TBA' }} 
                                        @if($exam->duration_minutes) ({{ $exam->duration_minutes }} Mins) @endif
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-1">
                                        Max: {{ $exam->total_marks ?? 100 }} | Pass: {{ $exam->passing_marks ?? 40 }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($hasResult)
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Result Declared
                                        </span>
                                    @elseif($isPast)
                                        <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Concluded
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-calendar-day me-1"></i> Upcoming
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('student.admit-card', $exam->id) }}" class="btn btn-sm btn-outline-primary rounded-2 fw-semibold text-nowrap" title="View & Print Admit Card">
                                            <i class="fa-solid fa-id-card-clip me-1"></i> Admit Card
                                        </a>
                                        @if($hasResult)
                                            <a href="{{ route('student.results') }}" class="btn btn-sm btn-primary rounded-2 fw-semibold text-nowrap" title="View Result">
                                                <i class="fa-solid fa-square-poll-vertical me-1"></i> Result
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $exams->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fa-regular fa-calendar-xmark text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Examination Schedules Active</h5>
                <p class="text-muted small mb-0" style="max-width: 400px; margin: 0 auto;">
                    There are no scheduled examinations or hall tickets assigned to your batch at this moment. You will be notified when dates are announced.
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Examination Instructions Card -->
<div class="card border border-light-subtle shadow-sm rounded-3 bg-light">
    <div class="card-body p-4">
        <h6 class="fw-bold text-dark mb-2">
            <i class="fa-solid fa-circle-info text-primary me-2"></i>Key Examination Rules & Guidelines
        </h6>
        <ul class="text-muted small mb-0 ps-3">
            <li class="mb-1">Candidates must carry their <strong>Printed Admit Card</strong> and <strong>Student Identity Card</strong> into the examination hall.</li>
            <li class="mb-1">Report to the examination lab at least <strong>15 minutes</strong> before the scheduled commencement time.</li>
            <li class="mb-1">Electronic gadgets, smartwatches, and unauthorized notes are strictly prohibited inside the terminal room.</li>
            <li>In case of technical issues during practical online tests, notify the centre invigilator immediately.</li>
        </ul>
    </div>
</div>
@endsection
