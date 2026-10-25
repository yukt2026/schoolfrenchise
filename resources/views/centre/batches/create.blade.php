@extends('layouts.centre')

@section('title', 'Create Training Batch - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.batches.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Batches
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-plus-circle text-primary me-2"></i>Create New Training Batch
    </h3>
    <p class="text-muted small mb-0">Configure daily computer lab shifts for centre {{ $centre->centre_name }} ({{ $centre->centre_code }}).</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.batches.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batch Name / Shift Title <span class="text-danger">*</span></label>
                        <input type="text" name="batch_name" class="form-control rounded-2" placeholder="e.g. Morning Lab Batch A (08:00 AM - 10:00 AM)" value="{{ old('batch_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select rounded-2" required>
                            <option value="">Select Associated Course</option>
                            @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->course_code }} - {{ $c->course_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control rounded-2" value="{{ old('start_time', '09:00') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control rounded-2" value="{{ old('end_time', '11:00') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Working Days Schedule <span class="text-danger">*</span></label>
                            <input type="text" name="days" class="form-control rounded-2" placeholder="e.g. Mon-Sat or Mon, Wed, Fri" value="{{ old('days', 'Mon-Sat') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Batch Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-2" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-2 px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i>Save Batch Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
