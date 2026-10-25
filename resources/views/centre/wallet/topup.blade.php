@extends('layouts.centre')

@section('title', 'Request Wallet Recharge - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.wallet.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Wallet Ledger
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-plus-circle text-warning me-2"></i>Franchise Wallet Top-Up Request
    </h3>
    <p class="text-muted small mb-0">Deposit funds to central accounts via UPI or NEFT/RTGS and submit transaction reference for instant balance approval.</p>
</div>

<div class="row g-4">
    <!-- Left Column: Top-up Request Form -->
    <div class="col-lg-7">
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Submit Recharge Particulars</h6>
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
                        <textarea name="notes" class="form-control rounded-2" rows="2" placeholder="e.g. Wallet recharge for 10 new student kits">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-warning text-dark btn-sm w-100 rounded-2 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i>Submit Top-up Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Official Head Office Bank Details -->
    <div class="col-lg-5">
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

        <div class="card border-0 rounded-3 shadow-sm border-start border-4 border-info">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-clock-rotate-left me-1 text-info"></i>Approval Turnaround</h6>
                <p class="small text-muted mb-0">Recharge requests submitted during banking hours are verified and credited to your franchise wallet within 15 to 30 minutes.</p>
            </div>
        </div>
    </div>
</div>
@endsection
