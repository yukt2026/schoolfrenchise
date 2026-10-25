@extends('layouts.app')

@section('title', 'Complete Payment - Order #' . $order['order_id'] . ' - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="dce-page-banner">
    <div class="container">
        <h1>Authorize Payment</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment.index') }}">Fee Payment</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gateway Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <div class="mb-2"><i class="fa-solid fa-lock fs-1 text-white"></i></div>
                        <h4 class="fw-bold mb-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} Payment Gateway</h4>
                        <div class="font-monospace small text-white-50">ORDER ID: {{ $order['order_id'] }}</div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if($order['mode'] === 'sandbox')
                            <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-center gap-2 small">
                                <i class="fa-solid fa-flask-vial fs-4 text-warning"></i>
                                <div>
                                    <strong>Sandbox Mode Active:</strong> You can simulate authoritative gateway callbacks below without real card charges.
                                </div>
                            </div>
                        @endif

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Payer Name:</span>
                                <span class="fw-semibold text-dark">{{ $transaction->payer_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Mobile:</span>
                                <span class="fw-semibold text-dark">{{ $transaction->payer_mobile }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Payment Purpose:</span>
                                <span class="fw-semibold text-primary">{{ $transaction->purpose }}</span>
                            </div>
                            @if($transaction->student_roll_no)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Student Roll No:</span>
                                    <span class="font-monospace fw-bold text-dark">{{ $transaction->student_roll_no }}</span>
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Amount to Authorize:</span>
                                <span class="fw-bold fs-4 text-success">₹{{ number_format($order['amount'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Sandbox Direct Action Form -->
                        @php
                            $testPaymentId = 'pay_' . strtolower(\Illuminate\Support\Str::random(14));
                            $validSignature = \App\Services\PaymentGatewayService::generateSignature($order['order_id'], $testPaymentId);
                        @endphp

                        <form action="{{ route('payment.verify') }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order['order_id'] }}">
                            <input type="hidden" name="gateway_payment_id" value="{{ $testPaymentId }}">
                            <input type="hidden" name="signature" value="{{ $validSignature }}">

                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-6 rounded-2 shadow-sm">
                                <i class="fa-solid fa-circle-check me-2"></i> Confirm & Authorize Payment (₹{{ number_format($order['amount'], 2) }})
                            </button>
                        </form>

                        <!-- Failure Simulation -->
                        <form action="{{ route('payment.verify') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order['order_id'] }}">
                            <input type="hidden" name="gateway_payment_id" value="{{ $testPaymentId }}">
                            <input type="hidden" name="signature" value="INVALID_SIGNATURE_TAMPER_TEST">

                            <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-2 small">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Simulate Signature Mismatch (Security Test)
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('payment.index') }}" class="text-muted small text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-1"></i> Cancel & Return to Payment Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
