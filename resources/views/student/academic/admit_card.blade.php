@extends('layouts.student')

@section('title', 'Examination Hall Ticket - ' . ($student->full_name ?? 'Student'))

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.exams') }}" class="text-decoration-none">Exams</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hall Ticket</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Official Examination Hall Ticket
        </h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('student.exams') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Exams
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-2 fw-semibold">
            <i class="fa-solid fa-print me-1"></i> Print Hall Ticket
        </button>
    </div>
</div>

<!-- PRINTABLE ADMIT CARD CONTAINER -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 print-card-container bg-white">
    <div class="card-body p-3 p-md-5">
        <!-- Admit Card Border Wrapper -->
        <div class="border border-2 border-dark rounded-2 p-3 p-md-4 position-relative" style="background: #ffffff;">
            <!-- Watermark / Security Crest -->
            <div class="position-absolute top-50 start-50 translate-middle opacity-10 pointer-events-none" style="z-index: 0;">
                <i class="fa-solid fa-graduation-cap" style="font-size: 14rem; color: #0284c7;"></i>
            </div>

            <div class="position-relative" style="z-index: 1;">
                <!-- Header Banner -->
                <div class="text-center border-bottom border-2 border-dark pb-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 52px; width: auto; object-fit: contain;">
                        <div class="text-start">
                            <h2 class="h4 fw-bolder mb-0 text-dark text-uppercase tracking-wider" style="color: #0284c7 !important;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                            <p class="small text-muted mb-0">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }} &bull; {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</p>
                        </div>
                    </div>
                    <div class="d-inline-block bg-dark text-white px-3 py-1 rounded-1 small fw-bold text-uppercase tracking-widest mt-1">
                        EXAMINATION ADMIT CARD / HALL TICKET
                    </div>
                </div>

                <!-- Card Meta Bar -->
                <div class="row g-2 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Admit Card No</div>
                        <div class="fw-bold font-monospace text-primary">
                            {{ $admitCard?->admit_card_no ?? ('AC-' . str_pad($exam->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT)) }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Roll Number</div>
                        <div class="fw-bold font-monospace text-dark">{{ $student->roll_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Registration No</div>
                        <div class="fw-bold font-monospace text-dark">{{ $student->registration_no }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">Exam Code</div>
                        <div class="fw-bold font-monospace text-dark">{{ $exam->exam_code ?? 'EXAM-' . $exam->id }}</div>
                    </div>
                </div>

                <!-- Student & Exam Details Grid -->
                <div class="row g-4 mb-4">
                    <!-- Left: Student Info -->
                    <div class="col-12 col-md-8">
                        <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-user me-2 text-primary"></i>Candidate Particulars
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0" style="width: 35%;">Candidate's Name:</th>
                                    <td class="fw-bold text-dark">{{ $student->full_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Father's Name:</th>
                                    <td class="text-dark">{{ $student->father_name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Date of Birth / Gender:</th>
                                    <td class="text-dark">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : 'N/A' }} ({{ ucfirst($student->gender ?? 'N/A') }})</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Enrolled Course:</th>
                                    <td class="fw-bold text-primary">{{ $exam?->course?->course_name ?? ($admission?->course?->course_name ?? 'Computer Course') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-semibold ps-0">Authorized Centre:</th>
                                    <td class="text-dark">{{ $student->centre?->centre_name ?? 'Main Campus' }} ({{ $student->centre?->centre_code ?? 'DCE' }})</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right: Photo & Signature Box -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="d-inline-block border border-2 border-secondary p-1 bg-white rounded-2 mb-2" style="width: 120px; height: 140px;">
                            @if($student->photo_path)
                                <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover rounded-1">
                            @else
                                <div class="w-100 h-100 bg-light d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-user fs-1"></i>
                                    <span style="font-size: 0.65rem;" class="mt-1">Candidate Photo</span>
                                </div>
                            @endif
                        </div>
                        <div class="small text-muted font-monospace border-top pt-1 mx-auto" style="width: 140px; font-size: 0.7rem;">
                            Candidate Photo
                        </div>
                    </div>
                </div>

                <!-- Examination Schedule Table -->
                <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-clock me-2 text-primary"></i>Examination Schedule & Venue
                </h6>
                <div class="table-scroll-hint d-md-none">
                    <i class="fa-solid fa-arrows-left-right text-primary"></i>
                    <span>Scroll horizontally to view exam schedule</span>
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle text-center mb-0" style="min-width: 540px;">
                        <thead class="table-light">
                            <tr>
                                <th>Exam Title</th>
                                <th>Exam Date</th>
                                <th>Reporting Time</th>
                                <th>Duration</th>
                                <th>Maximum Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-start ps-3">{{ $exam->title ?? $exam->exam_title }}</td>
                                <td class="fw-semibold text-primary">{{ \Carbon\Carbon::parse($exam->exam_date)->format('d-m-Y') }}</td>
                                <td>{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('h:i A') : '09:30 AM' }}</td>
                                <td>{{ $exam->duration_minutes ?? 90 }} Minutes</td>
                                <td>{{ $exam->total_marks ?? 100 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Exam Venue -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="small fw-bold text-uppercase text-muted mb-1" style="font-size: 0.72rem;">Examination Venue / Lab Location</div>
                    <div class="fw-bold text-dark">
                        {{ $admitCard?->exam_venue ?? (($student->centre?->centre_name ?? 'Main Campus') . ', ' . ($student->centre?->address ?? ($student->address ?? 'Campus')) . ', ' . ($student->city ?? 'Central')) }}
                    </div>
                </div>

                <!-- Important Instructions -->
                <div class="border rounded-3 p-3 mb-4 bg-white">
                    <div class="fw-bold text-dark small text-uppercase mb-2" style="font-size: 0.75rem;">Instructions to Candidates</div>
                    <ol class="small text-muted ps-3 mb-0" style="font-size: 0.78rem; line-height: 1.5;">
                        <li>This Admit Card must be preserved safely and presented at the examination centre along with an authorized photo ID.</li>
                        <li>Candidates must report at the examination centre at least 15 minutes before the reporting time.</li>
                        <li>Calculators, mobile phones, smartwatches, and study notes are strictly prohibited inside the lab.</li>
                        <li>Any malpractice or impersonation will lead to immediate cancellation of candidature and legal action.</li>
                    </ol>
                </div>

                <!-- Signatures Row -->
                <div class="row pt-4 mt-3 text-center border-top">
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 140px; height: 40px;"></div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Candidate Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1 d-flex align-items-center justify-content-center text-muted" style="width: 140px; height: 40px;">
                            <span class="badge border text-muted px-2 py-1" style="font-size: 0.65rem;">CENTRE SEAL</span>
                        </div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Centre Superintendent</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 140px; height: 40px;"></div>
                        <div class="small fw-semibold text-dark" style="font-size: 0.75rem;">Controller of Examinations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .student-header,
    .student-subnav,
    .mobile-bottom-nav,
    .no-print,
    footer {
        display: none !important;
    }
    body, .student-main-content {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .print-card-container {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
