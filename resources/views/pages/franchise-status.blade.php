@extends('layouts.app')

@section('title', 'Franchise Application Status | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Track Franchise Application Status</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Franchise</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Application Status</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Search Box -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold text-primary mb-2"><i class="fa-solid fa-hourglass-half me-2"></i>Check Application Status</h4>
                        <p class="text-muted small mb-4">Enter your Franchise Application Reference ID (e.g. <code>FR-2026-XXXX</code>) or your registered 10-digit mobile number.</p>

                        <form action="{{ route('franchise.status.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Application ID or Registered Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="search_query" class="form-control" placeholder="e.g. FR-2026-AB12CD or 9876543210" value="{{ $searchQuery ?? old('search_query') }}" required>
                            </div>
                            <button type="submit" class="btn btn-dce-primary w-100 py-2 fw-bold">
                                <i class="fa-solid fa-search me-1"></i> Track Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Result Display -->
                @if(isset($searched))
                    <div class="col-lg-8">
                        @if($application)
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                                <div class="bg-primary text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h4 class="fw-bold mb-0 text-white">Application Reference: {{ $application->application_no }}</h4>
                                        <small class="text-white-50">Submitted on: {{ $application->created_at->format('d M, Y') }}</small>
                                    </div>
                                    @php
                                        $badgeClass = match($application->status) {
                                            'approved' => 'bg-success',
                                            'reviewed' => 'bg-info text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-3 py-2 fw-bold text-uppercase fs-6">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3 small">
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Applicant Name:</span>
                                            <strong class="fs-6 text-dark">{{ $application->applicant_name }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Proposed Centre Name:</span>
                                            <strong class="fs-6 text-dark">{{ $application->centre_name }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Location:</span>
                                            <strong>{{ $application->city }}, {{ $application->district->name ?? '' }}, {{ $application->state->name ?? '' }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Contact Mobile:</span>
                                            <strong>{{ $application->mobile }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Lab Capacity:</span>
                                            <strong>{{ $application->computers_count }} Computer Systems ({{ $application->carpet_area_sqft }} sq. ft.)</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Director Qualification:</span>
                                            <strong>{{ $application->qualification }}</strong>
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded-3 border">
                                        <strong class="d-block text-primary"><i class="fa-solid fa-circle-info me-1"></i> Board Review Note:</strong>
                                        @if($application->status == 'approved')
                                            <p class="text-success mb-0 mt-1">
                                                <strong>Congratulations!</strong> Your franchise application has been approved by the board. Please contact the Head Office at {{ $instituteSettings['contact_phone'] ?? '9162384660' }} for your Centre Code allocation and Affiliation Certificate dispatch.
                                            </p>
                                        @elseif($application->status == 'rejected')
                                            <p class="text-danger mb-0 mt-1">
                                                Your application could not be approved at this time. Remarks: {{ $application->remarks ?? 'Does not meet minimum space/system criteria.' }}
                                            </p>
                                        @else
                                            <p class="text-muted mb-0 mt-1">
                                                Your application is currently <strong>Under Review</strong> by the central affiliation committee. The standard processing window is 24 to 48 business hours. You will receive an automated WhatsApp confirmation upon approval.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger shadow-sm rounded-4 p-4 text-center">
                                <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                <h4 class="fw-bold text-danger">No Application Record Found</h4>
                                <p class="text-muted mb-3">No application was found matching: <code>{{ $searchQuery }}</code>.</p>
                                <a href="{{ route('franchise.apply') }}" class="btn btn-outline-primary">
                                    Submit a New Franchise Application
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
