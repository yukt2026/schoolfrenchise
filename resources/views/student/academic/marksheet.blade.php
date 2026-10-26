@extends('layouts.student')

@section('title', 'Statement of Marks - ' . ($student->full_name ?? 'Student'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.results') }}" class="text-decoration-none">Results</a></li>
                <li class="breadcrumb-item active" aria-current="page">Marksheet</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-file-invoice text-primary me-2"></i>Official Statement of Marks
        </h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('student.results') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Results
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-print me-1"></i> Print Marksheet
        </button>
    </div>
</div>

<!-- PRINTABLE MARKSHEET CONTAINER -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 print-marksheet-container bg-white">
    <div class="card-body p-3 p-md-5">
        <!-- Marksheet Border Frame -->
        <div class="border border-2 border-dark rounded-2 p-3 p-md-4 position-relative" style="background: #ffffff;">
            <!-- Watermark Security Emblem -->
            <div class="position-absolute top-50 start-50 translate-middle opacity-10 pointer-events-none" style="z-index: 0;">
                <i class="fa-solid fa-graduation-cap" style="font-size: 16rem; color: #0284c7;"></i>
            </div>

            <div class="position-relative" style="z-index: 1;">
                <!-- Header Banner -->
                <div class="text-center border-bottom border-2 border-dark pb-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 52px; width: auto; object-fit: contain;">
                        <div class="text-start">
                            <h2 class="h4 fw-bolder mb-0 text-dark text-uppercase tracking-wider" style="color: #0284c7 !important;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                            <p class="small text-muted mb-0">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }} &bull; {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</p>
                        </div>
                    </div>
                    <div class="d-inline-block bg-dark text-white px-3 py-1 rounded-1 small fw-bold text-uppercase tracking-widest mt-1">
                        STATEMENT OF MARKS
                    </div>
                </div>

                <!-- Meta Numbers Bar -->
                <div class="row g-2 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Marksheet Serial No</div>
                        <div class="fw-bold font-monospace text-primary">
                            {{ $result->marksheet->marksheet_no ?? ('MS-' . str_pad($result->id, 5, '0', STR_PAD_LEFT)) }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Roll Number</div>
                        <div class="fw-bold font-monospace text-dark">{{ $student->roll_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Registration Number</div>
                        <div class="fw-bold font-monospace text-dark">{{ $student->registration_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Date of Issue</div>
                        <div class="fw-bold font-monospace text-dark">
                            {{ $result->marksheet?->issue_date ? \Carbon\Carbon::parse($result->marksheet->issue_date)->format('d-m-Y') : now()->format('d-m-Y') }}
                        </div>
                    </div>
                </div>

                <!-- Student & Course Information -->
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-12 col-md-5">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0" style="width: 40%;">Candidate's Name:</th>
                                    <td class="fw-bold text-dark text-uppercase">{{ $student->full_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Father's Name:</th>
                                    <td class="text-dark">{{ $student->father_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Mother's Name:</th>
                                    <td class="text-dark">{{ $student->mother_name ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-8 col-md-5">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0" style="width: 35%;">Course Title:</th>
                                    <td class="fw-bold text-primary">{{ $result->course->course_name ?? 'Computer Applications' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Course Duration:</th>
                                    <td class="text-dark">{{ $result->course->duration ?? '1 Year' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Study Centre:</th>
                                    <td class="text-dark">{{ $result->centre->centre_name ?? ($student->centre->centre_name ?? 'Main Centre') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-4 col-md-2 text-end">
                        <div class="border border-2 border-dark rounded-1 overflow-hidden d-inline-block bg-light shadow-sm" style="width: 85px; height: 100px;">
                            @if($student->photo_url)
                                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://placehold.co/90x105?text=Photo';">
                            @else
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted small p-1">
                                    <i class="fa-solid fa-user-graduate fs-4 opacity-50"></i>
                                    <span style="font-size: 0.6rem;">Photo</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subject-Wise Marks Breakdown Table -->
                <div class="table-scroll-hint d-md-none">
                    <i class="fa-solid fa-arrows-left-right text-primary"></i>
                    <span>Scroll horizontally to view subject scores</span>
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle text-center mb-0 border-dark" style="min-width: 580px;">
                        <thead class="table-dark text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="text-start ps-3" style="width: 40%;">Subject / Module Name</th>
                                <th>Max Marks</th>
                                <th>Pass Marks</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Total Obtained</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($result->subjectResults && $result->subjectResults->count() > 0)
                                @foreach($result->subjectResults as $subj)
                                    <tr>
                                        <td class="text-start ps-3 fw-semibold">
                                            {{ $subj->subject->subject_name ?? ('Module #' . $subj->course_subject_id) }}
                                        </td>
                                        <td>{{ (float)$subj->max_marks }}</td>
                                        <td>{{ round((float)$subj->max_marks * 0.4) }}</td>
                                        <td>{{ (float)$subj->theory_obtained }}</td>
                                        <td>{{ (float)$subj->practical_obtained }}</td>
                                        <td class="fw-bold text-dark">{{ (float)$subj->total_obtained }}</td>
                                        <td>
                                            @if($subj->is_passed)
                                                <span class="badge bg-success-subtle text-success">PASS</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">FAIL</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <!-- Aggregate Record Row if individual subjects not present -->
                                <tr>
                                    <td class="text-start ps-3 fw-semibold">Course Comprehensive Examination</td>
                                    <td>{{ (float)$result->total_max_marks }}</td>
                                    <td>{{ round((float)$result->total_max_marks * 0.4) }}</td>
                                    <td>{{ (float)$result->total_marks_obtained }}</td>
                                    <td>-</td>
                                    <td class="fw-bold text-dark">{{ (float)$result->total_marks_obtained }}</td>
                                    <td>
                                        <span class="badge {{ $result->result_status === 'passed' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                            {{ strtoupper($result->result_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start ps-3">GRAND TOTAL</td>
                                <td>{{ (float)$result->total_max_marks }}</td>
                                <td>-</td>
                                <td colspan="2" class="text-end pe-3">TOTAL OBTAINED:</td>
                                <td class="text-primary fs-6">{{ (float)$result->total_marks_obtained }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-1">{{ $result->grade }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Result Summary Box -->
                <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-4 text-center">
                        <div class="small text-muted text-uppercase" style="font-size: 0.72rem;">Percentage</div>
                        <div class="h5 fw-bold text-primary mb-0">{{ number_format($result->percentage, 2) }}%</div>
                    </div>
                    <div class="col-4 text-center border-start border-end">
                        <div class="small text-muted text-uppercase" style="font-size: 0.72rem;">Grade Awarded</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $result->grade }}</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="small text-muted text-uppercase" style="font-size: 0.72rem;">Final Result</div>
                        <div class="h5 fw-bold {{ $result->result_status === 'passed' ? 'text-success' : 'text-danger' }} mb-0 text-uppercase">
                            {{ $result->result_status }}
                        </div>
                    </div>
                </div>

                <!-- Grading Scale Key -->
                <div class="border rounded-2 p-2 mb-4 bg-white">
                    <div class="text-muted text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700;">Grading Key:</div>
                    <div class="d-flex flex-wrap gap-3 small text-muted" style="font-size: 0.72rem;">
                        <span><strong>A+</strong>: 85% & above (Outstanding)</span>
                        <span><strong>A</strong>: 70% to 84% (Excellent)</span>
                        <span><strong>B</strong>: 55% to 69% (Good)</span>
                        <span><strong>C</strong>: 40% to 54% (Pass)</span>
                        <span><strong>F</strong>: Below 40% (Fail)</span>
                    </div>
                </div>

                <!-- Verification QR Code & Tamper-Proof Assurance -->
                <div class="row align-items-center g-3 p-3 bg-light rounded-3 border mb-4">
                    <div class="col-sm-3 text-center text-sm-start">
                        <div class="border d-inline-block p-2 bg-white rounded text-center">
                            <i class="fa-solid fa-qrcode fa-3x text-dark"></i>
                            <div class="font-monospace text-muted mt-1" style="font-size: 0.65rem;">
                                VERIFIED: {{ $student->registration_no }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cryptographic Authenticity Verification</span>
                            <span class="badge bg-success-subtle text-success rounded-1" style="font-size: 0.65rem;">Official Academic Record</span>
                        </div>
                        <div class="font-monospace text-muted small text-break" style="font-size: 0.7rem;">
                            HASH: {{ $result->marksheet?->security_hash ?? hash('sha256', $result->id . $student->registration_no . ($result->marksheet?->marksheet_no ?? 'TEC')) }}
                        </div>
                        <div class="text-secondary small mt-1" style="font-size: 0.7rem;">
                            This marksheet is electronically authenticated by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} Examination Board. Scan QR code or verify at official portal.
                        </div>
                    </div>
                </div>

                <!-- Signatures Section -->
                <div class="row pt-4 mt-3 text-center border-top">
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 140px; height: 40px;"></div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Prepared By</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1 d-flex align-items-center justify-content-center text-muted" style="width: 140px; height: 40px;">
                            <span class="badge border text-muted px-2 py-1" style="font-size: 0.65rem;">SEAL & STAMP</span>
                        </div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Centre Director</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 140px; height: 40px;"></div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Controller of Examinations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .student-header,
    .student-subnav,
    .mobile-bottom-nav,
    .no-print,
    footer {
        display: none !important;
    }
    body, .student-main-content {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .print-marksheet-container {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
