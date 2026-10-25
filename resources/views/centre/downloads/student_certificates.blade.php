@extends('layouts.centre')

@section('title', 'Student Certificates - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-graduation-cap text-primary me-2"></i>Issued Student Certificates
        </h3>
        <p class="text-muted small mb-0">Central Board issued diplomas and course completion certificates for your centre's successful candidates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark fw-bold btn-sm rounded-2 px-3 shadow-sm">
            <i class="fa-solid fa-certificate me-1"></i>Centre Affiliation Certificate
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 bg-white rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Issued</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total']) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">Student diplomas</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Active & Valid</div>
            <div class="fs-4 fw-bold text-success my-1">{{ number_format($stats['active']) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">Verified online</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-danger rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Revoked</div>
            <div class="fs-4 fw-bold text-danger my-1">{{ number_format($stats['revoked']) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">Under re-issue</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Centre Code</div>
            <div class="fs-4 fw-bold text-info font-monospace my-1">{{ $centre->centre_code }}</div>
            <span class="small text-muted text-truncate d-block" style="font-size: 0.72rem;">{{ $centre->centre_name }}</span>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.downloads.student-certificates') }}" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-2" placeholder="Search by Candidate Name, Roll No, Certificate No..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select rounded-2">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->course_code }} - {{ $c->course_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select rounded-2">
                    <option value="">All Statuses</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued / Valid</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-2 btn-sm fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-secondary rounded-2 btn-sm" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Certificates Table Card -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-certificate me-2 text-primary"></i>Certified Candidates Registry
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $certificates->total() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view certificates
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 820px;">
                <thead>
                    <tr>
                        <th class="ps-3">Certificate No</th>
                        <th>Student Particulars</th>
                        <th>Course</th>
                        <th>Issue Date</th>
                        <th class="text-center">Grade</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-2">{{ $cert->certificate_no }}</span>
                            <div class="small text-muted font-monospace" style="font-size: 0.7rem;">HASH: {{ substr($cert->security_hash ?? 'SEC2026', 0, 10) }}...</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cert->student?->full_name }}</div>
                            <div class="small text-muted font-monospace">Roll: {{ $cert->student?->roll_no }} | Reg: {{ $cert->student?->registration_no }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-2">{{ $cert->course?->course_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $cert->course?->course_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $cert->issue_date ? \Carbon\Carbon::parse($cert->issue_date)->format('d M, Y') : 'Issued' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info font-monospace rounded-2 px-2 py-1">
                                {{ $cert->examResult?->grade ?? 'A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $cert->status === 'issued' ? 'bg-success' : 'bg-danger' }} rounded-2 text-capitalize">
                                <i class="fa-solid {{ $cert->status === 'issued' ? 'fa-check' : 'fa-xmark' }} me-1"></i>{{ $cert->status }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('centre.downloads.student-certificates.show', $cert->id) }}" target="_blank" class="btn btn-outline-primary rounded-start-2 btn-sm" title="Print Certificate">
                                    <i class="fa-solid fa-print me-1"></i>Print
                                </a>
                                @if($cert->exam_result_id)
                                <a href="{{ route('centre.exams.results.marksheet', $cert->exam_result_id) }}" target="_blank" class="btn btn-outline-info rounded-end-2 btn-sm" title="View Statement of Marks">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-certificate fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Student Certificates Issued Yet</h5>
                            <p class="small text-muted mb-0">Certificates approved by the Central Directorate for your candidates will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
        <div class="p-3 border-top">
            {{ $certificates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
