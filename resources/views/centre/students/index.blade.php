@extends('layouts.centre')

@section('title', 'Students Directory - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-user-graduate text-primary me-2"></i>Students Directory
        </h3>
        <p class="text-muted small mb-0">Manage candidates admitted and studying at study centre {{ $centre->centre_name }} ({{ $centre->centre_code }}).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.students.create') }}" class="btn btn-primary rounded-2 px-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i>New Admission Registration
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-dark">
            <div class="text-muted small fw-semibold text-uppercase">Total Enrolled</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total']) }}</div>
            <span class="small text-muted">All-time candidates</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-primary">
            <div class="text-muted small fw-semibold text-uppercase">Admitted</div>
            <div class="fs-4 fw-bold text-primary my-1">{{ number_format($stats['enrolled']) }}</div>
            <span class="small text-muted">Fresh admissions</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info">
            <div class="text-muted small fw-semibold text-uppercase">Ongoing Training</div>
            <div class="fs-4 fw-bold text-info my-1">{{ number_format($stats['ongoing']) }}</div>
            <span class="small text-muted">Active in batches</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success">
            <div class="text-muted small fw-semibold text-uppercase">Passed Out</div>
            <div class="fs-4 fw-bold text-success my-1">{{ number_format($stats['completed']) }}</div>
            <span class="small text-muted">Course completed</span>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.students.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search by name, roll no, reg no, mobile..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select bg-light">
                    <option value="">All Enrolled Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->course_code }} - {{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Filter</button>
                <a href="{{ route('centre.students.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Students Directory Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        @if($students->count() > 0)
            <div class="table-scroll-hint d-md-none">
                <i class="fa-solid fa-arrows-left-right text-primary"></i>
                <span>Scroll horizontally to view all student records</span>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table-custom table-hover align-middle mb-0" style="min-width: 780px;">
                <thead>
                    <tr>
                        <th class="ps-4">Roll No & Reg No</th>
                        <th>Student Name & Father</th>
                        <th>Course & Batch</th>
                        <th>Mobile</th>
                        <th>State / District</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php $adm = $student->admissions->first(); @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-1">{{ $student->roll_no }}</span>
                            <div class="small text-muted font-monospace mt-1" style="font-size: 0.72rem;">{{ $student->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $student->full_name }}</div>
                            <div class="small text-muted">Father: {{ $student->father_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-1">{{ $adm?->course?->course_code ?? 'Course' }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 180px;">{{ $adm?->course?->course_name ?? 'N/A' }}</div>
                            @if($adm?->batch)
                            <div class="small text-info fw-semibold"><i class="fa-solid fa-clock me-1"></i>{{ $adm->batch->batch_name }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $student->mobile }}</td>
                        <td>
                            <div class="small text-nowrap">{{ $student->city }}</div>
                            <div class="small text-muted text-nowrap">{{ $student->state?->name }}</div>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge rounded-1 {{ $student->status === 'completed' ? 'bg-success' : ($student->status === 'ongoing' ? 'bg-info' : 'bg-primary') }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td class="pe-4 text-end text-nowrap">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-2" type="button" data-bs-toggle="dropdown">
                                    Manage
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('centre.students.show', $student->id) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i>Full Profile Dossier
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('centre.students.id-card', $student->id) }}" target="_blank">
                                            <i class="fa-solid fa-id-card text-info me-2"></i>Print Digital ID Card
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('centre.students.admission-form', $student->id) }}" target="_blank">
                                            <i class="fa-solid fa-file-lines text-warning me-2"></i>Print Admission Form
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        @if($adm)
                                        <a class="dropdown-item py-2 text-success" href="{{ route('centre.fees.create', ['admission_id' => $adm->id]) }}">
                                            <i class="fa-solid fa-indian-rupee-sign me-2"></i>Collect Fee Payment
                                        </a>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-user-slash fs-1 text-black-50 d-block mb-3"></i>
                            <h5>No students found</h5>
                            <p class="small text-muted mb-3">No candidates match the specified criteria in this study centre.</p>
                            <a href="{{ route('centre.students.create') }}" class="btn btn-primary rounded-2 px-4">
                                <i class="fa-solid fa-user-plus me-1"></i>Register New Student
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="p-3 border-top">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
