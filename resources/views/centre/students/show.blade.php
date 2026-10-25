@extends('layouts.centre')

@section('title', 'Student Profile Dossier - ' . $student->full_name . ' | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.students.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Directory
        </a>
        <h3 class="fw-bold text-dark mb-0">{{ $student->full_name }}</h3>
        <span class="badge bg-primary rounded-2 font-monospace mt-1">ROLL NO: {{ $student->roll_no }}</span>
        <span class="badge bg-secondary-subtle text-secondary rounded-2 font-monospace mt-1">REG: {{ $student->registration_no }}</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.students.id-card', $student->id) }}" class="btn btn-outline-info rounded-2 px-3" target="_blank">
            <i class="fa-solid fa-id-card me-1"></i>Digital ID Card
        </a>
        <a href="{{ route('centre.students.admission-form', $student->id) }}" class="btn btn-outline-warning text-dark rounded-2 px-3" target="_blank">
            <i class="fa-solid fa-file-lines me-1"></i>Admission Form
        </a>
        @if($student->admissions->first())
        <a href="{{ route('centre.fees.create', ['admission_id' => $student->admissions->first()->id]) }}" class="btn btn-success rounded-2 px-3">
            <i class="fa-solid fa-indian-rupee-sign me-1"></i>Collect Fee
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Student Bio Card -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm text-center p-4 mb-4">
            <div class="position-relative d-inline-block mx-auto mb-3">
                @if($student->photo_path)
                <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="rounded-circle img-thumbnail shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 120px; height: 120px; font-size: 3rem;">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                @endif
                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success p-2 border border-white">
                    <span class="visually-hidden">Active</span>
                </span>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ $student->full_name }}</h5>
            <div class="text-muted small mb-3">Father: {{ $student->father_name }}</div>

            <div class="border-top pt-3 text-start small">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Mobile:</span>
                    <span class="fw-semibold">{{ $student->mobile }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Email:</span>
                    <span class="fw-semibold">{{ $student->email ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Date of Birth:</span>
                    <span class="fw-semibold">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d M, Y') : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Gender:</span>
                    <span class="fw-semibold text-capitalize">{{ $student->gender }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Qualification:</span>
                    <span class="fw-semibold">{{ $student->highest_qualification ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">District / State:</span>
                    <span class="fw-semibold">{{ $student->district?->name }}, {{ $student->state?->name }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-success-subtle text-success rounded-2">{{ ucfirst($student->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Academic Course, Fee Ledger & Attendance -->
    <div class="col-lg-8">
        <!-- 1. Enrolled Course & Batch Card -->
        @php $admission = $student->admissions->first(); @endphp
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Course & Batch Assignment</h6>
                @if($admission)
                <span class="badge bg-primary-subtle text-primary rounded-2">{{ ucfirst($admission->status) }}</span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($admission)
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Course Name:</div>
                        <div class="fw-bold text-dark fs-6">{{ $admission->course?->course_name }}</div>
                        <div class="font-monospace text-primary small">{{ $admission->course?->course_code }} ({{ $admission->course?->duration_months }} Months)</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Assigned Batch:</div>
                        @if($admission->batch)
                        <div class="fw-bold text-dark">{{ $admission->batch->batch_name }}</div>
                        <div class="small text-muted"><i class="fa-solid fa-clock me-1"></i>{{ $admission->batch->start_time }} - {{ $admission->batch->end_time }} ({{ $admission->batch->days }})</div>
                        @else
                        <div class="text-muted">No specific batch assigned</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Total Net Fee:</div>
                        <div class="fw-bold text-dark fs-5">₹{{ number_format($admission->net_fee, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Total Paid:</div>
                        <div class="fw-bold text-success fs-5">₹{{ number_format($admission->total_paid, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Remaining Balance:</div>
                        <div class="fw-bold text-danger fs-5">₹{{ number_format(max(0, $admission->net_fee - $admission->total_paid), 2) }}</div>
                    </div>
                </div>
                @else
                <div class="text-center py-3 text-muted">No course admission record found for this candidate.</div>
                @endif
            </div>
        </div>

        <!-- 2. Fee Payments History -->
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-success me-2"></i>Computerized Payment Receipts</h6>
                @if($admission && $admission->net_fee > $admission->total_paid)
                <a href="{{ route('centre.fees.create', ['admission_id' => $admission->id]) }}" class="btn btn-sm btn-outline-success rounded-2">
                    <i class="fa-solid fa-plus me-1"></i>Receive Payment
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                        <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete details
                    </div>
                    <table class="table table-custom align-middle mb-0" style="min-width: 580px;">
                        <thead>
                            <tr>
                                <th>Receipt No</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Amount</th>
                                <th class="text-end">Print Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->payments as $payment)
                            <tr>
                                <td class="font-monospace fw-bold text-primary">{{ $payment->receipt_no }}</td>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border rounded-2">{{ strtoupper($payment->payment_mode) }}</span></td>
                                <td class="fw-bold text-success">₹{{ number_format($payment->amount, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('centre.fees.receipt', $payment->paymentReceipt?->id ?? $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-2" target="_blank">
                                        <i class="fa-solid fa-print me-1"></i>Receipt
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Examination Results & Marksheets -->
        <div class="card border-0 rounded-3 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clipboard-check text-warning me-2"></i>Examination Results</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                        <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete details
                    </div>
                    <table class="table table-custom align-middle mb-0" style="min-width: 580px;">
                        <thead>
                            <tr>
                                <th>Exam / Session</th>
                                <th>Total Marks</th>
                                <th>Obtained</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->results ?? [] as $res)
                            <tr>
                                <td class="fw-semibold">{{ $res->exam?->exam_title ?? 'Session Exam' }}</td>
                                <td>{{ $res->total_max_marks }}</td>
                                <td class="fw-bold">{{ $res->total_marks_obtained }}</td>
                                <td>{{ $res->percentage }}%</td>
                                <td><span class="badge bg-primary rounded-2">{{ $res->grade }}</span></td>
                                <td><span class="badge {{ $res->result_status === 'passed' ? 'bg-success' : 'bg-danger' }} rounded-2">{{ ucfirst($res->result_status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No examination evaluations registered.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
