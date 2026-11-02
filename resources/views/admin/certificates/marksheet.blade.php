<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Statement of Marks - {{ $marksheet->marksheet_no ?? $certificate->certificate_no }} - {{ $certificate->student->full_name ?? 'Student' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @page {
            size: A4 portrait;
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
            color: #0f172a;
            padding: 16px;
            margin: 0;
        }

        .no-print-bar {
            max-width: 860px;
            margin: 0 auto 14px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .marksheet-container {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px 30px;
            border: 2px solid #0284c7;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .header-title {
            color: #0284c7;
            font-family: 'Cinzel', serif;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.45rem;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .header-tagline {
            color: #ea580c;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-badge {
            background: #0f172a;
            color: #fbbf24;
            font-weight: 800;
            letter-spacing: 1.5px;
            font-size: 0.82rem;
            padding: 3px 20px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
        }

        .info-table td, .info-table th {
            padding: 2px 4px;
            font-size: 0.78rem;
        }

        .table-marks th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 6px;
        }

        .table-marks td {
            font-size: 0.76rem;
            padding: 4px 6px;
        }

        .sign-box {
            border-top: 1.5px solid #334155;
            padding-top: 4px;
            width: 150px;
            margin: 0 auto;
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
            .marksheet-container {
                box-shadow: none !important;
                border: 2px solid #0f172a !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 16px 20px !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

<!-- Control Bar -->
<div class="no-print-bar">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary px-3 py-2 font-monospace">MARKSHEET: {{ $marksheet->marksheet_no ?? $certificate->certificate_no }}</span>
        <span class="text-muted small">Official Statement of Academic Marks</span>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #0284c7; border-color: #0284c7;">
            <i class="fa-solid fa-print me-1"></i> Print Official Marksheet
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3">
            <i class="fa-solid fa-xmark me-1"></i> Close
        </button>
    </div>
</div>

<div class="marksheet-container">
    <!-- Header -->
    <div class="text-center border-bottom border-2 pb-2 mb-3">
        <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
            <img src="{{ $certificate->centre?->centre_logo_url ?? ($instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png')) }}" alt="{{ $certificate->centre?->centre_name ?? 'Centre Logo' }}" style="height: 52px; width: auto; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
            <div class="text-start">
                <div class="header-title text-uppercase" style="color: #0284c7;">{{ $certificate->centre?->centre_name ?? ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }}</div>
                <div class="fw-bold text-uppercase" style="color: #b45309; font-size: 0.82rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-certificate me-1"></i> Authorized by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                </div>
            </div>
        </div>
        <div class="small text-muted" style="font-size: 0.68rem;">
            Registered Under MCA (Govt. of India) &bull; ISO 9001:2015 Certified Organization &bull; Centre Code: {{ $certificate->centre?->centre_code ?? 'MAIN' }}<br>
            {{ $certificate->centre?->centre_address ?? ($instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105') }} &bull; Helpline: {{ $certificate->centre?->contact_number ?? ($instituteSettings['contact_phone'] ?? '9162384660') }}
        </div>
        <div class="mt-2">
            <span class="doc-badge">STATEMENT OF MARKS</span>
        </div>
    </div>

    <!-- Student & Course Particulars Table -->
    <div class="row g-2 mb-3 align-items-center">
        <div class="col-sm-5">
            <table class="table table-sm table-borderless info-table mb-0">
                <tr>
                    <td class="text-muted fw-semibold" style="width: 120px;">Candidate Name:</td>
                    <td class="fw-bold text-dark text-uppercase">{{ $certificate->student->full_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Father's Name:</td>
                    <td class="fw-medium text-dark">{{ $certificate->student->father_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Mother's Name:</td>
                    <td class="text-dark">{{ $certificate->student->mother_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Roll Number:</td>
                    <td class="fw-bold text-primary font-monospace">{{ $certificate->student->roll_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Registration No:</td>
                    <td class="font-monospace text-dark">{{ $certificate->student->registration_no ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-5">
            <table class="table table-sm table-borderless info-table mb-0">
                <tr>
                    <td class="text-muted fw-semibold" style="width: 125px;">Course Curriculum:</td>
                    <td class="fw-bold text-dark">{{ $certificate->course->course_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Course Code:</td>
                    <td class="font-monospace text-dark">{{ $certificate->course->course_code ?? 'N/A' }} ({{ $certificate->course->duration ?? '6 Months' }})</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Study Centre:</td>
                    <td class="text-dark">{{ $certificate->centre->centre_name ?? 'Main Centre' }} ({{ $certificate->centre->centre_code ?? 'CENTRAL' }})</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Marksheet Serial:</td>
                    <td class="fw-bold text-primary font-monospace">{{ $marksheet->marksheet_no ?? 'DCE/MS' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-semibold">Date of Issue:</td>
                    <td class="text-dark">{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : date('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-2 text-end">
            <div class="border border-2 border-dark rounded-1 overflow-hidden d-inline-block bg-light shadow-sm" style="width: 85px; height: 100px;">
                @if($certificate->student?->photo_url)
                    <img src="{{ $certificate->student->photo_url }}" alt="{{ $certificate->student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://placehold.co/85x100?text=Photo';">
                @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted small p-1">
                        <i class="fa-solid fa-user-graduate fs-4 opacity-50"></i>
                        <span style="font-size: 0.65rem;">Photo</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Marks Grid -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-marks text-center align-middle mb-0 border-dark">
            <thead class="table-dark">
                <tr>
                    <th class="text-start ps-3" style="width: 38%;">Subject / Module Papers</th>
                    <th>Max</th>
                    <th>Pass</th>
                    <th>Theory</th>
                    <th>Practical</th>
                    <th>Viva</th>
                    <th>Total Obtained</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificate->result->subjectResults ?? [] as $subRes)
                    <tr>
                        <td class="text-start ps-3">
                            <div class="fw-bold text-dark">{{ $subRes->subject->subject_name ?? 'Curriculum Paper' }}</div>
                            <small class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $subRes->subject->subject_code ?? '' }}</small>
                        </td>
                        <td class="fw-semibold text-secondary">{{ number_format($subRes->max_marks, 0) }}</td>
                        <td>{{ number_format($subRes->subject?->pass_marks ?: round($subRes->max_marks * 0.4), 0) }}</td>
                        <td>{{ number_format($subRes->theory_obtained, 1) }}</td>
                        <td>{{ number_format($subRes->practical_obtained, 1) }}</td>
                        <td>{{ number_format($subRes->viva_obtained, 1) }}</td>
                        <td class="fw-bold text-dark">{{ number_format($subRes->total_obtained, 1) }}</td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $subRes->grade }}</span></td>
                        <td>
                            @if($subRes->is_passed)
                                <span class="text-success fw-bold small"><i class="fa-solid fa-check me-1"></i>Pass</span>
                            @else
                                <span class="text-danger fw-bold small"><i class="fa-solid fa-xmark me-1"></i>Fail</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-start ps-3 fw-bold">Course Comprehensive Examination</td>
                        <td>{{ number_format($certificate->result->total_max_marks ?? 100, 0) }}</td>
                        <td>{{ number_format(round(($certificate->result->total_max_marks ?? 100) * 0.4), 0) }}</td>
                        <td>{{ number_format($certificate->result->total_marks_obtained ?? 0, 1) }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="fw-bold text-dark">{{ number_format($certificate->result->total_marks_obtained ?? 0, 1) }}</td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $certificate->result->grade ?? 'A' }}</span></td>
                        <td><span class="text-success fw-bold small"><i class="fa-solid fa-check me-1"></i>Pass</span></td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold border-top border-2 border-dark">
                <tr>
                    <td class="text-start ps-3 text-uppercase">Grand Aggregate Total</td>
                    <td>{{ number_format($certificate->result->total_max_marks ?? 0, 0) }}</td>
                    <td>-</td>
                    <td colspan="3" class="text-muted small text-end pe-2">Total Obtained:</td>
                    <td class="text-primary fs-6">{{ number_format($certificate->result->total_marks_obtained ?? 0, 1) }}</td>
                    <td><span class="badge bg-primary font-monospace">{{ $certificate->result->grade ?? 'A' }}</span></td>
                    <td>
                        <span class="badge {{ ($certificate->result->result_status ?? 'passed') === 'passed' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($certificate->result->result_status ?? 'PASSED') }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Aggregate Overview & Grading Key -->
    <div class="row g-2 p-2 bg-light rounded-2 border mb-3 text-center align-items-center">
        <div class="col-3">
            <span class="text-muted small text-uppercase" style="font-size: 0.68rem;">Percentage Score</span>
            <div class="fw-bold fs-6 text-primary">{{ number_format($certificate->result->percentage ?? 0, 2) }}%</div>
        </div>
        <div class="col-3 border-start border-end">
            <span class="text-muted small text-uppercase" style="font-size: 0.68rem;">Final Grade</span>
            <div class="fw-bold fs-6 text-dark font-monospace">{{ $certificate->result->grade ?? 'A' }}</div>
        </div>
        <div class="col-3 border-end">
            <span class="text-muted small text-uppercase" style="font-size: 0.68rem;">Overall Result</span>
            <div class="fw-bold fs-6 text-success text-uppercase">{{ $certificate->result->result_status ?? 'PASSED' }}</div>
        </div>
        <div class="col-3 text-start ps-2">
            <div class="text-muted font-monospace" style="font-size: 0.62rem; line-height: 1.2;">
                <strong>Grading:</strong> A+ &ge;85% &bull; A &ge;70% &bull; B &ge;60% &bull; C &ge;50% &bull; D &ge;40% (Pass)
            </div>
        </div>
    </div>

    <!-- Signatures & Verification -->
    <div class="row text-center align-items-end pt-3 border-top border-dark">
        <div class="col-4">
            <div class="sign-box">
                <div class="fw-bold text-dark" style="font-size: 0.74rem;">Centre Incharge / Director</div>
                <div class="text-muted" style="font-size: 0.65rem;">{{ $certificate->centre?->director_name ?? 'Authorized Signatory' }}</div>
                <div class="text-secondary font-monospace" style="font-size: 0.58rem;">(Official Seal & Signature)</div>
            </div>
        </div>
        <div class="col-4">
            @php
                $qrTarget = $certificate->qr_code_url ?: route('verify.cert.direct', ['cert_no' => $certificate->certificate_no]);
            @endphp
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($qrTarget) }}" alt="QR" style="width: 62px; height: 62px;" class="border p-1 bg-white mb-1 shadow-sm">
            <div class="text-muted font-monospace" style="font-size: 0.6rem;">Scan with Google / Camera to Verify</div>
        </div>
        <div class="col-4">
            <div class="sign-box">
                <div class="fw-bold text-dark" style="font-size: 0.74rem;">Controller of Examination</div>
                <div class="text-muted" style="font-size: 0.65rem;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
