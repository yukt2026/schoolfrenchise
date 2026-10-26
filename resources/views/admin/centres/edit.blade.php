@extends('layouts.admin')

@section('title', 'Edit Centre: ' . $centre->centre_code)

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.index') }}">Centres</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.show', $centre) }}">{{ $centre->centre_code }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Centre Particulars: {{ $centre->centre_code }}
            </h2>
            <p class="text-muted small mb-0">Modify institutional affiliation details, contact particulars, or hardware capacity.</p>
        </div>
        <div>
            <a href="{{ route('admin.centres.show', $centre) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dossier
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.centres.update', $centre) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Section 1: Centre Identity & Affiliation -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-building-columns text-primary me-2"></i>1. Institute Identity & Accreditation
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Centre Code <span class="text-danger">*</span></label>
                        <input type="text" name="centre_code" class="form-control font-monospace @error('centre_code') is-invalid @enderror" value="{{ old('centre_code', $centre->centre_code) }}" required>
                        @error('centre_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Centre / Institute Name <span class="text-danger">*</span></label>
                        <input type="text" name="centre_name" class="form-control @error('centre_name') is-invalid @enderror" value="{{ old('centre_name', $centre->centre_name) }}" required>
                        @error('centre_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Affiliation Date <span class="text-danger">*</span></label>
                        <input type="date" name="affiliation_date" class="form-control @error('affiliation_date') is-invalid @enderror" value="{{ old('affiliation_date', $centre->affiliation_date?->toDateString()) }}" required>
                        @error('affiliation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Valid Upto</label>
                        <input type="date" name="valid_upto" class="form-control @error('valid_upto') is-invalid @enderror" value="{{ old('valid_upto', $centre->valid_upto?->toDateString()) }}">
                        @error('valid_upto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Operational Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $centre->status) === 'active' ? 'selected' : '' }}>Active (Operational)</option>
                            <option value="pending" {{ old('status', $centre->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ old('status', $centre->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="rejected" {{ old('status', $centre->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Director Particulars -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user-tie text-primary me-2"></i>2. Centre Director Details
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Centre Director / Head Name <span class="text-danger">*</span></label>
                        <input type="text" name="director_name" class="form-control @error('director_name') is-invalid @enderror" value="{{ old('director_name', $centre->director_name) }}" required>
                        @error('director_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Official Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $centre->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Primary Mobile No <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $centre->mobile) }}" required>
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Alternate Mobile / Helpline</label>
                        <input type="text" name="alternate_mobile" class="form-control @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile', $centre->alternate_mobile) }}">
                        @error('alternate_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Lab Workstation Capacity</label>
                        <input type="number" name="lab_capacity" class="form-control @error('lab_capacity') is-invalid @enderror" value="{{ old('lab_capacity', $centre->lab_capacity) }}" min="0">
                        @error('lab_capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Location -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-location-dot text-primary me-2"></i>3. Location & Physical Address
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">State <span class="text-danger">*</span></label>
                        <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror" required>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}" {{ old('state_id', $centre->state_id) == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">District <span class="text-danger">*</span></label>
                        <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                            @foreach($districts as $dst)
                                <option value="{{ $dst->id }}" {{ old('district_id', $centre->district_id) == $dst->id ? 'selected' : '' }}>{{ $dst->name }}</option>
                            @endforeach
                        </select>
                        @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">City / Town <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $centre->city) }}" required>
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-9">
                        <label class="form-label fw-semibold small">Complete Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $centre->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">PIN Code <span class="text-danger">*</span></label>
                        <input type="text" name="pincode" class="form-control font-monospace @error('pincode') is-invalid @enderror" value="{{ old('pincode', $centre->pincode) }}" required>
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Branding -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-camera text-primary me-2"></i>4. Update Centre Logo & Photos
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Update Centre Logo</label>
                        @if($centre->centre_logo)
                            <div class="mb-2">
                                <img src="{{ $centre->centre_logo_url ?? asset('storage/' . $centre->centre_logo) }}" alt="Logo" class="rounded border p-1 bg-white" style="height: 48px; max-width: 100px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/100x48?text=Logo';">
                            </div>
                        @endif
                        <input type="file" name="centre_logo" class="form-control" accept="image/*">
                        @if($centre->centre_logo)
                            <div class="small text-muted mt-1"><a href="{{ $centre->centre_logo_url ?? asset('storage/' . $centre->centre_logo) }}" target="_blank" class="text-decoration-none"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Full Logo</a></div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Update Director Photo</label>
                        @if($centre->director_photo)
                            <div class="mb-2">
                                <img src="{{ $centre->director_photo_url ?? asset('storage/' . $centre->director_photo) }}" alt="Director" class="rounded border p-1" style="height: 56px; width: 56px; object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/100x100?text=Photo';">
                            </div>
                        @endif
                        <input type="file" name="director_photo" class="form-control" accept="image/*">
                        @if($centre->director_photo)
                            <div class="small text-muted mt-1"><a href="{{ $centre->director_photo_url ?? asset('storage/' . $centre->director_photo) }}" target="_blank" class="text-decoration-none"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Full Photo</a></div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Update Director Signature</label>
                        @if($centre->director_signature)
                            <div class="mb-2">
                                <img src="{{ $centre->director_signature_url ?? asset('storage/' . $centre->director_signature) }}" alt="Signature" class="rounded border p-1 bg-white" style="height: 38px; max-width: 120px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/120x40?text=Signature';">
                            </div>
                        @endif
                        <input type="file" name="director_signature" class="form-control" accept="image/*">
                        @if($centre->director_signature)
                            <div class="small text-muted mt-1"><a href="{{ $centre->director_signature_url ?? asset('storage/' . $centre->director_signature) }}" target="_blank" class="text-decoration-none"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Full Signature</a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Controls -->
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('admin.centres.show', $centre) }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                <i class="fa-solid fa-check me-1"></i> Update Centre Details
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');

    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">Select State First</option>';
            return;
        }

        districtSelect.innerHTML = '<option value="">Loading districts...</option>';

        fetch(`/api/districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select District</option>';
                data.forEach(d => {
                    options += `<option value="${d.id}">${d.name}</option>`;
                });
                districtSelect.innerHTML = options;
            })
            .catch(() => {
                districtSelect.innerHTML = '<option value="">Failed to load districts</option>';
            });
    });
});
</script>
@endpush
@endsection
