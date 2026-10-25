@extends('layouts.app')

@section('title', 'Admission Application Status | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="dce-page-banner">
    <div class="container">
        <h1>Admission Application Status</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admission.apply') }}">Apply Online</a></li>
                <li class="breadcrumb-item active" aria-current="page">Track Status</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                    <h4 class="fw-bold text-dark mb-2">Track Your Admission Application</h4>
                    <p class="text-muted small mb-4">Enter your Application Number (e.g., APP-2026-XXXX) or registered Mobile Number.</p>

                    <form action="{{ route('admission.status.submit') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="application_no" class="form-control border-start-0 font-monospace" 
                                   placeholder="Enter Application No. or Mobile No." 
                                   value="{{ request('application_no') }}" required>
                            <button class="btn btn-dce-accent px-4 fw-semibold" type="submit">
                                Check Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(request()->filled('application_no'))
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if($application)
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                            <!-- Status Header -->
                            <div class="p-4 text-white text-center 
                                @if($application->status === 'approved') bg-success 
                                @elseif($application->status === 'rejected') bg-danger 
                                @else bg-warning text-dark @endif">
                                
                                <div class="mb-2">
                                    @if($application->status === 'approved')
                                        <i class="fa-solid fa-circle-check fa-3x"></i>
                                    @elseif($application->status === 'rejected')
                                        <i class="fa-solid fa-circle-xmark fa-3x"></i>
                                    @else
                                        <i class="fa-solid fa-clock-rotate-left fa-3x"></i>
                                    @endif
                                </div>
                                <h3 class="fw-bold mb-1">
                                    @if($application->status === 'approved')
                                        Application Approved & Registered!
                                    @elseif($application->status === 'rejected')
                                        Application Rejected
                                    @else
                                        Application Under Scrutiny
                                    @endif
                                </h3>
                                <div class="opacity-75 font-monospace">APPLICATION NO: {{ $application->application_no }}</div>
                            </div>

                            <div class="card-body p-4 p-md-5">
                                @if($application->status === 'approved')
                                    <div class="alert alert-success border-0 rounded-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-check fs-4"></i>
                                            <div>
                                                <strong>Congratulations!</strong> Your admission application has been approved and registered. Your official roll number and registration details have been issued.
                                            </div>
                                        </div>
                                    </div>

                                    @if($application->convertedStudent)
                                        <div class="p-3 rounded-3 mb-4 bg-light border text-center">
                                            <div class="text-muted small">Assigned Roll Number</div>
                                            <h2 class="fw-bold text-primary font-monospace mb-1">{{ $application->convertedStudent->roll_no }}</h2>
                                            <div class="font-monospace text-secondary small">REG NO: {{ $application->convertedStudent->registration_no }}</div>
                                            <div class="mt-3">
                                                <a href="{{ route('student.login') }}" class="btn btn-primary rounded-2 px-4">
                                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Login to Student Portal
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($application->status === 'rejected')
                                    <div class="alert alert-danger border-0 rounded-3 mb-4">
                                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Rejection Reason:</h6>
                                        <p class="mb-0">{{ $application->rejection_reason ?? 'Your application did not meet the eligibility or document verification requirements.' }}</p>
                                    </div>
                                @else
                                    <div class="alert alert-warning border-0 rounded-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-hourglass-half fs-4"></i>
                                            <div>
                                                Your application was submitted on <strong>{{ $application->created_at->format('d M, Y') }}</strong> and is currently being reviewed by the centre academic team. Roll number will be assigned upon document verification.
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Application Summary Table -->
                                <table class="table table-bordered mb-0 small">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light text-muted w-35">Applicant Full Name</th>
                                            <td class="fw-bold text-dark">{{ $application->applicant_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted">Course Applied</th>
                                            <td class="fw-semibold text-primary">{{ $application->course?->course_name }} ({{ $application->course?->course_code }})</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted">Study Centre</th>
                                            <td>
                                                <span class="fw-semibold">{{ $application->centre?->centre_name }}</span>
                                                <span class="badge bg-light text-muted ms-1">{{ $application->centre?->centre_code }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted">Mobile Number</th>
                                            <td>{{ $application->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted">Submission Date</th>
                                            <td>{{ $application->created_at->format('d M, Y h:i A') }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Acknowledgement Slip & Portal Action Bar -->
                                <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div>
                                        <a href="{{ route('admission.acknowledgement', $application->application_no) }}" target="_blank" class="btn btn-outline-primary rounded-2 px-4 shadow-sm fw-semibold">
                                            <i class="fa-solid fa-print me-1"></i> Print Acknowledgement Slip
                                        </a>
                                    </div>
                                    @if($application->status === 'approved' && $application->convertedStudent)
                                        <div>
                                            <a href="{{ route('student.login') }}" class="btn btn-primary rounded-2 px-4 shadow-sm">
                                                <i class="fa-solid fa-right-to-bracket me-1"></i> Student Portal Login
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card border-0 shadow-sm rounded-3 p-5 bg-white text-center">
                            <i class="fa-regular fa-file-circle-question fa-3x text-secondary opacity-50 mb-3"></i>
                            <h5 class="fw-bold text-dark">No Application Found</h5>
                            <p class="text-muted mb-4">We could not locate any admission application matching "<strong>{{ request('application_no') }}</strong>". Please verify the application number or mobile number.</p>
                            <div>
                                <a href="{{ route('admission.apply') }}" class="btn btn-outline-primary rounded-2 px-4">
                                    <i class="fa-solid fa-plus me-1"></i> Apply for New Admission
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
