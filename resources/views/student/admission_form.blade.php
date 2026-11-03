@extends('layouts.student')

@section('title', 'My Admission Form - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-file-contract text-primary me-2"></i> Official Admission Form
            </h4>
            <div class="text-muted small">STUDENT ADMISSION ENROLMENT FORM &bull; Verified enrolment dossier</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-2 px-4 btn-sm fw-bold">
                <i class="fa-solid fa-print me-1"></i> Print / Download PDF
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- EMBEDDED ADMISSION FORM TEMPLATE -->
    @include('partials.documents.admission_form_template', [
        'student'   => $student,
        'admission' => $admission ?? $student->admissions->first(),
        'centre'    => $student->centre,
        'course'    => $admission?->course ?? $student->course,
        'batch'     => $admission?->batch,
    ])
</div>
@endsection
