@extends('layouts.centre')

@section('title', 'Daily Attendance Hub - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Daily Classroom Attendance Hub
        </h3>
        <p class="text-muted small mb-0">Select class shift, enroll students into batches, and mark daily attendance records.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Date Selector Form -->
        <form method="GET" action="{{ route('centre.attendance.index') }}" class="d-flex align-items-center gap-2 bg-white p-1 rounded-2 border shadow-sm">
            <span class="text-muted small ps-2 fw-semibold"><i class="fa-regular fa-calendar me-1"></i> Date:</span>
            <input type="date" name="date" class="form-control form-control-sm border-0 font-monospace fw-bold text-primary" value="{{ $attendanceDate }}" onchange="this.form.submit()">
        </form>

        <a href="{{ route('centre.batches.create') }}" class="btn btn-primary btn-sm rounded-2 px-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New Batch
        </a>
        <a href="{{ route('centre.batches.attendance.report') }}" class="btn btn-outline-secondary btn-sm rounded-2 px-3">
            <i class="fa-solid fa-chart-line me-1"></i> Reports
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-primary">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Active Batches</div>
            <h3 class="fw-bold text-dark my-1 font-monospace">{{ $stats['total_batches'] }}</h3>
            <div class="small text-muted" style="font-size: 0.72rem;">Training shifts configured</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-info">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Enrolled in Batches</div>
            <h3 class="fw-bold text-info my-1 font-monospace">{{ $stats['enrolled_in_batches'] }} / {{ $stats['total_students'] }}</h3>
            <div class="small text-muted" style="font-size: 0.72rem;">Assigned to class shifts</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-success">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Marked on Date</div>
            <h3 class="fw-bold text-success my-1 font-monospace">{{ $stats['marked_today'] }}</h3>
            <div class="small text-muted" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($attendanceDate)->format('d M, Y') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 border-start border-4 border-warning">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Active Date</div>
            <h4 class="fw-bold text-dark my-1 font-monospace fs-6">{{ \Carbon\Carbon::parse($attendanceDate)->format('d M, Y') }}</h4>
            <div class="small text-muted" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($attendanceDate)->format('l') }}</div>
        </div>
    </div>
</div>

<!-- Batches Attendance Roster -->
<div class="row g-4">
    @forelse($batches as $batch)
        @php
            $todayAtt = $attendancesByBatch->get($batch->id);
            $isMarked = !empty($todayAtt) && $todayAtt->count() > 0;
            $presentCount = $isMarked ? $todayAtt->where('status', 'present')->count() : 0;
            $totalMarked = $isMarked ? $todayAtt->count() : 0;
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-card {{ $isMarked ? 'border-top border-3 border-success' : 'border-top border-3 border-warning' }}">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge {{ $batch->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-2" style="font-size: 0.68rem;">
                            {{ ucfirst($batch->status) }}
                        </span>
                        <span class="badge bg-light text-dark border ms-1 font-monospace" style="font-size: 0.68rem;">
                            <i class="fa-solid fa-users me-1"></i>{{ $batch->admissions_count }} Students
                        </span>
                    </div>
                    @if($isMarked)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-2 small">
                            <i class="fa-solid fa-circle-check me-1"></i>Marked ({{ $presentCount }}/{{ $totalMarked }} Present)
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-2 small">
                            <i class="fa-solid fa-clock me-1"></i>Attendance Pending
                        </span>
                    @endif
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1 text-truncate">{{ $batch->batch_name }}</h5>
                    <div class="text-primary small fw-semibold mb-3 text-truncate">
                        <i class="fa-solid fa-graduation-cap me-1"></i>{{ $batch->course?->course_name ?? 'All Courses' }}
                    </div>

                    <div class="bg-light p-3 rounded-2 small mb-3">
                        <div class="d-flex justify-content-between mb-1.5">
                            <span class="text-muted"><i class="fa-regular fa-clock me-1"></i>Timing:</span>
                            <span class="fw-bold text-dark font-monospace">{{ $batch->start_time }} - {{ $batch->end_time }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1.5">
                            <span class="text-muted"><i class="fa-regular fa-calendar-days me-1"></i>Schedule:</span>
                            <span class="fw-semibold text-dark">{{ $batch->days }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-solid fa-user-check me-1"></i>Enrolled Students:</span>
                            <strong class="{{ $batch->admissions_count > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $batch->admissions_count }} Candidates
                            </strong>
                        </div>
                    </div>

                    @if($batch->admissions_count == 0)
                        <div class="alert alert-warning border-0 py-2 px-3 small rounded-2 mb-3">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i><strong>0 Students Assigned!</strong> Enroll students to mark daily attendance.
                        </div>
                    @endif

                    <div class="mt-auto d-flex flex-column gap-2">
                        <!-- Mark Attendance Button -->
                        <a href="{{ route('centre.batches.attendance', [$batch->id, 'date' => $attendanceDate]) }}" class="btn {{ $isMarked ? 'btn-outline-success' : 'btn-success' }} w-100 rounded-2 fw-bold py-2 shadow-sm">
                            <i class="fa-solid fa-clipboard-user me-1"></i>
                            {{ $isMarked ? 'Update Attendance Sheet' : 'Mark Daily Attendance' }}
                        </a>

                        <!-- Enroll / Manage Students Button -->
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-2 fw-semibold py-1.5" data-bs-toggle="modal" data-bs-target="#enrollModal{{ $batch->id }}">
                            <i class="fa-solid fa-user-plus me-1"></i>Enroll / Manage Students ({{ $batch->admissions_count }})
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ENROLL STUDENTS MODAL FOR THIS BATCH -->
        <div class="modal fade" id="enrollModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form method="POST" action="{{ route('centre.batches.enroll', $batch->id) }}" class="modal-content border-0 shadow">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <div>
                            <h6 class="modal-title fw-bold mb-0">
                                <i class="fa-solid fa-user-plus me-2"></i>Enroll Students into [{{ $batch->batch_name }}]
                            </h6>
                            <div class="small text-white-50">Course: {{ $batch->course?->course_name ?? 'General' }} | Shift: {{ $batch->start_time }} - {{ $batch->end_time }}</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <p class="text-muted small mb-3">
                            Select students from your study centre to assign them to this batch. Once enrolled, their names will automatically appear on the daily attendance sheet.
                        </p>

                        <!-- Currently Enrolled Strip -->
                        @if($batch->admissions->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Currently Enrolled in this Batch ({{ $batch->admissions->count() }}):</label>
                                <div class="d-flex flex-wrap gap-1 border p-2 rounded bg-light" style="max-height: 120px; overflow-y: auto;">
                                    @foreach($batch->admissions as $adm)
                                        <span class="badge bg-white border text-dark d-inline-flex align-items-center gap-1 p-1.5 small">
                                            <i class="fa-solid fa-user-check text-success"></i>
                                            {{ $adm->student?->full_name }} ({{ $adm->student?->roll_no }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Student Selection Table -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-dark text-uppercase mb-0">Select Students to Enroll / Assign:</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="toggleSelectAll('batch_{{ $batch->id }}')">
                                Select All
                            </button>
                        </div>

                        <div class="table-responsive border rounded" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 40px;" class="ps-3">
                                            <input type="checkbox" class="form-check-input" id="selectAll_batch_{{ $batch->id }}" onchange="toggleSelectAll('batch_{{ $batch->id }}', this.checked)">
                                        </th>
                                        <th>Roll & Reg No</th>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Current Batch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allStudents as $st)
                                        @php
                                            $currBatchId = $st->admissions->first()?->batch_id;
                                            $isThisBatch = ($currBatchId == $batch->id);
                                        @endphp
                                        <tr class="{{ $isThisBatch ? 'table-success bg-opacity-10' : '' }}">
                                            <td class="ps-3">
                                                <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" class="form-check-input chk-batch_{{ $batch->id }}" {{ $isThisBatch ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <span class="font-monospace fw-bold text-dark">{{ $st->roll_no }}</span>
                                                <div class="text-muted" style="font-size: 10px;">{{ $st->registration_no }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $st->full_name }}</div>
                                                <div class="text-muted" style="font-size: 10px;">Father: {{ $st->father_name }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-primary border">{{ $st->admissions->first()?->course?->course_name ?? 'Enrolled' }}</span>
                                            </td>
                                            <td>
                                                @if($isThisBatch)
                                                    <span class="badge bg-success-subtle text-success">This Batch</span>
                                                @elseif($currBatchId)
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $st->admissions->first()?->batch?->batch_name ?? 'Other Batch' }}</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Not Assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                No active students found in this centre.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-2 d-flex justify-content-between">
                        <span class="small text-muted ps-2">Tip: You can re-assign students from other batches anytime.</span>
                        <div>
                            <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                                <i class="fa-solid fa-user-plus me-1"></i> Enroll Selected Students
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="p-4 bg-white rounded-3 shadow-sm d-inline-block" style="max-width: 480px;">
                <i class="fa-solid fa-calendar-xmark text-secondary opacity-50 fa-3x mb-3 d-block"></i>
                <h5 class="fw-bold text-dark mb-2">No Batches Configured Yet</h5>
                <p class="text-muted small mb-3">To start marking daily attendance, create your class shifts (e.g. Morning 08-10 AM, Evening 04-06 PM) and assign students to them.</p>
                <a href="{{ route('centre.batches.create') }}" class="btn btn-primary px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Create First Batch Now
                </a>
            </div>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
function toggleSelectAll(batchKey, forceState = null) {
    const checkboxes = document.querySelectorAll('.chk-' + batchKey);
    const selectAllCheckbox = document.getElementById('selectAll_' + batchKey);
    let state = forceState;
    if (state === null) {
        state = !Array.from(checkboxes).every(c => c.checked);
    }
    checkboxes.forEach(cb => cb.checked = state);
    if (selectAllCheckbox) selectAllCheckbox.checked = state;
}
</script>
@endpush
@endsection
