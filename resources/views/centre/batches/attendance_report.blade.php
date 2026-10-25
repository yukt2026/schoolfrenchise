@extends('layouts.centre')

@section('title', 'Attendance Analytics & Monthly Report - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.batches.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Batches
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-chart-line text-primary me-2"></i>Batch Attendance Report
        </h3>
        <p class="text-muted small mb-0">Monthly attendance summary and regularity percentage for centre students.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.batches.attendance.report') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="batch_id" class="form-select rounded-2" required>
                    <option value="">Select Batch / Shift</option>
                    @foreach($batches as $b)
                    <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                        {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="month" name="month" class="form-control rounded-2" value="{{ $month }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-2">
                    <i class="fa-solid fa-filter me-1"></i>Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

@if($reportData)
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-calendar-check text-success me-2"></i>
            {{ $reportData['batch']->batch_name }} &mdash; {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
        </h6>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary rounded-2">
            <i class="fa-solid fa-print me-1"></i>Print Report
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete report
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 680px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Late / Leave</th>
                        <th>Regularity (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['students'] as $idx => $student)
                    @php
                        $records = $reportData['attendances']->get($student->id) ?? collect();
                        $present = $records->where('status', 'present')->count();
                        $absent = $records->where('status', 'absent')->count();
                        $other = $records->whereIn('status', ['late', 'leave'])->count();
                        $total = $records->count();
                        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                        <td><span class="badge bg-primary-subtle text-primary font-monospace rounded-2">{{ $student->roll_no }}</span></td>
                        <td class="fw-bold text-dark">{{ $student->full_name }}</td>
                        <td class="text-center text-success fw-bold">{{ $present }}</td>
                        <td class="text-center text-danger fw-bold">{{ $absent }}</td>
                        <td class="text-center text-muted">{{ $other }}</td>
                        <td style="min-width: 140px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $percentage }}%;"></div>
                                </div>
                                <span class="small fw-bold">{{ $percentage }}%</span>
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
    </div>
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-chart-column fs-1 text-black-50 mb-3 d-block"></i>
    <h5>Select a batch and month above to generate the attendance summary report.</h5>
</div>
@endif
@endsection
