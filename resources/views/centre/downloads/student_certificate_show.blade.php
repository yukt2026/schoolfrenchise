<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Diploma Certificate - {{ $certificate->certificate_no }} - {{ $certificate->student->full_name ?? 'Candidate' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            background-color: #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }

        .no-print-bar {
            max-width: 1100px;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .cert-container {
            width: 297mm;
            height: 210mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            position: relative;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            padding: 10mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-border-outer {
            border: 5px solid #b45309;
            height: 100%;
            padding: 4px;
            position: relative;
            background: #ffffff;
        }

        .cert-border-inner {
            border: 2px dashed #d97706;
            height: 100%;
            padding: 14px 24px;
            position: relative;
            background: radial-gradient(circle at center, #ffffff 55%, #fffdf5 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .corner-accent {
            position: absolute;
            width: 24px;
            height: 24px;
            border: 3px solid #b45309;
            z-index: 2;
        }
        .corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
        .corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
        .corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }

        .cert-watermark {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 260px;
            color: rgba(217, 119, 6, 0.035);
            pointer-events: none;
            z-index: 0;
            line-height: 1;
        }

        .cert-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-brand-title {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            color: #0284c7;
            letter-spacing: 1.5px;
            font-size: 1.75rem;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .cert-brand-sub {
            font-size: 0.72rem;
            color: #ea580c;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .cert-accreditation {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .cert-title-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1e1b4b 0%, #0369a1 100%);
            color: #fbbf24;
            font-family: 'Cinzel', serif;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 2px;
            padding: 4px 28px;
            border-radius: 40px;
            border: 2px solid #b45309;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
            margin: 6px auto;
        }

        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: #1e1b4b;
            letter-spacing: 1px;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding: 0 24px 2px 24px;
            margin: 4px 0 2px 0;
            text-transform: uppercase;
        }

        .course-box {
            background: rgba(254, 243, 199, 0.35);
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 6px 18px;
            display: inline-block;
            margin: 4px 0;
        }

        .course-name {
            font-family: 'Cinzel', serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0369a1;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .eval-pill {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 3px 18px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        .sign-line {
            width: 140px;
            border-top: 1.5px solid #334155;
            margin: 0 auto 3px auto;
        }

        .seal-stamp {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 2px dashed #b45309;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(254, 243, 199, 0.25);
            color: #b45309;
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .cert-container {
                width: 297mm !important;
                height: 210mm !important;
                box-shadow: none !important;
                padding: 8mm !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }
        }
    </style>
</head>
<body>

<!-- Control Header for Centre Management -->
<div class="no-print-bar">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
        <span class="badge bg-primary px-3 py-2 font-monospace">CERT: {{ $certificate->certificate_no }}</span>
        @if($certificate->exam_result_id)
        <a href="{{ route('centre.exams.results.marksheet', $certificate->exam_result_id) }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="fa-solid fa-file-invoice me-1"></i> View Marksheet
        </a>
        @endif
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #0284c7; border-color: #0284c7;">
            <i class="fa-solid fa-print me-1"></i> Print Official Certificate
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3">
            <i class="fa-solid fa-xmark me-1"></i> Close
        </button>
    </div>
</div>

<!-- OFFICIAL CERTIFICATE CONTAINER -->
<div class="cert-container">
    <div class="cert-border-outer">
        <div class="corner-accent corner-tl"></div>
        <div class="corner-accent corner-tr"></div>
        <div class="corner-accent corner-bl"></div>
        <div class="corner-accent corner-br"></div>

        <div class="cert-border-inner text-center">
            <div class="cert-watermark">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div class="cert-content">
                <!-- 1. Header & Institutional Branding -->
                <div>
                    <div class="d-flex justify-content-between align-items-center px-2 mb-1" style="font-size: 0.72rem;">
                        <div class="text-start">
                            <span class="text-muted fw-bold">Reg. No:</span> <strong class="font-monospace text-dark">{{ $certificate->student->registration_no ?? 'N/A' }}</strong><br>
                            <span class="text-muted fw-bold">Roll No:</span> <strong class="font-monospace text-primary">{{ $certificate->student->roll_no ?? 'N/A' }}</strong>
                        </div>
                        <div class="text-center">
                            <div class="cert-accreditation text-uppercase">
                                Registered Under MCA (Govt. of India) &bull; ISO 9001:2015 Certified Institution
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted fw-bold">Cert No:</span> <strong class="font-monospace text-primary">{{ $certificate->certificate_no }}</strong><br>
                            <span class="text-muted fw-bold">Issue Date:</span> <strong class="text-dark">{{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d/m/Y') : date('d/m/Y') }}</strong>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <img src="{{ $centre->centre_logo_url ?? ($instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png')) }}" alt="{{ $centre->centre_name }}" style="height: 50px; width: auto; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                        <div>
                            <div class="cert-brand-title">{{ $centre->centre_name ?? 'AUTHORIZED STUDY CENTRE' }}</div>
                            <div class="cert-brand-sub" style="color: #b45309; font-weight: 800; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase;">
                                <i class="fa-solid fa-certificate me-1"></i> AUTHORIZED BY {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted mt-1" style="font-size: 0.68rem;">
                        {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} &bull; Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                    </div>

                    <div>
                        <div class="cert-title-badge">CERTIFICATE OF PROFICIENCY</div>
                    </div>
                </div>

                <!-- 2. Candidate Particulars Body -->
                <div class="my-auto py-1">
                    <p class="text-muted mb-0" style="font-size: 0.88rem; font-style: italic;">This is to certify that</p>
                    
                    <div class="student-name">
                        {{ $certificate->student->full_name ?? 'Candidate Name' }}
                    </div>

                    <div class="text-secondary mb-1" style="font-size: 0.85rem;">
                        Son / Daughter of <strong class="text-dark">{{ $certificate->student->father_name ?? 'Father Name' }}</strong>
                        @if(!empty($certificate->student->mother_name))
                            & Mother <strong class="text-dark">{{ $certificate->student->mother_name }}</strong>
                        @endif
                    </div>

                    <div class="text-secondary mb-1" style="font-size: 0.82rem; line-height: 1.4;">
                        having successfully cleared the prescribed curriculum examinations conducted through Authorized Study Centre:
                        <br>
                        <strong class="text-dark">{{ $certificate->centre->centre_name ?? 'Authorized Study Centre' }}</strong> 
                        (Centre Code: <span class="font-monospace text-primary fw-bold">{{ $certificate->centre->centre_code ?? 'CENTRAL' }}</span>)
                    </div>

                    <p class="text-muted mb-0" style="font-size: 0.82rem;">is hereby awarded this Diploma Certificate in</p>
                    
                    <div class="course-box">
                        <h4 class="course-name">{{ $certificate->course->course_name ?? 'Diploma Course' }}</h4>
                        <div class="text-muted font-monospace" style="font-size: 0.7rem;">
                            Course Code: <strong>{{ $certificate->course->course_code ?? 'DCE' }}</strong> &bull; Duration: <strong>{{ $certificate->course->duration ?? '6 Months' }}</strong>
                        </div>
                    </div>

                    <div>
                        <div class="eval-pill">
                            Performance Assessment: 
                            <span class="text-primary fw-bold font-monospace">Grade {{ $certificate->examResult->grade ?? ($certificate->result->grade ?? 'A') }}</span>
                            @if(isset($certificate->examResult->percentage) || isset($certificate->result->percentage))
                                ({{ number_format($certificate->examResult->percentage ?? $certificate->result->percentage, 2) }}%)
                            @endif
                            &bull; Status: <span class="text-success fw-bold">PASSED</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Footer Security, Signatures & Digital QR -->
                <div class="pt-2">
                    <div class="row align-items-end text-center">
                        <div class="col-4">
                            <div class="sign-line"></div>
                            <div class="fw-bold small text-dark" style="font-size: 0.78rem;">Study Centre Director</div>
                            <div class="text-muted" style="font-size: 0.68rem;">{{ $centre->director_name ?? 'Centre Incharge' }}</div>
                        </div>

                        <div class="col-4">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <div class="text-center">
                                    @php
                                        $qrTarget = $certificate->qr_code_url ?: route('verify.cert.direct', ['cert_no' => $certificate->certificate_no]);
                                    @endphp
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($qrTarget) }}" 
                                         alt="Scan QR" 
                                         style="width: 68px; height: 68px;" 
                                         class="border p-1 bg-white shadow-sm mb-1">
                                    <div class="text-muted fw-semibold" style="font-size: 0.6rem;">Scan to Verify Online</div>
                                </div>
                                <div class="seal-stamp d-none d-sm-inline-flex">
                                    <i class="fa-solid fa-stamp mb-1"></i>
                                    OFFICIAL<br>ACADEMIC<br>SEAL
                                </div>
                            </div>
                            <div class="text-muted font-monospace mt-1" style="font-size: 0.6rem;">
                                SEC-HASH: {{ substr($certificate->security_hash ?? hash('sha256', $certificate->certificate_no), 0, 20) }}...
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="sign-line"></div>
                            <div class="fw-bold small text-dark" style="font-size: 0.78rem;">Controller of Examination</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Central Examination Board</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
