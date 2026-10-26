@extends('layouts.admin')

@section('title', 'Review Application - ' . $application->application_no . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}" class="text-decoration-none">Admissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $application->application_no }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Review Admission Application</h3>
        <p class="text-muted small mb-0">Inspect applicant particulars, verify educational credentials, and process admission approval.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admission.acknowledgement', $application->application_no) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-print me-1"></i> Print Application Slip
        </a>
        <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Applications
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Applicant Dossier -->
    <div class="col-lg-7">
        <div class="card dce-table-card mb-4 border-top border-4 border-info">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user-tag text-primary me-2"></i>Applicant Particulars
                </h6>
                <span class="badge font-monospace fs-6 
                    @if($application->status === 'approved') bg-success 
                    @elseif($application->status === 'rejected') bg-danger 
                    @else bg-warning text-dark @endif">
                    {{ strtoupper(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 table-striped">
                    <tbody>
                        <tr>
                            <th class="ps-3 w-35 text-muted">Application Number</th>
                            <td class="fw-bold font-monospace text-primary">{{ $application->application_no }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Applicant Name</th>
                            <td class="fw-bold text-dark">{{ $application->applicant_name }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Father / Guardian</th>
                            <td>{{ $application->father_name }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Mother's Name</th>
                            <td>{{ $application->mother_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Date of Birth</th>
                            <td>{{ $application->dob ? \Carbon\Carbon::parse($application->dob)->format('d F Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Gender</th>
                            <td>{{ ucfirst($application->gender) }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Mobile Contact</th>
                            <td><strong class="text-success"><i class="fa-solid fa-phone me-1"></i>{{ $application->mobile }}</strong></td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Email</th>
                            <td>{{ $application->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Highest Qualification</th>
                            <td><span class="badge bg-light text-dark border">{{ $application->highest_qualification ?? 'Not Specified' }}</span></td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Course Applied</th>
                            <td>
                                <strong class="text-primary">{{ $application->course?->title }}</strong>
                                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $application->course?->course_code }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Target Study Centre</th>
                            <td>
                                <span class="fw-semibold">[{{ $application->centre?->centre_code }}] {{ $application->centre?->centre_name }}</span>
                                <div class="text-muted small">{{ $application->centre?->city }}, {{ $application->centre?->state?->name }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3 text-muted">Residential Address</th>
                            <td class="small">{{ $application->address }}, {{ $application->city }}, {{ $application->district?->name }}, {{ $application->state?->name }} - {{ $application->pincode }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KYC & Supporting Files -->
        <div class="card dce-table-card">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-paperclip text-info me-2"></i>Uploaded Applicant Documents</h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Passport Photo</label>
                        @if($application->photo_url)
                            <div class="border rounded p-2 text-center bg-light">
                                <img src="{{ $application->photo_url }}" alt="Photo" class="rounded img-fluid mb-2" style="max-height: 140px;" onerror="this.onerror=null;this.src='https://placehold.co/120x140?text=Photo';">
                                <div><a href="{{ $application->photo_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open Full Image</a></div>
                            </div>
                        @else
                            <div class="border rounded p-3 text-center text-muted small bg-light">No photo uploaded</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">KYC / Educational Document</label>
                        @if($application->id_proof_url)
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fa-solid fa-file-pdf fa-3x text-danger mb-2"></i>
                                <div class="small fw-semibold text-truncate mb-2">{{ basename($application->id_proof_doc) }}</div>
                                <a href="{{ $application->id_proof_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> View Document
                                </a>
                            </div>
                        @else
                            <div class="border rounded p-3 text-center text-muted small bg-light">No document attached</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Verification & Approval Actions -->
    <div class="col-lg-5">
        @if($application->status === 'approved')
            <div class="card dce-table-card border-top border-4 border-success">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-success"><i class="fa-solid fa-circle-check me-2"></i>Application Approved</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">This application has been verified and converted into an official enrolled student record.</p>
                    @if($application->convertedStudent)
                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="small text-muted">Enrolled Student:</div>
                            <div class="fw-bold text-dark fs-5">{{ $application->convertedStudent->full_name }}</div>
                            <div class="font-monospace text-primary">Roll No: {{ $application->convertedStudent->roll_no }}</div>
                            <div class="font-monospace text-secondary small">Reg No: {{ $application->convertedStudent->registration_no }}</div>
                        </div>
                        <a href="{{ route('admin.students.show', $application->convertedStudent->id) }}" class="btn btn-primary w-100 shadow-sm">
                            <i class="fa-solid fa-id-badge me-1"></i> Open Student Dossier
                        </a>
                    @endif
                </div>
            </div>
        @elseif($application->status === 'rejected')
            <div class="card dce-table-card border-top border-4 border-danger">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-circle-xmark me-2"></i>Application Rejected</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-0">
                        <div class="fw-bold small mb-1">Reason for Rejection:</div>
                        <div>{{ $application->rejection_reason }}</div>
                    </div>
                </div>
            </div>
        @else
            <!-- Action Form: Approve -->
            <div class="card dce-table-card border-top border-4 border-success mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-success"><i class="fa-solid fa-check-double me-2"></i>Approve & Enroll Student</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admissions.application.approve', $application->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="course_fee" class="form-control" 
                                   value="{{ old('course_fee', $application->course?->total_fee ?? 0) }}" required>
                            <div class="form-text small">Default fee set by course curriculum.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Discount / Concession Amount (₹)</label>
                            <input type="number" step="0.01" name="discount_amount" class="form-control" 
                                   value="{{ old('discount_amount', 0) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fee Structure / Instalment Plan</label>
                            <select name="fee_structure_id" class="form-select">
                                <option value="">-- Standard Single / Course Fee --</option>
                                @foreach($feeStructures as $fs)
                                    <option value="{{ $fs->id }}">
                                        {{ $fs->name }} (₹{{ number_format($fs->total_fee, 2) }} in {{ $fs->installments_count }} Instalments)
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Automatically configures monthly instalment schedule.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Allocate Batch (Optional)</label>
                            <select name="batch_id" class="form-select">
                                <option value="">-- No Batch Assigned Yet --</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">{{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Approval Remarks (Optional)</label>
                            <textarea name="remarks" rows="2" class="form-control" placeholder="Optional notes for enrolment dossier"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 shadow-sm fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Confirm Approval & Generate Roll No
                        </button>
                    </form>
                </div>
            </div>

            <!-- Action Form: Reject -->
            <div class="card dce-table-card border-top border-4 border-danger">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-ban me-2"></i>Reject Application</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admissions.application.reject', $application->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" rows="3" class="form-control @error('rejection_reason') is-invalid @enderror" 
                                      placeholder="e.g. Incomplete educational documents, illegible ID proof, invalid contact details..." required></textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-outline-danger w-100 py-2" data-confirm="Are you sure you want to reject this admission application?">
                            <i class="fa-solid fa-xmark me-1"></i> Reject Application
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
