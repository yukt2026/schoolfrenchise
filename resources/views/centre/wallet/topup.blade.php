@extends('layouts.centre')

@section('title', 'Recharge Franchise Wallet - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<style>
.recharge-method-nav .nav-link {
    color: #4b5563;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease-in-out;
}
.recharge-method-nav .nav-link:hover {
    background: #f3f4f6;
    color: #111827;
}
.recharge-method-nav .nav-link.active {
    background: #ffffff;
    color: #0f172a;
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.12);
}
.recharge-method-nav .nav-link.active .method-icon-wrap {
    background: #0d6efd;
    color: #ffffff !important;
}
.amount-chip {
    cursor: pointer;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 16px;
    background: #ffffff;
    font-weight: 700;
    color: #334155;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    user-select: none;
}
.amount-chip:hover {
    border-color: #10b981;
    background: #f0fdf4;
    color: #065f46;
    transform: translateY(-1px);
}
.amount-chip.active {
    border-color: #10b981;
    background: #ecfdf5;
    color: #047857;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}
.pay-btn-glow {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    border: none;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    transition: all 0.2s ease;
}
.pay-btn-glow:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
    transform: translateY(-1px);
}
.summary-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
}
</style>

<!-- Top Page Header -->
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <a href="{{ route('centre.wallet.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2 px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Wallet Ledger
            </a>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark p-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-wallet"></i>
                </span>
                Recharge Franchise Wallet
            </h3>
            <p class="text-muted small mb-0">Select your preferred recharge mode: instant automated gateway or manual UPI QR / Bank transfer.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-3">
                <i class="fa-solid fa-building-columns text-primary me-1"></i> {{ $centre->centre_code }} &bull; {{ $centre->centre_name }}
            </span>
            <span class="badge bg-primary text-white px-3 py-2 shadow-sm rounded-3 font-monospace">
                Balance: ₹{{ number_format($wallet?->balance ?? 0, 2) }}
            </span>
        </div>
    </div>
</div>

<!-- Notifications / Alerts -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check text-success me-2 fs-5 align-middle"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-exclamation text-danger me-2 fs-5 align-middle"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5 align-middle"></i><strong>Please correct the following:</strong>
        <ul class="mb-0 mt-1 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Payment Method Tabs Nav -->
<ul class="nav nav-pills recharge-method-nav p-1 bg-white rounded-4 shadow-sm mb-4 border g-2" id="rechargeMethodTabs" role="tablist">
    @if(!empty($razorpayEnabled))
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link active p-3 w-100 rounded-3 text-start d-flex align-items-center gap-3" id="tab-razorpay" data-bs-toggle="pill" data-bs-target="#pane-razorpay" type="button" role="tab" aria-selected="true">
            <div class="method-icon-wrap p-2 rounded-3 bg-success-subtle text-success fs-5">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark">Instant Online Recharge (Razorpay)</span>
                    <span class="badge bg-success text-white small px-2 py-1">Instant 0-Sec</span>
                </div>
                <div class="small text-muted">UPI, Cards, NetBanking &bull; Automatically added to wallet</div>
            </div>
        </button>
    </li>
    @endif

    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link {{ empty($razorpayEnabled) ? 'active' : '' }} p-3 w-100 rounded-3 text-start d-flex align-items-center gap-3" id="tab-offline" data-bs-toggle="pill" data-bs-target="#pane-offline" type="button" role="tab" aria-selected="{{ empty($razorpayEnabled) ? 'true' : 'false' }}">
            <div class="method-icon-wrap p-2 rounded-3 bg-primary-subtle text-primary fs-5">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark">Manual UPI QR & Bank Transfer</span>
                    <span class="badge bg-secondary-subtle text-secondary small px-2 py-1 border">Admin Approval</span>
                </div>
                <div class="small text-muted">Scan Dynamic QR &bull; Upload UTR reference screenshot</div>
            </div>
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="rechargeMethodTabsContent">

    @if(!empty($razorpayEnabled))
    <!-- PANE 1: RAZORPAY INSTANT RECHARGE -->
    <div class="tab-pane fade show active" id="pane-razorpay" role="tabpanel" aria-labelledby="tab-razorpay">
        <div class="row g-4">
            <!-- Left Side: Amount Selection & Payment Action -->
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle px-2 py-1">1</span>
                            <h6 class="fw-bold mb-0 text-dark">Choose Recharge Amount</h6>
                        </div>
                        @if($isSandbox)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1 small">
                                <i class="fa-solid fa-flask me-1"></i> Sandbox Test Mode
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 small">
                                <i class="fa-solid fa-shield-check me-1"></i> Official Razorpay Verified
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <!-- Quick Amount Chips -->
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">Popular Recharge Amounts</label>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <div class="amount-chip rzp-chip" data-val="500">
                                <span>₹500</span>
                            </div>
                            <div class="amount-chip rzp-chip active" data-val="1000">
                                <span>₹1,000</span>
                                <span class="badge bg-success-subtle text-success small" style="font-size: 0.65rem;">Popular</span>
                            </div>
                            <div class="amount-chip rzp-chip" data-val="2000">
                                <span>₹2,000</span>
                            </div>
                            <div class="amount-chip rzp-chip" data-val="5000">
                                <span>₹5,000</span>
                            </div>
                            <div class="amount-chip rzp-chip" data-val="10000">
                                <span>₹10,000</span>
                            </div>
                        </div>

                        <!-- Custom Input -->
                        <label for="rzpInputAmount" class="form-label fw-bold text-muted small text-uppercase mb-1">
                            Or Enter Custom Amount (₹) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg mb-2 shadow-sm rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-light text-success fw-bold px-4 border-0 fs-4">₹</span>
                            <input type="number" id="rzpInputAmount" class="form-control font-monospace fw-bold text-dark border-0 fs-3" min="100" max="500000" step="100" value="{{ old('amount', $defaultAmount ?? 1000) }}">
                            <span class="input-group-text bg-white text-muted small border-0 pe-3">INR</span>
                        </div>
                        <div class="form-text small mb-4">Minimum recharge amount: ₹100 &bull; Maximum limit: ₹5,00,000</div>

                        <!-- Payment Channels Supported Strip -->
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <div class="text-muted small fw-semibold mb-2">Accepted Payment Channels:</div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-white border text-dark px-2 py-1 shadow-xs"><i class="fa-brands fa-google text-danger me-1"></i> Google Pay</span>
                                <span class="badge bg-white border text-dark px-2 py-1 shadow-xs"><i class="fa-solid fa-mobile-screen text-primary me-1"></i> PhonePe</span>
                                <span class="badge bg-white border text-dark px-2 py-1 shadow-xs"><i class="fa-solid fa-wallet text-info me-1"></i> Paytm</span>
                                <span class="badge bg-white border text-dark px-2 py-1 shadow-xs"><i class="fa-solid fa-building-columns text-success me-1"></i> UPI & BHIM</span>
                                <span class="badge bg-white border text-dark px-2 py-1 shadow-xs"><i class="fa-solid fa-credit-card text-secondary me-1"></i> Cards & NetBanking</span>
                            </div>
                        </div>

                        <!-- CTA Pay Button -->
                        <button type="button" id="btnRazorpayPay" class="btn btn-success pay-btn-glow w-100 py-3 rounded-3 text-white fw-bold fs-5 d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Pay ₹<span id="btnRzpAmountText">{{ number_format($defaultAmount ?? 1000, 0) }}</span> via Razorpay</span>
                            <i class="fa-solid fa-arrow-right ms-1 small"></i>
                        </button>

                        <div class="d-flex align-items-center justify-content-center gap-3 small text-muted flex-wrap">
                            <span><i class="fa-solid fa-lock text-success me-1"></i> 256-bit Bank Grade Security</span>
                            <span><i class="fa-solid fa-bolt text-warning me-1"></i> Instant Auto-Credited</span>
                            <span><i class="fa-solid fa-receipt text-primary me-1"></i> Automated Invoice</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary & Live Balance Projection -->
            <div class="col-lg-5">
                <div class="summary-card text-white p-4 rounded-4 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-1 font-monospace">
                            {{ $centre->centre_code }}
                        </span>
                        <span class="badge bg-success bg-opacity-25 text-success-light border border-success border-opacity-50 px-2 py-1 small">
                            <i class="fa-solid fa-circle-check me-1"></i> 0% Fee
                        </span>
                    </div>

                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Recharge Breakdown</div>
                    
                    <div class="py-2 border-bottom border-white border-opacity-10 d-flex justify-content-between">
                        <span class="text-white-50">Current Balance:</span>
                        <span class="font-monospace fw-semibold text-white">₹{{ number_format($wallet?->balance ?? 0, 2) }}</span>
                    </div>

                    <div class="py-2 border-bottom border-white border-opacity-10 d-flex justify-content-between">
                        <span class="text-white-50">Recharge Amount:</span>
                        <span class="font-monospace fw-bold text-success fs-6">₹<span id="summaryRechargeAmt">{{ number_format($defaultAmount ?? 1000, 2) }}</span></span>
                    </div>

                    <div class="py-2 border-bottom border-white border-opacity-10 d-flex justify-content-between">
                        <span class="text-white-50">Platform Convenience Fee:</span>
                        <span class="text-white-50">₹0.00 (Waived)</span>
                    </div>

                    <div class="py-2 border-bottom border-white border-opacity-10 d-flex justify-content-between">
                        <span class="text-white-50">Taxes / GST:</span>
                        <span class="text-white-50">₹0.00</span>
                    </div>

                    <div class="pt-3 pb-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-6">Total Amount to Pay:</span>
                        <span class="fs-4 fw-bolder font-monospace text-warning">₹<span id="summaryTotalPayable">{{ number_format($defaultAmount ?? 1000, 2) }}</span></span>
                    </div>

                    <!-- Projected Balance Pill -->
                    <div class="mt-3 p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-15">
                        <div class="small text-white-50">Projected Balance After Recharge:</div>
                        <div class="fs-5 fw-bold font-monospace text-success mt-1">
                            ₹<span id="projectedBalance">{{ number_format(($wallet?->balance ?? 0) + ($defaultAmount ?? 1000), 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($isSandbox)
                <div class="card border-warning border-opacity-50 bg-warning bg-opacity-10 rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-flask-vial text-warning fs-5 mt-1"></i>
                        <div>
                            <strong class="text-dark">Developer / Sandbox Testing:</strong>
                            <p class="small text-muted mb-2">Test payment flow instantly without using real money.</p>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" id="btnSandboxSimulate">
                                <i class="fa-solid fa-bolt me-1"></i> Simulate Successful Payment
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Hidden Verification Form for Razorpay Submit -->
        <form id="razorpayVerifyForm" method="POST" action="{{ route('centre.wallet.recharge.verify') }}" class="d-none">
            @csrf
            <input type="hidden" name="order_id" id="rzp_order_id">
            <input type="hidden" name="gateway_payment_id" id="rzp_payment_id">
            <input type="hidden" name="gateway_signature" id="rzp_signature">
        </form>
    </div>
    @endif

    <!-- PANE 2: OFFLINE DYNAMIC UPI QR & BANK TRANSFER -->
    <div class="tab-pane fade {{ empty($razorpayEnabled) ? 'show active' : '' }}" id="pane-offline" role="tabpanel" aria-labelledby="tab-offline">
        <div class="row g-4">
            <!-- Left: Dynamic QR Generator -->
            <div class="col-lg-6">
                <!-- STEP 1: Enter Amount -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="badge bg-primary rounded-circle me-2 px-2 py-1">1</span>Enter Amount for UPI QR
                        </h6>
                        <span class="badge bg-light text-muted border small">Min: ₹100</span>
                    </div>
                    <div class="card-body p-4">
                        <label for="inputAmount" class="form-label fw-bold small text-muted text-uppercase">Recharge Amount (₹)</label>
                        <div class="input-group input-group-lg mb-3 shadow-sm rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-light text-primary fw-bold px-3 border-0">₹</span>
                            <input type="number" id="inputAmount" class="form-control font-monospace fw-bold text-primary border-0" min="100" max="500000" step="50" value="{{ old('amount', $defaultAmount ?? 1000) }}">
                            <button class="btn btn-primary px-3 fw-semibold border-0" type="button" id="btnUpdateQr">
                                <i class="fa-solid fa-rotate me-1"></i> Update QR
                            </button>
                        </div>

                        <!-- Quick Amount Pills -->
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="small text-muted me-1">Quick Select:</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-amt" data-val="500">+₹500</button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-amt active" data-val="1000">+₹1,000</button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-amt" data-val="2000">+₹2,000</button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-amt" data-val="5000">+₹5,000</button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-amt" data-val="10000">+₹10,000</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Scan Dynamic QR Code -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="badge bg-success rounded-circle me-2 px-2 py-1">2</span>Scan UPI QR & Pay
                        </h6>
                        <span class="badge bg-success-subtle text-success small font-monospace">
                            <i class="fa-solid fa-bolt me-1"></i>Exact Amount QR
                        </span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="p-3 bg-white rounded-3 border d-inline-block shadow-sm mb-3 position-relative" style="min-width: 240px; min-height: 240px;">
                            <div id="qrSpinner" class="position-absolute top-50 start-50 translate-middle d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Generating QR...</span>
                                </div>
                            </div>
                            <div id="qrContainer" class="d-flex justify-content-center align-items-center">
                                {!! $qrSvg !!}
                            </div>
                        </div>

                        <!-- Payee Details & Copy UPI -->
                        <div class="bg-light rounded-3 p-3 text-start small border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Payee Name:</span>
                                <strong class="text-dark">{{ $adminUpiName }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Recharge Amount:</span>
                                <strong class="text-success fs-6 font-monospace">₹<span id="displayAmount">{{ number_format($defaultAmount ?? 1000, 2) }}</span></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Official UPI ID:</span>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="fw-bold text-dark font-monospace" id="upiIdText">{{ $adminUpiId }}</code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnCopyUpi" title="Copy UPI ID">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Direct App Link -->
                        <div class="d-md-none mt-2">
                            <a href="{{ $upiUri }}" id="btnMobilePay" class="btn btn-outline-success w-100 fw-bold">
                                <i class="fa-solid fa-mobile-screen-button me-1"></i> Open in Installed UPI App
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Submit UTR & Screenshot -->
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="badge bg-warning text-dark rounded-circle me-2 px-2 py-1">3</span>Submit Payment Proof & UTR ID
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('centre.wallet.topup.store') }}" enctype="multipart/form-data" id="topupRequestForm">
                            @csrf

                            <!-- Synchronized Amount -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Recharge Amount (₹) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm rounded-2 overflow-hidden border">
                                    <span class="input-group-text bg-light fw-bold text-success border-0">₹</span>
                                    <input type="number" step="1" min="100" max="500000" name="amount" id="formAmount" class="form-control fw-bold font-monospace fs-5 text-success border-0" value="{{ old('amount', $defaultAmount ?? 1000) }}" required readonly>
                                    <span class="input-group-text bg-light text-muted small border-0"><i class="fa-solid fa-lock me-1"></i> Synced with QR</span>
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" id="paymentMode" class="form-select rounded-2" required>
                                    <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR Code Transfer (PhonePe/GPay/Paytm)</option>
                                    <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT / RTGS)</option>
                                    <option value="imps" {{ old('payment_mode') == 'imps' ? 'selected' : '' }}>IMPS Instant Transfer</option>
                                    <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Bank Branch Cash Deposit / Cheque</option>
                                </select>
                            </div>

                            <!-- UTR Reference -->
                            <div class="mb-3">
                                <label for="reference_no" class="form-label fw-bold small text-muted text-uppercase">
                                    Transaction / UTR Reference ID <span class="text-danger">*</span>
                                </label>
                                <div class="input-group shadow-sm rounded-2 overflow-hidden border">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-receipt text-primary"></i></span>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control font-monospace fw-bold text-uppercase border-0 @error('reference_no') is-invalid @enderror" placeholder="e.g. 431289560142 or UPI Ref" value="{{ old('reference_no') }}" required>
                                </div>
                                <div class="form-text small">Enter the 12-digit UTR number from your payment receipt.</div>
                                @error('reference_no')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Screenshot Upload -->
                            <div class="mb-3">
                                <label for="payment_slip" class="form-label fw-bold small text-muted text-uppercase">
                                    Upload Payment Screenshot / Slip <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="payment_slip" id="payment_slip" class="form-control rounded-2 @error('payment_slip') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" required>
                                <div class="form-text small">JPG, PNG, WEBP, GIF, PDF up to 5MB.</div>
                                @error('payment_slip')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div id="slipPreviewContainer" class="mt-2 d-none">
                                    <div class="p-2 border rounded bg-light d-flex align-items-center gap-3">
                                        <img id="slipPreviewImg" src="" alt="Screenshot Preview" class="img-thumbnail" style="max-height: 80px; max-width: 100px; object-fit: cover;">
                                        <div class="small">
                                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Screenshot Ready</span>
                                            <div class="text-muted mt-1 small" id="slipFileName"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label fw-bold small text-muted text-uppercase">Notes (Optional)</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control rounded-2" placeholder="e.g. Wallet top-up for student registrations">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Warning Info -->
                            <div class="alert alert-warning border-0 p-3 rounded-3 small mb-4">
                                <i class="fa-solid fa-circle-info me-1"></i> Admin will verify your UTR and slip proof against bank credits before approving balance.
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-warning text-dark w-100 fw-bold py-3 shadow-sm rounded-3 fs-6">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Request (₹<span id="btnSubmitAmount">{{ number_format($defaultAmount ?? 1000, 0) }}</span>)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="card border-0 rounded-4 shadow-sm bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-building-columns text-primary me-2"></i>Head Office Bank Details (NEFT/RTGS/IMPS)
                        </h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <ul class="list-unstyled mb-0 small">
                            <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted">Account Holder:</span>
                                <strong class="text-dark">{{ $adminBank['holder'] ?? ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') }}</strong>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted">Bank Name:</span>
                                <span class="fw-semibold text-dark">{{ $adminBank['name'] ?? 'State Bank of India' }}</span>
                            </li>
                            <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted">Account Number:</span>
                                <span class="fw-bold font-monospace text-primary fs-6">{{ $adminBank['account_no'] ?? '38491028472' }}</span>
                            </li>
                            <li class="py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted">IFSC Code:</span>
                                <span class="fw-bold font-monospace text-dark">{{ $adminBank['ifsc'] ?? 'SBIN0001234' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const currentBalance = {{ (float) ($wallet?->balance ?? 0) }};

    function formatNumber(num) {
        return Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // --- Razorpay Online Recharge Section ---
    const rzpInputAmount = document.getElementById('rzpInputAmount');
    const btnRazorpayPay = document.getElementById('btnRazorpayPay');
    const btnRzpAmountText = document.getElementById('btnRzpAmountText');
    const summaryRechargeAmt = document.getElementById('summaryRechargeAmt');
    const summaryTotalPayable = document.getElementById('summaryTotalPayable');
    const projectedBalance = document.getElementById('projectedBalance');
    const rzpChips = document.querySelectorAll('.rzp-chip');
    const btnSandboxSimulate = document.getElementById('btnSandboxSimulate');

    function updateRzpSummary(amt) {
        amt = parseFloat(amt) || 0;
        if (btnRzpAmountText) {
            btnRzpAmountText.textContent = Number(amt).toLocaleString('en-IN');
        }
        if (summaryRechargeAmt) {
            summaryRechargeAmt.textContent = formatNumber(amt);
        }
        if (summaryTotalPayable) {
            summaryTotalPayable.textContent = formatNumber(amt);
        }
        if (projectedBalance) {
            projectedBalance.textContent = formatNumber(currentBalance + amt);
        }
    }

    if (rzpInputAmount) {
        rzpChips.forEach(chip => {
            chip.addEventListener('click', function () {
                rzpChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const val = this.dataset.val;
                rzpInputAmount.value = val;
                updateRzpSummary(val);
            });
        });

        rzpInputAmount.addEventListener('input', function () {
            const val = parseFloat(this.value) || 0;
            rzpChips.forEach(c => {
                c.classList.toggle('active', parseFloat(c.dataset.val) === val);
            });
            updateRzpSummary(val);
        });

        if (btnRazorpayPay) {
            btnRazorpayPay.addEventListener('click', async function () {
                const amt = parseFloat(rzpInputAmount.value);
                if (isNaN(amt) || amt < 100) {
                    alert('Please enter a valid amount (Minimum ₹100).');
                    return;
                }

                const originalHtml = btnRazorpayPay.innerHTML;
                btnRazorpayPay.disabled = true;
                btnRazorpayPay.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Connecting Razorpay...';

                try {
                    const response = await fetch("{{ route('centre.wallet.recharge.initiate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: amt,
                            payment_method: 'UPI'
                        })
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        throw new Error(err.message || 'Failed to initiate gateway order.');
                    }

                    const data = await response.json();

                    if (typeof Razorpay !== 'undefined') {
                        const rzpOptions = {
                            key: data.key_id || "{{ $razorpayKeyId }}",
                            amount: data.amount_paise || (data.amount * 100),
                            currency: data.currency || "INR",
                            name: "{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}",
                            description: "Franchise Wallet Recharge - {{ $centre->centre_code }}",
                            order_id: (data.order_id && data.order_id.startsWith('order_')) ? data.order_id : undefined,
                            prefill: {
                                name: "{{ $centre->director_name ?? $centre->centre_name }}",
                                email: "{{ $centre->email }}",
                                contact: "{{ $centre->mobile }}"
                            },
                            notes: {
                                centre_id: "{{ $centre->id }}",
                                centre_code: "{{ $centre->centre_code }}"
                            },
                            theme: {
                                color: "#059669"
                            },
                            handler: function (paymentResult) {
                                btnRazorpayPay.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying Payment...';
                                document.getElementById('rzp_order_id').value = data.order_id;
                                document.getElementById('rzp_payment_id').value = paymentResult.razorpay_payment_id;
                                document.getElementById('rzp_signature').value = paymentResult.razorpay_signature || '';
                                document.getElementById('razorpayVerifyForm').submit();
                            },
                            modal: {
                                ondismiss: function () {
                                    btnRazorpayPay.disabled = false;
                                    btnRazorpayPay.innerHTML = originalHtml;
                                }
                            }
                        };

                        const rzpInstance = new Razorpay(rzpOptions);
                        rzpInstance.on('payment.failed', function (resp) {
                            alert('Payment failed: ' + (resp.error.description || 'Transaction was declined'));
                            btnRazorpayPay.disabled = false;
                            btnRazorpayPay.innerHTML = originalHtml;
                        });
                        rzpInstance.open();
                    } else {
                        alert('Razorpay Checkout library could not be loaded. Please ensure you have an active internet connection.');
                        btnRazorpayPay.disabled = false;
                        btnRazorpayPay.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Razorpay Error:', error);
                    alert(error.message || 'Error occurred while contacting payment gateway.');
                    btnRazorpayPay.disabled = false;
                    btnRazorpayPay.innerHTML = originalHtml;
                }
            });
        }

        if (btnSandboxSimulate) {
            btnSandboxSimulate.addEventListener('click', async function () {
                const amt = parseFloat(rzpInputAmount.value) || 1000;
                btnSandboxSimulate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Simulating...';
                try {
                    const response = await fetch("{{ route('centre.wallet.recharge.initiate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: amt,
                            payment_method: 'UPI'
                        })
                    });
                    const data = await response.json();
                    document.getElementById('rzp_order_id').value = data.order_id;
                    document.getElementById('rzp_payment_id').value = 'pay_sim_' + Math.random().toString(36).substring(2, 12);
                    document.getElementById('rzp_signature').value = '';
                    document.getElementById('razorpayVerifyForm').submit();
                } catch (e) {
                    alert('Simulation error: ' + e.message);
                    btnSandboxSimulate.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Simulate Successful Payment';
                }
            });
        }
    }

    // --- Offline Dynamic UPI QR Section ---
    const inputAmount = document.getElementById('inputAmount');
    const formAmount = document.getElementById('formAmount');
    const btnUpdateQr = document.getElementById('btnUpdateQr');
    const qrContainer = document.getElementById('qrContainer');
    const qrSpinner = document.getElementById('qrSpinner');
    const displayAmount = document.getElementById('displayAmount');
    const btnSubmitAmount = document.getElementById('btnSubmitAmount');
    const btnCopyUpi = document.getElementById('btnCopyUpi');
    const upiIdText = document.getElementById('upiIdText');
    const btnMobilePay = document.getElementById('btnMobilePay');
    const quickAmtBtns = document.querySelectorAll('.quick-amt');
    const paymentSlipInput = document.getElementById('payment_slip');
    const slipPreviewContainer = document.getElementById('slipPreviewContainer');
    const slipPreviewImg = document.getElementById('slipPreviewImg');
    const slipFileName = document.getElementById('slipFileName');

    let debounceTimer = null;

    function updateQrCode(amt) {
        amt = parseFloat(amt);
        if (isNaN(amt) || amt < 100) return;

        if (formAmount) formAmount.value = amt;
        if (displayAmount) displayAmount.textContent = formatNumber(amt);
        if (btnSubmitAmount) btnSubmitAmount.textContent = Number(amt).toLocaleString('en-IN');

        if (qrSpinner) qrSpinner.classList.remove('d-none');
        if (qrContainer) qrContainer.style.opacity = '0.3';

        fetch("{{ route('centre.wallet.qr') }}?amount=" + encodeURIComponent(amt), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.qr_svg && qrContainer) qrContainer.innerHTML = data.qr_svg;
            if (btnMobilePay && data.upi_uri) btnMobilePay.setAttribute('href', data.upi_uri);
        })
        .catch(err => console.error('QR code fetch error:', err))
        .finally(() => {
            if (qrSpinner) qrSpinner.classList.add('d-none');
            if (qrContainer) qrContainer.style.opacity = '1';
        });
    }

    if (inputAmount) {
        inputAmount.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => updateQrCode(this.value), 500);
        });
    }

    if (btnUpdateQr) {
        btnUpdateQr.addEventListener('click', function () {
            updateQrCode(inputAmount.value);
        });
    }

    quickAmtBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            quickAmtBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const val = this.dataset.val;
            if (inputAmount) inputAmount.value = val;
            updateQrCode(val);
        });
    });

    if (btnCopyUpi && upiIdText) {
        btnCopyUpi.addEventListener('click', function () {
            const text = upiIdText.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btnCopyUpi.innerHTML;
                btnCopyUpi.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                setTimeout(() => { btnCopyUpi.innerHTML = originalHtml; }, 2000);
            }).catch(() => alert('Copied UPI ID: ' + text));
        });
    }

    if (paymentSlipInput) {
        paymentSlipInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (slipFileName) slipFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (slipPreviewImg) slipPreviewImg.src = e.target.result;
                        if (slipPreviewImg) slipPreviewImg.classList.remove('d-none');
                        if (slipPreviewContainer) slipPreviewContainer.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (slipPreviewImg) slipPreviewImg.classList.add('d-none');
                    if (slipPreviewContainer) slipPreviewContainer.classList.remove('d-none');
                }
            } else {
                if (slipPreviewContainer) slipPreviewContainer.classList.add('d-none');
            }
        });
    }
});
</script>
@endpush
@endsection
