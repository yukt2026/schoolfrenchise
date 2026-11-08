@extends('layouts.admin')

@section('title', $course->course_name . ' (' . $course->course_code . ') - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $course->course_code }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Course Curriculum Dossier</h3>
        <p class="text-muted small mb-0">Standardized course outline, pricing schedule, batches, and active student admissions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit Course
        </a>
        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Public Page
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-light btn-sm px-3 shadow-sm border">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>
</div>

<!-- Course Banner Card -->
<div class="card dce-table-card mb-4 border-top border-4 border-primary">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->course_name }}" 
                         class="rounded border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center border" 
                         style="width: 80px; height: 80px; font-size: 32px;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-dark font-monospace fs-6">{{ $course->course_code }}</span>
                        @if($course->is_popular)
                            <span class="badge bg-warning text-dark">★ POPULAR</span>
                        @endif
                        @if($course->status)
                            <span class="badge bg-success">Active & Published</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $course->course_name }}</h4>
                    <div class="text-muted small">
                        <span><i class="fa-solid fa-layer-group text-primary me-1"></i>Category: {{ $course->category?->name ?? 'Information Technology' }}</span>
                        <span class="mx-2">•</span>
                        <span><i class="fa-solid fa-clock text-info me-1"></i>Duration: <strong>{{ $course->duration }}</strong></span>
                        <span class="mx-2">•</span>
                        <span><i class="fa-solid fa-graduation-cap text-success me-1"></i>Eligibility: {{ $course->eligibility }}</span>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <div class="text-muted small fw-semibold text-uppercase">Total Package Fee</div>
                <div class="h3 fw-bold text-success mb-0">₹{{ number_format($course->total_fee, 2) }}</div>
                <div class="text-muted small">{{ $course->admissions->count() }} Total Admitted Students</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content: Subjects, Curriculum & Admissions -->
    <div class="col-lg-8">
        <!-- Curriculum Subjects & Marking Scheme Card -->
        <div class="card dce-table-card mb-4" id="subjects-section">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-book-bookmark text-primary me-2"></i>Curriculum Subjects & Marking Scheme
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                        {{ $course->subjects->count() }} Subjects
                    </span>
                </div>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Subject
                </button>
            </div>

            @if($course->subjects->count() > 0)
            <!-- Summary Header Cards -->
            <div class="bg-light px-3 py-2 border-bottom">
                <div class="row g-2 text-center small">
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-2 rounded border">
                            <span class="text-muted d-block small">Course Full Marks</span>
                            <strong class="text-success fs-6">{{ $course->subjects->sum('total_max') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-2 rounded border">
                            <span class="text-muted d-block small">Total Theory Max</span>
                            <strong class="text-primary fs-6">{{ $course->subjects->sum('theory_max') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-2 rounded border">
                            <span class="text-muted d-block small">Total Practical Max</span>
                            <strong class="text-info fs-6">{{ $course->subjects->sum('practical_max') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-2 rounded border">
                            <span class="text-muted d-block small">Total Viva Max</span>
                            <strong class="text-secondary fs-6">{{ $course->subjects->sum('viva_max') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th>Subject Code</th>
                                <th>Subject Paper Title</th>
                                <th class="text-center">Theory</th>
                                <th class="text-center">Practical</th>
                                <th class="text-center">Viva</th>
                                <th class="text-center">Total Max</th>
                                <th class="text-center">Pass Marks</th>
                                <th class="text-end pe-3" style="width: 110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->subjects as $index => $sub)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $sub->sort_order ?: ($index + 1) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">
                                        {{ $sub->subject_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sub->subject_name }}</div>
                                </td>
                                <td class="text-center font-monospace">{{ $sub->theory_max }}</td>
                                <td class="text-center font-monospace text-muted">{{ $sub->practical_max }}</td>
                                <td class="text-center font-monospace text-muted">{{ $sub->viva_max }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace fw-bold">
                                        {{ $sub->total_max }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace">
                                        {{ $sub->min_pass_marks }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-subject-btn"
                                                data-id="{{ $sub->id }}"
                                                data-code="{{ $sub->subject_code }}"
                                                data-name="{{ $sub->subject_name }}"
                                                data-theory="{{ $sub->theory_max }}"
                                                data-practical="{{ $sub->practical_max }}"
                                                data-viva="{{ $sub->viva_max }}"
                                                data-pass="{{ $sub->min_pass_marks }}"
                                                data-order="{{ $sub->sort_order }}"
                                                title="Edit Subject & Marks">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('admin.courses.subjects.destroy', [$course->id, $sub->id]) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete subject [{{ $sub->subject_code }} - {{ $sub->subject_name }}]?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Subject">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card-body py-5 text-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">No Subjects Configured Yet</h6>
                <p class="text-muted small mb-3">This course does not have any subjects or examination marks defined yet. Click below to add curriculum papers.</p>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="fa-solid fa-plus me-1"></i> Add First Subject
                </button>
            </div>
            @endif
        </div>

        <!-- Syllabus and Curriculum -->
        <div class="card dce-table-card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Curriculum & Syllabus Breakdown</h6>
            </div>
            <div class="card-body">
                @if($course->syllabus)
                    <div class="bg-light p-3 rounded border font-monospace small" style="white-space: pre-wrap; line-height: 1.6;">
                        {{ $course->syllabus }}
                    </div>
                @else
                    <div class="text-muted text-center py-4">No detailed syllabus added yet. <a href="{{ route('admin.courses.edit', $course->id) }}">Edit course</a> to add modules.</div>
                @endif
            </div>
        </div>

        <!-- Enrolled Admissions Table -->
        <div class="card dce-table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-users-viewfinder text-primary me-2"></i>Recent Course Admissions ({{ $course->admissions->count() }})
                </h6>
                <a href="{{ route('admin.admissions.create') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> New Admission
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Centre</th>
                                <th>Admission Date</th>
                                <th>Net Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->admissions->take(10) as $adm)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.students.show', $adm->student_id) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $adm->student?->full_name }}
                                    </a>
                                    <div class="font-monospace text-muted small">Roll: {{ $adm->student?->roll_no }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $adm->centre?->centre_code }}</span></td>
                                <td>{{ $adm->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('d M Y') : '—' }}</td>
                                <td class="fw-bold text-dark">₹{{ number_format($adm->net_fee, 2) }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($adm->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No students currently enrolled in this course.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Structure & Batches -->
    <div class="col-lg-4">
        <!-- Fee Components Card -->
        <div class="card dce-table-card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-success me-2"></i>Fee Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 table-striped">
                    <tbody>
                        <tr>
                            <td class="ps-3 text-muted">Tuition / Base Course Fee</td>
                            <td class="text-end pe-3 fw-bold">₹{{ number_format($course->default_course_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Registration Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->registration_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Examination Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->exam_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Certification Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->certificate_fee, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="ps-3 fw-bold text-dark">Total Standard Fee</td>
                            <td class="text-end pe-3 fw-bold text-success fs-6">₹{{ number_format($course->total_fee, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Associated Batches Card -->
        <div class="card dce-table-card">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-days text-info me-2"></i>Active Batches ({{ $course->batches->count() }})</h6>
            </div>
            <div class="card-body p-3">
                @forelse($course->batches as $batch)
                    <div class="p-2 border rounded mb-2 bg-light">
                        <div class="fw-semibold text-dark">{{ $batch->batch_name }}</div>
                        <div class="small text-muted font-monospace"><i class="fa-solid fa-clock me-1 text-primary"></i>{{ $batch->start_time }} - {{ $batch->end_time }}</div>
                    </div>
                @empty
                    <div class="text-muted small text-center py-3">No active training batches linked to this course.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Subject -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.courses.subjects.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="addSubjectModalLabel">
                        <i class="fa-solid fa-plus-circle text-primary me-2"></i>Add Subject Paper
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="subject_code" class="form-control font-monospace text-uppercase" 
                               placeholder="e.g. DCA-101" required value="{{ old('subject_code') }}">
                        <div class="form-text small">Unique code for this paper within the curriculum.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject Name / Paper Title <span class="text-danger">*</span></label>
                        <input type="text" name="subject_name" class="form-control" 
                               placeholder="e.g. Fundamentals of Computer & OS" required value="{{ old('subject_name') }}">
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <label class="form-label fw-bold text-dark small mb-2"><i class="fa-solid fa-calculator text-primary me-1"></i>Marking Scheme (Max Marks)</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Theory Max <span class="text-danger">*</span></label>
                                <input type="number" name="theory_max" id="addTheoryMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000" value="70" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Practical Max</label>
                                <input type="number" name="practical_max" id="addPracticalMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000" value="20">
                            </div>
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Viva Max</label>
                                <input type="number" name="viva_max" id="addVivaMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000" value="10">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small fw-semibold text-muted">Total Computed Marks:</span>
                            <span class="badge bg-success fs-6 font-monospace" id="addTotalMarksDisplay">100</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Min Pass Marks <span class="text-danger">*</span></label>
                            <input type="number" name="min_pass_marks" id="addPassMarks" class="form-control form-control-sm font-monospace" 
                                   min="0" max="1000" value="40" required>
                            <div class="form-text small">Passing threshold for paper.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Sort Order / Sequence</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm font-monospace" 
                                   min="1" value="{{ ($course->subjects->max('sort_order') ?? $course->subjects->count()) + 1 }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Subject -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editSubjectForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="editSubjectModalLabel">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Subject Paper & Marks
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="subject_code" id="editSubjectCode" class="form-control font-monospace text-uppercase" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject Name / Paper Title <span class="text-danger">*</span></label>
                        <input type="text" name="subject_name" id="editSubjectName" class="form-control" required>
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <label class="form-label fw-bold text-dark small mb-2"><i class="fa-solid fa-calculator text-primary me-1"></i>Marking Scheme (Max Marks)</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Theory Max <span class="text-danger">*</span></label>
                                <input type="number" name="theory_max" id="editTheoryMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Practical Max</label>
                                <input type="number" name="practical_max" id="editPracticalMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000">
                            </div>
                            <div class="col-4">
                                <label class="form-label small text-muted mb-1">Viva Max</label>
                                <input type="number" name="viva_max" id="editVivaMax" class="form-control form-control-sm text-center font-monospace" 
                                       min="0" max="1000">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small fw-semibold text-muted">Total Computed Marks:</span>
                            <span class="badge bg-success fs-6 font-monospace" id="editTotalMarksDisplay">0</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Min Pass Marks <span class="text-danger">*</span></label>
                            <input type="number" name="min_pass_marks" id="editPassMarks" class="form-control form-control-sm font-monospace" 
                                   min="0" max="1000" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Sort Order / Sequence</label>
                            <input type="number" name="sort_order" id="editSortOrder" class="form-control form-control-sm font-monospace" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Modal Calculation
    const addTheory = document.getElementById('addTheoryMax');
    const addPractical = document.getElementById('addPracticalMax');
    const addViva = document.getElementById('addVivaMax');
    const addDisplay = document.getElementById('addTotalMarksDisplay');

    function updateAddTotal() {
        if (!addTheory || !addDisplay) return;
        const t = parseInt(addTheory.value) || 0;
        const p = parseInt(addPractical.value) || 0;
        const v = parseInt(addViva.value) || 0;
        addDisplay.textContent = t + p + v;
    }

    if (addTheory) {
        [addTheory, addPractical, addViva].forEach(el => el.addEventListener('input', updateAddTotal));
        updateAddTotal();
    }

    // Edit Modal Calculation & Populate
    const editForm = document.getElementById('editSubjectForm');
    const editCode = document.getElementById('editSubjectCode');
    const editName = document.getElementById('editSubjectName');
    const editTheory = document.getElementById('editTheoryMax');
    const editPractical = document.getElementById('editPracticalMax');
    const editViva = document.getElementById('editVivaMax');
    const editPass = document.getElementById('editPassMarks');
    const editOrder = document.getElementById('editSortOrder');
    const editDisplay = document.getElementById('editTotalMarksDisplay');
    const courseId = {{ $course->id }};

    function updateEditTotal() {
        if (!editTheory || !editDisplay) return;
        const t = parseInt(editTheory.value) || 0;
        const p = parseInt(editPractical.value) || 0;
        const v = parseInt(editViva.value) || 0;
        editDisplay.textContent = t + p + v;
    }

    if (editTheory) {
        [editTheory, editPractical, editViva].forEach(el => el.addEventListener('input', updateEditTotal));
    }

    const editModalEl = document.getElementById('editSubjectModal');
    let editModal = null;
    if (editModalEl && typeof bootstrap !== 'undefined') {
        editModal = new bootstrap.Modal(editModalEl);
    }

    document.querySelectorAll('.edit-subject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = `/admin/courses/${courseId}/subjects/${id}`;
            editCode.value = this.getAttribute('data-code');
            editName.value = this.getAttribute('data-name');
            editTheory.value = this.getAttribute('data-theory');
            editPractical.value = this.getAttribute('data-practical') || 0;
            editViva.value = this.getAttribute('data-viva') || 0;
            editPass.value = this.getAttribute('data-pass');
            editOrder.value = this.getAttribute('data-order');
            updateEditTotal();
            if (editModal) {
                editModal.show();
            }
        });
    });
});
</script>
@endpush
