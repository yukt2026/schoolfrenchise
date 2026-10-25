@extends('layouts.centre')

@section('title', 'Statement of Marks - ' . ($result->student->full_name ?? 'Candidate') . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('centre.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('centre.exams.results') }}" class="text-decoration-none">Results</a></li>
                <li class="breadcrumb-item active" aria-current="page">Marksheet</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-file-invoice text-primary me-2"></i>Official Statement of Marks
        </h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.exams.results') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Results
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print Marksheet
        </button>
    </div>
</div>

<style>
@media print {
    .centre-sidebar, .centre-topbar, .centre-footer, .no-print {
        display: none !important;
    }
    .centre-content-area {
        margin: 0 !important;
        padding: 0 !important;
    }
    body {
        background: #ffffff !important;
    }
    .print-marksheet-container {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

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
                        <div class="fw-bold font-monospace text-dark">{{ $result->student->roll_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Registration Number</div>
                        <div class="fw-bold font-monospace text-dark">{{ $result->student->registration_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Date of Evaluation</div>
                        <div class="fw-bold font-monospace text-dark">
                            {{ $result->approved_at ? \Carbon\Carbon::parse($result->approved_at)->format('d-m-Y') : now()->format('d-m-Y') }}
                        </div>
                    </div>
                </div>

                <!-- Student & Course Information -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0" style="width: 40%;">Candidate's Name:</th>
                                    <td class="fw-bold text-dark text-uppercase">{{ $result->student->full_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Father's Name:</th>
                                    <td class="text-dark">{{ $result->student->father_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Mother's Name:</th>
                                    <td class="text-dark">{{ $result->student->mother_name ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0" style="width: 40%;">Course Title:</th>
                                    <td class="fw-bold text-primary">{{ $result->course->course_name ?? 'Computer Applications' }} ({{ $result->course->course_code ?? 'DCE' }})</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Exam Session:</th>
                                    <td class="text-dark">{{ $result->exam->session_name ?? 'Standard Academic Session' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Study Centre:</th>
                                    <td class="text-dark">{{ $centre->centre_name }} (CODE: {{ $centre->centre_code }})</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Subject-Wise Marks Breakdown Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle text-center mb-0 border-dark" style="min-width: 580px;">
                        <thead class="table-dark text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="text-start ps-3" style="width: 40%;">Subject / Module Name</th>
                                <th>Max Marks</th>
                                <th>Pass Marks</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Viva / Project</th>
                                <th>Total Obtained</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($result->subjectResults as $subj)
                            <tr>
                                <td class="text-start ps-3 fw-semibold">
                                    {{ $subj->subject->subject_name ?? 'Curriculum Subject' }}
                                    <div class="small text-muted font-monospace" style="font-size: 0.7rem;">{{ $subj->subject->subject_code ?? '' }}</div>
                                </td>
                                <td>{{ number_format($subj->max_marks, 0) }}</td>
                                <td>{{ number_format($subj->subject?->pass_marks ?: 40, 0) }}</td>
                                <td>{{ number_format($subj->theory_obtained, 1) }}</td>
                                <td>{{ number_format($subj->practical_obtained, 1) }}</td>
                                <td>{{ number_format($subj->viva_obtained, 1) }}</td>
                                <td class="fw-bold text-dark">{{ number_format($subj->total_obtained, 1) }}</td>
                                <td>
                                    @if($subj->is_passed)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2 py-1">PASS</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace px-2 py-1">FAIL</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">Marks evaluation completed in aggregate.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start ps-3 text-uppercase">Grand Aggregate Total</td>
                                <td>{{ number_format($result->total_max_marks, 1) }}</td>
                                <td>-</td>
                                <td colspan="3" class="text-end pe-3">Total Marks Obtained:</td>
                                <td class="fw-bolder fs-6 text-primary">{{ number_format($result->total_marks_obtained, 1) }}</td>
                                <td>
                                    <span class="badge {{ $result->result_status === 'passed' ? 'bg-success' : 'bg-danger' }} px-3 py-1">
                                        {{ strtoupper($result->result_status) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Final Grade Summary & QR Block -->
                <div class="row align-items-center g-3 p-3 bg-light rounded-3 border mb-4">
                    <div class="col-8 col-md-9">
                        <div class="row g-2 text-center text-md-start">
                            <div class="col-6 col-md-3">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Percentage</div>
                                <div class="fs-4 fw-bold {{ $result->percentage >= 40 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($result->percentage, 2) }}%
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Assigned Grade</div>
                                <div class="fs-4 fw-bolder text-primary font-monospace">{{ $result->grade }}</div>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="text-muted small">
                                    <strong>Grading Scale:</strong> &ge;85% (A+ Excellent), &ge;70% (A Very Good), &ge;60% (B Good), &ge;50% (C Satisfactory), &ge;40% (D Pass).
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-3 text-end">
                        <div class="d-inline-block text-center">
                            @php
                                $verifyUrl = route('verify.certificate') . '?code=' . urlencode($result->student->roll_no);
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($verifyUrl) }}" alt="QR Verification" class="border p-1 bg-white rounded-1 shadow-sm" style="width: 80px; height: 80px;">
                            <div class="text-muted font-monospace mt-1" style="font-size: 0.65rem;">SCAN TO VERIFY</div>
                        </div>
                    </div>
                </div>

                <!-- Signatures Section -->
                <div class="row pt-4 mt-4 border-top border-2 border-dark text-center">
                    <div class="col-4">
                        <div class="fw-semibold text-dark mb-4">Prepared By</div>
                        <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">Evaluation Assistant</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-semibold text-dark mb-4">Centre Director</div>
                        <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">{{ $centre->director_name ?? 'Centre Incharge' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-semibold text-dark mb-4">Controller of Examinations</div>
                        <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">Central Examination Board</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
