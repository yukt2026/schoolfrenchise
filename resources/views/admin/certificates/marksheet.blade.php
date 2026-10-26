<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $marksheet->marksheet_no ?? $certificate->certificate_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            padding: 20px;
        }

        .marksheet-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border: 2px solid #0891b2;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            position: relative;
        }

        .header-title {
            color: #0891b2;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .sign-box {
            border-top: 1px solid #475569;
            padding-top: 5px;
            width: 170px;
            margin: 0 auto;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .marksheet-container {
                box-shadow: none;
                border: 2px solid #000;
                width: 100%;
                max-width: 100%;
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
        <i class="fa-solid fa-print me-2"></i> Print Official Marksheet
    </button>
</div>

<div class="marksheet-container">
    <!-- Header -->
    <div class="text-center border-bottom pb-3 mb-4">
        <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 52px; width: auto; object-fit: contain;">
            <div class="text-start">
                <h3 class="header-title mb-0" style="color: #0284c7;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h3>
                <div class="text-muted small fw-semibold">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
            </div>
        </div>
        <div class="small text-muted">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} | Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</div>
        <div class="badge bg-primary px-3 py-1 mt-2 text-uppercase fs-6">Statement of Marks</div>
    </div>

    <!-- Student & Course Particulars Table -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-sm-5">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted small fw-semibold" style="width: 140px;">Candidate Name:</td>
                    <td class="fw-bold text-dark">{{ $certificate->student->full_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Father's Name:</td>
                    <td class="fw-medium text-dark">{{ $certificate->student->father_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Roll Number:</td>
                    <td class="fw-bold text-primary font-monospace">{{ $certificate->student->roll_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Registration No:</td>
                    <td class="font-monospace text-dark">{{ $certificate->student->registration_no ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-5">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted small fw-semibold" style="width: 140px;">Course Curriculum:</td>
                    <td class="fw-bold text-dark">{{ $certificate->course->course_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Course Code:</td>
                    <td class="font-monospace text-dark">{{ $certificate->course->course_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Marksheet Serial:</td>
                    <td class="fw-bold text-dark font-monospace">{{ $marksheet->marksheet_no ?? 'DCE/MS' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Date of Issue:</td>
                    <td class="text-dark">{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : date('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-2 text-end">
            <div class="border border-2 border-dark rounded-1 overflow-hidden d-inline-block bg-light shadow-sm" style="width: 90px; height: 110px;">
                @if($certificate->student?->photo_url)
                    <img src="{{ $certificate->student->photo_url }}" alt="{{ $certificate->student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://placehold.co/90x110?text=Photo';">
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
    <div class="table-responsive mb-4">
        <table class="table table-bordered text-center align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th class="text-start ps-3" style="width: 35%;">Subject Papers</th>
                    <th>Max Marks</th>
                    <th>Theory</th>
                    <th>Practical</th>
                    <th>Viva</th>
                    <th>Total Obtained</th>
                    <th>Grade</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificate->result->subjectResults ?? [] as $subRes)
                    <tr>
                        <td class="text-start ps-3">
                            <div class="fw-semibold text-dark">{{ $subRes->subject->subject_name ?? 'Module Paper' }}</div>
                            <small class="text-muted font-monospace">{{ $subRes->subject->subject_code ?? '' }}</small>
                        </td>
                        <td class="fw-semibold text-secondary">{{ number_format($subRes->max_marks, 0) }}</td>
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
                        <td colspan="8" class="text-muted py-3">No individual subject entries found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td class="text-start ps-3">Aggregate Total</td>
                    <td>{{ number_format($certificate->result->total_max_marks ?? 0, 0) }}</td>
                    <td colspan="3" class="text-muted small">Marks Obtained</td>
                    <td class="text-primary fs-6">{{ number_format($certificate->result->total_marks_obtained ?? 0, 2) }}</td>
                    <td><span class="badge bg-secondary font-monospace">{{ $certificate->result->grade ?? 'A' }}</span></td>
                    <td><span class="badge bg-success">PASSED</span></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Aggregate Overview -->
    <div class="row g-2 p-3 bg-light rounded-3 mb-5 text-center">
        <div class="col-4">
            <span class="text-muted small text-uppercase">Percentage Score:</span>
            <div class="fw-bold fs-5 text-primary">{{ number_format($certificate->result->percentage ?? 0, 2) }}%</div>
        </div>
        <div class="col-4 border-start border-end">
            <span class="text-muted small text-uppercase">Final Grade:</span>
            <div class="fw-bold fs-5 text-dark font-monospace">{{ $certificate->result->grade ?? 'A' }}</div>
        </div>
        <div class="col-4">
            <span class="text-muted small text-uppercase">Result Status:</span>
            <div class="fw-bold fs-5 text-success text-uppercase">{{ $certificate->result->result_status ?? 'PASSED' }}</div>
        </div>
    </div>

    <!-- Signatures & Verification -->
    <div class="row text-center mt-5">
        <div class="col-4">
            <div class="sign-box">
                <div class="fw-bold small text-dark">Prepared By</div>
                <div class="text-muted" style="font-size: 0.72rem;">Examination Section</div>
            </div>
        </div>
        <div class="col-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($certificate->qr_code_url ?? route('verify.certificate.public', ['cert_no' => $certificate->certificate_no])) }}" alt="QR" style="width: 65px; height: 65px;" class="border p-1 bg-white mb-1">
            <div class="text-muted" style="font-size: 0.65rem;">Digital Verification QR</div>
        </div>
        <div class="col-4">
            <div class="sign-box">
                <div class="fw-bold small text-dark">Controller of Examination</div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
