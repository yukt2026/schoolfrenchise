@extends('layouts.centre')

@section('title', 'Daily Attendance Sheet - ' . $batch->batch_name . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.batches.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Batches
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Daily Attendance Sheet
        </h3>
        <p class="text-muted small mb-0">
            <strong>{{ $batch->batch_name }}</strong> ({{ $batch->start_time }} - {{ $batch->end_time }}) |
            Course: {{ $batch->course?->course_name }}
        </p>
    </div>
    <div>
        <!-- Date Selector Form -->
        <form method="GET" action="{{ route('centre.batches.attendance', $batch->id) }}" class="d-flex align-items-center gap-2">
            <label class="fw-semibold small text-muted text-nowrap">Attendance Date:</label>
            <input type="date" name="date" class="form-control form-control-sm rounded-2" value="{{ $attendanceDate }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<!-- Attendance Form -->
<form method="POST" action="{{ route('centre.batches.attendance.mark', $batch->id) }}">
    @csrf
    <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="fw-bold text-dark">
                <i class="fa-solid fa-calendar-day text-info me-1"></i>
                Date: <span class="badge bg-primary-subtle text-primary font-monospace rounded-2">{{ \Carbon\Carbon::parse($attendanceDate)->format('d F, Y (l)') }}</span>
            </div>
            <!-- Quick Mark Buttons -->
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success rounded-start-2" onclick="markAll('present')">
                    <i class="fa-solid fa-check-double me-1"></i>Mark All Present
                </button>
                <button type="button" class="btn btn-outline-danger rounded-end-2" onclick="markAll('absent')">
                    <i class="fa-solid fa-xmark me-1"></i>Mark All Absent
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                    <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete sheet
                </div>
                <table class="table table-custom align-middle mb-0" style="min-width: 680px;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Roll No & Reg No</th>
                            <th>Student Name & Father</th>
                            <th class="text-center" style="width: 320px;">Attendance Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batch->admissions as $idx => $admission)
                        @php
                            $student = $admission->student;
                            $currentStatus = $existingRecords->get($student->id)?->status ?? 'present';
                            $currentRemarks = $existingRecords->get($student->id)?->remarks ?? '';
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
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
                                    <label class="btn btn-outline-success" for="att_{{ $student->id }}_present">P</label>

                                    <input type="radio" class="btn-check att-absent" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_absent" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="att_{{ $student->id }}_absent">A</label>

                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_late" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="att_{{ $student->id }}_late">L</label>

                                    <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="att_{{ $student->id }}_leave" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="att_{{ $student->id }}_leave">Leave</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm rounded-2" placeholder="Optional notes" value="{{ $currentRemarks }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-1 text-black-50 mb-3 d-block"></i>
                                <h5>No Students Enrolled in this Batch</h5>
                                <p class="small text-muted mb-0">Admit or assign students to this batch to mark attendance.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($batch->admissions->isNotEmpty())
            <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-muted">Total: {{ $batch->admissions->count() }} enrolled candidates in this shift</span>
                <button type="submit" class="btn btn-primary rounded-2 px-4 fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>Save Attendance
                </button>
            </div>
            @endif
        </div>
    </div>
</form>

@push('scripts')
<script>
    function markAll(status) {
        if (status === 'present') {
            document.querySelectorAll('.att-present').forEach(el => el.checked = true);
        } else if (status === 'absent') {
            document.querySelectorAll('.att-absent').forEach(el => el.checked = true);
        }
    }
</script>
@endpush
@endsection
