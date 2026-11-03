@extends('layouts.centre')

@section('title', 'Daily Attendance Sheet - ' . $batch->batch_name . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.attendance.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Daily Attendance Hub
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Daily Attendance Sheet
        </h3>
        <p class="text-muted small mb-0">
            <strong>{{ $batch->batch_name }}</strong> ({{ $batch->start_time }} - {{ $batch->end_time }}) |
            Course: <span class="text-primary fw-semibold">{{ $batch->course?->course_name ?? 'General Course' }}</span>
        </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Enroll Students Button -->
        <button type="button" class="btn btn-primary btn-sm rounded-2 px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#enrollModal">
            <i class="fa-solid fa-user-plus me-1"></i> Enroll / Add Students ({{ $batch->admissions->count() }})
        </button>

        <!-- Date Selector Form -->
        <form method="GET" action="{{ route('centre.batches.attendance', $batch->id) }}" class="d-flex align-items-center gap-2 bg-white p-1 rounded-2 border shadow-sm">
            <label class="fw-semibold small text-muted text-nowrap ps-1 mb-0"><i class="fa-regular fa-calendar me-1"></i> Date:</label>
            <input type="date" name="date" class="form-control form-control-sm border-0 font-monospace fw-bold text-primary" value="{{ $attendanceDate }}" onchange="this.form.submit()">
        </form>
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

<!-- Attendance Form -->
<form method="POST" action="{{ route('centre.batches.attendance.mark', $batch->id) }}">
    @csrf
    <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-calendar-day text-info"></i>
                <span>Roll Call Date:</span>
                <span class="badge bg-primary-subtle text-primary font-monospace rounded-2 fs-6">
                    {{ \Carbon\Carbon::parse($attendanceDate)->format('d F, Y (l)') }}
                </span>
                <span class="badge bg-light text-muted border font-monospace ms-1">
                    {{ $batch->admissions->count() }} Candidates
                </span>
            </div>

            @if($batch->admissions->isNotEmpty())
                <!-- Quick Mark Buttons -->
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-success rounded-start-2 px-3 fw-semibold" onclick="markAll('present')">
                        <i class="fa-solid fa-check-double me-1"></i> Mark All Present
                    </button>
                    <button type="button" class="btn btn-outline-danger rounded-end-2 px-3 fw-semibold" onclick="markAll('absent')">
                        <i class="fa-solid fa-xmark me-1"></i> Mark All Absent
                    </button>
                </div>
            @endif
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                    <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete sheet
                </div>
                <table class="table table-custom align-middle mb-0" style="min-width: 720px;">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="ps-3">#</th>
                            <th style="width: 140px;">Roll & Reg No</th>
                            <th>Student Full Name & Father</th>
                            <th class="text-center" style="width: 290px;">Attendance Status</th>
                            <th>Remarks / Notes</th>
                            <th class="text-end pe-3" style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batch->admissions as $idx => $admission)
                            @php
                                $student = $admission->student;
                                $record = $existingRecords->get($student?->id);
                                $currentStatus = $record?->status ?? 'present';
                                $currentRemarks = $record?->remarks ?? '';
                            @endphp
                            @if($student)
                                <tr>
                                    <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-2">{{ $student->roll_no }}</span>
                                        <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $student->registration_no }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->full_name }}</div>
                                        <div class="small text-muted">Father: {{ $student->father_name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            <input type="radio" class="btn-check att-present" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_present" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success fw-bold" for="att_{{ $student->id }}_present" title="Present">P</label>

                                            <input type="radio" class="btn-check att-absent" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_absent" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger fw-bold" for="att_{{ $student->id }}_absent" title="Absent">A</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_late" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning fw-bold" for="att_{{ $student->id }}_late" title="Late">L</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_leave" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-info fw-bold" for="att_{{ $student->id }}_leave" title="Leave">Leave</label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm rounded-2" placeholder="Optional notes" value="{{ $currentRemarks }}">
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger p-1 rounded-2" title="Remove student from this batch" onclick="unenrollStudent({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
                                            <i class="fa-solid fa-user-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-inline-flex mb-3">
                                            <i class="fa-solid fa-user-plus fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Students Enrolled in this Batch Yet</h5>
                                        <p class="text-muted small mb-3">You can enroll students of your study centre into this shift with just 1 click.</p>
                                        <button type="button" class="btn btn-primary px-4 py-2 fw-bold rounded-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#enrollModal">
                                            <i class="fa-solid fa-user-plus me-1"></i> Enroll Students into {{ $batch->batch_name }} Now
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batch->admissions->isNotEmpty())
                <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Total: <strong>{{ $batch->admissions->count() }}</strong> enrolled candidates in this shift</span>
                    <button type="submit" class="btn btn-primary rounded-2 px-5 py-2 fw-bold shadow-sm fs-6">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Save Attendance Records
                    </button>
                </div>
            @endif
        </div>
    </div>
</form>

<!-- HIDDEN UNENROLL FORM -->
<form id="unenrollForm" method="POST" action="" class="d-none">
    @csrf
</form>

<!-- ENROLL STUDENTS MODAL -->
<div class="modal fade" id="enrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('centre.batches.enroll', $batch->id) }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <div>
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="fa-solid fa-user-plus me-2"></i> Enroll Students into [{{ $batch->batch_name }}]
                    </h6>
                    <div class="small text-white-50">Shift: {{ $batch->start_time }} - {{ $batch->end_time }} | Course: {{ $batch->course?->course_name ?? 'General' }}</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p class="text-muted small mb-3">
                    Check the students you want to assign to this batch. They will immediately appear on the daily roll call sheet above.
                </p>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <input type="text" id="modalStudentSearch" class="form-control form-control-sm" placeholder="Type to filter students..." style="max-width: 250px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="toggleSelectAllModal()">
                        Select All
                    </button>
                </div>

                <div class="table-responsive border rounded" style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 small" id="enrollTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;" class="ps-3">
                                    <input type="checkbox" class="form-check-input" id="selectAllModalCheck" onchange="toggleSelectAllModal(this.checked)">
                                </th>
                                <th>Roll & Reg No</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Current Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($availableStudents ?? [] as $st)
                                @php
                                    $curBatch = $st->admissions->first()?->batch;
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" class="form-check-input modal-chk">
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">{{ $st->roll_no }}</span>
                                        <div class="text-muted" style="font-size: 10px;">{{ $st->registration_no }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark student-name">{{ $st->full_name }}</div>
                                        <div class="text-muted" style="font-size: 10px;">Father: {{ $st->father_name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border">{{ $st->admissions->first()?->course?->course_name ?? 'Enrolled' }}</span>
                                    </td>
                                    <td>
                                        @if($curBatch)
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $curBatch->batch_name }}</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        All active students in this centre are already enrolled in this batch.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light p-2 d-flex justify-content-between">
                <span class="small text-muted ps-2">Tip: Check all students and click Confirm.</span>
                <div>
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-4">
                        <i class="fa-solid fa-user-plus me-1"></i> Enroll Selected Students
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function markAll(status) {
    if (status === 'present') {
        document.querySelectorAll('.att-present').forEach(el => el.checked = true);
    } else if (status === 'absent') {
        document.querySelectorAll('.att-absent').forEach(el => el.checked = true);
    }
}

function unenrollStudent(studentId, studentName) {
    if (confirm('Are you sure you want to remove ' + studentName + ' from this batch?')) {
        const form = document.getElementById('unenrollForm');
        form.action = "{{ url('centre/batches/' . $batch->id . '/unenroll') }}/" + studentId;
        form.submit();
    }
}

function toggleSelectAllModal(forceState = null) {
    const checkboxes = document.querySelectorAll('.modal-chk');
    const selectAll = document.getElementById('selectAllModalCheck');
    let state = forceState;
    if (state === null) {
        state = !Array.from(checkboxes).every(c => c.checked);
    }
    checkboxes.forEach(cb => cb.checked = state);
    if (selectAll) selectAll.checked = state;
}

// Modal filter search
document.getElementById('modalStudentSearch')?.addEventListener('input', function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#enrollTable tbody tr').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
