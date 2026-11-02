@extends('layouts.centre')

@section('title', 'Request Wallet Recharge - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.wallet.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Wallet Ledger
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-wallet text-warning me-2"></i>Franchise Wallet Top-Up & Recharge
    </h3>
    <p class="text-muted small mb-0">Recharge your authorized franchise balance instantly via Payment Gateway or submit bank transfer UTR details for manual verification.</p>
</div>

@if(session('online_order'))
    @php $order = session('online_order'); @endphp
    <!-- Online Order Verification Card -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-success">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-success"><i class="fa-solid fa-credit-card me-2"></i>Complete Online Authorization</h6>
            <span class="badge bg-success-subtle text-success font-monospace">{{ $order['order_id'] }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-7">
                    <h5 class="fw-bold text-dark mb-1">Amount to Authorize: ₹{{ number_format($order['amount'], 2) }}</h5>
                    <div class="text-muted small font-monospace">Order Reference: {{ $order['order_id'] }} | Gateway: {{ strtoupper($order['provider']) }} ({{ ucfirst($order['mode']) }})</div>
                    <p class="text-secondary small mt-2 mb-0">Click the button below to authorize payment. The system will verify the server-side cryptographic signature before crediting your wallet balance.</p>
                </div>
                <div class="col-md-5 text-end">
                    <form method="POST" action="{{ route('centre.wallet.recharge.verify') }}" id="onlineVerifyForm">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order['order_id'] }}">
                        <input type="hidden" name="gateway_payment_id" id="gatewayPaymentId" value="PAY-{{ strtoupper(Str::random(12)) }}">
                        <input type="hidden" name="gateway_signature" id="gatewaySignature" value="">
                        
                        <button type="submit" class="btn btn-success rounded-2 px-4 py-2 fw-bold shadow-sm w-100">
                            <i class="fa-solid fa-shield-check me-2"></i>Authorize & Credit ₹{{ number_format($order['amount'], 2) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Online & Offline Topup Forms -->
    <div class="col-lg-7">
        <!-- 1. Instant Online Recharge via Gateway -->
        <div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-bolt text-warning me-2"></i>Instant Online Wallet Recharge
                </h6>
                <span class="badge bg-primary-subtle text-primary font-monospace rounded-2">Instant Credit</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.wallet.recharge.initiate') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recharge Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold rounded-start-2">₹</span>
                            <input type="number" step="10" min="100" max="500000" name="amount" class="form-control fw-bold text-primary rounded-end-2 @error('amount') is-invalid @enderror" placeholder="e.g. 2000" value="{{ old('amount', 1000) }}" required>
                        </div>
                        <div class="form-text" style="font-size: 0.72rem;">Minimum instant recharge amount is ₹100.</div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Preferred Payment Method</label>
                        <select name="payment_method" class="form-select rounded-2">
                            <option value="UPI">UPI / QR Code Transfer</option>
                            <option value="DebitCard">Debit Card</option>
                            <option value="CreditCard">Credit Card</option>
                            <option value="NetBanking">Net Banking</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-2 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-shield-halved me-1"></i>Proceed to Secure Online Payment
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Manual Bank Transfer / Offline UTR Submission -->
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice-dollar text-secondary me-2"></i>Manual Bank Transfer / UTR Slip Submission</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.wallet.topup.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recharge Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold rounded-start-2">₹</span>
                            <input type="number" step="0.01" min="100" name="amount" class="form-control fw-bold text-primary rounded-end-2" placeholder="e.g. 5000" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-text" style="font-size: 0.72rem;">Minimum recharge amount is ₹100.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-select rounded-2" required>
                            <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR Code Transfer</option>
                            <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT / RTGS)</option>
                            <option value="imps" {{ old('payment_mode') == 'imps' ? 'selected' : '' }}>IMPS Instant Transfer</option>
                            <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Direct Bank Deposit / Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Reference / UTR Number <span class="text-danger">*</span></label>
                        <input type="text" name="reference_no" class="form-control rounded-2" placeholder="12-digit UPI UTR or Bank Ref Number" value="{{ old('reference_no') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Payment Slip / Screenshot <small class="text-muted">(Optional)</small></label>
                        <input type="file" name="payment_slip" class="form-control rounded-2" accept=".pdf,image/*">
                        <div class="form-text" style="font-size: 0.72rem;">JPG, PNG, PDF up to 5MB. Speeds up transaction verification.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes / Purpose</label>
                        <textarea name="notes" class="form-control rounded-2" rows="2" placeholder="e.g. Wallet recharge for admission kits">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-2 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i>Submit Offline Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Official Head Office Bank Details & Active Balance -->
    <div class="col-lg-5">
        <!-- Active Balance Card -->
        <div class="card border-0 rounded-3 shadow-sm mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 text-uppercase small fw-bold">Current Wallet Balance</span>
                    <span class="badge bg-white text-primary font-monospace">{{ $centre->centre_code }}</span>
                </div>
                <h2 class="fw-bold mb-0">₹{{ number_format($wallet?->balance ?? 0, 2) }}</h2>
                <div class="text-white-50 small mt-2">
                    <i class="fa-solid fa-circle-check text-success me-1"></i>Authorized for Admission Kits & Certifications
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-building-columns text-success me-2"></i>Head Office Official Bank Account</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 small">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Account Name:</span>
                        <span class="fw-bold text-dark">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Bank Name:</span>
                        <span class="fw-semibold">State Bank of India (SBI)</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Account Number:</span>
                        <span class="fw-bold font-monospace text-primary">38491028472</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">IFSC Code:</span>
                        <span class="fw-bold font-monospace">SBIN0001234</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Account Type:</span>
                        <span class="fw-semibold">Current Account</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Official UPI ID:</span>
                        <span class="fw-bold font-monospace text-success">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}@upi</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
