@extends('layouts.admin')

@section('title', 'Students Management - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Students</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Student Directory</h3>
        <p class="text-muted small mb-0">Search, manage enrollments, profile status, and academic dossiers.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.students.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-file-csv me-1 text-success"></i> Export CSV
        </a>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Add New Student
        </a>
    </div>
</div>

<!-- KPI Status Summary Cards -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-sm-4 col-md-3 col-xl">
        <a href="{{ route('admin.students.index', request()->except('status')) }}" class="text-decoration-none">
            <div class="card dce-stat-card border-start border-4 border-dark h-100 {{ !request('status') ? 'bg-light shadow-sm' : '' }}">
                <div class="card-body p-2.5 p-md-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">All Students</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ number_format($counts['all'] ?? 0) }}</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Total registered</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-md-3 col-xl">
        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['status' => 'enrolled'])) }}" class="text-decoration-none">
            <div class="card dce-stat-card border-start border-4 border-info h-100 {{ request('status') === 'enrolled' ? 'bg-light shadow-sm' : '' }}">
                <div class="card-body p-2.5 p-md-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Enrolled</div>
                    <div class="h4 fw-bold mb-0 text-info">{{ number_format($counts['enrolled'] ?? 0) }}</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Fresh admissions</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-sm-4 col-md-3 col-xl">
        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['status' => 'ongoing'])) }}" class="text-decoration-none">
            <div class="card dce-stat-card border-start border-4 border-primary h-100 {{ request('status') === 'ongoing' ? 'bg-light shadow-sm' : '' }}">
                <div class="card-body p-2.5 p-md-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Ongoing</div>
                    <div class="h4 fw-bold mb-0 text-primary">{{ number_format($counts['ongoing'] ?? 0) }}</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Attending classes</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-sm-6 col-md-3 col-xl">
        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['status' => 'completed'])) }}" class="text-decoration-none">
            <div class="card dce-stat-card border-start border-4 border-success h-100 {{ request('status') === 'completed' ? 'bg-light shadow-sm' : '' }}">
                <div class="card-body p-2.5 p-md-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Completed</div>
                    <div class="h4 fw-bold mb-0 text-success">{{ number_format($counts['completed'] ?? 0) }}</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Graduated students</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-3 col-xl">
        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['status' => 'dropped'])) }}" class="text-decoration-none">
            <div class="card dce-stat-card border-start border-4 border-danger h-100 {{ request('status') === 'dropped' ? 'bg-light shadow-sm' : '' }}">
                <div class="card-body p-2.5 p-md-3">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Dropped</div>
                    <div class="h4 fw-bold mb-0 text-danger">{{ number_format($counts['dropped'] ?? 0) }}</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Discontinued</div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Status Tab Filters -->
<div class="card dce-table-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, roll no, reg no, mobile..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="centre_id" class="form-select">
                    <option value="">-- All Study Centres --</option>
                    @foreach($centres as $c)
                        <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->centre_code }} - {{ $c->centre_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'centre_id', 'status']))
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Students Roster Table -->
<div class="card dce-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-users text-primary me-2"></i>Students Roster 
            <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $students->total() }} Total</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Roll No / Reg</th>
                        <th>Student Name</th>
                        <th>Centre</th>
                        <th>Mobile</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                    <tr>
                        <td>
                            <div class="fw-bold font-monospace text-primary">{{ $s->roll_no }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $s->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $s->full_name }}</div>
                            <div class="text-muted small">S/o: {{ $s->father_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-dark-subtle text-dark font-monospace">{{ $s->centre?->centre_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $s->centre?->centre_name }}</div>
                        </td>
                        <td>
                            <a href="tel:{{ $s->mobile }}" class="text-decoration-none text-dark fw-semibold small">
                                <i class="fa-solid fa-phone me-1 text-success"></i>{{ $s->mobile }}
                            </a>
                        </td>
                        <td>
                            @if($s->admissions->isNotEmpty())
                                <span class="badge bg-primary-subtle text-primary">{{ $s->admissions->first()->course?->course_code }}</span>
                            @else
                                <span class="text-muted small">Not Assigned</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusBadge = match($s->status) {
                                    'enrolled' => 'bg-info',
                                    'ongoing' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'dropped' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ ucfirst($s->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.students.show', $s->id) }}" class="btn btn-outline-primary" title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $s->id) }}" class="btn btn-outline-secondary" title="Edit Profile">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.students.destroy', $s->id) }}" class="d-inline" data-confirm="Are you sure you want to soft-delete this student record?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Soft Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-user-slash fa-2x mb-3 text-secondary"></i>
                            <p class="mb-0">No student records matched your query.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-white py-3 border-top">
        {{ $students->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
