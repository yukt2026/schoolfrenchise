@extends('layouts.centre')

@section('title', 'Attendance Analytics & Date-Wise Report - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex gap-2 mb-2">
            <a href="{{ route('centre.batches.index') }}" class="btn btn-outline-secondary btn-sm rounded-2">
                <i class="fa-solid fa-arrow-left me-1"></i>Batches
            </a>
            <a href="{{ route('centre.attendance.index') }}" class="btn btn-outline-primary btn-sm rounded-2">
                <i class="fa-solid fa-calendar-day me-1"></i>Daily Hub
            </a>
        </div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Date-Wise Attendance Report
        </h3>
        <p class="text-muted small mb-0">Detailed date-wise log, student regularity analysis, and classroom attendance audit.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.batches.attendance.report.export', request()->query()) }}" class="btn btn-success btn-sm rounded-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i>Export CSV
        </a>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-print me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- 1. Top KPI Summary Cards -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-primary h-100">
            <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Entries</div>
            <h3 class="fw-bold text-dark mb-0 fs-4 fs-md-3 mt-1">{{ number_format($totalSessions) }}</h3>
            <div class="text-muted small" style="font-size: 0.75rem;">Recorded in date range</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-success h-100">
            <div class="text-success small fw-bold text-uppercase" style="font-size: 0.72rem;">Present Sessions</div>
            <h3 class="fw-bold text-success mb-0 fs-4 fs-md-3 mt-1">{{ number_format($presentCount) }}</h3>
            <div class="text-success small fw-semibold" style="font-size: 0.75rem;">{{ $overallRate }}% Attendance Rate</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-danger h-100">
            <div class="text-danger small fw-bold text-uppercase" style="font-size: 0.72rem;">Absent Sessions</div>
            <h3 class="fw-bold text-danger mb-0 fs-4 fs-md-3 mt-1">{{ number_format($absentCount) }}</h3>
            <div class="text-muted small" style="font-size: 0.75rem;">Unattended classes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-warning h-100">
            <div class="text-warning small fw-bold text-uppercase" style="font-size: 0.72rem;">Late / Excused</div>
            <h3 class="fw-bold text-warning mb-0 fs-4 fs-md-3 mt-1">{{ number_format($lateCount) }}</h3>
            <div class="text-muted small" style="font-size: 0.75rem;">Approved leave & late entries</div>
        </div>
    </div>
</div>

<!-- 2. Date-Wise Filter & View Mode Controls -->
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.batches.attendance.report') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Batch / Shift</label>
                <select name="batch_id" class="form-select form-select-sm rounded-2">
                    <option value="">All Batches & Shifts</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                            {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                <input type="date" name="date_from" class="form-control form-select-sm rounded-2" value="{{ $dateFrom }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                <input type="date" name="date_to" class="form-control form-select-sm rounded-2" value="{{ $dateTo }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm rounded-2">
                    <option value="">All Statuses</option>
                    <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="excused" {{ $status == 'excused' ? 'selected' : '' }}>Excused / Leave</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-2 fw-semibold flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i>Apply Filter
                </button>
                <a href="{{ route('centre.batches.attendance.report') }}" class="btn btn-outline-secondary btn-sm rounded-2" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 3. Report View Mode Tabs -->
<ul class="nav nav-pills mb-3 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ request('view', 'daily_log') === 'daily_log' ? 'active fw-bold' : 'bg-white border' }} rounded-2 py-1.5 px-3 small" 
           href="{{ request()->fullUrlWithQuery(['view' => 'daily_log']) }}">
            <i class="fa-solid fa-list-ol me-1.5"></i>Date-Wise Daily Log
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('view') === 'summary' ? 'active fw-bold' : 'bg-white border' }} rounded-2 py-1.5 px-3 small" 
           href="{{ request()->fullUrlWithQuery(['view' => 'summary']) }}">
            <i class="fa-solid fa-chart-pie me-1.5"></i>Student Regularity Summary
        </a>
    </li>
    @if($selectedBatchId)
    <li class="nav-item">
        <a class="nav-link {{ request('view') === 'matrix' ? 'active fw-bold' : 'bg-white border' }} rounded-2 py-1.5 px-3 small" 
           href="{{ request()->fullUrlWithQuery(['view' => 'matrix']) }}">
            <i class="fa-solid fa-table-cells me-1.5"></i>Day-by-Day Register Matrix
        </a>
    </li>
    @endif
</ul>

<!-- 4. VIEW CONTENT -->
@if(request('view', 'daily_log') === 'daily_log')
    <!-- TAB 1: Date-Wise Daily Log -->
    <div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-calendar-check text-primary me-2"></i>Date-Wise Attendance Log &bull; {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            </h6>
            <span class="badge bg-primary-subtle text-primary rounded-1">Showing {{ $attendanceLogs->count() }} of {{ $attendanceLogs->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                    <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete records
                </div>
                <table class="table table-custom table-hover align-middle mb-0" style="min-width: 680px;">
                    <thead>
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Day</th>
                            <th>Roll Number</th>
                            <th>Student Full Name</th>
                            <th>Batch Shift</th>
                            <th class="text-center">Status</th>
                            <th class="pe-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceLogs as $log)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark text-nowrap">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $log->attendance_date ? $log->attendance_date->format('d M, Y') : 'N/A' }}
                                </td>
                                <td class="text-muted small text-nowrap">
                                    {{ $log->attendance_date ? $log->attendance_date->format('l') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary font-monospace rounded-1 px-2 py-1">
                                        {{ $log->student?->roll_no ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $log->student?->full_name ?? 'N/A' }}</div>
                                    <div class="text-muted small" style="font-size: 0.70rem;">Reg: {{ $log->student?->registration_no }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border font-monospace small rounded-1">
                                        {{ $log->batch?->batch_name ?? 'General' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($log->status === 'present')
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-check me-1"></i> Present
                                        </span>
                                    @elseif($log->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-clock me-1"></i> Late
                                        </span>
                                    @elseif($log->status === 'excused' || $log->status === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-envelope-open-text me-1"></i> Excused
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-xmark me-1"></i> Absent
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 small text-muted">
                                    {{ $log->remarks ?: '&mdash;' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clipboard-user fa-2x mb-2 text-secondary opacity-50"></i>
                                    <div>No attendance records found for the selected date range.</div>
                                    <a href="{{ route('centre.attendance.index') }}" class="btn btn-sm btn-primary mt-2 rounded-2 fw-semibold">
                                        Go to Daily Attendance Hub to Mark
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendanceLogs->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $attendanceLogs->links() }}
                </div>
            @endif
        </div>
    </div>

@elseif(request('view') === 'matrix' && $batchReport)
    <!-- TAB 3: Day-by-Day Register Matrix Grid -->
    <div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-table-cells text-primary me-2"></i>Day-by-Day Attendance Matrix &bull; {{ $batchReport['batch']->batch_name }}
            </h6>
            <div class="small text-muted">
                <span class="badge bg-success-subtle text-success me-1">P = Present</span>
                <span class="badge bg-danger-subtle text-danger me-1">A = Absent</span>
                <span class="badge bg-warning-subtle text-warning me-1">L = Late</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0 text-center" style="font-size: 0.76rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-start" style="min-width: 160px;">Student Name</th>
                            <th style="min-width: 100px;">Roll No</th>
                            @foreach($daysInPeriod as $day)
                                <th class="p-1 {{ $day->isSunday() ? 'bg-danger-subtle text-danger' : '' }}" style="min-width: 30px;">
                                    <div>{{ $day->format('d') }}</div>
                                    <div style="font-size: 0.62rem;">{{ substr($day->format('D'), 0, 1) }}</div>
                                </th>
                            @endforeach
                            <th class="pe-3" style="min-width: 60px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batchReport['students'] as $student)
                            @php
                                $records = $batchReport['attendances']->get($student->id) ?? collect();
                                $recordsByDate = $records->keyBy(function($item) {
                                    return $item->attendance_date ? $item->attendance_date->format('Y-m-d') : '';
                                });
                                $presentCountStudent = $records->where('status', 'present')->count();
                            @endphp
                            <tr>
                                <td class="ps-3 text-start fw-bold text-dark text-nowrap">{{ $student->full_name }}</td>
                                <td class="font-monospace text-muted small">{{ $student->roll_no }}</td>
                                @foreach($daysInPeriod as $day)
                                    @php
                                        $dStr = $day->format('Y-m-d');
                                        $rec = $recordsByDate->get($dStr);
                                    @endphp
                                    <td class="p-1 {{ $day->isSunday() ? 'bg-light text-muted' : '' }}">
                                        @if($rec)
                                            @if($rec->status === 'present')
                                                <span class="badge bg-success text-white px-1 py-0.5 rounded-1" style="font-size: 0.65rem;">P</span>
                                            @elseif($rec->status === 'late')
                                                <span class="badge bg-warning text-dark px-1 py-0.5 rounded-1" style="font-size: 0.65rem;">L</span>
                                            @else
                                                <span class="badge bg-danger text-white px-1 py-0.5 rounded-1" style="font-size: 0.65rem;">A</span>
                                            @endif
                                        @else
                                            <span class="text-muted opacity-50">&bull;</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="pe-3 fw-bold text-success">{{ $presentCountStudent }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($daysInPeriod) + 3 }}" class="text-center py-4 text-muted">No students assigned to this batch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@else
    <!-- TAB 2: Student Regularity Summary -->
    <div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-chart-pie text-primary me-2"></i>Student Regularity & Attendance Percentage &bull; {{ $batchReport ? $batchReport['batch']->batch_name : 'All Centre Students' }}
            </h6>
        </div>
        <div class="card-body p-0">
            @if($batchReport)
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0" style="min-width: 680px;">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late / Leave</th>
                                <th class="pe-4">Regularity (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batchReport['students'] as $idx => $student)
                            @php
                                $records = $batchReport['attendances']->get($student->id) ?? collect();
                                $pCount = $records->where('status', 'present')->count();
                                $aCount = $records->where('status', 'absent')->count();
                                $lCount = $records->whereIn('status', ['late', 'excused', 'leave'])->count();
                                $tot = $records->count();
                                $pct = $tot > 0 ? round(($pCount / $tot) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted fw-bold">{{ $idx + 1 }}</td>
                                <td><span class="badge bg-primary-subtle text-primary font-monospace rounded-1">{{ $student->roll_no }}</span></td>
                                <td class="fw-bold text-dark">{{ $student->full_name }}</td>
                                <td class="text-center text-success fw-bold">{{ $pCount }}</td>
                                <td class="text-center text-danger fw-bold">{{ $aCount }}</td>
                                <td class="text-center text-muted">{{ $lCount }}</td>
                                <td class="pe-4" style="min-width: 150px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $pct }}%;"></div>
                                        </div>
                                        <span class="small fw-bold">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No students assigned to this batch.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-chart-column fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                    <h5>Select a specific Batch / Shift from the filter above to generate the student regularity summary.</h5>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
