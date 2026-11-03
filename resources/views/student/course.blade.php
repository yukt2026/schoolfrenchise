@extends('layouts.student')

@section('title', 'My Course - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-book-open-reader text-primary me-2"></i> My Enrolled Course
            </h4>
            <div class="text-muted small">Course syllabus, curriculum subjects and learning modules</div>
        </div>
        <div>
            <a href="{{ route('student.study-materials') }}" class="btn btn-outline-primary btn-sm rounded-2 px-3 fw-semibold">
                <i class="fa-solid fa-folder-open me-1"></i> Access Notes
            </a>
        </div>
    </div>

    @if($course)
        <!-- Course Hero Card -->
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="row align-items-center g-4">
                <div class="col-sm-3 col-md-2 text-center d-none d-sm-block">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->course_name }}" class="img-fluid rounded-3 shadow-sm border p-2 bg-white" style="max-height: 95px; width: auto; object-fit: contain;">
                </div>
                <div class="col-sm-9 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 fw-bold fs-6 rounded-1">
                            {{ $course->course_code }}
                        </span>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 small">
                            <i class="fa-solid fa-circle-check me-1"></i> Enrolled
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">{{ $course->course_name }}</h3>
                    <p class="text-muted mb-3">{{ $course->description ?? 'Comprehensive professional vocational and software development program designed in alignment with national IT industry standards.' }}</p>
                    
                    <div class="d-flex flex-wrap gap-4 text-secondary small">
                        <div>
                            <i class="fa-regular fa-clock text-primary me-1"></i>
                            Duration: <strong class="text-dark">{{ $course->duration ?? 'Standard' }}</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-award text-primary me-1"></i>
                            Eligibility: <strong class="text-dark">{{ $course->eligibility ?? '10th / 12th Pass or Equivalent' }}</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-calendar-days text-primary me-1"></i>
                            Batch: <strong class="text-dark">{{ $admission?->batch?->batch_name ?? 'Regular Shift' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="p-3 bg-light rounded-3 border text-center text-md-end">
                        <div class="text-muted small mb-1">
                            {{ $admission ? 'Enrolled Course Fee' : 'Standard Course Fee' }}
                        </div>
                        <h3 class="fw-bold text-primary mb-1">
                            ₹{{ number_format($admission ? (float) $admission->net_fee : (float) ($course->course_fee ?? $course->default_course_fee), 2) }}
                        </h3>
                        @if($admission && (float) $admission->discount_amount > 0)
                            <div class="text-success small fw-semibold">
                                <i class="fa-solid fa-tag me-1"></i> Discount Applied: ₹{{ number_format($admission->discount_amount, 2) }}
                            </div>
                        @endif
                        @if($admission)
                            <div class="d-flex align-items-center justify-content-between text-muted small mt-2 pt-2 border-top">
                                <span>Paid: <strong class="text-success">₹{{ number_format((float) $admission->total_paid, 2) }}</strong></span>
                                <span>Due: <strong class="{{ ((float) $admission->net_fee - (float) $admission->total_paid) > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format(max(0, (float) $admission->net_fee - (float) $admission->total_paid), 2) }}</strong></span>
                            </div>
                        @else
                            <div class="text-muted small">Inclusive of curriculum & study notes</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Curriculum & Subjects Breakdown -->
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                        <i class="fa-solid fa-list-check text-primary me-2"></i> Curriculum Subjects & Marking Scheme
                    </h5>
                    <div class="text-muted small mt-1">Subjects covered under this certification curriculum</div>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-1">{{ $course->subjects->count() }} Subjects</span>
            </div>
            <div class="card-body p-0">
                @if($course->subjects->count() > 0)
                    <div class="table-scroll-hint d-md-none">
                        <i class="fa-solid fa-arrows-left-right text-primary"></i>
                        <span>Scroll horizontally to view marking scheme</span>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table-custom table-hover align-middle mb-0" style="min-width: 680px;">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th style="width: 140px;">Subject Code</th>
                                <th>Subject Title</th>
                                <th class="text-center" style="width: 110px;">Theory Max</th>
                                <th class="text-center" style="width: 110px;">Practical Max</th>
                                <th class="text-center" style="width: 90px;">Viva</th>
                                <th class="text-center" style="width: 100px;">Pass Marks</th>
                                <th class="pe-4 text-center" style="width: 110px;">Total Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->subjects as $index => $subject)
                                <tr>
                                    <td class="ps-4 text-muted small fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="font-monospace fw-bold small text-primary bg-primary-subtle px-2 py-0.5 rounded-1">
                                            {{ $subject->subject_code ?? 'SUB-'.($index+1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $subject->subject_name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-1 px-2 py-1">
                                            {{ $subject->theory_max ?? ($subject->theory_marks ?? 70) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-1 px-2 py-1">
                                            {{ $subject->practical_max ?? ($subject->practical_marks ?? 20) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-muted border rounded-1 px-2 py-1">
                                            {{ $subject->viva_max ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-1 px-2 py-1">
                                            {{ $subject->min_pass_marks ?? 40 }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 fw-bold fs-7">
                                            {{ $subject->total_max ?? ($subject->total_marks ?? 100) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-book fa-2x mb-2 text-secondary opacity-50"></i>
                                        <p class="mb-0">Curriculum subjects will be updated shortly by the academic controller.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-5 text-center">
            <i class="fa-solid fa-graduation-cap fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark">No Enrolled Course Found</h5>
            <p class="text-muted">You do not currently have an approved course enrolment record linked to your profile.</p>
        </div>
    @endif
</div>
@endsection
