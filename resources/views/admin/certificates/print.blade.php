<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }

        .cert-container {
            width: 1050px;
            min-height: 740px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .cert-border-outer {
            border: 8px solid #b45309;
            padding: 6px;
            position: relative;
        }

        .cert-border-inner {
            border: 2px dashed #d97706;
            padding: 30px;
            position: relative;
            background: radial-gradient(circle at center, #ffffff 60%, #fffbeb 100%);
        }

        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 160px;
            color: rgba(217, 119, 6, 0.04);
            pointer-events: none;
            z-index: 1;
        }

        .cert-title-brand {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            color: #78350f;
            letter-spacing: 2px;
            font-size: 2.1rem;
        }

        .cert-subtitle {
            font-size: 0.9rem;
            color: #92400e;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cert-heading {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            color: #1e293b;
            letter-spacing: 3px;
            font-weight: 700;
            margin-top: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding-bottom: 5px;
        }

        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            font-weight: 900;
            color: #0369a1;
            letter-spacing: 1px;
            border-bottom: 1px solid #cbd5e1;
            display: inline-block;
            padding: 0 30px 4px 30px;
            margin: 10px 0;
        }

        .course-name {
            font-family: 'Cinzel', serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .cert-footer-sign {
            margin-top: 40px;
        }

        .sign-line {
            width: 170px;
            border-top: 2px solid #64748b;
            margin: 0 auto 5px auto;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .cert-container {
                box-shadow: none;
                width: 100%;
                min-height: auto;
                page-break-inside: avoid;
            }
            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="text-center mb-3 d-print-none">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm" style="background-color: #0891b2; border-color: #0891b2;">
        <i class="fa-solid fa-print me-2"></i> Print Official Certificate
    </button>
</div>

<div class="cert-container">
    <div class="cert-border-outer">
        <div class="cert-border-inner text-center">
            <div class="cert-watermark"><i class="fa-solid fa-graduation-cap"></i></div>

            <!-- Top Header & Accreditation Details -->
            <div class="d-flex justify-content-between align-items-center mb-2 px-3">
                <div class="text-start">
                    <div class="small fw-bold text-muted">Reg. No: <span class="font-monospace text-dark">{{ $certificate->student->registration_no ?? 'DCE-REG' }}</span></div>
                    <div class="small fw-bold text-muted">Roll No: <span class="font-monospace text-dark">{{ $certificate->student->roll_no ?? 'DCE-ROLL' }}</span></div>
                </div>
                @if($certificate->student?->photo_url)
                <div class="text-center">
                    <img src="{{ $certificate->student->photo_url }}" alt="Candidate" class="border border-2 border-dark rounded-1 shadow-sm" style="width: 65px; height: 75px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/65x75?text=Photo';">
                </div>
                @endif
                <div class="text-end">
                    <div class="small fw-bold text-muted">Cert No: <span class="font-monospace text-primary fw-bold">{{ $certificate->certificate_no }}</span></div>
                    <div class="small fw-bold text-muted">Issue Date: <span class="text-dark">{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : date('d/m/Y') }}</span></div>
                </div>
            </div>

            <!-- Institution Name -->
            <div class="mt-2 text-center">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="mb-2" style="max-height: 65px; width: auto; object-fit: contain;">
                <div class="cert-title-brand" style="color: #0284c7;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                <div class="cert-subtitle" style="color: #ea580c;">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                <div class="small text-muted mt-1">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} | Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</div>
            </div>

            <!-- Certificate Title -->
            <div class="my-3">
                <div class="cert-heading">Certificate of Proficiency</div>
            </div>

            <p class="fs-6 text-muted mb-1">This is to certify that</p>
            <div class="student-name">{{ $certificate->student->full_name ?? 'Candidate Name' }}</div>

            <p class="fs-6 text-secondary mb-1">
                Son / Daughter of <strong class="text-dark">{{ $certificate->student->father_name ?? 'Father Name' }}</strong>
            </p>

            <p class="fs-6 text-secondary mb-2">
                having successfully completed the prescribed academic curriculum and passed the examination conducted at Authorized Study Centre
                <br>
                <strong class="text-dark">{{ $certificate->centre->centre_name ?? 'Central Study Centre' }}</strong> (Code: {{ $certificate->centre->centre_code ?? 'CENTRAL' }})
            </p>

            <p class="fs-6 text-muted mb-1">is hereby awarded this diploma certificate in</p>
            <div class="course-name my-2">{{ $certificate->course->course_name ?? 'Computer Course' }}</div>
            <div class="text-muted small">Course Code: <strong>{{ $certificate->course->course_code ?? '' }}</strong> | Duration: <strong>{{ $certificate->course->duration ?? '6 Months' }}</strong></div>

            <!-- Performance Ribbon -->
            <div class="my-3 d-inline-block px-4 py-1 rounded-pill bg-light border border-warning text-dark fw-bold">
                Performance Evaluation: <span class="text-primary">Grade {{ $certificate->result->grade ?? 'A' }}</span> (Aggregate: {{ number_format($certificate->result->percentage ?? 0, 2) }}%)
            </div>

            <!-- Footer Signatures & QR Code -->
            <div class="row align-items-end cert-footer-sign">
                <div class="col-4">
                    <div class="sign-line"></div>
                    <div class="fw-bold small text-dark">Controller of Examination</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Central Examination Board</div>
                </div>

                <div class="col-4 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode($certificate->qr_code_url ?? route('verify.certificate.public', ['cert_no' => $certificate->certificate_no])) }}" alt="Scan QR" style="width: 80px; height: 80px;" class="border p-1 bg-white mb-1">
                    <div class="text-muted fw-semibold" style="font-size: 0.68rem;">Scan to Verify Authenticity</div>
                    <div class="text-muted font-monospace" style="font-size: 0.65rem;">{{ substr($certificate->security_hash ?? 'CERT-VALID', 0, 16) }}...</div>
                </div>

                <div class="col-4">
                    <div class="sign-line"></div>
                    <div class="fw-bold small text-dark">Executive Director / Chairman</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
