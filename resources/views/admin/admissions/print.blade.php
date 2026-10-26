<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Dossier - {{ $admission->student?->roll_no ?? $admission->id }} - {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            font-size: 0.9rem;
        }
        .dossier-card {
            background: #ffffff;
            max-width: 900px;
            margin: 30px auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header-title {
            color: #4338ca;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .section-heading {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4338ca;
            border-bottom: 2px solid #e0e7ff;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .photo-box {
            width: 120px;
            height: 140px;
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f1f5f9;
        }
        @media print {
            body { background: #ffffff !important; }
            .dossier-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <!-- Action Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print" style="max-width: 900px; margin: 0 auto;">
            <div>
                <a href="{{ route('admin.admissions.index', ['tab' => 'active']) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Admissions
                </a>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
                    <i class="fa-solid fa-print me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>

        <!-- Official Printable Admission Document -->
        <div class="dossier-card">
            <!-- Header Letterhead -->
            <div class="text-center border-bottom pb-4 mb-4">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 52px; width: auto; object-fit: contain;">
                    <div class="text-start">
                        <h2 class="header-title mb-0" style="color: #0284c7;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                        <div class="text-muted small fw-semibold">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                    </div>
                </div>
                <div class="text-secondary small">
                    Head Office / Campus: {{ $admission->centre?->centre_name ?? ($instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105') }} &bull;
                    Centre Code: <strong class="text-dark">{{ $admission->centre?->centre_code ?? 'MAIN' }}</strong> &bull;
                    Phone: {{ $admission->centre?->contact_number ?? ($instituteSettings['contact_phone'] ?? '9162384660') }}
                </div>
                <div class="mt-3">
                    <span class="badge bg-primary px-3 py-2 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.8rem;">
                        OFFICIAL STUDENT ADMISSION DOSSIER & ENROLMENT RECORD
                    </span>
                </div>
            </div>

            <!-- Identifiers and Photo Strip -->
            <div class="row align-items-center g-3 mb-4 p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="col-8 col-sm-9">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted small">Assigned Roll Number:</span>
                            <div class="fw-bold font-monospace text-primary fs-5">{{ $admission->student?->roll_no ?? 'Under Process' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small">Registration Number:</span>
                            <div class="fw-bold font-monospace text-dark fs-6">{{ $admission->student?->registration_no }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small">Admission Date:</span>
                            <div class="fw-semibold text-dark">{{ $admission->admission_date ? $admission->admission_date->format('d F Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small">Academic Session:</span>
                            <div class="fw-semibold text-dark">{{ $admission->student?->academic_session ?? date('Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-sm-3 text-end">
                    <div class="photo-box ms-auto">
                        @if($admission->student?->photo_url)
                            <img src="{{ $admission->student->photo_url }}" alt="{{ $admission->student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.style.display='none';">
                        @else
                            <div class="text-center text-muted small p-1">
                                <i class="fa-regular fa-user fs-2 mb-1"></i>
                                <div>Passport Photo</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 1. Candidate Particulars -->
            <div class="section-heading">1. Candidate Personal Particulars</div>
            <div class="row g-2 mb-4 small">
                <div class="col-md-6">
                    <span class="text-muted">Student Full Name:</span>
                    <div class="fw-bold text-dark fs-6">{{ $admission->student?->full_name }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Father's / Guardian's Name:</span>
                    <div class="fw-semibold text-dark">{{ $admission->student?->father_name ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Mother's Name:</span>
                    <div class="fw-semibold text-dark">{{ $admission->student?->mother_name ?? '—' }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Date of Birth:</span>
                    <div class="fw-semibold text-dark">{{ $admission->student?->date_of_birth ? $admission->student->date_of_birth->format('d/m/Y') : '—' }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Gender:</span>
                    <div class="fw-semibold text-dark text-capitalize">{{ $admission->student?->gender ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Mobile / WhatsApp:</span>
                    <div class="fw-semibold text-success"><i class="fa-solid fa-phone me-1"></i>{{ $admission->student?->phone }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Email Address:</span>
                    <div class="fw-semibold text-dark">{{ $admission->student?->email ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <span class="text-muted">Permanent Address:</span>
                    <div class="fw-semibold text-dark">{{ $admission->student?->address ?? '—' }}, {{ $admission->student?->district?->name }}, {{ $admission->student?->state?->name }} - {{ $admission->student?->pincode }}</div>
                </div>
            </div>

            <!-- 2. Course & Batch Details -->
            <div class="section-heading">2. Course Enrolment & Training Schedule</div>
            <div class="row g-2 mb-4 small">
                <div class="col-md-6">
                    <span class="text-muted">Enrolled Course:</span>
                    <div class="fw-bold text-dark fs-6">{{ $admission->course?->course_name }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Course Code:</span>
                    <div class="fw-bold text-primary font-monospace">{{ $admission->course?->course_code }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Duration:</span>
                    <div class="fw-semibold text-dark">{{ $admission->course?->duration ?? 'Standard' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Assigned Batch Shift:</span>
                    <div class="fw-semibold text-dark">{{ $admission->batch?->batch_name ?? 'Regular Shift' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Class Timings:</span>
                    <div class="fw-semibold text-dark">
                        {{ $admission->batch ? $admission->batch->start_time . ' - ' . $admission->batch->end_time : 'Standard Class Timing' }}
                    </div>
                </div>
            </div>

            <!-- 3. Fee Assessment & Installment Schedule -->
            <div class="section-heading">3. Fee Assessment & Instalment Schedule</div>
            <div class="row g-2 mb-3 text-center small">
                <div class="col-3">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted">Course Fee</div>
                        <div class="fw-bold text-dark">₹{{ number_format($admission->course_fee, 2) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted">Discount / Concession</div>
                        <div class="fw-bold text-danger">₹{{ number_format($admission->discount_amount, 2) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted">Net Applicable Fee</div>
                        <div class="fw-bold text-primary">₹{{ number_format($admission->net_fee, 2) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted">Outstanding Balance</div>
                        <div class="fw-bold {{ ($admission->net_fee - $admission->total_paid) > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format(max(0, $admission->net_fee - $admission->total_paid), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installment Breakdown Table -->
            @if($admission->studentFees && $admission->studentFees->isNotEmpty())
                <table class="table table-sm table-bordered small mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Installment Title</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount Due</th>
                            <th class="text-end">Amount Paid</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admission->studentFees as $fee)
                            <tr>
                                <td>{{ $fee->installment_no }}</td>
                                <td>{{ $fee->title }}</td>
                                <td>{{ $fee->due_date ? $fee->due_date->format('d/m/Y') : '—' }}</td>
                                <td class="text-end">₹{{ number_format($fee->amount, 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="text-center">
                                    @if($fee->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($fee->status === 'partial')
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Signatures & Authority Seal -->
            <div class="mt-5 pt-4 border-top">
                <p class="text-muted small fst-italic mb-5">
                    This document is an authentic computerized record of student admission maintained in the central database of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}.
                </p>
                <div class="row text-center pt-3">
                    <div class="col-4">
                        <div class="border-top border-dark pt-1 small fw-bold">Student Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="border-top border-dark pt-1 small fw-bold">Centre Incharge / Seal</div>
                    </div>
                    <div class="col-4">
                        <div class="border-top border-dark pt-1 small fw-bold">Authorized Controller</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
