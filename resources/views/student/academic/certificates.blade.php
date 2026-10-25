@extends('layouts.student')

@section('title', 'Certificates & Diplomas - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Certificates</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-award text-primary me-2"></i>Certificates & Diplomas
        </h2>
        <p class="text-muted small mb-0">View official course completion certificates, online verification credentials, and credentials issued by the council.</p>
    </div>
    @if($admission && $admission->course)
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-1 fs-7">
            <i class="fa-solid fa-graduation-cap me-1"></i> {{ $admission->course->course_name }}
        </span>
    @endif
</div>

<!-- Certificate Cards List -->
@if($certificates->count() > 0)
    <div class="row g-4 mb-4">
        @foreach($certificates as $cert)
            <div class="col-12 col-lg-6">
                <div class="card border border-light-subtle shadow-sm rounded-3 h-100 overflow-hidden bg-white">
                    <div class="card-header bg-gradient p-4 text-white" style="background: linear-gradient(135deg, #0b1329 0%, #0284c7 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-white text-primary text-uppercase px-2 py-1 mb-2 fw-bold rounded-1" style="font-size: 0.7rem;">
                                    OFFICIAL CERTIFICATE
                                </span>
                                <h5 class="fw-bold mb-1 text-white">{{ $cert->course->course_name ?? 'Computer Course' }}</h5>
                                <div class="text-white-50 small">Issued by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 text-white">
                                <i class="fa-solid fa-medal fs-3"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Certificate No</div>
                                <div class="fw-bold font-monospace text-primary fs-6">{{ $cert->certificate_no }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Date of Issue</div>
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($cert->issue_date)->format('d M, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Grade / Division</div>
                                <div class="fw-bold text-dark">
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-1">
                                        {{ $cert->result->grade ?? 'A' }} ({{ $cert->result->percentage ?? '80' }}%)
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Status</div>
                                <div>
                                    @if($cert->status === 'issued')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> ISSUED & VALID
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-1">
                                            <i class="fa-solid fa-ban me-1"></i> REVOKED
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Verification Security Hash & QR Info -->
                        @if($cert->security_hash)
                            <div class="p-3 bg-light rounded-3 border mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="small fw-bold text-muted text-uppercase" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Cryptographic Verification Hash
                                    </div>
                                    <span class="badge bg-success-subtle text-success rounded-1" style="font-size: 0.65rem;">Tamper-Proof</span>
                                </div>
                                <div class="font-monospace text-muted small text-break" style="font-size: 0.75rem;">
                                    {{ $cert->security_hash }}
                                </div>
                            </div>
                        @endif

                        <!-- Action Controls -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('verify.certificate', ['cert_no' => $cert->certificate_no]) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-2 flex-grow-1 fw-semibold">
                                <i class="fa-solid fa-qrcode me-1"></i> Verify Authenticity
                            </a>
                            <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 fw-semibold px-3">
                                <i class="fa-solid fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $certificates->links() }}
    </div>
@else
    <div class="card border border-light-subtle shadow-sm rounded-3 text-center py-5 bg-white">
        <div class="card-body">
            <div class="bg-light rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-award text-muted fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No Certificates Issued Yet</h5>
            <p class="text-muted small mb-4" style="max-width: 440px; margin: 0 auto;">
                Official course diplomas and completion certificates are generated upon successful clearance of course exams, practical assessments, and fee clearance.
            </p>

            <div class="d-inline-flex flex-column align-items-start p-3 bg-light rounded-3 border text-start" style="max-width: 460px;">
                <div class="small fw-bold text-dark mb-2"><i class="fa-solid fa-list-check text-primary me-2"></i>Certificate Eligibility Checklist:</div>
                <ul class="text-muted small mb-0 ps-3" style="font-size: 0.8rem;">
                    <li>Complete all module lectures & required attendance (&ge; 75%).</li>
                    <li>Appear and score passing grade in terminal theory & practical exams.</li>
                    <li>Clear any outstanding tuition fee dues with the centre office.</li>
                </ul>
            </div>
        </div>
    </div>
@endif
@endsection
