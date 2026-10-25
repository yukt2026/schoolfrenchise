@extends('layouts.student')

@section('title', 'Batch Schedule & Timetable - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-calendar-days text-primary me-2"></i> Batch Schedule & Timetable
            </h4>
            <div class="text-muted small">Daily class timings, lab schedules, and weekly batch shift details</div>
        </div>
        <div>
            <a href="{{ route('student.attendance') }}" class="btn btn-outline-primary btn-sm rounded-2 px-3 fw-semibold">
                <i class="fa-solid fa-clipboard-user me-1"></i> View Attendance
            </a>
        </div>
    </div>

    @if($batch)
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="row align-items-center g-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 fw-bold fs-6 rounded-1">
                            BATCH CODE: {{ $batch->batch_code ?? 'BCH-REG' }}
                        </span>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 small">
                            <i class="fa-solid fa-circle-dot me-1"></i> Active Batch
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">{{ $batch->batch_name }}</h3>
                    <p class="text-muted mb-3">Enrolled in <strong>{{ $admission?->course?->course_name ?? 'Enrolled Program' }}</strong> under batch coordination at {{ $student->centre?->centre_name ?? 'Central Campus' }}.</p>

                    <div class="d-flex flex-wrap gap-4 text-secondary small">
                        <div>
                            <i class="fa-regular fa-clock text-primary me-1"></i>
                            Class Timings: <strong class="text-dark">{{ $batch->start_time ?? '10:00 AM' }} - {{ $batch->end_time ?? '12:00 PM' }}</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-calendar-week text-primary me-1"></i>
                            Schedule Type: <strong class="text-dark">Monday to Friday (Regular)</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <div class="text-muted small mb-1">Assigned Shift</div>
                        <h4 class="fw-bold text-primary mb-1">{{ $batch->batch_name }}</h4>
                        <span class="badge bg-white text-secondary border font-monospace small rounded-1">Room / Lab 1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Schedule Grid -->
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                    <i class="fa-solid fa-timeline text-primary me-2"></i> Weekly Routine & Lab Allocations
                </h5>
                <span class="badge bg-primary-subtle text-primary rounded-1">6 Days / Week</span>
            </div>
            <div class="card-body p-0">
                <div class="table-scroll-hint d-md-none">
                    <i class="fa-solid fa-arrows-left-right text-primary"></i>
                    <span>Scroll horizontally to view timings & subject focus</span>
                </div>
                <div class="table-responsive">
                    <table class="table-custom table-hover align-middle mb-0" style="min-width: 620px;">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 140px;">Day</th>
                                <th style="width: 170px;">Class Timing</th>
                                <th style="width: 170px;">Session Type</th>
                                <th>Subject Focus</th>
                                <th class="pe-4 text-end" style="width: 160px;">Faculty / Lab</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $days = [
                                    ['day' => 'Monday', 'type' => 'Theory Lecture', 'focus' => 'Core Programming & Logic', 'type_class' => 'bg-primary-subtle text-primary'],
                                    ['day' => 'Tuesday', 'type' => 'Practical Lab', 'focus' => 'Hands-on Coding & Execution', 'type_class' => 'bg-success-subtle text-success'],
                                    ['day' => 'Wednesday', 'type' => 'Theory Lecture', 'focus' => 'Database Design & Queries', 'type_class' => 'bg-primary-subtle text-primary'],
                                    ['day' => 'Thursday', 'type' => 'Practical Lab', 'focus' => 'System Administration & Tools', 'type_class' => 'bg-success-subtle text-success'],
                                    ['day' => 'Friday', 'type' => 'Doubt Clearing & Review', 'focus' => 'Weekly Assignment Assessment', 'type_class' => 'bg-warning-subtle text-warning'],
                                    ['day' => 'Saturday', 'type' => 'Self Practice / Project', 'focus' => 'Lab Machine Practice', 'type_class' => 'bg-info-subtle text-info'],
                                ];
                            @endphp
                            @foreach($days as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark text-nowrap">
                                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i> {{ $item['day'] }}
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="font-monospace small text-dark">
                                            <i class="fa-regular fa-clock me-1 text-muted"></i>
                                            {{ $batch->start_time ?? '10:00 AM' }} - {{ $batch->end_time ?? '12:00 PM' }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge {{ $item['type_class'] }} px-2.5 py-1 rounded-1 small">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="text-secondary small">{{ $item['focus'] }}</td>
                                    <td class="pe-4 text-end text-muted small text-nowrap">
                                        <span class="badge bg-light text-muted border rounded-1">Lab Terminal 1</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-5 text-center">
            <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark">No Batch Assigned Yet</h5>
            <p class="text-muted">A batch shift will be assigned by the centre administrator shortly.</p>
        </div>
    @endif
</div>
@endsection
