@extends('layouts.student')

@section('title', 'My Admission Form - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-file-contract text-primary me-2"></i> Official Admission Form
            </h4>
            <div class="text-muted small">Verified admission record and student enrolment dossier</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-2 px-4 btn-sm">
                <i class="fa-solid fa-print me-1"></i> Print / Save PDF
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Official Printable Admission Form Document -->
    <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-3 p-md-5 mx-auto" style="max-width: 900px;">
        <!-- Header / Letterhead -->
        <div class="border-bottom pb-4 mb-4 text-center position-relative">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 55px; width: auto; object-fit: contain;">
                <div class="text-start">
                    <h3 class="fw-bold text-primary mb-0" style="letter-spacing: 1px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h3>
                    <div class="small text-muted fw-semibold">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                </div>
            </div>
            <div class="small text-secondary">
                {{ $student->centre?->centre_name ?? ($instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105') }} &bull;
                Phone: {{ $student->centre?->contact_number ?? ($instituteSettings['contact_phone'] ?? '9162384660') }}
            </div>
            <div class="mt-3">
                <span class="badge bg-primary px-3 py-2 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">
                    STUDENT ADMISSION ENROLMENT FORM
                </span>
            </div>
        </div>

        <!-- Student Photo & Identification Strip -->
        <div class="row align-items-center g-3 mb-4 p-3 rounded-3" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
            <div class="col-8 col-md-9">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <span class="text-muted small">Roll Number:</span>
                        <div class="fw-bold font-monospace text-primary fs-6">{{ $student->roll_no ?? 'Under Process' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small">Registration Number:</span>
                        <div class="fw-bold font-monospace text-dark fs-6">{{ $student->registration_no }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small">Academic Session:</span>
                        <div class="fw-semibold text-dark">{{ $student->academic_session ?? date('Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small">Admission Status:</span>
                        <span class="badge bg-success-subtle text-success fw-semibold">Verified & Approved</span>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-3 text-end">
                <div class="border border-2 border-secondary rounded-2 overflow-hidden d-inline-block bg-light" style="width: 110px; height: 130px;">
                    @if($student->photo_path && file_exists(public_path('storage/' . $student->photo_path)))
                        <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted small p-1">
                            <i class="fa-regular fa-image fs-3 mb-1"></i>
                            Passport Photo
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 1: Candidate Details -->
        <h6 class="fw-bold text-uppercase text-primary border-bottom pb-2 mb-3" style="letter-spacing: 0.5px;">
            1. Candidate Personal Particulars
        </h6>
        <div class="row g-3 mb-4 small">
            <div class="col-md-6">
                <div class="text-muted">Full Name of Student:</div>
                <div class="fw-bold text-dark fs-6">{{ $student->full_name }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Father's / Guardian's Name:</div>
                <div class="fw-semibold text-dark">{{ $student->father_name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Mother's Name:</div>
                <div class="fw-semibold text-dark">{{ $student->mother_name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Date of Birth:</div>
                <div class="fw-semibold text-dark">{{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : 'N/A' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Gender:</div>
                <div class="fw-semibold text-dark text-capitalize">{{ $student->gender ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Contact Phone / WhatsApp:</div>
                <div class="fw-semibold text-dark">{{ $student->phone }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Email Address:</div>
                <div class="fw-semibold text-dark">{{ $student->email ?? 'N/A' }}</div>
            </div>
            <div class="col-12">
                <div class="text-muted">Permanent Address:</div>
                <div class="fw-semibold text-dark">{{ $student->address ?? 'N/A' }}, {{ $student->district?->name ?? '' }}, {{ $student->state?->name ?? '' }} - {{ $student->pincode ?? '' }}</div>
            </div>
        </div>

        <!-- Section 2: Course & Admission Information -->
        <h6 class="fw-bold text-uppercase text-primary border-bottom pb-2 mb-3" style="letter-spacing: 0.5px;">
            2. Course & Batch Enrolment
        </h6>
        <div class="row g-3 mb-4 small">
            <div class="col-md-6">
                <div class="text-muted">Enrolled Course:</div>
                <div class="fw-bold text-dark fs-6">{{ $admission?->course?->course_name ?? 'Not Enrolled' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Course Code:</div>
                <div class="fw-bold text-primary font-monospace">{{ $admission?->course?->course_code ?? 'N/A' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Course Duration:</div>
                <div class="fw-semibold text-dark">{{ $admission?->course?->duration ?? 'Standard' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted">Allocated Batch:</div>
                <div class="fw-semibold text-dark">{{ $admission?->batch?->batch_name ?? 'Regular Shift' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Batch Timing:</div>
                <div class="fw-semibold text-dark">{{ $admission?->batch ? $admission->batch->start_time . ' - ' . $admission->batch->end_time : 'Standard' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Admission Date:</div>
                <div class="fw-semibold text-dark">{{ $admission?->admission_date ? $admission->admission_date->format('d/m/Y') : date('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Section 3: Fee Ledger Overview -->
        <h6 class="fw-bold text-uppercase text-primary border-bottom pb-2 mb-3" style="letter-spacing: 0.5px;">
            3. Fee Assessment Summary
        </h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Gross Fee</th>
                        <th>Concession / Discount</th>
                        <th>Net Applicable Fee</th>
                        <th>Fee Paid to Date</th>
                        <th>Outstanding Dues</th>
                    </tr>
                </thead>
                <tbody>
                    @if($admission)
                        <tr>
                            <td>₹{{ number_format($admission->course_fee, 2) }}</td>
                            <td class="text-danger">₹{{ number_format($admission->discount_amount, 2) }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($admission->net_fee, 2) }}</td>
                            <td class="fw-bold text-success">₹{{ number_format($admission->total_paid, 2) }}</td>
                            <td class="fw-bold {{ ($admission->net_fee - $admission->total_paid) > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format(max(0, $admission->net_fee - $admission->total_paid), 2) }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted">No admission fee structure linked.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Signatures & Declaration -->
        <div class="mt-5 pt-4 border-top">
            <p class="text-muted small fst-italic mb-4">
                <strong>Declaration:</strong> I hereby declare that the particulars provided above are authentic and accurate to the best of my knowledge. I undertake to adhere strictly to all academic, examination and disciplinary regulations enforced by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}.
            </p>
            <div class="row text-center pt-4">
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small fw-bold">Student Signature</div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small fw-bold">Centre Incharge / Seal</div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-2 small fw-bold">Authorized Controller</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
