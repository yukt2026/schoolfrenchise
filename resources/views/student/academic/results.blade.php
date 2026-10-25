@extends('layouts.student')

@section('title', 'Examination Results - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Results</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-square-poll-vertical text-primary me-2"></i>Results & Performance
        </h2>
        <p class="text-muted small mb-0">Review declared examination scores, grading assessments, and official marksheet records.</p>
    </div>
    @if($admission && $admission->course)
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-1 fs-7">
            <i class="fa-solid fa-graduation-cap me-1"></i> {{ $admission->course->course_name }}
        </span>
    @endif
</div>

<!-- Performance Summary Row -->
@php
    $totalExams = $results->total();
    $passedCount = $results->where('result_status', 'passed')->count();
    $avgPercentage = $results->avg('percentage');
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-primary-subtle text-primary">
                    <i class="fa-solid fa-pen-to-square fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Tests Taken</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $totalExams }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Passed Exams</div>
                    <div class="h4 fw-bold text-success mb-0">{{ $passedCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-info-subtle text-info">
                    <i class="fa-solid fa-chart-line fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Average Score</div>
                    <div class="h4 fw-bold text-info mb-0">{{ $avgPercentage ? number_format($avgPercentage, 1) . '%' : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table Card -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 bg-white">
    <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold fs-6 mb-0 text-dark">
                <i class="fa-solid fa-clipboard-list text-primary me-2"></i>Declared Examination Results
            </h5>
            <span class="badge bg-primary-subtle text-primary border px-2 py-1 small rounded-1">
                Records: {{ $results->total() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($results->count() > 0)
            <div class="table-scroll-hint d-md-none">
                <i class="fa-solid fa-arrows-left-right text-primary"></i>
                <span>Scroll horizontally to view marks & marksheets</span>
            </div>
            <div class="table-responsive">
                <table class="table-custom table-hover align-middle mb-0" style="min-width: 640px;">
                    <thead>
                        <tr>
                            <th class="ps-4">Exam & Course</th>
                            <th class="text-center">Marks Obtained</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">Result Status</th>
                            <th class="pe-4 text-end">Marksheet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $res)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $res->exam->title ?? ($res->exam->exam_title ?? 'Course Examination') }}</div>
                                    <div class="small text-muted">{{ $res->course->course_name ?? 'Enrolled Course' }}</div>
                                    @if($res->approved_at)
                                        <div class="text-muted small text-nowrap" style="font-size: 0.72rem;">
                                            <i class="fa-regular fa-calendar-check me-1 text-primary"></i> Declared: {{ \Carbon\Carbon::parse($res->approved_at)->format('d M, Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="fw-bold text-dark">{{ (float)$res->total_marks_obtained }} / {{ (float)$res->total_max_marks }}</div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="fw-bold text-primary">{{ number_format($res->percentage, 1) }}%</div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1 fw-bold fs-7 rounded-1">
                                        {{ $res->grade ?? 'A' }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($res->result_status === 'passed')
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-check-circle me-1"></i> PASSED
                                        </span>
                                    @elseif($res->result_status === 'failed')
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> FAILED
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-clock me-1"></i> WITHHELD
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a href="{{ route('student.marksheet', $res->id) }}" class="btn btn-sm btn-outline-primary rounded-2 fw-semibold text-nowrap">
                                        <i class="fa-solid fa-file-invoice me-1"></i> View Marksheet
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $results->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-square-poll-vertical text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Examination Results Published Yet</h5>
                <p class="text-muted small mb-0" style="max-width: 400px; margin: 0 auto;">
                    Once examinations have been evaluated and approved by the academic council, your verified marksheet will appear here.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
