@extends('layouts.student')

@section('title', 'Pay Course Fees - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-credit-card text-primary me-2"></i> Online Fee Payment Checkout
            </h4>
            <div class="text-muted small">Secure payment gateway integration with instantaneous digital receipt issuance</div>
        </div>
        <div>
            <a href="{{ route('student.fees') }}" class="btn btn-outline-secondary rounded-2 px-3 btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Fees
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Dues Banner -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #0b1329 0%, #0f172a 60%, #0284c7 100%);">
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <span class="badge bg-warning text-dark font-monospace mb-1 px-3 py-1 fw-bold rounded-1">
                            ROLL NO: {{ $student->roll_no ?? 'UNASSIGNED' }}
                        </span>
                        <h4 class="fw-bold text-white mb-1">{{ $admission->course?->course_name }}</h4>
                        <div class="text-white-50 small">
                            Centre: {{ $student->centre?->centre_name ?? 'Head Office' }} &bull; Session: {{ $student->academic_session ?? date('Y') }}
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <div class="text-white-50 small">Outstanding Balance</div>
                        <h2 class="fw-bold text-white mb-0">₹{{ number_format($totalDues, 2) }}</h2>
                    </div>
                </div>
            </div>

            <!-- Payment Form Card -->
            <div class="card border border-light-subtle shadow-sm rounded-3 bg-white p-4 p-md-5 mb-4">
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(!empty($isSandbox))
                    <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-center gap-3">
                        <i class="fa-solid fa-flask-vial fs-4 text-warning"></i>
                        <div class="small">
                            <strong>Sandbox Test Mode Active:</strong> Payment gateway is running in sandbox verification mode. Live merchant card will not be debited. Full cryptographic signature verification and fee ledger reconciliation will execute in real-time.
                        </div>
                    </div>
                @endif

                <form id="checkoutForm" onsubmit="return false;">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Enter Payment Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-primary">₹</span>
                            <input type="number" step="0.01" name="amount" id="amountInput" 
                                   class="form-control border-start-0 fw-bold fs-4 text-dark @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', min($suggestedAmount, $totalDues)) }}" 
                                   min="1" max="{{ $totalDues }}" required>
                        </div>
                        <div class="form-text small mt-2 d-flex justify-content-between">
                            <span>Suggested milestone amount: <strong class="text-primary">₹{{ number_format(min($suggestedAmount, $totalDues), 2) }}</strong></span>
                            <span>Max payable: <strong>₹{{ number_format($totalDues, 2) }}</strong></span>
                        </div>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Payment Mode Selection -->
                    <label class="form-label fw-bold text-dark mb-2">Select Payment Method</label>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-sm-3">
                            <label class="card p-3 border rounded-3 text-center cursor-pointer payment-option-card h-100">
                                <input type="radio" name="payment_mode" value="upi" class="form-check-input visually-hidden" checked>
                                <div class="fs-3 text-success mb-2"><i class="fa-solid fa-qrcode"></i></div>
                                <div class="fw-bold small text-dark">UPI QR / App</div>
                                <div class="text-muted" style="font-size: 0.7rem;">GPay, PhonePe, Paytm</div>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <label class="card p-3 border rounded-3 text-center cursor-pointer payment-option-card h-100">
                                <input type="radio" name="payment_mode" value="card" class="form-check-input visually-hidden">
                                <div class="fs-3 text-primary mb-2"><i class="fa-solid fa-credit-card"></i></div>
                                <div class="fw-bold small text-dark">Debit / Credit</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Visa, MC, RuPay</div>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <label class="card p-3 border rounded-3 text-center cursor-pointer payment-option-card h-100">
                                <input type="radio" name="payment_mode" value="net_banking" class="form-check-input visually-hidden">
                                <div class="fs-3 text-info mb-2"><i class="fa-solid fa-building-columns"></i></div>
                                <div class="fw-bold small text-dark">Net Banking</div>
                                <div class="text-muted" style="font-size: 0.7rem;">50+ Major Banks</div>
                            </label>
                        </div>
                        <div class="col-6 col-sm-3">
                            <label class="card p-3 border rounded-3 text-center cursor-pointer payment-option-card h-100">
                                <input type="radio" name="payment_mode" value="online" class="form-check-input visually-hidden">
                                <div class="fs-3 text-warning mb-2"><i class="fa-solid fa-wallet"></i></div>
                                <div class="fw-bold small text-dark">Wallets</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Amazon, Mobikwik</div>
                            </label>
                        </div>
                    </div>

                    <!-- Security Assurance -->
                    <div class="p-3 rounded-3 bg-light border small text-muted mb-4 d-flex align-items-center gap-3">
                        <i class="fa-solid fa-shield-halved fs-3 text-success"></i>
                        <div>
                            <strong>256-bit Encrypted Transaction:</strong> Server-side order creation and authoritative webhook reconciliation. Fees will only be credited once actual payment is cryptographically verified by the gateway.
                        </div>
                    </div>

                    <div class="text-center d-flex flex-column align-items-center gap-2">
                        <button type="button" id="btnProceedPay" class="btn btn-primary rounded-2 px-5 py-3 fw-bold fs-6 shadow w-100 w-md-auto">
                            <i class="fa-solid fa-lock me-2"></i> Pay ₹<span id="btnAmountDisplay">{{ number_format(min($suggestedAmount, $totalDues), 2) }}</span> via Razorpay
                        </button>

                        @if(!empty($isSandbox))
                        <button type="button" id="btnSimulateSandbox" class="btn btn-outline-warning btn-sm rounded-2 mt-2 px-4 py-1.5 fw-semibold">
                            <i class="fa-solid fa-bolt text-warning me-1"></i> Simulate Sandbox Payment (Testing Only)
                        </button>
                        @endif
                    </div>
                </form>

                <!-- Hidden Authoritative Razorpay Verification Form -->
                <form id="razorpayVerifyForm" method="POST" action="{{ route('student.fees.pay.verify') }}" class="d-none">
                    @csrf
                    <input type="hidden" name="order_id" id="rzp_order_id">
                    <input type="hidden" name="gateway_payment_id" id="rzp_payment_id">
                    <input type="hidden" name="gateway_signature" id="rzp_signature">
                    <input type="hidden" name="payment_mode" id="rzp_payment_mode" value="online">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-option-card {
    transition: all 0.2s ease;
    cursor: pointer;
}
.payment-option-card:hover {
    border-color: #0284c7 !important;
    background: #f0f9ff;
}
.payment-option-card:has(input:checked) {
    border-color: #0284c7 !important;
    background: #e0f2fe;
    box-shadow: 0 0 0 2px #0284c7;
}
</style>
@endpush

@push('scripts')
<!-- Official Razorpay Checkout Library -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('amountInput');
    const btnAmountDisplay = document.getElementById('btnAmountDisplay');
    const btnProceedPay = document.getElementById('btnProceedPay');
    const btnSimulateSandbox = document.getElementById('btnSimulateSandbox');
    const verifyForm = document.getElementById('razorpayVerifyForm');
    const originalBtnHtml = btnProceedPay ? btnProceedPay.innerHTML : '';

    // Update button text when amount changes
    if (amountInput && btnAmountDisplay) {
        amountInput.addEventListener('input', function () {
            const val = parseFloat(this.value) || 0;
            btnAmountDisplay.textContent = val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    }

    function getSelectedMode() {
        const checked = document.querySelector('input[name="payment_mode"]:checked');
        return checked ? checked.value : 'upi';
    }

    // 1. Proceed with real Razorpay Checkout
    if (btnProceedPay) {
        btnProceedPay.addEventListener('click', async function () {
            const amount = parseFloat(amountInput.value);
            const maxDues = parseFloat("{{ $totalDues }}");

            if (isNaN(amount) || amount < 1) {
                alert('Please enter a valid payment amount of at least ₹1.00');
                amountInput.focus();
                return;
            }

            if (amount > maxDues) {
                alert('Amount cannot exceed current outstanding course balance (₹' + maxDues.toFixed(2) + ').');
                amountInput.focus();
                return;
            }

            const selectedMode = getSelectedMode();

            // Set loading state
            btnProceedPay.disabled = true;
            btnProceedPay.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Connecting to Payment Gateway...';

            try {
                // Step 1: Create server-side order
                const response = await fetch("{{ route('student.fees.pay.initiate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount,
                        payment_mode: selectedMode
                    })
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.message || errData.error || 'Failed to initiate order with server.');
                }

                const orderData = await response.json();

                if (!orderData.order_id) {
                    throw new Error('Server did not return a valid order ID.');
                }

                // Step 2: Open Razorpay checkout modal
                if (typeof Razorpay !== 'undefined') {
                    const rzpOptions = {
                        key: orderData.key_id,
                        amount: orderData.amount_paise,
                        currency: orderData.currency || 'INR',
                        name: "{{ addslashes($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }}",
                        description: "Course Tuition Fee - {{ addslashes($admission->course?->course_name ?? 'Course') }}",
                        image: "{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}",
                        order_id: orderData.order_id,
                        prefill: {
                            name: "{{ addslashes($student->full_name) }}",
                            contact: "{{ $student->mobile }}",
                            email: "{{ $student->email ?? '' }}"
                        },
                        notes: {
                            student_id: "{{ $student->id }}",
                            roll_no: "{{ $student->roll_no }}"
                        },
                        theme: {
                            color: "#0284c7"
                        },
                        handler: function (rzpResp) {
                            // Step 3: Success callback - submit for server-side cryptographic signature verification
                            btnProceedPay.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying Gateway Signature...';
                            document.getElementById('rzp_order_id').value = rzpResp.razorpay_order_id || orderData.order_id;
                            document.getElementById('rzp_payment_id').value = rzpResp.razorpay_payment_id;
                            document.getElementById('rzp_signature').value = rzpResp.razorpay_signature;
                            document.getElementById('rzp_payment_mode').value = selectedMode;
                            verifyForm.submit();
                        },
                        modal: {
                            ondismiss: function () {
                                // DO NOT SUBMIT FORM. Payment was not made!
                                btnProceedPay.disabled = false;
                                btnProceedPay.innerHTML = originalBtnHtml;
                            }
                        }
                    };

                    const rzpInstance = new Razorpay(rzpOptions);
                    rzpInstance.on('payment.failed', function (failResp) {
                        alert('Payment failed: ' + (failResp.error.description || 'Transaction was declined by bank/gateway.'));
                        btnProceedPay.disabled = false;
                        btnProceedPay.innerHTML = originalBtnHtml;
                    });
                    rzpInstance.open();
                } else {
                    alert('Razorpay Checkout library could not be loaded. Please ensure you have an active internet connection.');
                    btnProceedPay.disabled = false;
                    btnProceedPay.innerHTML = originalBtnHtml;
                }
            } catch (err) {
                console.error('Payment Error:', err);
                alert(err.message || 'An error occurred while contacting payment gateway.');
                btnProceedPay.disabled = false;
                btnProceedPay.innerHTML = originalBtnHtml;
            }
        });
    }

    // 2. Sandbox simulation button (Only visible in test/sandbox mode)
    if (btnSimulateSandbox) {
        btnSimulateSandbox.addEventListener('click', async function () {
            const amount = parseFloat(amountInput.value);
            const maxDues = parseFloat("{{ $totalDues }}");

            if (isNaN(amount) || amount < 1 || amount > maxDues) {
                alert('Please enter a valid payment amount between ₹1.00 and ₹' + maxDues.toFixed(2));
                return;
            }

            btnSimulateSandbox.disabled = true;
            btnSimulateSandbox.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Simulating Sandbox Payment...';

            try {
                const response = await fetch("{{ route('student.fees.pay.initiate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount,
                        payment_mode: getSelectedMode()
                    })
                });

                const orderData = await response.json();
                if (!orderData.order_id) {
                    throw new Error('Could not initiate sandbox order.');
                }

                document.getElementById('rzp_order_id').value = orderData.order_id;
                document.getElementById('rzp_payment_id').value = 'pay_sim_' + Math.random().toString(36).substring(2, 12);
                document.getElementById('rzp_signature').value = '';
                document.getElementById('rzp_payment_mode').value = getSelectedMode();
                verifyForm.submit();
            } catch (e) {
                alert('Simulation error: ' + e.message);
                btnSimulateSandbox.disabled = false;
                btnSimulateSandbox.innerHTML = '<i class="fa-solid fa-bolt text-warning me-1"></i> Simulate Sandbox Payment (Testing Only)';
            }
        });
    }
});
</script>
@endpush
