@extends('layouts.admin')

@section('title', 'Certificate Dossier - ' . $certificate->certificate_no)

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0" style="color: #0f172a;">{{ $certificate->certificate_no }}</h4>
                @if($certificate->status === 'issued')
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-circle-check me-1"></i> Valid Credential</span>
                @else
                    <span class="badge bg-danger fs-6"><i class="fa-solid fa-ban me-1"></i> Revoked</span>
                @endif
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $certificate->certificate_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-print me-1"></i> Print Certificate
            </a>
            <a href="{{ route('admin.certificates.marksheet', $certificate) }}" target="_blank" class="btn btn-outline-info">
                <i class="fa-solid fa-file-invoice me-1"></i> View Marksheet
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Roster
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Certificate Dossier Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-award me-2 text-primary"></i>Certified Graduate Particulars
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-semibold">Graduate Candidate</div>
                            <div class="fs-5 fw-bold text-dark mt-1">{{ $certificate->student->full_name ?? 'N/A' }}</div>
                            <div class="text-muted small">Father: {{ $certificate->student->father_name ?? 'N/A' }}</div>
                            <div class="small font-monospace text-primary mt-1">Roll No: {{ $certificate->student->roll_no ?? 'N/A' }}</div>
                            <div class="small font-monospace text-muted">Reg No: {{ $certificate->student->registration_no ?? 'N/A' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-semibold">Course Completed</div>
                            <div class="fs-5 fw-bold text-dark mt-1">{{ $certificate->course->course_name ?? 'N/A' }}</div>
                            <div class="text-muted small">Duration: {{ $certificate->course->duration ?? 'N/A' }}</div>
                            <div class="mt-2">
                                <span class="badge bg-primary-subtle text-primary fs-6">
                                    Grade: {{ $certificate->result->grade ?? 'A' }}
                                </span>
                                <span class="badge bg-light text-dark border fs-6 ms-1">
                                    Score: {{ number_format($certificate->result->percentage ?? 0, 2) }}%
                                </span>
                            </div>
                        </div>

                        <div class="col-12"><hr class="my-0"></div>

                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-semibold">Issuing Study Centre</div>
                            <div class="fw-bold text-dark mt-1">{{ $certificate->centre->centre_name ?? 'Central Head Office' }}</div>
                            <div class="small text-muted">{{ $certificate->centre->address ?? 'Noida, Uttar Pradesh' }}</div>
                            <span class="badge bg-light text-secondary border font-monospace mt-1">
                                Code: {{ $certificate->centre->centre_code ?? 'CENTRAL' }}
                            </span>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-muted small text-uppercase fw-semibold">Issuance Timeline</div>
                            <div class="fw-bold text-dark mt-1">{{ $certificate->issue_date ? $certificate->issue_date->format('d F, Y') : 'N/A' }}</div>
                            <div class="small text-muted">Registered in National Verification Ledger</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject-wise Academic Performance Breakdown -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-list-check me-2 text-primary"></i>Curriculum Marks Statement
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3">Subject Paper</th>
                                <th>Max Marks</th>
                                <th>Obtained</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificate->result->subjectResults ?? [] as $subRes)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $subRes->subject->subject_name ?? 'Module' }}</div>
                                        <small class="text-muted font-monospace">{{ $subRes->subject->subject_code ?? '' }}</small>
                                    </td>
                                    <td>{{ number_format($subRes->max_marks, 0) }}</td>
                                    <td class="fw-bold text-dark">{{ number_format($subRes->total_obtained, 1) }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $subRes->grade }}</span></td>
                                    <td><span class="text-success fw-bold small"><i class="fa-solid fa-check me-1"></i>Pass</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">No individual subject entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Security & Verification Box -->
        <div class="col-lg-4">
            <!-- QR & Anti-Counterfeit Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 text-center">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-qrcode me-2 text-primary"></i>Live Verification QR
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-3 d-inline-block border mb-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($certificate->qr_code_url ?? route('verify.cert.direct', ['cert_no' => $certificate->certificate_no])) }}" alt="Certificate QR" class="img-fluid" style="width: 140px; height: 140px;">
                    </div>
                    <div class="small text-muted mb-2">Scan with camera to verify online authenticity</div>
                    <a href="{{ $certificate->qr_code_url ?? route('verify.cert.direct', ['cert_no' => $certificate->certificate_no]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Public Verification Link
                    </a>

                    <div class="text-start mt-3 pt-3 border-top small">
                        <div class="text-muted text-uppercase fw-semibold mb-1">SHA-256 Security Hash</div>
                        <div class="font-monospace text-break bg-light p-2 rounded small text-secondary" style="font-size: 0.72rem;">
                            {{ $certificate->security_hash ?: hash('sha256', $certificate->certificate_no) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revocation / Status Actions -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-shield-halved me-2 text-primary"></i>Credential Status Management
                    </h6>
                </div>
                <div class="card-body p-3">
                    @if($certificate->status === 'issued')
                        <div class="alert alert-success small mb-3 py-2">
                            <i class="fa-solid fa-circle-check me-1"></i> This certificate is officially active and valid.
                        </div>
                        <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" onsubmit="return confirm('Are you sure you want to revoke this certificate? The verification portal will display it as revoked.');">
                            @csrf
                            <div class="mb-2">
                                <label for="reason" class="form-label small fw-semibold">Revocation Reason</label>
                                <input type="text" name="reason" id="reason" class="form-control form-control-sm" placeholder="e.g. Identity discrepancy, Board order">
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="fa-solid fa-ban me-1"></i> Revoke Certificate
                            </button>
                        </form>
                    @else
                        <div class="alert alert-danger small mb-3 py-2">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> This certificate has been revoked.
                        </div>
                        <form method="POST" action="{{ route('admin.certificates.reissue', $certificate) }}" onsubmit="return confirm('Reinstate this certificate to active status?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reinstate to Active Status
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
