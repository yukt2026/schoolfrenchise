@extends('layouts.centre')

@section('title', 'Centre Profile & Settings - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-3 mb-md-4">
    <h3 class="fw-bold text-dark mb-1 fs-5 fs-md-4">
        <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Study Centre Profile & Settings
    </h3>
    <p class="text-muted small mb-0">Authorized franchise branch credentials, director profile, registered campus address, and security credentials.</p>
</div>

<!-- Main Admin Profile Protection Notice -->
<div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 mb-4 p-3">
    <i class="fa-solid fa-shield-halved fs-3 text-primary flex-shrink-0"></i>
    <div>
        <h6 class="fw-bold mb-1 text-dark">Official Profile Details Protected</h6>
        <p class="small mb-0 text-secondary">
            Franchise study centre profile information (Director identity, phone numbers, registered address, and official name) is verified and controlled directly by <strong>Main Administration</strong>. If any profile adjustments are required, please contact Head Office Support.
        </p>
    </div>
</div>

<div class="row g-3 g-md-4">
    <!-- Left Column: Read-Only Dossier & Change Password -->
    <div class="col-lg-8">
        <!-- Centre Profile Dossier (Read-Only) -->
        <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
            <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-building text-primary me-2"></i>Official Study Centre Particulars
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-secondary border rounded-2 font-monospace">
                        <i class="fa-solid fa-lock me-1"></i>Admin Locked
                    </span>
                    <span class="badge bg-primary font-monospace rounded-2">{{ $centre->centre_code }}</span>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Study Centre Official Name</label>
                        <div class="form-control bg-light rounded-2 fw-bold text-dark">{{ $centre->centre_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Director / Head Person</label>
                        <div class="form-control bg-light rounded-2 fw-semibold text-dark">{{ $centre->director_name }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Primary Contact Mobile</label>
                        <div class="form-control bg-light rounded-2 font-monospace">{{ $centre->mobile }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Alternate Mobile</label>
                        <div class="form-control bg-light rounded-2 font-monospace">{{ $centre->alternate_mobile ?: 'N/A' }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Official Contact Email</label>
                        <div class="form-control bg-light rounded-2">{{ $centre->email }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Computer Lab Workstations Capacity</label>
                        <div class="form-control bg-light rounded-2 fw-bold text-primary">{{ $centre->lab_capacity }} PCs</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label text-muted small fw-semibold">Registered Campus Address</label>
                        <div class="form-control bg-light rounded-2">{{ $centre->address }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Postal Pincode</label>
                        <div class="form-control bg-light rounded-2 font-monospace">{{ $centre->pincode }}</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">City / Town</label>
                        <div class="form-control bg-light rounded-2">{{ $centre->city }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">District</label>
                        <div class="form-control bg-light rounded-2">{{ $centre->district?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">State</label>
                        <div class="form-control bg-light rounded-2">{{ $centre->state?->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-muted small fw-semibold mb-0">Centre Logo</label>
                            <span class="badge bg-secondary-subtle text-muted border small"><i class="fa-solid fa-lock me-1"></i>Locked</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light text-center">
                            @if($centre->centre_logo_url)
                                <img src="{{ $centre->centre_logo_url }}" alt="Logo" class="rounded bg-white p-1" style="height: 60px; max-width: 120px; object-fit: contain;">
                            @else
                                <span class="text-muted small">No logo registered</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-muted small fw-semibold mb-0">Director Photo</label>
                            <span class="badge bg-secondary-subtle text-muted border small"><i class="fa-solid fa-lock me-1"></i>Locked</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light text-center">
                            @if($centre->director_photo_url)
                                <img src="{{ $centre->director_photo_url }}" alt="Director" class="rounded-circle border" style="height: 60px; width: 60px; object-fit: cover;">
                            @else
                                <span class="text-muted small">No photo on record</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-muted small fw-semibold mb-0">Director Specimen Signature</label>
                            <span class="badge bg-secondary-subtle text-muted border small"><i class="fa-solid fa-lock me-1"></i>Locked</span>
                        </div>
                        <div class="p-2 border rounded-2 bg-light text-center">
                            @if($centre->director_signature_url)
                                <img src="{{ $centre->director_signature_url }}" alt="Signature" class="bg-white p-1 rounded" style="height: 35px; max-width: 120px; object-fit: contain;">
                            @else
                                <span class="text-muted small">No signature uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password Card (Self-Service) -->
        <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-0">
            <div class="card-header bg-white py-2 py-md-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-lock text-warning me-2"></i>Change Centre Login Password</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('centre.profile.change-password') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control rounded-2 @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-2 @error('password') is-invalid @enderror" minlength="6" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control rounded-2" minlength="6" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning text-dark rounded-2 px-4 fw-bold mt-3 mt-md-4">
                        <i class="fa-solid fa-key me-1"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Affiliation Credentials Summary -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-2 py-md-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-award text-warning me-2"></i>Affiliation Standing</h6>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="text-center mb-3">
                    @if($centre->centre_logo_url)
                        <div class="mb-2">
                            <img src="{{ $centre->centre_logo_url }}" alt="{{ $centre->centre_name }}" class="rounded border p-1 bg-white shadow-sm" style="max-height: 55px; max-width: 140px; object-fit: contain;">
                        </div>
                    @endif
                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 fw-bold rounded-2">
                        <i class="fa-solid fa-circle-check me-1"></i>AUTHORIZED ACTIVE
                    </span>
                </div>

                <ul class="list-unstyled mb-0 small">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Centre Code:</span>
                        <span class="fw-bold font-monospace text-primary">{{ $centre->centre_code }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Affiliation Date:</span>
                        <span class="fw-semibold">{{ $centre->affiliation_date ? $centre->affiliation_date->format('d M, Y') : 'N/A' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Validity Term:</span>
                        <span class="fw-semibold">{{ $centre->valid_upto ? $centre->valid_upto->format('d M, Y') : 'Annual Regular' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Director:</span>
                        <span class="fw-bold text-dark">{{ $centre->director_name }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Prepaid Wallet:</span>
                        <span class="fw-bold text-success font-monospace">₹{{ number_format($centre->wallet?->balance ?? 0, 2) }}</span>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-outline-primary rounded-2 w-100 fw-semibold" target="_blank">
                        <i class="fa-solid fa-certificate me-1"></i>Affiliation Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
