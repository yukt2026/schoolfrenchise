@extends('layouts.student')

@section('title', 'Payment Receipt - ' . $payment->receipt_no . ' - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-receipt text-success me-2"></i> Computerized Fee Receipt
            </h4>
            <div class="text-muted small">Official tax & fee payment confirmation issued by {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-2 px-4 btn-sm fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Print / Download PDF
            </button>
            <a href="{{ route('student.fees.history') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to History
            </a>
        </div>
    </div>

    <!-- Official Printable Receipt Container -->
    <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-3 p-md-5 mx-auto receipt-card" style="max-width: 800px;">
        <!-- Header -->
        <div class="border-bottom pb-4 mb-4 text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 50px; width: auto; object-fit: contain;">
                <div class="text-start">
                    <h3 class="fw-bold text-primary mb-0" style="letter-spacing: 0.5px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h3>
                    <div class="small text-muted fw-semibold">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                </div>
            </div>
            <div class="text-secondary small">
                {{ $payment->centre?->centre_name ?? ($instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105') }} &bull;
                Ph: {{ $payment->centre?->contact_number ?? ($instituteSettings['contact_phone'] ?? '9162384660') }}
            </div>
            <div class="mt-3">
                <span class="badge bg-success-subtle text-success px-3 py-1 font-monospace fw-bold" style="font-size: 0.85rem;">
                    OFFICIAL FEE PAYMENT RECEIPT
                </span>
            </div>
        </div>

        <!-- Receipt Meta Row -->
        <div class="row g-3 mb-4 p-3 rounded-3" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
            <div class="col-sm-6">
                <div class="text-muted small">Receipt Number:</div>
                <div class="fw-bold font-monospace text-primary fs-5">{{ $payment->receipt_no }}</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <div class="text-muted small">Payment Date:</div>
                <div class="fw-bold text-dark fs-6">{{ $payment->payment_date ? $payment->payment_date->format('d F Y') : $payment->created_at->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Student Particulars -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Student Particulars</h6>
        <div class="row g-2 mb-4 small">
            <div class="col-md-6">
                <span class="text-muted">Student Name:</span>
                <div class="fw-bold text-dark fs-6">{{ $payment->student?->full_name }}</div>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Father's Name:</span>
                <div class="fw-semibold text-dark">{{ $payment->student?->father_name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Roll Number:</span>
                <div class="fw-bold font-monospace text-primary">{{ $payment->student?->roll_no ?? 'Provisional' }}</div>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Registration Number:</span>
                <div class="fw-bold font-monospace text-dark">{{ $payment->student?->registration_no }}</div>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Enrolled Course:</span>
                <div class="fw-semibold text-dark">{{ $payment->admission?->course?->course_name }}</div>
            </div>
            <div class="col-md-6">
                <span class="text-muted">Payment Mode & Ref:</span>
                <div class="fw-semibold text-dark text-uppercase">
                    {{ str_replace('_', ' ', $payment->payment_mode) }} 
                    @if($payment->transaction_ref) ({{ $payment->transaction_ref }}) @endif
                </div>
            </div>
        </div>

        <!-- Payment Breakdown Table -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Transaction Details</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Item Description</th>
                        <th class="text-end">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">Course Fee Installment Payment</div>
                            <div class="text-muted small">{{ $payment->notes ?? 'Fee credit towards ongoing academic session' }}</div>
                        </td>
                        <td class="text-end fw-bold text-success fs-6">₹{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <th class="text-end">Total Amount Received:</th>
                        <th class="text-end text-success fs-5">₹{{ number_format($payment->amount, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Verification QR Code & Signatures -->
        <div class="row align-items-center g-3 pt-3 border-top mt-4">
            <div class="col-sm-4 text-center text-sm-start">
                <div class="border d-inline-block p-2 bg-light rounded text-center">
                    <i class="fa-solid fa-qrcode fa-3x text-dark"></i>
                    <div class="font-monospace text-muted mt-1" style="font-size: 0.65rem;">
                        VERIFIED SECURE
                    </div>
                </div>
            </div>
            <div class="col-sm-8 text-center text-sm-end">
                <div class="d-inline-block text-center mt-3 mt-sm-0" style="min-width: 180px;">
                    <div class="border-top border-dark pt-1 small fw-bold">Authorized Signatory / Seal</div>
                    <div class="text-muted" style="font-size: 0.72rem;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body { background: #ffffff !important; }
    .receipt-card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
}
</style>
@endpush
