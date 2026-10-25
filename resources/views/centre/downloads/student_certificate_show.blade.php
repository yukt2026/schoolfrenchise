@extends('layouts.centre')

@section('title', 'Official Certificate - ' . ($certificate->student->full_name ?? 'Candidate') . ' (' . $certificate->certificate_no . ')')

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('centre.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('centre.downloads.student-certificates') }}" class="text-decoration-none">Student Certificates</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $certificate->certificate_no }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-graduation-cap text-primary me-2"></i>Official Diploma Certificate
        </h3>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        @if($certificate->exam_result_id)
        <a href="{{ route('centre.exams.results.marksheet', $certificate->exam_result_id) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-file-invoice me-1"></i>Marksheet
        </a>
        @endif
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print Certificate
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
    .print-cert-container {
        border: none !important;
        box-shadow: none !important;
    }
}

.cert-border-outer {
    border: 8px double #1e1b4b;
    padding: 30px;
    background: #ffffff;
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.cert-border-inner {
    border: 2px solid #b45309;
    padding: 25px;
    position: relative;
    background-image: radial-gradient(circle at center, #ffffff 60%, #fffbeb 100%);
}
</style>

<!-- PRINTABLE CERTIFICATE CONTAINER -->
<div class="card border-0 rounded-3 shadow-sm mb-4 print-cert-container bg-white">
    <div class="card-body p-2 p-md-4">
        <div class="cert-border-outer rounded-2">
            <div class="cert-border-inner rounded-1 text-center position-relative">
                <!-- Watermark -->
                <div class="position-absolute top-50 start-50 translate-middle opacity-10 pointer-events-none" style="z-index: 0;">
                    <i class="fa-solid fa-award" style="font-size: 15rem; color: #b45309;"></i>
                </div>

                <div class="position-relative" style="z-index: 1;">
                    <!-- Certificate Header -->
                    <div class="mb-3">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="mb-2" style="height: 60px; width: auto; object-fit: contain;">
                        <h2 class="fw-bolder mb-0 text-dark text-uppercase tracking-wider" style="color: #1e1b4b !important;">
                            {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                        </h2>
                        <p class="text-muted small mb-1">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
                        <div class="small text-muted font-monospace">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</div>
                    </div>

                    <div class="d-inline-block bg-dark text-warning px-4 py-1.5 rounded-1 fw-bold text-uppercase tracking-widest mb-3 border border-warning" style="font-size: 0.95rem;">
                        CERTIFICATE OF COMPLETION
                    </div>

                    <p class="lead text-muted mb-2 fst-italic" style="font-family: Georgia, serif;">This is to certify that</p>

                    <h3 class="fw-bold text-dark text-uppercase mb-2 text-decoration-underline" style="color: #0284c7 !important; letter-spacing: 0.5px;">
                        {{ $certificate->student->full_name ?? 'Candidate Name' }}
                    </h3>

                    <p class="mb-2" style="font-size: 1.05rem; line-height: 1.8;">
                        Son/Daughter of <strong>{{ $certificate->student->father_name ?? 'Father Name' }}</strong><br>
                        Roll No: <span class="font-monospace fw-bold text-primary">{{ $certificate->student->roll_no ?? 'N/A' }}</span> | 
                        Reg No: <span class="font-monospace fw-bold">{{ $certificate->student->registration_no ?? 'N/A' }}</span>
                    </p>

                    <p class="mb-3" style="font-size: 1.05rem; line-height: 1.8;">
                        has successfully completed the prescribed course of study in
                    </p>

                    <div class="p-3 bg-light rounded-2 border border-secondary-subtle d-inline-block mb-3 px-4">
                        <h4 class="fw-bold text-dark mb-0 text-uppercase">{{ $certificate->course->course_name ?? 'Computer Course' }}</h4>
                        <div class="small text-muted mt-1 font-monospace">Course Code: {{ $certificate->course->course_code ?? 'DCE' }} &bull; Duration: {{ $certificate->course->duration ?? '1 Year' }}</div>
                    </div>

                    <p class="mb-4" style="font-size: 1rem;">
                        at Authorised Study Centre: <strong>{{ $centre->centre_name }} (Code: {{ $centre->centre_code }})</strong><br>
                        and has achieved Grade: <span class="badge bg-primary px-3 py-1 font-monospace fs-6">{{ $certificate->examResult?->grade ?? 'A' }}</span>
                    </p>

                    <!-- Meta Strip & QR -->
                    <div class="row align-items-center g-3 p-3 bg-white border rounded-2 mb-4">
                        <div class="col-8 text-start">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="small text-muted text-uppercase" style="font-size: 0.68rem;">Certificate Number</div>
                                    <div class="fw-bold font-monospace text-primary">{{ $certificate->certificate_no }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted text-uppercase" style="font-size: 0.68rem;">Date of Issue</div>
                                    <div class="fw-bold font-monospace text-dark">{{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d F, Y') : now()->format('d F, Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            @php
                                $verifyUrl = route('verify.certificate') . '?code=' . urlencode($certificate->certificate_no);
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($verifyUrl) }}" alt="QR Verification" class="border p-1 bg-white rounded-1 shadow-sm" style="width: 75px; height: 75px;">
                            <div class="text-muted font-monospace mt-1" style="font-size: 0.65rem;">SCAN TO VERIFY</div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="row pt-4 mt-3 border-top border-dark text-center">
                        <div class="col-4">
                            <div class="fw-semibold text-dark mb-4">Study Centre Director</div>
                            <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">{{ $centre->director_name ?? 'Centre Incharge' }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-semibold text-dark mb-4">Controller of Examinations</div>
                            <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">Examination Board</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-semibold text-dark mb-4">Director General</div>
                            <div class="border-top border-dark w-75 mx-auto pt-1 small text-muted">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
