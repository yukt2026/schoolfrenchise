@extends('layouts.app')

@section('title', 'Student ID Card Live Verification | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="dce-page-banner py-4" style="background: linear-gradient(135deg, #0b1a2e 0%, #1e3a5f 100%); color: #ffffff;">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-1 rounded-pill small mb-2 text-warning font-monospace">
            <i class="fa-solid fa-qrcode"></i> QR CODE AUTHENTICATION
        </div>
        <h2 class="fw-bold mb-1">Official Student ID Card Verification</h2>
        <p class="text-white-50 small mb-0">Central Academic Credential Verification System &bull; {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</p>
    </div>
</div>

<section class="py-5" style="background-color: #f8fafc; min-height: 60vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">

                @if($status === 'valid' && $student)
                    <!-- VALID STATUS CARD -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-sm mb-2" style="width: 68px; height: 68px;">
                                <i class="fa-solid fa-shield-check fa-2x"></i>
                            </div>
                            <h3 class="fw-bolder mb-1 letter-spacing-1">VALID STUDENT CREDENTIAL</h3>
                            <p class="small mb-0 text-white-50">This student identity card is officially active and verified against the central database.</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <!-- Prominent Franchise & Authorization Header -->
                            <div class="p-3 rounded-3 mb-4 border text-center text-md-start" style="background-color: #f1f5f9;">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-2 text-center">
                                        @if($centre && !empty($centre->centre_logo_url))
                                            <img src="{{ $centre->centre_logo_url }}" alt="{{ $centre->centre_name }}" class="rounded bg-white p-1 border shadow-sm" style="max-height: 56px; max-width: 100%; object-fit: contain;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                                                <i class="fa-solid fa-building-columns fs-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-10">
                                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Issuing Franchise / Study Centre</div>
                                        <h4 class="fw-bolder text-dark mb-1">{{ $centre->centre_name ?? 'AUTHORIZED STUDY CENTRE' }}</h4>
                                        <div class="fw-bold small text-uppercase" style="color: #b45309;">
                                            <i class="fa-solid fa-certificate me-1"></i> Authorized by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                                        </div>
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $centre->centre_address ?? ($centre->district?->name . ', ' . ($centre->state?->name ?? 'Bihar')) }}
                                            &bull; Centre Code: <strong class="text-dark font-monospace">{{ $centre->centre_code ?? 'MAIN' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Particulars -->
                            <div class="row g-4 align-items-center mb-4">
                                <div class="col-12 col-sm-4 text-center">
                                    <div class="d-inline-block p-1 rounded-3 border border-2 border-success bg-white shadow-sm" style="width: 120px; height: 140px; overflow: hidden;">
                                        @if($student->photo_url)
                                            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover rounded-2" onerror="this.onerror=null;this.src='https://placehold.co/120x140?text=Photo';">
                                        @else
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted bg-light rounded-2">
                                                <i class="fa-solid fa-user-graduate fs-1 opacity-50"></i>
                                                <span style="font-size: 0.65rem;" class="mt-1">Verified</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-1 fw-bold">
                                            <i class="fa-solid fa-circle-check me-1"></i> VALID CARD
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <h3 class="fw-bolder text-dark text-uppercase mb-1">{{ $student->full_name }}</h3>
                                    <div class="text-primary fw-bold mb-3 fs-6">
                                        {{ $course->course_name ?? 'Computer Course' }}
                                        @if($course && $course->course_code)
                                            <span class="text-muted font-monospace">({{ $course->course_code }})</span>
                                        @endif
                                    </div>

                                    <table class="table table-sm table-borderless mb-0 small">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted ps-0" style="width: 40%;">Student ID / Roll No:</th>
                                                <td class="fw-bold font-monospace text-dark">{{ $student->roll_no ?? $student->registration_no }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-0">Registration No:</th>
                                                <td class="font-monospace text-dark">{{ $student->registration_no }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-0">Father's Name:</th>
                                                <td class="fw-semibold text-dark">{{ $student->father_name }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-0">Admission / Joining Date:</th>
                                                <td class="text-dark">{{ $admission?->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('d F, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-0">Card Validity / Expiry:</th>
                                                <td class="fw-bold text-success">
                                                    {{ $student->calculated_expiry_date ? $student->calculated_expiry_date->format('d F, Y') : 'Valid' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-0">Central Verification Token:</th>
                                                <td class="font-monospace text-muted" style="font-size: 0.72rem;">{{ $student->verification_token }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Footer Trust Assurance -->
                            <div class="border-top pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
                                <div>
                                    <i class="fa-solid fa-building me-1 text-primary"></i> Head Office: {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}
                                </div>
                                <div class="font-monospace text-success fw-bold">
                                    <i class="fa-solid fa-lock me-1"></i> SECURE LIVE RECORD
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($status === 'expired' && $student)
                    <!-- EXPIRED STATUS CARD -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-warning shadow-sm mb-2" style="width: 68px; height: 68px;">
                                <i class="fa-solid fa-calendar-xmark fa-2x text-warning"></i>
                            </div>
                            <h3 class="fw-bolder mb-1">ID CARD EXPIRED</h3>
                            <p class="small mb-0 text-white-50">This student identity credential has passed its validity period.</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <div class="alert alert-warning border-0 rounded-3 mb-4">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                <strong>Card Validity Expired:</strong> This ID card was valid until <strong>{{ $student->calculated_expiry_date ? $student->calculated_expiry_date->format('d F, Y') : 'Past Date' }}</strong>. Please contact the issuing centre (<strong>{{ $centre->centre_name ?? 'Study Centre' }}</strong>) for renewal.
                            </div>

                            <div class="row g-3 align-items-center">
                                <div class="col-sm-4 text-center">
                                    <div class="d-inline-block p-1 rounded-3 border border-2 border-warning bg-white shadow-sm" style="width: 110px; height: 130px; overflow: hidden;">
                                        @if($student->photo_url)
                                            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover rounded-2">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                                <i class="fa-solid fa-user-graduate fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h4 class="fw-bold text-dark mb-1">{{ $student->full_name }}</h4>
                                    <div class="text-muted small mb-2">ID: <span class="font-monospace fw-bold text-dark">{{ $student->roll_no ?? $student->registration_no }}</span></div>
                                    <div class="small text-muted">Centre: <strong>{{ $centre->centre_name ?? 'N/A' }}</strong></div>
                                    <div class="small text-muted">Authorization: <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong></div>
                                    <div class="small text-danger fw-bold mt-2">Expired on: {{ $student->calculated_expiry_date ? $student->calculated_expiry_date->format('d-m-Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($status === 'revoked' && $student)
                    <!-- REVOKED STATUS CARD -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-danger shadow-sm mb-2" style="width: 68px; height: 68px;">
                                <i class="fa-solid fa-ban fa-2x"></i>
                            </div>
                            <h3 class="fw-bolder mb-1">ID CARD REVOKED / SUSPENDED</h3>
                            <p class="small mb-0 text-white-50">This student credential has been officially revoked or deactivated by administration.</p>
                        </div>

                        <div class="card-body p-4 p-md-5 text-center">
                            <p class="text-danger fw-semibold mb-3">Notice: This credential is no longer recognized as an active student identity pass.</p>
                            <div class="p-3 bg-light rounded-3 text-start mb-3">
                                <div><strong>Student Name:</strong> {{ $student->full_name }}</div>
                                <div><strong>Student ID:</strong> {{ $student->roll_no ?? $student->registration_no }}</div>
                                <div><strong>Issuing Centre:</strong> {{ $centre->centre_name ?? 'N/A' }}</div>
                                <div><strong>Authorized by:</strong> {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- INVALID / NOT FOUND STATUS CARD -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 100%);">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-danger shadow-sm mb-2" style="width: 68px; height: 68px;">
                                <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                            </div>
                            <h3 class="fw-bolder mb-1">INVALID VERIFICATION RECORD</h3>
                            <p class="small mb-0 text-white-50">No matching student credential found for this token.</p>
                        </div>

                        <div class="card-body p-4 p-md-5 text-center">
                            <div class="alert alert-danger border-0 rounded-3 mb-4">
                                <i class="fa-solid fa-circle-xmark me-2"></i>
                                <strong>Verification Failed:</strong> The verification token <code class="bg-white px-2 py-1 rounded text-danger fw-bold">{{ $token }}</code> does not exist in our authorized academic database.
                            </div>
                            <p class="text-muted small">This ID card may be counterfeit, invalid, or tampered with. For official confirmation, please contact the central examination board.</p>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-2 px-4 fw-semibold mt-2">
                                <i class="fa-solid fa-arrow-left me-1"></i> Return to Homepage
                            </a>
                        </div>
                    </div>
                @endif

                <div class="text-center text-muted small mt-3">
                    &copy; {{ date('Y') }} {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
