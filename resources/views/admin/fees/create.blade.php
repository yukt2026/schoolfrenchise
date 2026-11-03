@extends('layouts.admin')

@section('title', 'Collect Fee Payment')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-cash-register me-2" style="color: #0891b2;"></i>Receive Student Fee Payment
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.fees.index') }}">Fees</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Collect Payment</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-credit-card me-2 text-primary"></i>Payment Collection Particulars
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.fees.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="admission_id" class="form-label fw-semibold">Candidate Account / Admission <span class="text-danger">*</span></label>
                                <select class="form-select @error('admission_id') is-invalid @enderror" id="admission_id" name="admission_id" required>
                                    <option value="">Select Candidate with Pending Balance</option>
                                    @foreach($admissions as $adm)
                                        @php
                                            $due = max(0, (float) $adm->net_fee - (float) $adm->total_paid);
                                        @endphp
                                        <option value="{{ $adm->id }}"
                                            data-student="{{ $adm->student?->full_name ?? 'N/A' }}"
                                            data-roll="{{ $adm->student?->roll_no ?? 'N/A' }}"
                                            data-course="{{ $adm->course?->course_name ?? 'N/A' }}"
                                            data-centre="{{ $adm->centre->centre_name ?? 'Head Office' }}"
                                            data-net="{{ $adm->net_fee }}"
                                            data-paid="{{ $adm->total_paid }}"
                                            data-due="{{ $due }}"
                                            {{ (old('admission_id') == $adm->id || ($selectedAdmission && $selectedAdmission->id == $adm->id)) ? 'selected' : '' }}>
                                            {{ $adm->student?->full_name ?? 'Student' }} (Roll: {{ $adm->student?->roll_no ?? 'N/A' }}) - {{ $adm->course?->course_name ?? 'Course' }} - Due: ₹{{ number_format($due, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admission_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Live Candidate Financial Card -->
                            <div class="col-md-12">
                                <div class="card bg-light border-0 p-3 rounded-2" id="accountSummaryCard">
                                    <div class="fw-bold text-dark fs-6" id="summaryStudent">Account: None Selected</div>
                                    <div class="small text-muted mt-1" id="summaryCourse">Course: -</div>
                                    <div class="small text-muted" id="summaryCentre">Study Centre: -</div>
                                    <div class="d-flex flex-wrap gap-4 mt-3 pt-2 border-top">
                                        <div>
                                            <div class="small text-muted text-uppercase">Net Package Fee</div>
                                            <div class="fw-bold text-dark fs-6" id="summaryNet">₹0.00</div>
                                        </div>
                                        <div>
                                            <div class="small text-muted text-uppercase">Paid to Date</div>
                                            <div class="fw-bold text-success fs-6" id="summaryPaid">₹0.00</div>
                                        </div>
                                        <div>
                                            <div class="small text-muted text-uppercase">Balance Due</div>
                                            <div class="fw-bold text-danger fs-5" id="summaryDue">₹0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">Payment Amount (INR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">₹</span>
                                    <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror fw-bold fs-6 text-primary" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                </div>
                                <div class="form-text small" id="amountHelp">Cannot exceed outstanding balance.</div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_mode" class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_mode') is-invalid @enderror" id="payment_mode" name="payment_mode" required>
                                    <option value="cash" {{ old('payment_mode') === 'cash' ? 'selected' : '' }}>Cash (At Counter)</option>
                                    <option value="upi" {{ old('payment_mode') === 'upi' ? 'selected' : '' }}>UPI / QR Code Scan</option>
                                    <option value="online" {{ old('payment_mode') === 'online' ? 'selected' : '' }}>Online Payment Gateway</option>
                                    <option value="cheque" {{ old('payment_mode') === 'cheque' ? 'selected' : '' }}>Bank Cheque</option>
                                    <option value="dd" {{ old('payment_mode') === 'dd' ? 'selected' : '' }}>Demand Draft (DD)</option>
                                </select>
                                @error('payment_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="transaction_ref" class="form-label fw-semibold">Transaction Reference / UTR / Cheque No</label>
                                <input type="text" class="form-control @error('transaction_ref') is-invalid @enderror font-monospace" id="transaction_ref" name="transaction_ref" value="{{ old('transaction_ref') }}" placeholder="e.g. UPI Ref / Bank TXN / Cheque #">
                                @error('transaction_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="notes" class="form-label fw-semibold">Remarks / Internal Notes</label>
                                <input type="text" class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. 2nd Installment payment">
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.fees.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-receipt me-1"></i> Collect & Print Computerized Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('admission_id');
    const amountInput = document.getElementById('amount');
    const summaryStudent = document.getElementById('summaryStudent');
    const summaryCourse = document.getElementById('summaryCourse');
    const summaryCentre = document.getElementById('summaryCentre');
    const summaryNet = document.getElementById('summaryNet');
    const summaryPaid = document.getElementById('summaryPaid');
    const summaryDue = document.getElementById('summaryDue');
    const amountHelp = document.getElementById('amountHelp');

    function updateAccount() {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            summaryStudent.textContent = 'Account: None Selected';
            summaryCourse.textContent = 'Course: -';
            summaryCentre.textContent = 'Study Centre: -';
            summaryNet.textContent = '₹0.00';
            summaryPaid.textContent = '₹0.00';
            summaryDue.textContent = '₹0.00';
            amountInput.max = '';
            return;
        }

        const due = parseFloat(opt.dataset.due || 0);
        summaryStudent.textContent = 'Candidate: ' + opt.dataset.student + ' (Roll: ' + opt.dataset.roll + ')';
        summaryCourse.textContent = 'Course: ' + opt.dataset.course;
        summaryCentre.textContent = 'Study Centre: ' + opt.dataset.centre;
        summaryNet.textContent = '₹' + parseFloat(opt.dataset.net || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
        summaryPaid.textContent = '₹' + parseFloat(opt.dataset.paid || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
        summaryDue.textContent = '₹' + due.toLocaleString('en-IN', {minimumFractionDigits: 2});

        amountInput.max = due;
        amountHelp.textContent = `Maximum collectable amount: ₹${due.toFixed(2)}`;
        if (!amountInput.value || parseFloat(amountInput.value) <= 0) {
            amountInput.value = due;
        }
    }

    select.addEventListener('change', updateAccount);
    if (select.value) {
        updateAccount();
    }
});
</script>
@endpush
@endsection
