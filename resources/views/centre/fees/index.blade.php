@extends('layouts.centre')

@section('title', 'Fee Collections & Receipts - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-receipt text-success me-2"></i>Fee Collections & Receipts Ledger
        </h3>
        <p class="text-muted small mb-0">Record offline tuition and examination fee payments, issue computerized receipts, and review outstanding dues.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.fees.create') }}" class="btn btn-success btn-sm rounded-2 px-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-hand-holding-dollar me-1"></i>Collect Student Fee
        </a>
        <a href="{{ route('centre.fees.dues') }}" class="btn btn-outline-danger btn-sm rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-file-invoice-dollar me-1"></i>Outstanding Dues Report
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success h-100 rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Total Collections</div>
            <div class="fs-5 fs-md-4 fw-bold text-success my-1">₹{{ number_format($stats['total_collected'], 2) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">All-time receipts</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-primary h-100 rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Today's Collection</div>
            <div class="fs-5 fs-md-4 fw-bold text-primary my-1">₹{{ number_format($stats['today_collected'], 2) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">{{ now()->format('d M, Y') }}</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info h-100 rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Cash Transactions</div>
            <div class="fs-5 fs-md-4 fw-bold text-dark my-1">{{ number_format($stats['cash_count']) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">Counter receipts</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-warning h-100 rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Digital / UPI</div>
            <div class="fs-5 fs-md-4 fw-bold text-info my-1">{{ number_format($stats['online_count']) }}</div>
            <span class="small text-muted" style="font-size: 0.72rem;">UPI, Bank transfers</span>
        </div>
    </div>
</div>

<!-- Filter Bar Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.fees.index') }}" class="row g-2 align-items-center">
            @if(request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-2" placeholder="Search receipt no, student name, roll no..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="payment_mode" class="form-select rounded-2">
                    <option value="">All Payment Modes</option>
                    <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR</option>
                    <option value="bank_transfer" {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque / DD</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control rounded-2" value="{{ request('date_from') }}" title="From date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control rounded-2" value="{{ request('date_to') }}" title="To date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold rounded-2">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-2" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-list-check me-2 text-primary"></i>Fee Collections Roster
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $payments->total() }} Transactions</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete ledger
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 760px;">
                <thead>
                    <tr>
                        <th class="ps-3">Receipt No</th>
                        <th>Student Name & Roll</th>
                        <th>Course</th>
                        <th>Payment Date</th>
                        <th>Mode & Ref</th>
                        <th>Amount</th>
                        <th class="text-end pe-3">Print Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-secondary-subtle text-dark font-monospace fw-bold rounded-2">{{ $payment->receipt_no }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $payment->student?->full_name }}</div>
                            <div class="small text-muted font-monospace">{{ $payment->student?->roll_no }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-2">{{ $payment->admission?->course?->course_code ?? 'Course' }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $payment->admission?->course?->course_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-2">{{ strtoupper($payment->payment_mode) }}</span>
                            @if($payment->transaction_ref)
                            <div class="small text-muted font-monospace">{{ $payment->transaction_ref }}</div>
                            @endif
                        </td>
                        <td class="fw-bold text-success fs-6">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('centre.fees.receipt', $payment->paymentReceipt?->id ?? $payment->id) }}" class="btn btn-sm btn-outline-success rounded-2 fw-semibold" target="_blank" title="Print Official Receipt">
                                <i class="fa-solid fa-print me-1"></i>Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Fee Payments Recorded</h5>
                            <p class="small text-muted mb-3">Collect tuition fees from admitted students to issue computerized receipts.</p>
                            <a href="{{ route('centre.fees.create') }}" class="btn btn-success btn-sm rounded-2 px-3 fw-semibold">
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i>Collect First Payment
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="p-3 border-top">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
