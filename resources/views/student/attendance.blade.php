@extends('layouts.student')

@section('title', 'Attendance Ledger - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-clipboard-user text-primary me-2"></i> Attendance Record & Ledger
            </h4>
            <div class="text-muted small">Daily attendance records, cumulative percentage, and session status</div>
        </div>
        <div>
            <span class="badge {{ $attendanceRate >= 75 ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2 rounded-2 fw-semibold">
                {{ $attendanceRate }}% Cumulative Attendance
            </span>
        </div>
    </div>

    <!-- Attendance Metric Summary Cards -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 p-sm-3 p-md-4 bg-white text-center h-100 border-start border-4 border-dark">
                <div class="text-muted small text-uppercase" style="font-size: 0.72rem;">Total Classes</div>
                <h3 class="fw-bold text-dark mt-1 mt-md-2 mb-0 fs-4 fs-md-3">{{ $totalSessions }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 p-sm-3 p-md-4 bg-white text-center h-100 border-start border-4 border-success">
                <div class="text-success small text-uppercase fw-semibold" style="font-size: 0.72rem;">Present</div>
                <h3 class="fw-bold text-success mt-1 mt-md-2 mb-0 fs-4 fs-md-3">{{ $presentSessions }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 p-sm-3 p-md-4 bg-white text-center h-100 border-start border-4 border-danger">
                <div class="text-danger small text-uppercase fw-semibold" style="font-size: 0.72rem;">Absent</div>
                <h3 class="fw-bold text-danger mt-1 mt-md-2 mb-0 fs-4 fs-md-3">{{ $absentSessions }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 p-sm-3 p-md-4 bg-white text-center h-100 border-start border-4 border-warning">
                <div class="text-warning small text-uppercase fw-semibold" style="font-size: 0.72rem;">Late / Leave</div>
                <h3 class="fw-bold text-warning mt-1 mt-md-2 mb-0 fs-4 fs-md-3">{{ $lateSessions }}</h3>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card border border-light-subtle shadow-sm rounded-3 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Attendance Log History
            </h5>
            <span class="badge bg-primary-subtle text-primary rounded-1">Showing {{ $attendances->count() }} records</span>
        </div>
        <div class="card-body p-0">
            @if($attendances->count() > 0)
                <div class="table-scroll-hint d-md-none">
                    <i class="fa-solid fa-arrows-left-right text-primary"></i>
                    <span>Scroll horizontally to view attendance remarks</span>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table-custom table-hover align-middle mb-0" style="min-width: 480px;">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 160px;">Date</th>
                            <th style="width: 120px;">Day</th>
                            <th style="width: 140px;">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $record)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark text-nowrap">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $record->attendance_date ? $record->attendance_date->format('d M, Y') : 'N/A' }}
                                </td>
                                <td class="text-muted small text-nowrap">
                                    {{ $record->attendance_date ? $record->attendance_date->format('l') : 'N/A' }}
                                </td>
                                <td>
                                    @if($record->status === 'present')
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-check me-1"></i> Present
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-clock me-1"></i> Late
                                        </span>
                                    @elseif($record->status === 'excused' || $record->status === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-envelope-open-text me-1"></i> Excused
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-xmark me-1"></i> Absent
                                        </span>
                                    @endif
                                </td>
                                <td class="text-secondary small">
                                    {{ $record->remarks ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clipboard-user fa-2x mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0">No attendance records logged yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendances->hasPages())
                <div class="p-3 border-top">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
