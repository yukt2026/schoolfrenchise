@extends('layouts.student')

@section('title', 'Digital ID Card - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-id-card text-primary me-2"></i> Student Identity Card
            </h4>
            <div class="text-muted small">Official digital student identity credential issued by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-2 px-4 btn-sm fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Print / Save ID Card
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Dual-Sided ID Card Container -->
    <div class="d-flex flex-wrap justify-content-center gap-4 py-3">
        <!-- FRONT SIDE -->
        <div class="id-card-wrap shadow-lg rounded-3 overflow-hidden bg-white position-relative" style="width: 340px; max-width: 100%; height: 530px; border: 2px solid #0284c7;">
            <!-- Header -->
            <div class="text-center py-2 px-2 text-white" style="background: linear-gradient(135deg, #0a192f, #0284c7);">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <img src="{{ $student->centre?->centre_logo_url ?? ($instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png')) }}" alt="{{ $student->centre?->centre_name }}" class="rounded bg-white p-1" style="height: 30px; width: auto; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                    <div class="fw-bold fs-6 lh-1" style="letter-spacing: 0.5px;">{{ $student->centre?->centre_name ?? ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }}</div>
                </div>
                <div class="text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #fbbf24;">AUTHORIZED BY {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                <div class="text-white-50" style="font-size: 0.58rem;">Centre Code: {{ $student->centre?->centre_code ?? 'MAIN' }} &bull; Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</div>
            </div>

            <!-- Photo & Name -->
            <div class="text-center pt-3 pb-2 px-3">
                <div class="rounded-circle border border-3 border-primary overflow-hidden mx-auto mb-2 bg-light shadow-sm" style="width: 100px; height: 100px;">
                    @if($student->photo_url)
                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.style.display='none';">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                            <i class="fa-solid fa-user fa-3x opacity-50"></i>
                        </div>
                    @endif
                </div>
                <h5 class="fw-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.5px;">{{ $student->full_name }}</h5>
                <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 mt-1 fw-bold" style="font-size: 0.72rem;">
                    ROLL NO: {{ $student->roll_no ?? 'PROVISIONAL' }}
                </span>
                <div class="text-center mt-1">
                    <div style="font-family: 'Courier New', monospace; font-weight: 900; font-size: 1rem; letter-spacing: 2px; line-height: 1; color: #0f172a;">
                        ||| | |||| | ||| |||| |
                    </div>
                    <span class="font-monospace text-muted" style="font-size: 0.55rem; letter-spacing: 1px;">BARCODE: {{ $student->roll_no }}</span>
                </div>
            </div>

            <!-- Details Table -->
            <div class="px-4 py-2 small">
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-0 py-1" style="width: 38%;">Reg. No:</td>
                            <td class="fw-bold font-monospace py-1">{{ $student->registration_no }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 py-1">Course:</td>
                            <td class="fw-semibold text-primary py-1">{{ Str::limit($admission?->course?->course_name ?? 'General IT', 22) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 py-1">Batch:</td>
                            <td class="fw-semibold py-1">{{ $admission?->batch?->batch_name ?? 'Regular Shift' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 py-1">Father's:</td>
                            <td class="fw-semibold py-1">{{ Str::limit($student->father_name ?? 'N/A', 22) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 py-1">Contact:</td>
                            <td class="fw-semibold py-1">{{ $student->phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 py-1">Session:</td>
                            <td class="fw-semibold py-1">{{ $student->academic_session ?? date('Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Strip -->
            <div class="position-absolute bottom-0 start-0 end-0 bg-light border-top p-2 text-center" style="font-size: 0.68rem;">
                <div class="fw-bold text-dark">{{ $student->centre?->centre_name ?? ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }}</div>
                <div class="text-muted">{{ $student->centre?->contact_number ?? ($instituteSettings['contact_phone'] ?? '9162384660') }}</div>
            </div>
        </div>

        <!-- BACK SIDE -->
        <div class="id-card-wrap shadow-lg rounded-3 overflow-hidden bg-white position-relative" style="width: 340px; max-width: 100%; height: 530px; border: 2px solid #0284c7;">
            <!-- Header -->
            <div class="text-center py-2 px-2 text-white" style="background: #0a192f;">
                <div class="fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">TERMS & CONDITIONS</div>
            </div>

            <!-- Terms & Instructions -->
            <div class="p-3" style="font-size: 0.72rem; line-height: 1.4; color: #475569;">
                <ol class="ps-3 mb-3">
                    <li class="mb-1">This identity card is strictly non-transferable and remains institute property.</li>
                    <li class="mb-1">The student must carry and produce this card upon entering institute premises, computer labs, and examinations.</li>
                    <li class="mb-1">Loss of card must be reported immediately to the Centre Incharge. Replacement charges apply.</li>
                    <li class="mb-1">Misuse of this identity card may lead to disciplinary action or suspension of registration.</li>
                </ol>

                <div class="border-top pt-2 mt-2">
                    <div class="text-muted">Permanent Address:</div>
                    <div class="fw-semibold text-dark mt-1">
                        {{ $student->address ?? 'N/A' }},
                        {{ $student->district?->name ?? '' }}, {{ $student->state?->name ?? '' }} - {{ $student->pincode ?? '' }}
                    </div>
                </div>

                <!-- Verification QR Code -->
                <div class="text-center my-2">
                    @php
                        $verifyUrl = route('verify.admission') . '?search_key=' . urlencode($student->registration_no);
                    @endphp
                    <div class="border d-inline-block p-1 bg-white rounded shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode($verifyUrl) }}" alt="QR Code" style="width: 75px; height: 75px;">
                    </div>
                    <div class="font-monospace text-muted mt-1" style="font-size: 0.65rem;">
                        SCAN TO VERIFY CREDENTIALS
                    </div>
                    <div class="text-secondary font-monospace" style="font-size: 0.6rem;">
                        REG: {{ $student->registration_no }}
                    </div>
                </div>
            </div>

            <!-- Back Footer Signatures -->
            <div class="position-absolute bottom-0 start-0 end-0 bg-light border-top p-2">
                <div class="d-flex justify-content-between align-items-end text-center" style="font-size: 0.68rem;">
                    <div>
                        @if($student->signature_url)
                            <img src="{{ $student->signature_url }}" alt="Sign" style="height: 20px; max-width: 75px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                        @endif
                        <div class="border-top border-dark pt-1 fw-bold">Student Sign</div>
                    </div>
                    <div>
                        @if(!empty($student->centre?->director_signature_url))
                            <img src="{{ $student->centre->director_signature_url }}" alt="Director" style="height: 20px; max-width: 75px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                        @elseif(!empty($instituteSettings['seal_url']))
                            <img src="{{ $instituteSettings['seal_url'] }}" alt="Seal" style="height: 22px; max-width: 50px; object-fit: contain; display: block; margin: 0 auto -2px auto;">
                        @endif
                        <div class="border-top border-dark pt-1 fw-bold">Director / Issuing Authority</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@page {
    size: auto;
    margin: 8mm;
}
@media print {
    .student-header,
    .student-subnav,
    .mobile-bottom-nav,
    .no-print,
    nav,
    footer {
        display: none !important;
    }
    body, .student-main-content {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .id-card-wrap {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
    }
}
</style>
@endpush
