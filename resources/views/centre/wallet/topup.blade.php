@extends('layouts.centre')

@section('title', 'Recharge Wallet via Dynamic UPI QR - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <a href="{{ route('centre.wallet.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Wallet Ledger
            </a>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-wallet text-warning me-2"></i>Franchise Wallet Recharge (Dynamic UPI QR)
            </h3>
            <p class="text-muted small mb-0">Generate a dynamic UPI QR code for the exact amount, pay via any UPI App (GPay/PhonePe/Paytm), and submit the UTR & screenshot for Admin approval.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                <i class="fa-solid fa-building-columns me-1"></i> Centre: {{ $centre->centre_code }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Please correct the errors below:</strong>
        <ul class="mb-0 mt-1 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- LEFT COLUMN: Step 1 Amount & Step 2 Dynamic UPI QR -->
    <div class="col-lg-6">
        <!-- STEP 1: Enter Amount -->
        <div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-primary">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <span class="badge bg-primary rounded-circle me-2 px-2 py-1">1</span>Enter Recharge Amount
                </h6>
                <span class="badge bg-light text-muted border small">Min: ₹100</span>
            </div>
            <div class="card-body p-4">
                <label for="inputAmount" class="form-label fw-bold small text-muted text-uppercase">Enter Amount to Recharge (₹)</label>
                <div class="input-group input-group-lg mb-3">
                    <span class="input-group-text bg-light text-primary fw-bold px-3">₹</span>
                    <input type="number" id="inputAmount" class="form-control font-monospace fw-bold text-primary" min="100" max="500000" step="50" value="{{ old('amount', $defaultAmount ?? 1000) }}">
                    <button class="btn btn-primary px-3 fw-semibold" type="button" id="btnUpdateQr">
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
        <div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-success">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <span class="badge bg-success rounded-circle me-2 px-2 py-1">2</span>Scan Dynamic UPI QR & Pay
                </h6>
                <span class="badge bg-success-subtle text-success small font-monospace">
                    <i class="fa-solid fa-bolt me-1"></i>Exact Amount QR
                </span>
            </div>
            <div class="card-body p-4 text-center">
                <!-- QR Code Display Box -->
                <div class="p-3 bg-white rounded-3 border d-inline-block shadow-sm mb-3 position-relative" style="min-width: 250px; min-height: 250px;">
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

                <!-- UPI Apps Supported Strip -->
                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mb-2 text-muted small">
                    <span class="fw-semibold text-secondary">Pay using:</span>
                    <span class="badge bg-white border text-dark shadow-sm px-2 py-1"><i class="fa-brands fa-google text-danger me-1"></i> Google Pay</span>
                    <span class="badge bg-white border text-dark shadow-sm px-2 py-1"><i class="fa-solid fa-mobile-screen text-primary me-1"></i> PhonePe</span>
                    <span class="badge bg-white border text-dark shadow-sm px-2 py-1"><i class="fa-solid fa-wallet text-info me-1"></i> Paytm</span>
                    <span class="badge bg-white border text-dark shadow-sm px-2 py-1"><i class="fa-solid fa-building-columns text-success me-1"></i> BHIM / Any UPI</span>
                </div>

                <!-- Mobile Direct App Link -->
                <div class="d-md-none mt-3">
                    <a href="{{ $upiUri }}" id="btnMobilePay" class="btn btn-outline-success w-100 fw-bold">
                        <i class="fa-solid fa-mobile-screen-button me-1"></i> Open in Installed UPI App
                    </a>
                </div>

                @if(!empty($adminQrCode))
                    <div class="mt-3 text-center border-top pt-2">
                        <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-toggle="collapse" data-bs-target="#merchantQrBox">
                            <i class="fa-solid fa-image me-1"></i> View Head Office Static Merchant QR Standee
                        </button>
                        <div class="collapse mt-2" id="merchantQrBox">
                            <div class="card card-body bg-light border p-2">
                                <img src="{{ asset('storage/' . $adminQrCode) }}" alt="Head Office Merchant QR" class="img-fluid rounded mx-auto" style="max-height: 220px;">
                                <div class="small text-muted mt-1">Official Merchant Standee</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Step 3 UTR & Screenshot Submission Form + Bank Details -->
    <div class="col-lg-6">
        <!-- Active Balance Card -->
        <div class="card border-0 rounded-3 shadow-sm mb-4 bg-primary text-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-white-50 text-uppercase small fw-bold">Current Wallet Balance</span>
                    <span class="badge bg-white text-primary font-monospace">{{ $centre->centre_code }}</span>
                </div>
                <h2 class="fw-bold mb-0">₹{{ number_format($wallet?->balance ?? 0, 2) }}</h2>
                <div class="text-white-50 small mt-1">
                    <i class="fa-solid fa-shield-check text-warning me-1"></i> Balance credited only upon Admin verification of payment.
                </div>
            </div>
        </div>

        <!-- STEP 3: Submit Payment Request -->
        <div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-white py-3 border-bottom">
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
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-success">₹</span>
                            <input type="number" step="1" min="100" max="500000" name="amount" id="formAmount" class="form-control fw-bold font-monospace fs-5 text-success" value="{{ old('amount', $defaultAmount ?? 1000) }}" required readonly>
                            <span class="input-group-text bg-light text-muted small"><i class="fa-solid fa-lock me-1"></i> Synced with QR</span>
                        </div>
                        <div class="form-text small">Change amount in Step 1 to regenerate QR for a different value.</div>
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

                    <!-- UTR / Transaction Reference ID -->
                    <div class="mb-3">
                        <label for="reference_no" class="form-label fw-bold small text-muted text-uppercase">
                            Transaction / UTR Reference ID <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-receipt text-primary"></i></span>
                            <input type="text" name="reference_no" id="reference_no" class="form-control font-monospace fw-bold text-uppercase @error('reference_no') is-invalid @enderror" placeholder="e.g. 431289560142 or UPI Transaction ID" value="{{ old('reference_no') }}" required>
                        </div>
                        <div class="form-text small">Enter the 12-digit UTR number or UPI transaction reference from your payment confirmation screen.</div>
                        @error('reference_no')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Payment Screenshot Upload -->
                    <div class="mb-3">
                        <label for="payment_slip" class="form-label fw-bold small text-muted text-uppercase">
                            Upload Payment Screenshot / Slip <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="payment_slip" id="payment_slip" class="form-control @error('payment_slip') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" required>
                        <div class="form-text small">Attach the payment success screenshot (JPG, PNG, WEBP, GIF, PDF up to 5MB). Admin will verify this proof.</div>
                        @error('payment_slip')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <!-- Live Screenshot Preview -->
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
                        <label for="notes" class="form-label fw-bold small text-muted text-uppercase">Notes / Purpose <small class="text-muted">(Optional)</small></label>
                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="e.g. Wallet recharge for new student admissions">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Warning Alert -->
                    <div class="alert alert-warning border-0 p-3 rounded-2 small mb-4">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-info text-warning fs-5 mt-1"></i>
                            <div>
                                <strong>Admin Verification Policy:</strong> Wallet funds are NOT added automatically. Once you submit this request, Head Office Admin will review your UTR ID & screenshot proof against bank credits and approve the recharge.
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success w-100 fw-bold py-3 shadow-sm rounded-2 fs-6">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Recharge Request (₹<span id="btnSubmitAmount">{{ number_format($defaultAmount ?? 1000, 0) }}</span>)
                    </button>
                </form>
            </div>
        </div>

        <!-- Head Office Bank Details Card -->
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-building-columns text-primary me-2"></i>Official Head Office Bank Account
                </h6>
                <span class="badge bg-light text-muted border small">NEFT / RTGS / IMPS</span>
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
                    <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted">IFSC Code:</span>
                        <span class="fw-bold font-monospace text-dark">{{ $adminBank['ifsc'] ?? 'SBIN0001234' }}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Official UPI ID:</span>
                        <span class="fw-bold font-monospace text-success">{{ $adminUpiId }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    function formatNumber(num) {
        return Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateQrCode(amt) {
        amt = parseFloat(amt);
        if (isNaN(amt) || amt < 100) {
            return;
        }

        formAmount.value = amt;
        displayAmount.textContent = formatNumber(amt);
        btnSubmitAmount.textContent = Number(amt).toLocaleString('en-IN');

        qrSpinner.classList.remove('d-none');
        qrContainer.style.opacity = '0.3';

        fetch("{{ route('centre.wallet.qr') }}?amount=" + encodeURIComponent(amt), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.qr_svg) {
                qrContainer.innerHTML = data.qr_svg;
            }
            if (btnMobilePay && data.upi_uri) {
                btnMobilePay.setAttribute('href', data.upi_uri);
            }
        })
        .catch(err => {
            console.error('QR code fetch error:', err);
        })
        .finally(() => {
            qrSpinner.classList.add('d-none');
            qrContainer.style.opacity = '1';
        });
    }

    // Input change with debounce
    inputAmount.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            updateQrCode(this.value);
        }, 500);
    });

    btnUpdateQr.addEventListener('click', function () {
        updateQrCode(inputAmount.value);
    });

    // Quick select buttons
    quickAmtBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            quickAmtBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const val = this.dataset.val;
            inputAmount.value = val;
            updateQrCode(val);
        });
    });

    // Copy UPI ID button
    if (btnCopyUpi) {
        btnCopyUpi.addEventListener('click', function () {
            const text = upiIdText.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btnCopyUpi.innerHTML;
                btnCopyUpi.innerHTML = '<i class="fa-solid fa-check text-success"></i> Copied!';
                setTimeout(() => {
                    btnCopyUpi.innerHTML = originalHtml;
                }, 2000);
            }).catch(() => {
                alert('Copied UPI ID: ' + text);
            });
        });
    }

    // Live screenshot file preview
    if (paymentSlipInput) {
        paymentSlipInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                slipFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        slipPreviewImg.src = e.target.result;
                        slipPreviewImg.classList.remove('d-none');
                        slipPreviewContainer.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    slipPreviewImg.classList.add('d-none');
                    slipPreviewContainer.classList.remove('d-none');
                }
            } else {
                slipPreviewContainer.classList.add('d-none');
            }
        });
    }
});
</script>
@endpush
@endsection
