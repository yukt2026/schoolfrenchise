@extends('layouts.app')

@section('title', 'Check Certificate / Marksheet | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Verify official Certificate and Marksheet issued by TECH ERA COMPUTER. Enter Registration Number and Father Name to verify authenticity.')

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Check Certificate / Marksheet Verification</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Student Zone</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Check Certificate</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Search Box -->
                <div class="col-lg-5 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-stamp text-success fs-3"></i>
                            <div>
                                <h4 class="fw-bold text-primary mb-0">Online Verification</h4>
                                <small class="text-muted">Instant Anti-Fraud Verification Engine</small>
                            </div>
                        </div>

                        <form action="{{ route('verify.certificate.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Registration No. / Roll No. / Certificate No. <span class="text-danger">*</span></label>
                                <input type="text" name="registration" class="form-control" placeholder="e.g. TEC2601001 or TEC/CERT/2026/001" value="{{ $queryRegistration ?? old('registration') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Father's Name (Optional)</label>
                                <input type="text" name="father" class="form-control" placeholder="Enter Father's Name" value="{{ $queryFather ?? old('father') }}">
                            </div>
                            <button type="submit" class="btn btn-dce-primary w-100 py-2 fw-bold">
                                <i class="fa-solid fa-circle-check me-2"></i> Verify Now
                            </button>
                        </form>

                        <div class="mt-4 p-3 bg-light rounded-3 border small text-muted">
                            <strong class="d-block text-dark"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Quick Tip:</strong>
                            You can also verify any official certificate by pointing your mobile phone camera at the QR code printed on the physical certificate.
                        </div>
                    </div>
                </div>

                <!-- Result Card -->
                @if(isset($searched))
                    <div class="col-lg-8">
                        @if($certificate)
                            <div class="dce-verified-card mb-4" id="printableCertificate">
                                <div class="dce-seal-badge">
                                    <i class="fa-solid fa-check-double fs-4 mb-1"></i>
                                    <span>Verified</span>
                                </div>
                                <div class="dce-verified-header">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fa-solid fa-award text-warning fs-3"></i>
                                        <h3 class="fw-bold mb-0 text-white">OFFICIALLY VERIFIED CERTIFICATE RECORD</h3>
                                    </div>
                                    <p class="small text-white-50 mb-0">Central Registry of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</p>
                                </div>

                                <div class="p-4 p-md-5 bg-white">
                                    <!-- Candidate Info -->
                                    <div class="row g-4 align-items-center mb-4">
                                        <div class="col-md-3 text-center">
                                            <div class="rounded-circle border p-1 bg-light d-inline-block shadow-sm" style="width: 110px; height: 110px; overflow: hidden;">
                                                @if($certificate->student?->photo_url)
                                                    <img src="{{ $certificate->student->photo_url }}" alt="{{ $certificate->student->full_name }}" class="w-100 h-100 object-fit-cover rounded-circle" onerror="this.onerror=null;this.src='https://placehold.co/110x110?text=Photo';">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-100 h-100" style="font-size: 2.8rem;">
                                                        <i class="fa-solid fa-user-graduate"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-2 fw-bold text-dark fs-6">{{ $certificate->student->full_name }}</div>
                                            <span class="badge bg-success mt-1">Status: {{ ucfirst($certificate->status) }}</span>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="row g-3 small">
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Student Name:</span>
                                                    <strong class="fs-6 text-dark">{{ $certificate->student->full_name }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Father's Name:</span>
                                                    <strong class="fs-6 text-dark">{{ $certificate->student->father_name }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Registration Number:</span>
                                                    <code class="fs-6 text-primary fw-bold">{{ $certificate->student->registration_no }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Roll Number:</span>
                                                    <code class="fs-6 text-primary fw-bold">{{ $certificate->student->roll_no }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Certificate Number:</span>
                                                    <strong class="text-success">{{ $certificate->certificate_no }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Marksheet Serial:</span>
                                                    <strong>{{ $marksheet->marksheet_no ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Date of Issue:</span>
                                                    <strong>{{ $certificate->issue_date ? $certificate->issue_date->format('d M, Y') : 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Accredited Study Centre:</span>
                                                    <strong>{{ $certificate->centre->centre_name ?? ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }} ({{ $certificate->centre->centre_code ?? 'MAIN' }})</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Performance -->
                                    <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                                        <div class="row align-items-center text-center g-3">
                                            <div class="col-md-4 border-end">
                                                <small class="text-muted d-block uppercase fw-bold">Certified Course</small>
                                                <h5 class="fw-bold text-primary mb-0 mt-1">{{ $certificate->course->course_name }}</h5>
                                                <span class="badge bg-secondary mt-1">Duration: {{ $certificate->course->duration }}</span>
                                            </div>
                                            <div class="col-md-4 border-end">
                                                <small class="text-muted d-block uppercase fw-bold">Marks Evaluation</small>
                                                <h4 class="fw-bold text-dark mb-0 mt-1">
                                                    {{ $certificate->result->total_marks_obtained ?? 'N/A' }} / {{ $certificate->result->total_max_marks ?? 'N/A' }}
                                                </h4>
                                                <small class="text-success fw-bold">Percentage: {{ $certificate->result->percentage ?? 'N/A' }}%</small>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block uppercase fw-bold">Grade Awarded</small>
                                                <h2 class="fw-extrabold text-success mb-0 mt-1">
                                                    {{ $certificate->result->grade ?? 'A' }}
                                                </h2>
                                                <small class="text-muted">Result: {{ strtoupper($certificate->result->result_status ?? 'PASSED') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subject Marks Table (if subjects evaluated) -->
                                    @if($certificate->result && $certificate->result->subjectResults->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-list-check text-primary me-2"></i>Subject Performance Breakdown:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0 small text-center align-middle">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th class="text-start">Subject / Module</th>
                                                        <th>Theory</th>
                                                        <th>Practical</th>
                                                        <th>Viva</th>
                                                        <th>Total Obtained</th>
                                                        <th>Max</th>
                                                        <th>Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($certificate->result->subjectResults as $sr)
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
                                            <span class="text-muted d-block">Security Validation Hash:</span>
                                            <code>{{ $certificate->security_hash ?? hash('sha256', $certificate->certificate_no) }}</code>
                                        </div>
                                        <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold">
                                            <i class="fa-solid fa-print me-1"></i> Print Verification
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger shadow-sm rounded-4 p-4 text-center">
                                <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-circle-xmark"></i></div>
                                <h4 class="fw-bold text-danger">No Certificate Record Found</h4>
                                <p class="text-muted mb-3">
                                    No verified certificate or marksheet was found matching: <code>{{ $queryRegistration }}</code>
                                    @if(!empty($queryFather)) with Father's Name: <code>{{ $queryFather }}</code> @endif.
                                </p>
                                <p class="small text-secondary mb-0">Please check your Registration Number or verify with your issuing study centre.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <style>
    @media print {
        header, footer, .dce-page-banner, .col-lg-5, .btn, .dce-topbar, .navbar, nav, .dce-footer {
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
        .dce-verified-card {
            box-shadow: none !important;
            border: 2px solid #0f172a !important;
            page-break-inside: avoid !important;
        }
    }
    </style>
@endsection
