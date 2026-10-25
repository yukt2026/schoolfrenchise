@extends('layouts.centre')

@section('title', 'Centre Director Dashboard - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<!-- Header Banner Card -->
<div class="card border-0 rounded-4 shadow-sm text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark font-monospace fw-bold px-3 py-1">
                        <i class="fa-solid fa-code-branch me-1"></i>{{ $centre->centre_code }}
                    </span>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1">
                        <i class="fa-solid fa-circle-check me-1"></i>Authorized Centre
                    </span>
                </div>
                <h2 class="fw-extrabold text-white mb-2">{{ $centre->centre_name }}</h2>
                <p class="text-white-50 mb-0">
                    <i class="fa-solid fa-user-tie me-1"></i> Director: <strong>{{ $centre->director_name }}</strong> &nbsp;|&nbsp;
                    <i class="fa-solid fa-location-dot me-1"></i> {{ $centre->city }} &nbsp;|&nbsp;
                    <i class="fa-solid fa-desktop me-1"></i> Lab Capacity: {{ $centre->lab_capacity }} PCs
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 backdrop-blur d-inline-block text-start" style="min-width: 200px;">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Available Wallet</div>
                    <div class="fs-3 fw-bold text-warning mb-2">₹{{ number_format($stats['wallet_balance'], 2) }}</div>
                    <a href="{{ route('centre.wallet.topup') }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill w-100">
                        <i class="fa-solid fa-plus-circle me-1"></i>Recharge Wallet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Statistics KPI Grid -->
<div class="row g-2.5 g-sm-3 mb-4">
    <!-- Total Students -->
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Admitted Students</div>
                    <h3 class="fw-bold text-dark my-1 fs-5 fs-sm-4 fs-md-3">{{ number_format($stats['total_students']) }}</h3>
                    <a href="{{ route('centre.students.index') }}" class="text-decoration-none small text-primary fw-semibold" style="font-size: 0.72rem;">
                        Directory <i class="fa-solid fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0; font-size: 0.95rem;">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Batches -->
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 h-100 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Active Batches</div>
                    <h3 class="fw-bold text-dark my-1 fs-5 fs-sm-4 fs-md-3">{{ number_format($stats['active_batches']) }}</h3>
                    <a href="{{ route('centre.batches.index') }}" class="text-decoration-none small text-info fw-semibold" style="font-size: 0.72rem;">
                        Daily Shifts <i class="fa-solid fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0; font-size: 0.95rem;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Fee Collected -->
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Fee Collections</div>
                    <h3 class="fw-bold text-success my-1 fs-6 fs-sm-5 fs-md-4">₹{{ number_format($stats['fee_collected'], 2) }}</h3>
                    <a href="{{ route('centre.fees.index') }}" class="text-decoration-none small text-success fw-semibold" style="font-size: 0.72rem;">
                        Receipts <i class="fa-solid fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0; font-size: 0.95rem;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Exams -->
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Upcoming Exams</div>
                    <h3 class="fw-bold text-dark my-1 fs-5 fs-sm-4 fs-md-3">{{ number_format($stats['upcoming_exams']) }}</h3>
                    <a href="{{ route('centre.exams.index') }}" class="text-decoration-none small text-warning-emphasis fw-semibold" style="font-size: 0.72rem;">
                        Hall Tickets <i class="fa-solid fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
                <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0; font-size: 0.95rem;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fast Operational Shortcuts -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3 p-lg-4">
        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i>Quick Actions</h6>
        <div class="row g-2">
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.students.create') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-user-plus fs-3 mb-2"></i>
                    <span class="small fw-bold">New Student</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.batches.index') }}" class="btn btn-outline-info w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-calendar-check fs-3 mb-2"></i>
                    <span class="small fw-bold">Mark Attendance</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.fees.create') }}" class="btn btn-outline-success w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-hand-holding-dollar fs-3 mb-2"></i>
                    <span class="small fw-bold">Collect Fee</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.exams.index') }}" class="btn btn-outline-warning text-dark w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-pen-to-square fs-3 mb-2"></i>
                    <span class="small fw-bold">Enter Marks</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.wallet.topup') }}" class="btn btn-outline-secondary w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-wallet fs-3 mb-2"></i>
                    <span class="small fw-bold">Top-up Wallet</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-outline-dark w-100 p-3 rounded-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-certificate fs-3 mb-2 text-warning"></i>
                    <span class="small fw-bold">Affiliation Cert</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Section: Recent Activity & Notices -->
<div class="row g-4">
    <!-- Left Column: Recent Students & Recent Payments -->
    <div class="col-lg-8">
        <!-- Recent Students Table -->
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-users text-primary me-2"></i>Recently Enrolled Candidates
                </h6>
                <a href="{{ route('centre.students.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold">
                    View All ({{ $stats['total_students'] }})
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentStudents->count() > 0)
                    <div class="table-scroll-hint d-md-none">
                        <i class="fa-solid fa-arrows-left-right text-primary"></i>
                        <span>Scroll horizontally to view candidates</span>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table-custom table-hover align-middle mb-0" style="min-width: 680px;">
                        <thead>
                            <tr>
                                <th class="ps-4">Roll No / Reg No</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $s)
                            @php $adm = $s->admissions->first(); @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-1">{{ $s->roll_no }}</span>
                                    <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $s->registration_no }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->full_name }}</div>
                                    <div class="small text-muted">S/O, D/O: {{ $s->father_name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-1">{{ $adm?->course?->course_code ?? 'Course' }}</span>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $adm?->course?->course_name }}</div>
                                </td>
                                <td class="text-nowrap">{{ $s->mobile }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success rounded-1 text-nowrap">{{ ucfirst($s->status) }}</span>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('centre.students.show', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-2" title="View Profile">
                                            <i class="fa-solid fa-eye me-1"></i>Profile
                                        </a>
                                        <a href="{{ route('centre.students.id-card', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-2" title="ID Card" target="_blank">
                                            <i class="fa-solid fa-id-card me-1"></i>Card
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-user-slash fs-3 d-block mb-2 text-black-50"></i>
                                    No students admitted yet. <a href="{{ route('centre.students.create') }}">Register first candidate</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Fee Collections Table -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-receipt text-success me-2"></i>Recent Computerized Fee Receipts
                </h6>
                <a href="{{ route('centre.fees.index') }}" class="btn btn-sm btn-link text-success text-decoration-none fw-semibold">
                    All Receipts
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentPayments->count() > 0)
                    <div class="table-scroll-hint d-md-none">
                        <i class="fa-solid fa-arrows-left-right text-primary"></i>
                        <span>Scroll horizontally to view fee receipts</span>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table-custom table-hover align-middle mb-0" style="min-width: 620px;">
                        <thead>
                            <tr>
                                <th class="ps-4">Receipt No</th>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Amount</th>
                                <th class="pe-4 text-end">Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <span class="badge bg-secondary-subtle text-dark font-monospace rounded-1">{{ $p->receipt_no }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->student?->full_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $p->student?->roll_no }}</div>
                                </td>
                                <td class="text-nowrap small text-muted">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $p->payment_date ? $p->payment_date->format('d M, Y') : 'N/A' }}
                                </td>
                                <td><span class="badge bg-info-subtle text-info rounded-1 text-nowrap">{{ strtoupper(str_replace('_', ' ', $p->payment_mode)) }}</span></td>
                                <td class="fw-bold text-success text-nowrap">₹{{ number_format($p->amount, 2) }}</td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a href="{{ route('centre.fees.receipt', $p->paymentReceipt?->id ?? $p->id) }}" class="btn btn-sm btn-outline-success rounded-2 fw-semibold text-nowrap" target="_blank">
                                        <i class="fa-solid fa-print me-1"></i>Receipt
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No fee payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Central Notices & Wallet Transactions -->
    <div class="col-lg-4">
        <!-- Central Board Announcements -->
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-bullhorn text-warning me-2"></i>Board Announcements
                </h6>
            </div>
            <div class="card-body p-3">
                @forelse($notices as $notice)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-primary-subtle text-primary small">{{ $notice->category ?? 'Notice' }}</span>
                        <span class="small text-muted">{{ $notice->created_at->format('d M') }}</span>
                    </div>
                    <div class="fw-bold text-dark mb-1">{{ $notice->title }}</div>
                    <p class="small text-muted mb-0">{{ Str::limit($notice->content ?? $notice->description, 100) }}</p>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No active announcements from central directorate.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Wallet Ledger Activity -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-wallet text-secondary me-2"></i>Wallet Ledger
                </h6>
                <a href="{{ route('centre.wallet.index') }}" class="btn btn-sm btn-link text-decoration-none">History</a>
            </div>
            <div class="card-body p-3">
                @forelse($recentWalletTxns as $txn)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-bold text-dark font-monospace small">#{{ $txn->reference_no }}</div>
                        <div class="small text-muted">{{ $txn->created_at->format('d M, Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold {{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                        </div>
                        <span class="badge {{ $txn->status === 'approved' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size: 0.65rem;">
                            {{ ucfirst($txn->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No wallet transactions recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
