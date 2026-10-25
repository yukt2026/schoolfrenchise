@extends('layouts.centre')

@section('title', 'Collect Student Fee - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Collections Ledger
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i>Collect Student Fee Payment
    </h3>
    <p class="text-muted small mb-0">Record offline tuition and examination fee installment, update ledger, and generate computerized receipt.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice text-primary me-2"></i>Payment Details & Student Selection</h6>
            </div>
            <div class="card-body p-4">
                <!-- Step 1: Select Student Admission -->
                <form method="GET" action="{{ route('centre.fees.create') }}" class="mb-4">
                    <label class="form-label fw-bold text-dark mb-1">Select Candidate with Pending Dues: <span class="text-danger">*</span></label>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <select name="admission_id" class="form-select rounded-2" onchange="this.form.submit()" required>
                                <option value="">-- Choose Candidate --</option>
                                @foreach($students as $s)
                                @php $adm = $s->admissions->first(); @endphp
                                @if($adm)
                                @php $due = max(0, $adm->net_fee - $adm->total_paid); @endphp
                                <option value="{{ $adm->id }}" {{ ($selectedAdmission && $selectedAdmission->id == $adm->id) ? 'selected' : '' }}>
                                    {{ $s->roll_no }} - {{ $s->full_name }} ({{ $adm->course?->course_code }}) &mdash; Due: ₹{{ number_format($due, 2) }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold rounded-2 py-2">Select Candidate</button>
                        </div>
                    </div>
                </form>

                @if($selectedAdmission)
                @php $outstanding = max(0, $selectedAdmission->net_fee - $selectedAdmission->total_paid); @endphp

                <!-- Student & Course Ledger Snapshot -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $selectedAdmission->student?->full_name }}</div>
                            <div class="small text-muted font-monospace">ROLL: {{ $selectedAdmission->student?->roll_no }} &bull; REG: {{ $selectedAdmission->student?->registration_no }}</div>
                        </div>
                        <span class="badge bg-primary font-monospace rounded-2">{{ $selectedAdmission->course?->course_code }}</span>
                    </div>

                    <div class="row g-2 text-center pt-1">
                        <div class="col-4">
                            <div class="small text-muted text-uppercase" style="font-size: 0.68rem;">Total Net Fee</div>
                            <div class="fw-bold text-dark fs-6 font-monospace">₹{{ number_format($selectedAdmission->net_fee, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted text-uppercase" style="font-size: 0.68rem;">Already Paid</div>
                            <div class="fw-bold text-success fs-6 font-monospace">₹{{ number_format($selectedAdmission->total_paid, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted text-uppercase" style="font-size: 0.68rem;">Outstanding Due</div>
                            <div class="fw-bold text-danger fs-6 font-monospace">₹{{ number_format($outstanding, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form method="POST" action="{{ route('centre.fees.store') }}">
                    @csrf
                    <input type="hidden" name="admission_id" value="{{ $selectedAdmission->id }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" max="{{ $outstanding }}" name="amount" class="form-control fw-bold text-success rounded-2" value="{{ old('amount', $outstanding) }}" required>
                            <div class="form-text" style="font-size: 0.72rem;">Maximum collectable: ₹{{ number_format($outstanding, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select rounded-2" required>
                                <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash (Direct Counter)</option>
                                <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR Code</option>
                                <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS/IMPS)</option>
                                <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque / Demand Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transaction Ref / Cheque No / UTR</label>
                            <input type="text" name="transaction_ref" class="form-control rounded-2" value="{{ old('transaction_ref') }}" placeholder="e.g. UPI-93821038 or CHQ-001234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Payment <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control rounded-2" value="{{ old('payment_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Remarks / Receipt Note</label>
                        <textarea name="remarks" class="form-control rounded-2" rows="2" placeholder="e.g. Installment 2 of 3 paid at counter">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 px-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm rounded-2 px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-circle-check me-1"></i>Collect Fee & Issue Receipt
                        </button>
                    </div>
                </form>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-user-tag fs-1 text-black-50 mb-3 d-block"></i>
                    <h6>Select a Candidate Above</h6>
                    <p class="small text-muted mb-0">Choose an admitted student from the dropdown list to load their fee schedule and record payment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Side Information Card -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info text-info me-2"></i>Collection Policy</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="py-2 border-bottom">
                        <i class="fa-solid fa-check text-success me-2"></i>Offline payments are credited immediately to the student's ledger.
                    </li>
                    <li class="py-2 border-bottom">
                        <i class="fa-solid fa-check text-success me-2"></i>Official computerized receipts with QR code are generated on collection.
                    </li>
                    <li class="py-2 border-bottom">
                        <i class="fa-solid fa-check text-success me-2"></i>A payment cannot exceed the candidate's remaining net due balance.
                    </li>
                    <li class="py-2">
                        <i class="fa-solid fa-shield text-primary me-2"></i>All fee transactions are permanently audit-logged for board verification.
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-body p-3 text-center">
                <div class="text-muted small fw-semibold text-uppercase">Outstanding Dues</div>
                <a href="{{ route('centre.fees.dues') }}" class="btn btn-outline-danger btn-sm rounded-2 w-100 mt-2 fw-semibold">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i>Check Centre Dues Report
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
