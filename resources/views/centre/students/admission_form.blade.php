<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form - {{ $student->full_name }} ({{ $student->registration_no }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            padding: 30px;
        }

        .form-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border: 2px solid #1e1b4b;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .header-title {
            text-align: center;
            border-bottom: 2px double #1e1b4b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-title h2 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #1e1b4b;
            margin: 0;
        }

        .header-title p {
            font-size: 0.85rem;
            margin: 2px 0 0 0;
            color: #334155;
            font-style: italic;
        }

        .dossier-heading {
            background: #1e1b4b;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 0.95rem;
            padding: 4px;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .photo-box {
            width: 120px;
            height: 140px;
            border: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8fafc;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-table td, .info-table th {
            padding: 6px 10px;
            font-size: 0.9rem;
            border: 1px solid #cbd5e1;
        }

        .info-table th {
            background-color: #f1f5f9;
            width: 25%;
            font-weight: 700;
        }

        .section-bar {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 0.85rem;
            padding: 5px 10px;
            margin-top: 15px;
            border-left: 4px solid #1e1b4b;
            text-transform: uppercase;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .form-container {
                border: 2px solid #000000;
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-2 shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print Official Form Dossier
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 rounded-2 ms-2">
            <i class="fa-solid fa-xmark me-1"></i>Close
        </button>
    </div>

    <div class="form-container">
        <!-- Official Institute Header -->
        <div class="header-title">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 55px; width: auto; object-fit: contain;">
                <div>
                    <h2>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                    <p>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
                </div>
            </div>
            <small class="fw-bold">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} &bull; Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</small>
        </div>

        <div class="dossier-heading">OFFICIAL STUDENT ADMISSION DOSSIER & REGISTRATION FORM</div>

        <!-- Top Overview & Photo Grid -->
        <div class="row align-items-center mb-3">
            <div class="col-8">
                <table class="table table-sm info-table mb-0">
                    <tr>
                        <th>Registration No:</th>
                        <td class="fw-bold font-monospace">{{ $student->registration_no }}</td>
                    </tr>
                    <tr>
                        <th>Roll Number:</th>
                        <td class="fw-bold text-primary font-monospace">{{ $student->roll_no }}</td>
                    </tr>
                    <tr>
                        <th>Admission Date:</th>
                        <td>{{ $admission?->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->format('d F, Y') : now()->format('d F, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Study Centre:</th>
                        <td class="fw-bold">{{ $centre->centre_name }} ({{ $centre->centre_code }})</td>
                    </tr>
                </table>
            </div>
            <div class="col-4 d-flex justify-content-end">
                <div class="photo-box text-center">
                    @if($student->photo_path)
                    <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}">
                    @else
                    <span class="text-muted small">Affix Passport Photo</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 1. Personal Identity Particulars -->
        <div class="section-bar">1. Candidate Identity Details</div>
        <table class="table table-sm info-table mt-1 mb-0">
            <tr>
                <th>Candidate Full Name:</th>
                <td colspan="3" class="fw-bold text-uppercase">{{ $student->full_name }}</td>
            </tr>
            <tr>
                <th>Father's Name:</th>
                <td>{{ $student->father_name }}</td>
                <th>Mother's Name:</th>
                <td>{{ $student->mother_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date of Birth:</th>
                <td>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d F, Y') : 'N/A' }}</td>
                <th>Gender:</th>
                <td class="text-capitalize">{{ $student->gender }}</td>
            </tr>
            <tr>
                <th>Contact Mobile:</th>
                <td class="fw-bold">{{ $student->mobile }}</td>
                <th>Email Address:</th>
                <td>{{ $student->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Qualification:</th>
                <td>{{ $student->highest_qualification ?? 'N/A' }}</td>
                <th>Alternate Mobile:</th>
                <td>{{ $student->alternate_mobile ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Residential Address:</th>
                <td colspan="3">{{ $student->address }}, City: {{ $student->city }}, Dist: {{ $student->district?->name }}, State: {{ $student->state?->name }} - {{ $student->pincode }}</td>
            </tr>
        </table>

        <!-- 2. Course Details & Batch Allocation -->
        <div class="section-bar">2. Enrolled Course & Academic Batch</div>
        <table class="table table-sm info-table mt-1 mb-0">
            <tr>
                <th>Course Name:</th>
                <td colspan="3" class="fw-bold">{{ $admission?->course?->course_name }} ({{ $admission?->course?->course_code }})</td>
            </tr>
            <tr>
                <th>Course Duration:</th>
                <td>{{ $admission?->course?->duration_months ?? 0 }} Months</td>
                <th>Batch Allocation:</th>
                <td>{{ $admission?->batch?->batch_name ?? 'Regular Shift' }} ({{ $admission?->batch?->start_time }} - {{ $admission?->batch?->end_time }})</td>
            </tr>
        </table>

        <!-- 3. Fee Structure & Settlement Ledger -->
        <div class="section-bar">3. Fee Ledger Particulars</div>
        <table class="table table-sm info-table mt-1 mb-0">
            <tr>
                <th>Standard Course Fee:</th>
                <td>₹{{ number_format($admission?->course_fee ?? 0, 2) }}</td>
                <th>Approved Concession:</th>
                <td>₹{{ number_format($admission?->discount_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Net Payable Fee:</th>
                <td class="fw-bold">₹{{ number_format($admission?->net_fee ?? 0, 2) }}</td>
                <th>Total Settled / Paid:</th>
                <td class="fw-bold text-success">₹{{ number_format($admission?->total_paid ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Remaining Dues:</th>
                <td colspan="3" class="fw-bold text-danger">
                    ₹{{ number_format(max(0, ($admission?->net_fee ?? 0) - ($admission?->total_paid ?? 0)), 2) }}
                </td>
            </tr>
        </table>

        <!-- Declaration & Signatures -->
        <div class="section-bar">4. Candidate Declaration & Verification</div>
        <p class="small text-muted mt-2 mb-4" style="line-height: 1.4;">
            I hereby declare that all particulars furnished above are true and correct to the best of my knowledge and belief. I agree to abide by all the rules, code of conduct, and examination regulations prescribed by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}.
        </p>

        <div class="row pt-4 text-center">
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Candidate's Signature</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Study Centre Director & Seal</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Central Controller of Exams</div>
            </div>
        </div>
    </div>
</body>
</html>
