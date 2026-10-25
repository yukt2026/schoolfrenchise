@extends('layouts.centre')

@section('title', 'Outstanding Dues Report - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Collections
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i>Outstanding Dues Report
        </h3>
        <p class="text-muted small mb-0">List of admitted students with remaining fee installments at study centre {{ $centre->centre_name }} ({{ $centre->centre_code }}).</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-print me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- Total Dues Highlight Card -->
<div class="card border-0 rounded-3 shadow-sm mb-4 border-start border-4 border-danger">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Centre Outstanding Balance</div>
                <h2 class="fw-bold text-danger my-1 font-monospace">₹{{ number_format($totalOutstanding, 2) }}</h2>
                <span class="small text-muted" style="font-size: 0.75rem;">Across {{ $admissionsWithDues->total() }} candidate accounts with pending installments</span>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('centre.fees.create') }}" class="btn btn-success btn-sm rounded-2 px-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-hand-holding-dollar me-1"></i>Record Fee Collection
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dues Table Card -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-users-line me-2 text-primary"></i>Candidates With Outstanding Balance
        </h6>
        <span class="badge bg-light text-dark border font-monospace">Count: {{ $admissionsWithDues->total() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete table
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 740px;">
                <thead>
                    <tr>
                        <th class="ps-3">Roll & Registration No</th>
                        <th>Student Name & Contact</th>
                        <th>Course</th>
                        <th>Net Course Fee</th>
                        <th>Amount Paid</th>
                        <th>Outstanding Due</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissionsWithDues as $adm)
                    @php $due = max(0, $adm->net_fee - $adm->total_paid); @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold rounded-2">{{ $adm->student?->roll_no }}</span>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $adm->student?->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $adm->student?->full_name }}</div>
                            <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $adm->student?->mobile }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-2">{{ $adm->course?->course_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 160px;">{{ $adm->course?->course_name }}</div>
                        </td>
                        <td class="font-monospace">₹{{ number_format($adm->net_fee, 2) }}</td>
                        <td class="text-success fw-bold font-monospace">₹{{ number_format($adm->total_paid, 2) }}</td>
                        <td class="text-danger fw-bold fs-6 font-monospace">₹{{ number_format($due, 2) }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('centre.fees.create', ['admission_id' => $adm->id]) }}" class="btn btn-sm btn-success rounded-2 px-3 fw-semibold">
                                <i class="fa-solid fa-indian-rupee-sign me-1"></i>Collect Fee
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-success">
                            <i class="fa-solid fa-circle-check fs-1 text-success mb-3 d-block"></i>
                            <h5>No Outstanding Dues!</h5>
                            <p class="small text-muted mb-0">All admitted students at this centre have completely settled their course fees.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admissionsWithDues->hasPages())
        <div class="p-3 border-top">
            {{ $admissionsWithDues->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
