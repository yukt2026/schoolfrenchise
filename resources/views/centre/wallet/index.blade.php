@extends('layouts.centre')

@section('title', 'Franchise Wallet & Ledger - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-wallet text-warning me-2"></i>Franchise Wallet & Ledger
        </h3>
        <p class="text-muted small mb-0">Study centre prepaid wallet for candidate admission fee, student kit deductions, and top-up recharges.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.wallet.topup') }}" class="btn btn-warning text-dark btn-sm fw-bold rounded-2 px-3 shadow-sm">
            <i class="fa-solid fa-plus-circle me-1"></i>Request Top-up
        </a>
    </div>
</div>

<!-- Wallet Balance & Metrics Strip -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm text-white h-100 p-3 p-md-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-white-50 text-uppercase fw-semibold" style="font-size: 0.7rem;">Current Balance</span>
                <span class="badge {{ $wallet->status === 'active' ? 'bg-success' : 'bg-danger' }} rounded-2 font-monospace">
                    {{ ucfirst($wallet->status) }}
                </span>
            </div>
            <h2 class="fw-bolder text-warning my-1 font-monospace">₹{{ number_format($wallet->balance, 2) }}</h2>
            <div class="small text-white-50 mb-3" style="font-size: 0.75rem;">Instant credit for student registration & kit allocations</div>
            <a href="{{ route('centre.wallet.topup') }}" class="btn btn-warning text-dark btn-sm fw-bold rounded-2 w-100 mt-auto shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Recharge Balance
            </a>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-2 g-md-3 h-100">
            <div class="col-6">
                <div class="stat-card p-3 bg-white h-100 border-start border-4 border-success rounded-3 shadow-sm">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Total Credited</div>
                    <h4 class="fw-bold text-success my-1 font-monospace fs-5">₹{{ number_format($wallet->total_credited, 2) }}</h4>
                    <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Approved top-up recharges</span>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card p-3 bg-white h-100 border-start border-4 border-primary rounded-3 shadow-sm">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Total Debited</div>
                    <h4 class="fw-bold text-dark my-1 font-monospace fs-5">₹{{ number_format($wallet->total_debited, 2) }}</h4>
                    <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Admissions & exam fee settlements</span>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card p-3 bg-white h-100 border-start border-4 border-warning rounded-3 shadow-sm">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Pending Requests</div>
                    <h4 class="fw-bold text-warning-emphasis my-1 font-monospace fs-5">{{ $stats['pending_requests'] }}</h4>
                    <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">₹{{ number_format($stats['pending_amount'], 2) }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card p-3 bg-white h-100 border-start border-4 border-info rounded-3 shadow-sm">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Centre Code</div>
                    <h4 class="fw-bold text-primary font-monospace my-1 fs-5">{{ $centre->centre_code }}</h4>
                    <span class="small text-muted text-truncate d-block" style="font-size: 0.72rem;">{{ $centre->centre_name }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Transactions Table Card -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Wallet Transaction Ledger</h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $transactions->total() }} Records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete ledger
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 740px;">
                <thead>
                    <tr>
                        <th class="ps-3">Date & Time</th>
                        <th>Reference / UTR</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th class="text-end pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold text-dark">{{ $txn->created_at->format('d M, Y') }}</div>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $txn->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border font-monospace rounded-2">{{ $txn->reference_no ?? 'TRX-' . $txn->id }}</span>
                        </td>
                        <td>
                            @if($txn->type === 'credit')
                            <span class="badge bg-success-subtle text-success rounded-2">
                                <i class="fa-solid fa-arrow-down-left me-1"></i>Credit
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger rounded-2">
                                <i class="fa-solid fa-arrow-up-right me-1"></i>Debit
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small fw-semibold">{{ $txn->description }}</div>
                        </td>
                        <td class="font-monospace fw-bold {{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                        </td>
                        <td class="font-monospace fw-semibold text-dark">
                            ₹{{ number_format($txn->balance_after, 2) }}
                        </td>
                        <td class="text-end pe-3">
                            <span class="badge {{ $txn->status === 'completed' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-2 text-capitalize">
                                {{ $txn->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-wallet fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Wallet Transactions Yet</h5>
                            <p class="small text-muted mb-0">Recharge your wallet balance or enroll students to record transactions.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-3 border-top">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
