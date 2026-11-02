<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card - {{ $student->full_name }} ({{ $student->roll_no }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@700;800&display=swap" rel="stylesheet">

    <style>
        @page {
            size: auto;
            margin: 8mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .no-print-toolbar {
            width: 100%;
            max-width: 740px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Dual Card Container */
        .id-print-canvas {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: stretch;
            gap: 20px;
            position: relative;
        }

        /* Standard CR80 / Portrait Card: 330px x 515px */
        .id-card-unit {
            width: 330px;
            height: 515px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            border: 1.5px solid #0284c7;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Card Header */
        .id-card-header {
            background: linear-gradient(135deg, #0b1329 0%, #0369a1 100%);
            color: #ffffff;
            padding: 10px 12px 8px 12px;
            text-align: center;
            border-bottom: 2.5px solid #f59e0b;
        }

        .id-org-title {
            font-family: 'Cinzel', serif;
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            line-height: 1.2;
            margin: 0;
            text-transform: uppercase;
        }

        .id-org-sub {
            font-size: 0.62rem;
            color: #fbbf24;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .id-badge-title {
            display: inline-block;
            background: #f59e0b;
            color: #0f172a;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 2px 12px;
            border-radius: 20px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Photo Holder */
        .id-photo-frame {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            border: 3px solid #0284c7;
            overflow: hidden;
            margin: 8px auto 4px auto;
            background: #f8fafc;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .id-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Candidate Details */
        .student-name-box {
            text-align: center;
            padding: 0 10px;
        }

        .student-full-name {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .roll-pill {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-family: monospace;
            font-weight: 800;
            font-size: 0.74rem;
            padding: 2px 10px;
            border-radius: 30px;
            border: 1px solid #bae6fd;
            margin-bottom: 6px;
        }

        .id-details-grid {
            padding: 0 16px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 0.73rem;
            padding: 2px 0;
            border-bottom: 1px dashed #e2e8f0;
            line-height: 1.3;
        }

        .detail-label {
            color: #64748b;
            font-weight: 600;
            width: 38%;
            flex-shrink: 0;
        }

        .detail-val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            width: 62%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Card Footer */
        .id-card-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 0.62rem;
        }

        .sign-area {
            text-align: center;
            width: 90px;
        }

        .sign-line {
            border-top: 1px solid #334155;
            width: 75px;
            margin: 12px auto 2px auto;
        }

        /* Back Side Specifics */
        .rules-list {
            padding: 0 14px 0 28px;
            margin: 6px 0;
            font-size: 0.66rem;
            line-height: 1.35;
            color: #475569;
        }

        .rules-list li {
            margin-bottom: 4px;
        }

        .address-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin: 0 14px 6px 14px;
            font-size: 0.66rem;
            line-height: 1.3;
        }

        .qr-section {
            text-align: center;
            margin: 4px 0;
        }

        /* Cutting / Folding Guide */
        .fold-divider {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            border-left: 1.5px dashed #cbd5e1;
            padding: 0 4px;
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .id-card-unit {
                box-shadow: none !important;
                border: 1px solid #0f172a !important;
                page-break-inside: avoid !important;
            }
            .id-print-canvas {
                gap: 16px !important;
                justify-content: center !important;
                margin-top: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Toolbar -->
    <div class="no-print-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2 font-monospace">ROLL: {{ $student->roll_no }}</span>
            <span class="text-muted small">Dual-Sided Official Student ID Card</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #0284c7; border-color: #0284c7;">
                <i class="fa-solid fa-print me-1"></i> Print ID Card
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-xmark me-1"></i> Close
            </button>
        </div>
    </div>

    <!-- Dual-Sided Printable Canvas -->
    <div class="id-print-canvas">

        <!-- ================= FRONT SIDE ================= -->
        <div class="id-card-unit">
            <!-- Header -->
            <div class="id-card-header">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 28px; width: auto; object-fit: contain;">
                    <div class="text-start">
                        <div class="id-org-title text-white">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                        <div class="id-org-sub">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                    </div>
                </div>
                <div>
                    <span class="id-badge-title">STUDENT IDENTITY CARD</span>
                </div>
            </div>

            <!-- Photo & Name -->
            <div>
                <div class="id-photo-frame">
                    @if($student->photo_url)
                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" onerror="this.onerror=null;this.src='https://placehold.co/100x100?text=Photo';">
                    @else
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light text-primary fs-3">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                </div>

                <div class="student-name-box">
                    <div class="student-full-name">{{ $student->full_name }}</div>
                    <div class="roll-pill">ROLL NO: {{ $student->roll_no }}</div>
                    <!-- Barcode Verification -->
                    <div class="text-center my-1">
                        <div style="font-family: 'Courier New', monospace; font-weight: 900; font-size: 1.1rem; letter-spacing: 3px; line-height: 1; color: #0f172a; text-transform: uppercase;">
                            ||| | |||| | ||| |||| |
                        </div>
                        <span class="font-monospace text-muted" style="font-size: 0.56rem; letter-spacing: 1px;">BARCODE: {{ $student->roll_no }}</span>
                    </div>
                </div>
            </div>

            <!-- Details Body -->
            <div class="id-details-grid">
                <div class="detail-row">
                    <span class="detail-label">Reg. Number:</span>
                    <span class="detail-val font-monospace">{{ $student->registration_no }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Course Enrolled:</span>
                    <span class="detail-val text-primary">{{ $admission?->course?->course_name ?? 'Computer Course' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Course Code:</span>
                    <span class="detail-val font-monospace">{{ $admission?->course?->course_code ?? 'DCE' }} ({{ $admission?->course?->duration ?? '6 Months' }})</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Father's Name:</span>
                    <span class="detail-val">{{ $student->father_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Contact Mobile:</span>
                    <span class="detail-val font-monospace">{{ $student->mobile }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date of Birth:</span>
                    <span class="detail-val">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Study Centre:</span>
                    <span class="detail-val">{{ $centre->centre_name ?? $student->centre?->centre_name ?? 'Head Office' }}</span>
                </div>
                <div class="detail-row border-0">
                    <span class="detail-label">Centre Code:</span>
                    <span class="detail-val font-monospace text-primary">{{ $centre->centre_code ?? $student->centre?->centre_code ?? 'HO-01' }}</span>
                </div>
            </div>

            <!-- Front Footer -->
            <div class="id-card-footer">
                <div class="text-muted">
                    <strong>ACADEMIC SESSION:</strong> {{ $student->academic_session ?? date('Y') }}<br>
                    <span style="font-size: 0.58rem;">Official Cardholder Credential</span>
                </div>
                <div class="sign-area">
                    @if(!empty($centre->director_signature_url))
                        <img src="{{ $centre->director_signature_url }}" alt="Centre Director" style="height: 22px; max-width: 80px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                    @endif
                    <div class="sign-line" style="margin-top: {{ !empty($centre->director_signature_url) ? '2px' : '12px' }};"></div>
                    <strong style="font-size: 0.58rem; text-transform: uppercase;">Centre Incharge</strong>
                </div>
            </div>
        </div>

        <!-- ================= BACK SIDE ================= -->
        <div class="id-card-unit">
            <!-- Back Header -->
            <div class="id-card-header" style="background: linear-gradient(135deg, #0b1329 0%, #1e293b 100%);">
                <div class="id-org-title text-white">TERMS & VERIFICATION</div>
                <div class="id-org-sub" style="color: #cbd5e1;">Official Institutional Guidelines</div>
            </div>

            <!-- Rules List -->
            <div class="pt-2">
                <ol class="rules-list">
                    <li>This card is non-transferable and remains institute property.</li>
                    <li>Must be carried and produced upon entering classes, labs, and exam centres.</li>
                    <li>Loss of card must be reported immediately to centre administration.</li>
                    <li>Tampering or misuse leads to immediate forfeiture of student admission.</li>
                </ol>
            </div>

            <!-- Residential Address -->
            <div class="address-box">
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.58rem;">Permanent Address:</div>
                <div class="fw-semibold text-dark mt-0.5">
                    {{ $student->address ?? 'N/A' }},
                    {{ $student->district?->name ?? $student->city }}, {{ $student->state?->name ?? 'Bihar' }} - {{ $student->pincode ?? '801105' }}
                </div>
            </div>

            <!-- Dynamic Verification QR Code -->
            <div class="qr-section">
                @php
                    $verifyUrl = route('verify.admission') . '?search_key=' . urlencode($student->registration_no);
                @endphp
                <div class="border d-inline-block p-1 bg-white rounded shadow-sm">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($verifyUrl) }}" alt="QR Scan" style="width: 75px; height: 75px;">
                </div>
                <div class="text-muted font-monospace mt-1" style="font-size: 0.62rem;">
                    SCAN TO VERIFY CREDENTIALS
                </div>
                <div class="text-secondary font-monospace" style="font-size: 0.58rem;">
                    REG: {{ $student->registration_no }}
                </div>
            </div>

            <!-- Back Signatures & Footer -->
            <div class="id-card-footer">
                <div class="sign-area">
                    @if($student->signature_url)
                        <img src="{{ $student->signature_url }}" alt="Student Signature" style="height: 20px; max-width: 80px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                    @endif
                    <div class="sign-line" style="margin-top: {{ $student->signature_url ? '2px' : '12px' }};"></div>
                    <strong style="font-size: 0.58rem; text-transform: uppercase;">Student Sign</strong>
                </div>
                <div class="text-center text-muted" style="font-size: 0.55rem; line-height: 1.1;">
                    <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong><br>
                    Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                </div>
                <div class="sign-area">
                    @if(!empty($instituteSettings['seal_url']))
                        <img src="{{ $instituteSettings['seal_url'] }}" alt="Director Seal" style="height: 24px; max-width: 60px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                    @endif
                    <div class="sign-line" style="margin-top: {{ !empty($instituteSettings['seal_url']) ? '2px' : '12px' }};"></div>
                    <strong style="font-size: 0.58rem; text-transform: uppercase;">Director Sign</strong>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
