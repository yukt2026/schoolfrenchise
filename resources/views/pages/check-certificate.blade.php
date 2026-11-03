@extends('layouts.app')

@section('title', 'Online Document Verification | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Verify official Certificate and Marksheet issued by TECH ERA COMPUTER and its authorized franchise centres. Authentic real-time database verification.')

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Official Certificate &amp; Marksheet Verification</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Student Zone</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verification Engine</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Search Box -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-stamp text-success fs-3"></i>
                            <div>
                                <h5 class="fw-bold text-primary mb-0">Live Document Verification</h5>
                                <small class="text-muted">Anti-Fraud Central Registry</small>
                            </div>
                        </div>

                        <form action="{{ route('verify.certificate.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Registration / Roll / Certificate / MS No. <span class="text-danger">*</span></label>
                                <input type="text" name="registration" class="form-control" placeholder="e.g. DCE/24/8430002 or 2608248430002" value="{{ $queryRegistration ?? old('registration') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Father's Name (Optional)</label>
                                <input type="text" name="father" class="form-control" placeholder="Enter Father's Name" value="{{ $queryFather ?? old('father') }}">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background-color: #0284c7; border-color: #0284c7;">
                                <i class="fa-solid fa-circle-check me-2"></i> Verify Authenticity
                            </button>
                        </form>

                        <div class="mt-4 p-3 bg-white rounded-3 border small text-muted">
                            <strong class="d-block text-dark mb-1"><i class="fa-solid fa-qrcode text-primary me-1"></i> Instant QR Scanner:</strong>
                            Every Certificate, Marksheet, and Student ID Card contains a unique cryptographic QR code. Point any smartphone camera at the QR code to open this genuine verification record instantly.
                        </div>

                        <div class="mt-3 text-center">
                            <span class="badge bg-dark px-3 py-2 text-uppercase font-monospace" style="font-size: 0.72rem;">
                                Authorized by: TECH ERA COMPUTER
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Verification Result Display -->
                @if(isset($searched))
                    @php
                        $mainDoc = $certificate ?: $marksheet;
                        $student = $mainDoc ? $mainDoc->student : null;
                        $course = $mainDoc ? $mainDoc->course : null;
                        $centre = $mainDoc ? $mainDoc->centre : null;
                        $result = $mainDoc ? ($mainDoc->examResult ?? $mainDoc->result) : null;
                        $isRevoked = $certificate && $certificate->status === 'revoked';
                        $statusBadgeClass = $isRevoked ? 'bg-danger' : 'bg-success';
                        $statusText = $isRevoked ? 'REVOKED' : 'OFFICIALLY VERIFIED & ACTIVE';
                    @endphp

                    <div class="col-lg-8">
                        @if($mainDoc)
                            <div class="card border-0 shadow rounded-4 overflow-hidden mb-4" id="printableVerification">
                                
                                <!-- Header Banner -->
                                <div class="p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3" 
                                     style="background: {{ $isRevoked ? 'linear-gradient(135deg, #991b1b 0%, #dc2626 100%)' : 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%)' }};">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                                            <i class="fa-solid {{ $isRevoked ? 'fa-ban text-danger' : 'fa-award text-warning' }} fs-3"></i>
                                        </div>
                                        <div>
                                            <span class="badge {{ $statusBadgeClass }} px-3 py-1 mb-1 font-monospace">STATUS: {{ $statusText }}</span>
                                            <h4 class="fw-extrabold mb-0 text-white">OFFICIALLY VERIFIED CERTIFICATE RECORD</h4>
                                            <small class="text-white-50">Central Directorate of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button onclick="window.print()" class="btn btn-light btn-sm fw-bold">
                                            <i class="fa-solid fa-print me-1"></i> Print Verification
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 p-md-5 bg-white">
                                    
                                    <!-- Institutional Authority Badge -->
                                    <div class="alert alert-info py-2 px-3 mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 small">
                                        <div>
                                            <i class="fa-solid fa-shield-check text-success me-1 fs-6"></i>
                                            <strong>Central Verification:</strong> This document was officially granted by <strong>{{ $centre->centre_name ?? 'Authorized Centre' }}</strong> and verified by <strong>TECH ERA COMPUTER</strong>.
                                        </div>
                                        <span class="badge bg-dark font-monospace">AUTHORIZED BY: TECH ERA COMPUTER</span>
                                    </div>

                                    <!-- Candidate Info & Passport Photo -->
                                    <div class="row g-4 align-items-center mb-4">
                                        <div class="col-md-3 text-center">
                                            <div class="rounded-3 border p-1 bg-light d-inline-block shadow-sm" style="width: 110px; height: 135px; overflow: hidden;">
                                                @if(!empty($student?->photo_url))
                                                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover rounded-2" onerror="this.onerror=null;this.src='https://placehold.co/110x135?text=Photo';">
                                                @elseif(!empty($student?->photo_path) && file_exists(public_path('uploads/' . $student->photo_path)))
                                                    <img src="{{ asset('uploads/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover rounded-2">
                                                @else
                                                    <div class="rounded-2 bg-secondary text-white d-flex align-items-center justify-content-center w-100 h-100" style="font-size: 2.8rem;">
                                                        <i class="fa-solid fa-user-graduate"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-2 fw-bold text-dark fs-6">{{ $student->full_name ?? 'N/A' }}</div>
                                            <span class="badge {{ $statusBadgeClass }} mt-1">Status: {{ ucfirst($statusText) }}</span>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="row g-3 small">
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Student Full Name:</span>
                                                    <strong class="fs-6 text-dark">{{ $student->full_name ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Father's Name:</span>
                                                    <strong class="fs-6 text-dark">{{ $student->father_name ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Registration Number:</span>
                                                    <code class="fs-6 text-primary fw-bold">{{ $student->registration_no ?? 'N/A' }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Roll Number:</span>
                                                    <code class="fs-6 text-primary fw-bold">{{ $student->roll_no ?? 'N/A' }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Certificate Number:</span>
                                                    <strong class="text-danger font-monospace">{{ $certificate->certificate_no ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Marksheet Serial / MS No.:</span>
                                                    <strong class="text-dark font-monospace">{{ $marksheet->marksheet_no ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Date of Issue:</span>
                                                    <strong class="text-dark">{{ $mainDoc->issue_date ? $mainDoc->issue_date->format('d M, Y') : 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Issuing Study Centre:</span>
                                                    <strong class="text-dark">{{ $centre->centre_name ?? 'Tech Era Authorized Centre' }} ({{ $centre->centre_code ?? 'MAIN' }})</strong>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted d-block">Franchise Centre Location:</span>
                                                    <span class="text-secondary">{{ $centre->address ?? ($centre->city . ', ' . ($centre->state->name ?? 'Bihar')) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Course & Evaluation -->
                                    <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                                        <div class="row align-items-center text-center g-3">
                                            <div class="col-md-4 border-end">
                                                <small class="text-muted d-block uppercase fw-bold">Certified Course</small>
                                                <h5 class="fw-bold text-primary mb-0 mt-1">{{ $course->course_name ?? 'Diploma Course' }}</h5>
                                                <span class="badge bg-secondary mt-1">Duration: {{ $course->duration ?? '12 Months' }}</span>
                                            </div>
                                            <div class="col-md-4 border-end">
                                                <small class="text-muted d-block uppercase fw-bold">Total Marks Evaluation</small>
                                                <h4 class="fw-bold text-dark mb-0 mt-1">
                                                    {{ $result->total_marks_obtained ?? 'N/A' }} / {{ $result->total_max_marks ?? 'N/A' }}
                                                </h4>
                                                <small class="text-success fw-bold">Percentage: {{ $result->percentage ?? 'N/A' }}%</small>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block uppercase fw-bold">Grade Awarded</small>
                                                <h2 class="fw-extrabold text-success mb-0 mt-1">
                                                    {{ $result->grade ?? 'A' }}
                                                </h2>
                                                <small class="badge bg-success-subtle text-success">Result: {{ strtoupper($result->result_status ?? 'PASSED') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subject Marks Table (if subjects evaluated) -->
                                    @if($result && $result->subjectResults && $result->subjectResults->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-list-check text-primary me-2"></i>Subject Performance Breakdown:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0 small text-center align-middle">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="text-start">Subject / Module</th>
                                                        <th>Theory</th>
                                                        <th>Practical</th>
                                                        <th>Viva</th>
                                                        <th>Total Obtained</th>
                                                        <th>Max Marks</th>
                                                        <th>Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($result->subjectResults as $sr)
                                                        <tr>
                                                            <td class="text-start fw-bold">{{ $sr->subject->subject_name ?? 'Module' }}</td>
                                                            <td>{{ $sr->theory_obtained }}</td>
                                                            <td>{{ $sr->practical_obtained }}</td>
                                                            <td>{{ $sr->viva_obtained }}</td>
                                                            <td class="fw-bold text-primary">{{ $sr->total_obtained }}</td>
                                                            <td>{{ $sr->max_marks }}</td>
                                                            <td><span class="badge bg-success">{{ $sr->grade ?? 'A' }}</span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Digital Security Seal & Verification Hash -->
                                    <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                                        <div>
                                            <span class="text-muted d-block">Cryptographic Token / Validation Hash:</span>
                                            <code class="text-break">{{ $mainDoc->verification_token ?? ($mainDoc->security_hash ?? hash('sha256', $mainDoc->id)) }}</code>
                                        </div>
                                        <div>
                                            <span class="badge bg-dark text-white px-3 py-2 font-monospace">AUTHORIZED BY: TECH ERA COMPUTER</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger shadow-sm rounded-4 p-4 text-center">
                                <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-circle-xmark"></i></div>
                                <h4 class="fw-bold text-danger">No Genuine Record Found</h4>
                                <p class="text-muted mb-3">
                                    No verified certificate or marksheet was found in the central registry matching: <code>{{ $queryRegistration }}</code>
                                    @if(!empty($queryFather)) with Father's Name: <code>{{ $queryFather }}</code> @endif.
                                </p>
                                <div class="alert alert-warning d-inline-block small mb-0">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    <strong>Anti-Fraud Warning:</strong> If you were provided a certificate or marksheet with this number, it may be invalid or counterfeit. Please report it to <a href="mailto:info@techeracomputer.in" class="fw-bold text-dark">info@techeracomputer.in</a>.
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <style>
    @media print {
        header, footer, .dce-page-banner, .col-lg-4, .btn, .dce-topbar, .navbar, nav, .dce-footer {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .col-lg-8 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
        #printableVerification {
            box-shadow: none !important;
            border: 2px solid #0f172a !important;
            page-break-inside: avoid !important;
        }
    }
    </style>
@endsection
