@extends('layouts.student')

@section('title', 'Digital Student ID Card - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<style>
    :root {
        --olive-green: #8f9f1b;
        --olive-green-dark: #7a8915;
        --navy-blue: #0d2238;
        --navy-dark: #071524;
        --card-width: 334px;
        --card-height: 528px;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        box-sizing: border-box;
    }

    .id-print-canvas {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        gap: 28px;
        position: relative;
    }

    .id-card-unit {
        width: var(--card-width);
        height: var(--card-height);
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .slot-punch {
        width: 44px;
        height: 9px;
        border-radius: 5px;
        background: #e2e8f0;
        margin: 10px auto 4px auto;
        box-shadow: inset 0 1.5px 3px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .front-header {
        padding: 4px 14px 6px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .front-header-logo {
        width: 44px;
        height: 44px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .front-header-info {
        flex-grow: 1;
        line-height: 1.15;
    }

    .franchise-title {
        font-size: 0.84rem;
        font-weight: 800;
        color: var(--navy-blue);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin: 0 0 2px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .franchise-address {
        font-size: 0.56rem;
        color: #64748b;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 250px;
    }

    .authorized-sub {
        font-size: 0.60rem;
        font-weight: 700;
        color: var(--navy-blue);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .photo-stage {
        position: relative;
        width: 100%;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 2px;
    }

    .wedge-left {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 58px;
        height: 96px;
        pointer-events: none;
    }

    .wedge-right {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 58px;
        height: 96px;
        pointer-events: none;
    }

    .hexagon-wrap {
        position: relative;
        width: 98px;
        height: 112px;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.15));
        z-index: 2;
    }

    .hexagon-outer {
        width: 100%;
        height: 100%;
        background: var(--olive-green);
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3.5px;
    }

    .hexagon-inner {
        width: 100%;
        height: 100%;
        background: #ffffff;
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5px;
    }

    .hexagon-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    }

    .student-header-block {
        text-align: center;
        padding: 2px 14px 0 14px;
    }

    .student-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        line-height: 1.2;
    }

    .student-role {
        font-size: 0.72rem;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-top: 1px;
    }

    .info-grid-container {
        padding: 4px 22px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.69rem;
        line-height: 1.32;
    }

    .info-table td {
        padding: 1.5px 0;
        vertical-align: baseline;
    }

    .info-label {
        width: 58px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .info-colon {
        width: 12px;
        text-align: center;
        font-weight: 700;
        color: #0f172a;
    }

    .info-value {
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
    }

    .signature-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding: 0 18px 6px 18px;
        margin-top: auto;
    }

    .sig-block {
        text-align: center;
        width: 82px;
    }

    .sig-script {
        font-family: 'Dancing Script', cursive;
        font-size: 0.95rem;
        color: #0f172a;
        line-height: 1;
        margin-bottom: -2px;
        min-height: 18px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .sig-img {
        max-height: 20px;
        max-width: 78px;
        object-fit: contain;
        margin: 0 auto -2px auto;
        display: block;
    }

    .sig-line {
        border-top: 1px solid #1e293b;
        width: 100%;
        margin: 2px auto 2px auto;
    }

    .sig-caption {
        font-size: 0.58rem;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .stamp-center {
        text-align: center;
        width: 56px;
    }

    .front-footer-bar {
        position: relative;
        width: 100%;
        height: 24px;
        background: var(--navy-blue);
        display: flex;
        align-items: center;
        padding-left: 14px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .front-footer-text {
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        z-index: 2;
    }

    .front-footer-accent {
        position: absolute;
        right: 0;
        top: 0;
        width: 32px;
        height: 100%;
        background: var(--olive-green);
        clip-path: polygon(35% 0, 100% 0, 100% 100%, 0% 100%);
    }

    .back-top-banner {
        width: 100%;
        height: 38px;
        flex-shrink: 0;
    }

    .back-terms-box {
        padding: 4px 20px 0 20px;
        text-align: center;
    }

    .back-terms-heading {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--navy-blue);
        letter-spacing: 0.5px;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .back-terms-text {
        font-size: 0.56rem;
        line-height: 1.35;
        color: #475569;
        margin-bottom: 0;
        text-align: justify;
    }

    .back-info-box {
        padding: 6px 32px;
        display: flex;
        justify-content: center;
    }

    .back-ho-sig {
        text-align: center;
        margin: 2px auto 6px auto;
        width: 110px;
    }

    .back-qr-seal-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 22px;
        padding: 4px 20px;
    }

    .qr-frame {
        background: #ffffff;
        padding: 3px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        display: inline-block;
    }

    .qr-frame img {
        width: 66px;
        height: 66px;
        display: block;
    }

    .back-branding {
        text-align: center;
        padding: 4px 14px 2px 14px;
    }

    .back-franchise-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--navy-blue);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 0 0 1px 0;
        line-height: 1.2;
    }

    .back-authorized-by {
        font-size: 0.60rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        margin: 0 0 1px 0;
    }

    .back-address {
        font-size: 0.55rem;
        color: #64748b;
        margin: 0;
        line-height: 1.25;
    }

    .back-bottom-accent {
        width: 100%;
        height: 26px;
        flex-shrink: 0;
    }

    @page {
        size: 54mm 85.6mm portrait;
        margin: 0;
    }

    @media print {
        header, footer, nav, .sidebar, .navbar, .no-print {
            display: none !important;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .id-print-canvas {
            display: block !important;
            gap: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .id-card-unit {
            width: 54mm !important;
            height: 85.6mm !important;
            max-width: 54mm !important;
            max-height: 85.6mm !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            page-break-after: always !important;
            page-break-inside: avoid !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $centre = $student->centre;
@endphp
<div class="container-fluid px-0">
    <!-- Action Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 no-print gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-id-card text-primary me-2"></i> Official Student Identity Card
            </h4>
            <div class="text-muted small">Standard CR80 portrait credential issued by <strong>{{ $centre->centre_name ?? 'Authorized Study Centre' }}</strong></div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-2 px-4 btn-sm fw-bold shadow-sm" style="background-color: #0d2238; border-color: #0d2238;">
                <i class="fa-solid fa-print me-1"></i> Print / Save PDF
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Dual-Sided ID Canvas -->
    <div class="id-print-canvas py-3">

        <!-- ================= FRONT SIDE ================= -->
        <div class="id-card-unit">
            <!-- Slot Punch Cutout -->
            <div class="slot-punch"></div>

            <!-- Front Header -->
            <div class="front-header">
                @php
                    $franchiseLogo = $centre->centre_logo_url ?? ($instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png'));
                @endphp
                <img src="{{ $franchiseLogo }}" alt="{{ $centre->centre_name }}" class="front-header-logo" onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                <div class="front-header-info">
                    <h1 class="franchise-title">{{ $centre->centre_name ?? 'AMAN COMPUTER SKILLS ACADEMY' }}</h1>
                    <div class="franchise-address">Address: {{ $centre->centre_address ?? ($centre->district?->name . ', ' . ($centre->state?->name ?? 'Bihar')) }}</div>
                    <div class="authorized-sub">Authorized by: {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                </div>
            </div>

            <!-- Hexagonal Photo Stage with Flanking Chevrons -->
            <div class="photo-stage">
                <!-- Left Wedge SVG -->
                <svg class="wedge-left" viewBox="0 0 58 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="0,0 52,48 0,96" fill="#0d2238" />
                    <polygon points="0,14 38,48 0,82" fill="#8f9f1b" />
                </svg>

                <!-- Hexagonal Photo -->
                <div class="hexagon-wrap">
                    <div class="hexagon-outer">
                        <div class="hexagon-inner">
                            @if($student->photo_url)
                                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="hexagon-photo" onerror="this.onerror=null;this.src='https://placehold.co/120x135?text=Photo';">
                            @else
                                <div class="hexagon-photo d-flex align-items-center justify-content-center bg-light text-muted">
                                    <i class="fa-solid fa-user-graduate fa-2x opacity-50"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Wedge SVG -->
                <svg class="wedge-right" viewBox="0 0 58 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="58,0 6,48 58,96" fill="#0d2238" />
                    <polygon points="58,14 20,48 58,82" fill="#8f9f1b" />
                </svg>
            </div>

            <!-- Student Name and Course Title -->
            <div class="student-header-block">
                <h2 class="student-name">{{ strtoupper($student->full_name) }}</h2>
                <div class="student-role">STUDENT ({{ strtoupper($admission?->course?->course_code ?? ($admission?->course?->course_name ?? 'ADCA')) }})</div>
            </div>

            <!-- Clean Aligned Particulars Grid -->
            <div class="info-grid-container">
                <table class="info-table">
                    <tbody>
                        <tr>
                            <td class="info-label">ID No</td>
                            <td class="info-colon">:</td>
                            <td class="info-value font-monospace fw-bold">{{ $student->roll_no ?? $student->registration_no }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">DOB</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : '--/--/----' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone</td>
                            <td class="info-colon">:</td>
                            <td class="info-value font-monospace">{{ $student->mobile }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Father</td>
                            <td class="info-colon">:</td>
                            <td class="info-value text-uppercase">{{ $student->father_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Mother</td>
                            <td class="info-colon">:</td>
                            <td class="info-value text-uppercase">{{ $student->mother_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Email</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $student->email ?? 'abc@gmail.com' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Join</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $admission?->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('d-m-Y') : '--/--/----' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Expiry</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $student->calculated_expiry_date ? $student->calculated_expiry_date->format('d-m-Y') : '31-03-2025' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures and Official Stamp Area -->
            <div class="signature-row">
                <!-- Student Signature -->
                <div class="sig-block">
                    <div class="sig-script">
                        @if($student->signature_url)
                            <img src="{{ $student->signature_url }}" alt="Sign" class="sig-img">
                        @else
                            {{ ucwords(strtolower($student->full_name)) }}
                        @endif
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-caption text-truncate">{{ ucwords(strtolower($student->full_name)) }}</div>
                </div>

                <!-- Center Official Seal -->
                <div class="stamp-center">
                    @if(!empty($centre->seal_url))
                        <img src="{{ $centre->seal_url }}" alt="Seal" style="width: 44px; height: 44px; object-fit: contain;">
                    @else
                        <svg viewBox="0 0 100 100" style="width: 44px; height: 44px;">
                            <circle cx="50" cy="50" r="47" fill="#ffffff" stroke="#0284c7" stroke-width="2.5" stroke-dasharray="2,1"/>
                            <circle cx="50" cy="50" r="41" fill="#f0f9ff" stroke="#0369a1" stroke-width="1.5"/>
                            <circle cx="50" cy="50" r="27" fill="#ffffff" stroke="#0284c7" stroke-width="1"/>
                            <path id="frontSealTextStudent" d="M 18,50 A 32,32 0 1,1 82,50 A 32,32 0 1,1 18,50" fill="none"/>
                            <text font-size="7" font-weight="bold" fill="#0369a1" letter-spacing="1">
                                <textPath href="#frontSealTextStudent" startOffset="50%" text-anchor="middle">
                                    {{ strtoupper(substr($centre->centre_name ?? 'TECH ERA COMPUTER', 0, 24)) }}
                                </textPath>
                            </text>
                            <path d="M40,54 L50,48 L60,54 L50,60 Z M44,57 L44,63 C44,65 56,65 56,63 L56,57" fill="#0284c7" stroke="#0369a1" stroke-width="0.8"/>
                        </svg>
                    @endif
                </div>

                <!-- Authorized Signatory -->
                <div class="sig-block">
                    <div class="sig-script">
                        @if(!empty($centre->director_signature_url))
                            <img src="{{ $centre->director_signature_url }}" alt="Sign" class="sig-img">
                        @else
                            Authorized
                        @endif
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-caption">Authorized Signatory</div>
                </div>
            </div>

            <!-- Bottom Navy and Green Footer -->
            <div class="front-footer-bar">
                <span class="front-footer-text">ID Number: {{ $student->roll_no ?? $student->registration_no }}</span>
                <div class="front-footer-accent"></div>
            </div>
        </div>

        <!-- ================= BACK SIDE ================= -->
        <div class="id-card-unit">
            <!-- Slot Punch Cutout -->
            <div class="slot-punch"></div>

            <!-- Top Curved Olive and Navy Geometric Wave -->
            <div class="back-top-banner">
                <svg viewBox="0 0 340 44" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                    <path d="M0,0 L340,0 L340,24 Q170,44 0,24 Z" fill="#8f9f1b" />
                    <path d="M0,24 Q170,44 340,24 L340,29 Q170,49 0,29 Z" fill="#0d2238" />
                </svg>
            </div>

            <!-- TERMS AND CONDITIONS -->
            <div class="back-terms-box">
                <h3 class="back-terms-heading">TERMS AND CONDITIONS</h3>
                <p class="back-terms-text">
                    1. This identity credential is non-transferable and remains the property of the issuing institute.<br>
                    2. Must be presented on demand during regular classes, laboratory practicals, and examination cycles.<br>
                    3. In the event of loss or damage, report immediately to the centre coordinator for official re-issuance.
                </p>
            </div>

            <!-- Student Enrolment Particulars -->
            <div class="back-info-box">
                <table class="info-table" style="max-width: 230px;">
                    <tbody>
                        <tr>
                            <td class="info-label" style="width: 60px;">Joined</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $admission?->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('d-m-Y') : '--/--/----' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Expire</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $student->calculated_expiry_date ? $student->calculated_expiry_date->format('d-m-Y') : '31-03-2025' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone</td>
                            <td class="info-colon">:</td>
                            <td class="info-value font-monospace">{{ $student->mobile }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Email</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $student->email ?? 'abc@gmail.com' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- H.O. Signature Area -->
            <div class="back-ho-sig">
                <div class="sig-script" style="height: 24px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('images/deepak-kumar-signature.png') }}" alt="H.O. Signature" style="max-height: 24px; max-width: 90px; object-fit: contain;">
                </div>
                <div class="sig-line" style="width: 90px;"></div>
                <div class="sig-caption">H.O. Signature</div>
            </div>

            <!-- QR Code Section & Seal Side-by-Side -->
            <div class="back-qr-seal-row">
                <!-- Real, Automatic, Unique Cryptographic QR Code -->
                <div class="qr-frame">
                    <img src="{{ $student->qr_code_data_uri }}" alt="Verify QR" width="66" height="66">
                </div>

                <!-- Round Institute Seal -->
                <div class="stamp-center">
                    @if(!empty($centre->seal_url))
                        <img src="{{ $centre->seal_url }}" alt="Seal" style="width: 58px; height: 58px; object-fit: contain;">
                    @elseif(file_exists(public_path('images/tech-era-seal.png')))
                        <img src="{{ asset('images/tech-era-seal.png') }}" alt="Tech Era Seal" style="width: 58px; height: 58px; object-fit: contain;">
                    @else
                        <svg viewBox="0 0 100 100" style="width: 58px; height: 58px;">
                            <circle cx="50" cy="50" r="47" fill="#ffffff" stroke="#0284c7" stroke-width="2.5" stroke-dasharray="2,1"/>
                            <circle cx="50" cy="50" r="41" fill="#f0f9ff" stroke="#0369a1" stroke-width="1.5"/>
                            <circle cx="50" cy="50" r="27" fill="#ffffff" stroke="#0284c7" stroke-width="1"/>
                            <path id="backSealTextStudent" d="M 18,50 A 32,32 0 1,1 82,50 A 32,32 0 1,1 18,50" fill="none"/>
                            <text font-size="7" font-weight="bold" fill="#0369a1" letter-spacing="1">
                                <textPath href="#backSealTextStudent" startOffset="50%" text-anchor="middle">
                                    {{ strtoupper(substr($centre->centre_name ?? 'TECH ERA COMPUTER', 0, 24)) }}
                                </textPath>
                            </text>
                            <path d="M40,54 L50,48 L60,54 L50,60 Z M44,57 L44,63 C44,65 56,65 56,63 L56,57" fill="#0284c7" stroke="#0369a1" stroke-width="0.8"/>
                        </svg>
                    @endif
                </div>
            </div>

            <!-- Bottom Branding -->
            <div class="back-branding">
                <h4 class="back-franchise-name">{{ $centre->centre_name ?? 'AMAN COMPUTER INSTITUTE' }}</h4>
                <div class="back-authorized-by">Authorized by: {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER PVT LTD' }}</div>
                <p class="back-address">Address: {{ $centre->centre_address ?? ($centre->district?->name . ', ' . ($centre->state?->name ?? 'Bihar') . ' - 805432') }}</p>
            </div>

            <!-- Bottom Curved Geometric Accent -->
            <div class="back-bottom-accent">
                <svg viewBox="0 0 340 32" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                    <path d="M0,9 Q170,-9 340,9 L340,5 Q170,-13 0,5 Z" fill="#0d2238" />
                    <path d="M0,9 Q170,-9 340,9 L340,32 L0,32 Z" fill="#8f9f1b" />
                </svg>
            </div>
        </div>

    </div>
</div>
@endsection
