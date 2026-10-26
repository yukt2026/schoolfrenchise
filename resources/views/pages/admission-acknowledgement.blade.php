<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Application Acknowledgement Slip - {{ $application->application_no }} - {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --dce-primary: #0284c7;
            --dce-secondary: #ea580c;
            --dce-dark: #0a192f;
            --dce-muted: #64748b;
            --dce-border: #cbd5e1;
            --dce-light-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: var(--dce-dark);
            font-size: 0.88rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .ack-container {
            max-width: 920px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            position: relative;
        }

        .ack-inner {
            padding: 32px 36px;
        }

        .top-watermark-ribbon {
            background: linear-gradient(135deg, #0a192f 0%, #0284c7 50%, #ea580c 100%);
            height: 8px;
            width: 100%;
        }

        .letterhead-header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .institute-title {
            color: #0284c7;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .institute-tagline {
            color: #475569;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .institute-accreditation {
            color: #64748b;
            font-size: 0.76rem;
            font-weight: 500;
        }

        .slip-title-badge {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            display: inline-block;
        }

        .section-header {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #312e81;
            border-bottom: 1.5px solid #e0e7ff;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-ack {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-ack th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 6px 12px;
            border: 1px solid var(--dce-border);
            font-size: 0.82rem;
            width: 25%;
            vertical-align: middle;
        }

        .table-ack td {
            padding: 6px 12px;
            border: 1px solid var(--dce-border);
            color: #0f172a;
            font-size: 0.83rem;
            vertical-align: middle;
        }

        .photo-container {
            width: 125px;
            height: 150px;
            border: 2px dashed #94a3b8;
            border-radius: 6px;
            padding: 3px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            margin: 0 auto;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .qr-container {
            width: 105px;
            height: 105px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .qr-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .signature-box {
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            height: 70px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 6px;
            background: #fafafa;
        }

        .signature-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            text-align: center;
            margin-top: 4px;
        }

        .instruction-list {
            padding-left: 18px;
            margin-bottom: 0;
            font-size: 0.77rem;
            color: #475569;
        }

        .instruction-list li {
            margin-bottom: 4px;
        }

        .scrutiny-box {
            border: 1.5px solid #c7d2fe;
            background: #f8faff;
            border-radius: 8px;
            padding: 12px 14px;
            margin-top: 14px;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 0.82rem;
            }

            .ack-container {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            .ack-inner {
                padding: 12px 15px !important;
            }

            .no-print {
                display: none !important;
            }

            .table-ack th, .table-ack td {
                padding: 4px 8px !important;
                border-color: #64748b !important;
            }

            .signature-box {
                height: 55px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <!-- Action Toolbar (Hidden during print) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print" style="max-width: 920px; margin: 0 auto;">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admission.status', ['application_no' => $application->application_no]) }}" class="btn btn-outline-secondary btn-sm rounded-2 px-3 shadow-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Status
                </a>
                <a href="{{ route('home') }}" class="btn btn-link btn-sm text-decoration-none text-muted">Home</a>
                <span class="text-muted small">&bull;</span>
                <a href="{{ route('admission.apply') }}" class="btn btn-link btn-sm text-decoration-none text-muted">Apply Another</a>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 px-4 shadow-sm fw-semibold">
                    <i class="fa-solid fa-print me-1"></i> Print Acknowledgement Slip
                </button>
            </div>
        </div>

        <!-- Official Printable Acknowledgement Slip Card -->
        <div class="ack-container">
            <div class="top-watermark-ribbon"></div>
            
            <div class="ack-inner">
                <!-- Header Letterhead -->
                <div class="letterhead-header text-center">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 60px; width: auto; object-fit: contain;">
                        <div>
                            <div class="institute-title">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            <div class="institute-tagline">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                            <div class="institute-accreditation">
                                Premier Institute for Information Technology & Professional Vocational Education
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-muted small" style="font-size: 0.77rem;">
                        <strong>Campus Address:</strong> {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} &bull;
                        <strong>Helpline / WhatsApp:</strong> +91 {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                    </div>

                    <div class="mt-2">
                        <div class="slip-title-badge">
                            <i class="fa-solid fa-file-circle-check me-1"></i>
                            ONLINE ADMISSION APPLICATION ACKNOWLEDGEMENT SLIP (ऑनलाइन प्रवेश पावती)
                        </div>
                    </div>
                </div>

                <!-- Top Row: Application Status, Metadata, Photo & QR -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-8 col-sm-9">
                        <table class="table-ack">
                            <tbody>
                                <tr>
                                    <th>Application No.</th>
                                    <td>
                                        <span class="fw-bold font-mono text-primary fs-6">{{ $application->application_no }}</span>
                                    </td>
                                    <th>Submission Date</th>
                                    <td>{{ $application->created_at->format('d M, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Application Status</th>
                                    <td>
                                        @if($application->status === 'approved')
                                            <span class="badge bg-success font-mono">APPROVED & REGISTERED</span>
                                        @elseif($application->status === 'rejected')
                                            <span class="badge bg-danger font-mono">REJECTED</span>
                                        @else
                                            <span class="badge bg-warning text-dark font-mono">UNDER SCRUTINY</span>
                                        @endif
                                    </td>
                                    <th>Academic Session</th>
                                    <td class="font-mono fw-semibold">{{ date('Y') }}-{{ date('Y') + 1 }}</td>
                                </tr>
                                @if($application->status === 'approved' && $application->convertedStudent)
                                <tr>
                                    <th>Allotted Roll No.</th>
                                    <td>
                                        <span class="fw-bold font-mono text-success fs-6">{{ $application->convertedStudent->roll_no }}</span>
                                    </td>
                                    <th>Registration No.</th>
                                    <td class="font-mono text-secondary small">{{ $application->convertedStudent->registration_no }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-4 col-sm-3 text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Candidate Photo -->
                            <div class="photo-container">
                                @if($application->photo_url)
                                    <img src="{{ $application->photo_url }}" alt="Candidate Photo" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($application->applicant_name) }}&background=4338ca&color=fff&size=200';">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->applicant_name) }}&background=4338ca&color=fff&size=200" alt="Avatar">
                                @endif
                            </div>

                            <!-- Verification QR Code -->
                            <div class="d-none d-sm-block">
                                <div class="qr-container">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('admission.status', ['application_no' => $application->application_no])) }}" alt="Verification QR">
                                </div>
                                <div class="text-muted" style="font-size: 0.65rem; margin-top: 2px;">Scan to Verify</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 1: Candidate Particulars -->
                <div class="section-header">
                    <i class="fa-solid fa-user-circle"></i> 1. Candidate Particulars (अभ्यर्थी का व्यक्तिगत विवरण)
                </div>
                <table class="table-ack mb-3">
                    <tbody>
                        <tr>
                            <th>Candidate Full Name</th>
                            <td class="fw-bold text-dark">{{ $application->applicant_name }}</td>
                            <th>Father's / Guardian's Name</th>
                            <td>{{ $application->father_name }}</td>
                        </tr>
                        <tr>
                            <th>Mother's Name</th>
                            <td>{{ $application->mother_name ?? '—' }}</td>
                            <th>Date of Birth & Gender</th>
                            <td>
                                {{ $application->dob ? $application->dob->format('d M, Y') : '—' }} &bull;
                                <span class="text-capitalize">{{ $application->gender }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Contact Mobile No.</th>
                            <td class="font-mono fw-semibold">{{ $application->mobile }}</td>
                            <th>Email Address</th>
                            <td>{{ $application->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Highest Qualification</th>
                            <td>{{ $application->highest_qualification ?? 'Secondary / 10th' }}</td>
                            <th>State & District</th>
                            <td>{{ $application->district?->name ?? '—' }}, {{ $application->state?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Complete Postal Address</th>
                            <td colspan="3">
                                {{ $application->address }}, {{ $application->city }},
                                {{ $application->district?->name }}, {{ $application->state?->name }} - <span class="font-mono fw-semibold">{{ $application->pincode }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Section 2: Applied Academic Program & Study Centre -->
                <div class="section-header">
                    <i class="fa-solid fa-book-open-reader"></i> 2. Academic Program & Study Centre (पाठ्यक्रम एवं अध्ययन केंद्र)
                </div>
                <table class="table-ack mb-3">
                    <tbody>
                        <tr>
                            <th>Course Name & Code</th>
                            <td class="fw-bold text-primary">
                                {{ $application->course?->course_name ?? 'Computer Course' }}
                                <span class="badge bg-light text-primary border font-mono ms-1">{{ $application->course?->course_code }}</span>
                            </td>
                            <th>Course Duration</th>
                            <td>{{ $application->course?->duration ?? '6 Months' }}</td>
                        </tr>
                        <tr>
                            <th>Standard Eligibility</th>
                            <td>{{ $application->course?->eligibility ?? '10th / 12th Standard' }}</td>
                            <th>Standard Course Fee</th>
                            <td class="fw-bold font-mono">
                                ₹{{ number_format($application->course?->course_fee ?? $application->course?->default_course_fee ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Allotted Study Centre</th>
                            <td colspan="3">
                                <strong>{{ $application->centre?->centre_name ?? 'Main Head Office Centre' }}</strong>
                                <span class="badge bg-secondary font-mono ms-1">CODE: {{ $application->centre?->centre_code ?? 'HO-01' }}</span>
                                <div class="text-muted small mt-1">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    {{ $application->centre?->address }}, {{ $application->centre?->city ?? '' }}
                                    @if($application->centre?->mobile)
                                        &bull; <i class="fa-solid fa-phone me-1 text-success"></i> Phone: {{ $application->centre?->mobile }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Section 3: Study Centre Office Scrutiny Section -->
                <div class="scrutiny-box mb-3">
                    <div class="fw-bold text-primary small text-uppercase mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-clipboard-check me-1"></i> 3. For Study Centre Verification & Office Use Only (कार्यालय उपयोग हेतु)</span>
                        <span class="text-muted font-mono" style="font-size: 0.72rem;">REF: {{ $application->application_no }}</span>
                    </div>
                    <div class="row g-2 align-items-center small">
                        <div class="col-md-4">
                            <div><strong>Documents Verified:</strong></div>
                            <div class="text-muted" style="font-size: 0.74rem;">
                                [&nbsp;] 10th/12th Marksheet &nbsp;
                                [&nbsp;] Photo ID Proof &nbsp;
                                [&nbsp;] Photos
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div><strong>Batch Allotted:</strong> ________________________</div>
                            <div class="mt-1"><strong>Shift Timing:</strong> ________________________</div>
                        </div>
                        <div class="col-md-4">
                            <div><strong>Fee Receipt No:</strong> ________________________</div>
                            <div class="mt-1"><strong>Enrolment Date:</strong> ____ / ____ / 2026</div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Declaration & Signatures -->
                <div class="section-header">
                    <i class="fa-solid fa-file-signature"></i> 4. Candidate Declaration & Authorized Signatures (घोषणा एवं हस्ताक्षर)
                </div>
                
                <p class="text-muted small mb-3" style="font-size: 0.75rem; text-align: justify;">
                    <strong>Declaration:</strong> I hereby declare that all the information and uploaded particulars furnished in this admission application are true, complete, and correct to the best of my knowledge. I agree to abide by all the academic rules, attendance requirements, and examination regulations of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}. In the event of any document being found fraudulent or misrepresented, my provisional candidature is liable to be terminated immediately.
                </p>

                <div class="row g-3 mb-3 text-center">
                    <div class="col-4">
                        <div class="signature-box">
                            <span class="text-muted opacity-50 small">Candidate Signature</span>
                        </div>
                        <div class="signature-label">Signature of Applicant</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <span class="text-muted opacity-50 small">Parent Signature</span>
                        </div>
                        <div class="signature-label">Parent / Guardian Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <span class="text-muted opacity-50 small">Centre Seal & Sign</span>
                        </div>
                        <div class="signature-label">Centre Director / Scrutiny Officer</div>
                    </div>
                </div>

                <!-- Section 5: Candidate Instructions -->
                <div class="border-top pt-2 mt-2">
                    <div class="fw-bold text-dark small mb-1" style="font-size: 0.76rem;">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i> IMPORTANT INSTRUCTIONS FOR THE CANDIDATE:
                    </div>
                    <ol class="instruction-list">
                        <li>Please print this Computerized Acknowledgement Slip and bring it along when visiting your selected Study Centre.</li>
                        <li>Report to the Study Centre within <strong>7 working days</strong> with original educational certificates (Marksheets) and valid Government ID proof (Aadhaar / Voter ID).</li>
                        <li>Online applications are provisional and subject to verification of candidate documents by the Centre Director and Central Board.</li>
                        <li>You can check your real-time application and roll number status anytime at <span class="font-mono text-primary">{{ route('admission.status') }}</span> using Application No: <strong class="font-mono">{{ $application->application_no }}</strong>.</li>
                    </ol>
                </div>

                <!-- Footer System Timestamp -->
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2 text-muted" style="font-size: 0.68rem;">
                    <div>System Generated On: {{ now()->format('d M, Y h:i:s A') }} &bull; IP: {{ request()->ip() }}</div>
                    <div class="font-mono">TEC-DOC-ACK-{{ substr(md5($application->application_no), 0, 8) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
