@extends('layouts.centre')

@section('title', 'Centre Batches & Timetable - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-users text-info me-2"></i>Batches & Shifts Timetable
        </h3>
        <p class="text-muted small mb-0">Manage training batches, lab workstations schedule, and daily student attendance.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.batches.create') }}" class="btn btn-primary rounded-2 px-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i>Create New Batch
        </a>
        <a href="{{ route('centre.batches.attendance.report') }}" class="btn btn-outline-secondary rounded-2 px-3">
            <i class="fa-solid fa-chart-line me-1"></i>Attendance Report
        </a>
    </div>
</div>

<!-- Batches Grid -->
<div class="row g-2 g-md-4">
    @forelse($batches as $batch)
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 shadow-sm h-100 hover-card">
            <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="badge {{ $batch->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-2" style="font-size: 0.65rem;">
                    {{ ucfirst($batch->status) }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('centre.batches.attendance', $batch->id) }}">
                                <i class="fa-solid fa-calendar-check text-success me-2"></i>Daily Attendance Sheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('centre.batches.edit', $batch->id) }}">
                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Batch Schedule
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-2.5 p-sm-3 p-md-4 d-flex flex-column">
                <h6 class="fw-bold text-dark mb-1 small fs-md-6 text-truncate">{{ $batch->batch_name }}</h6>
                <div class="text-primary small fw-semibold mb-2 mb-md-3 text-truncate" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-graduation-cap me-1"></i>{{ $batch->course?->course_name ?? 'All Courses' }}
                </div>

                <div class="bg-light p-2 p-md-3 rounded-2 small mb-2 mb-md-3" style="font-size: 0.72rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted"><i class="fa-solid fa-clock me-1"></i>Timing:</span>
                        <span class="fw-bold text-dark">{{ $batch->start_time }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 d-none d-sm-flex">
                        <span class="text-muted"><i class="fa-solid fa-calendar-days me-1"></i>Days:</span>
                        <span class="fw-semibold text-dark">{{ $batch->days }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="fa-solid fa-users-line me-1"></i>Students:</span>
                        <span class="fw-bold text-primary">{{ $batch->admissions_count }}</span>
                    </div>
                </div>

                <div class="mt-auto">
                    <a href="{{ route('centre.batches.attendance', $batch->id) }}" class="btn btn-outline-primary w-100 rounded-2 fw-semibold py-1" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-clipboard-user me-1"></i>Attendance
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fa-solid fa-calendar-xmark fs-1 text-black-50 mb-3 d-block"></i>
        <h5>No Batches Created Yet</h5>
        <p class="text-muted small mb-3">Organize your students into morning, afternoon or evening lab batches.</p>
        <a href="{{ route('centre.batches.create') }}" class="btn btn-primary rounded-2 px-4">
            <i class="fa-solid fa-plus me-1"></i>Create First Batch
        </a>
    </div>
    @endforelse
</div>

@if($batches->hasPages())
<div class="mt-4">
    {{ $batches->links() }}
</div>
@endif
@endsection
