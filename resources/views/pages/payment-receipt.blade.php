@extends('layouts.app')

@section('title', 'Official Payment Receipt #' . $transaction->transaction_id . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Official Payment Receipt</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('payment.index') }}">Online Payment</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Receipt</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" id="printableReceipt">
                        <!-- Receipt Top Header -->
                        <div class="bg-primary text-white p-4 p-md-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">Transaction Confirmed</span>
                                <h2 class="fw-bold mb-0 text-white">Payment Receipt</h2>
                                <small class="text-white-50">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} &bull; {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</small>
                            </div>
                            <div class="text-end">
                                <h1 class="display-6 fw-extrabold mb-0 text-white">₹{{ number_format($transaction->amount, 2) }}</h1>
                                <span class="badge bg-success-subtle text-success px-3 py-1"><i class="fa-solid fa-check me-1"></i> Paid Successfully</span>
                            </div>
                        </div>

                        <!-- Receipt Body Details -->
                        <div class="p-4 p-md-5 bg-white">
                            <div class="row g-4 mb-4 pb-4 border-bottom">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Receipt Number:</span>
                                    <strong class="fs-5 text-dark">{{ $transaction->transaction_id }}</strong>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <span class="text-muted d-block small">Date & Time:</span>
                                    <strong class="text-dark">{{ $transaction->created_at->format('d M, Y - h:i A') }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Order Reference ID:</span>
                                    <code>{{ $transaction->order_id }}</code>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <span class="text-muted d-block small">Payment Gateway Ref:</span>
                                    <code>{{ $transaction->gateway_payment_id }}</code>
                                </div>
                            </div>

                            <!-- Payer & Student Info -->
                            <div class="row g-3 mb-4 small align-items-center">
                                <div class="col-sm-8">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Payer Name:</span>
                                            <strong class="fs-6 text-dark">{{ $transaction->payer_name }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Payer Mobile:</span>
                                            <strong>{{ $transaction->payer_mobile }}</strong>
                                        </div>
                                        @if($transaction->student_roll_no)
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Student Roll No:</span>
                                            <code>{{ $transaction->student_roll_no }}</code>
                                        </div>
                                        @endif
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Payment Purpose:</span>
                                            <strong class="text-primary">{{ $transaction->purpose }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Payment Mode:</span>
                                            <span class="badge bg-secondary">{{ $transaction->payment_method }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Digital Verification Signature:</span>
                                            <code class="small text-truncate d-block" style="max-width: 250px;">{{ $transaction->gateway_signature }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">
                                    <div class="d-inline-block p-2 bg-light rounded-3 border">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode(route('payment.receipt', $transaction->transaction_id)) }}" alt="QR Verification" style="width: 80px; height: 80px;">
                                        <div class="text-muted font-monospace mt-1" style="font-size: 0.65rem;">SCAN TO VERIFY</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-house me-1"></i> Return Home
                                </a>
                                <div class="d-flex gap-2">
                                    <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
                                        <i class="fa-solid fa-print me-1"></i> Print Official Receipt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
@media print {
    header, footer, nav, .dce-page-banner, .btn, .breadcrumb, a {
        display: none !important;
    }
    body, section {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
    }
    .card {
        border: 2px solid #333 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    #printableReceipt {
        margin: 0 auto !important;
        page-break-inside: avoid;
    }
}
</style>
@endpush
