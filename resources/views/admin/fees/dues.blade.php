@extends('layouts.admin')

@section('title', 'Outstanding Fee Dues Roster')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-hand-holding-dollar me-2" style="color: #0891b2;"></i>Outstanding Fee Dues Roster
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.fees.index') }}">Fees</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Outstanding Dues</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-receipt me-1"></i> Payment Ledger
            </a>
            <a href="{{ route('admin.fees.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-plus-circle me-1"></i> Collect Payment
            </a>
        </div>
    </div>

    <!-- Outstanding Summary Banner -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 text-white" style="background: linear-gradient(135deg, #0e7490, #0891b2);">
        <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-uppercase small fw-semibold opacity-75">Cumulative Outstanding Receivables</div>
                <h2 class="fw-bold my-1">₹{{ number_format($totalOutstanding, 2) }}</h2>
                <div class="small opacity-75">Across {{ $admissions->total() }} active candidate accounts</div>
            </div>
            <div>
                <a href="{{ route('admin.fees.create') }}" class="btn btn-light btn-lg text-primary fw-bold shadow-sm">
                    <i class="fa-solid fa-cash-register me-2"></i> Receive Installment Now
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.fees.dues') }}" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Student name, Roll or Reg No..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-select bg-light">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->course_name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="centre_id" class="form-select bg-light">
                        <option value="">All Centres</option>
                        @foreach($centres as $ctr)
                            <option value="{{ $ctr->id }}" {{ request('centre_id') == $ctr->id ? 'selected' : '' }}>
                                {{ $ctr->centre_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 px-0" style="background-color: #0891b2; border-color: #0891b2;" title="Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.fees.dues') }}" class="btn btn-outline-secondary px-2" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Dues Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-ol me-2 text-warning"></i>Accounts with Pending Balances ({{ $admissions->total() }})
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Student Particulars</th>
                        <th>Course & Centre</th>
                        <th>Net Fee</th>
                        <th>Paid to Date</th>
                        <th>Remaining Due</th>
                        <th style="width: 180px;">Collection Progress</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissions as $adm)
                        @php
                            $due = max(0, (float) $adm->net_fee - (float) $adm->total_paid);
                            $pct = (float) $adm->net_fee > 0 ? min(100, round(((float) $adm->total_paid / (float) $adm->net_fee) * 100, 1)) : 0;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $adm->student->full_name ?? 'N/A' }}</div>
                                <div class="small text-muted font-monospace">
                                    Roll: {{ $adm->student->roll_no ?? 'N/A' }} | Reg: {{ $adm->student->registration_no ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $adm->course->course_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $adm->centre->centre_name ?? 'Main Head Office' }}</small>
                            </td>
                            <td class="fw-semibold text-secondary">₹{{ number_format($adm->net_fee, 2) }}</td>
                            <td class="fw-bold text-success">₹{{ number_format($adm->total_paid, 2) }}</td>
                            <td>
                                <span class="fw-bold fs-6 text-danger">₹{{ number_format($due, 2) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="small fw-bold text-muted">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.fees.schedule', $adm->id) }}" class="btn btn-outline-secondary" title="Configure Instalment Schedule & Concessions">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </a>
                                    <a href="{{ route('admin.fees.create', ['admission_id' => $adm->id]) }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                        <i class="fa-solid fa-cash-register me-1"></i> Collect
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-circle-check fs-1 text-success opacity-50"></i></div>
                                <h6>No Outstanding Dues!</h6>
                                <p class="small mb-0">All active student accounts are completely settled.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admissions->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $admissions->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
