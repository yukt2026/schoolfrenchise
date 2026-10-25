@extends('layouts.student')

@section('title', 'Student Dashboard - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="student-dashboard">
    <!-- Student Greeting & Enrolment Strip -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0b1329 0%, #0f172a 60%, #0284c7 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 58px; height: 58px; font-size: 1.5rem; border: 3px solid rgba(255,255,255,0.4);">
                            @if($student->photo_path && file_exists(public_path('storage/' . $student->photo_path)))
                                <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-100 h-100 rounded-circle object-fit-cover">
                            @else
                                {{ strtoupper(substr($student->full_name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark mb-1 px-2 py-1 fw-bold font-monospace rounded-1" style="font-size: 0.72rem;">
                                ROLL NO: {{ $student->roll_no ?? 'UNASSIGNED' }}
                            </span>
                            <h3 class="fw-bold mb-0 text-white">{{ $student->full_name }}</h3>
                            <div class="text-white-50 small mt-1">
                                Reg No: <span class="text-white font-monospace">{{ $student->registration_no }}</span> |
                                Session: <span class="text-white">{{ $student->academic_session ?? date('Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    @if($admission)
                        <span class="badge bg-success text-white px-3 py-2 rounded-2 fw-semibold">
                            <i class="fa-solid fa-circle-check me-1"></i> Course Active
                        </span>
                        <div class="text-white-50 small mt-2">
                            Batch: <strong class="text-white">{{ $admission->batch?->batch_name ?? 'Default Shift' }}</strong>
                        </div>
                    @else
                        <span class="badge bg-secondary px-3 py-2 rounded-2">Pending Admission Approval</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Top Metrics: Total Dues, Total Fees, Attendance Rate -->
    <div class="row g-2 g-md-3 mb-4">
        <!-- Card 1: Total Dues -->
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white {{ $totalDues > 0 ? 'border-start border-4 border-danger' : 'border-start border-4 border-success' }}">
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Dues</span>
                        <div class="p-1.5 p-md-2 rounded-2 {{ $totalDues > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                            <i class="fa-solid {{ $totalDues > 0 ? 'fa-hand-holding-dollar' : 'fa-check-double' }} fs-6 fs-md-5"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 fs-5 fs-sm-4 fs-md-2 {{ $totalDues > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format($totalDues, 2) }}
                        </h3>
                        @if($totalDues > 0)
                            <div class="text-muted small mb-2 d-none d-sm-block" style="font-size: 0.75rem;">Balance outstanding for active session</div>
                            <a href="{{ route('student.fees.pay') }}" class="btn btn-sm btn-danger px-2.5 px-md-3 py-1.5 rounded-2 fw-semibold w-100 w-sm-auto text-center" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-credit-card me-1"></i> Pay Now
                            </a>
                        @else
                            <div class="text-success small fw-semibold" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-circle-check me-1"></i> All dues cleared
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Fees & Paid -->
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Fees</span>
                        <div class="p-1.5 p-md-2 rounded-2 bg-primary-subtle text-primary">
                            <i class="fa-solid fa-wallet fs-6 fs-md-5"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-dark fs-5 fs-sm-4 fs-md-2">₹{{ number_format($totalFees, 2) }}</h3>
                        <div class="d-flex align-items-center justify-content-between small text-muted mb-2" style="font-size: 0.72rem;">
                            <span>Paid: <strong class="text-success">₹{{ number_format($totalPaid, 2) }}</strong></span>
                            <span>{{ $totalFees > 0 ? round(($totalPaid / $totalFees) * 100) : 0 }}%</span>
                        </div>
                        <div class="progress rounded-1" style="height: 6px;">
                            <div class="progress-bar bg-success rounded-1" role="progressbar" style="width: {{ $totalFees > 0 ? min(100, round(($totalPaid / $totalFees) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Attendance Performance -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Attendance</span>
                        <div class="p-1.5 p-md-2 rounded-2 bg-info-subtle text-info">
                            <i class="fa-solid fa-user-check fs-6 fs-md-5"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-dark fs-5 fs-sm-4 fs-md-2">{{ $attendanceRate }}%</h3>
                        <div class="text-muted small mb-2" style="font-size: 0.75rem;">
                            Attended <strong>{{ $presentSessions }}</strong> of <strong>{{ $totalSessions }}</strong> logged sessions
                        </div>
                        <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-primary px-3 py-1.5 rounded-2 fw-semibold" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-clipboard-user me-1"></i> View Attendance Ledger
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Quick Action Shortcuts Row -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('student.fees.history') }}" class="card border border-light-subtle shadow-sm rounded-3 p-2.5 p-md-3 bg-white text-decoration-none text-center h-100 hover-scale">
                <div class="rounded-2 bg-success-subtle text-success mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-receipt fs-5"></i>
                </div>
                <div class="fw-bold text-dark small">Recent Payments</div>
                <div class="text-muted d-none d-sm-block" style="font-size: 0.72rem;">Download fee receipts</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('student.id-card') }}" class="card border border-light-subtle shadow-sm rounded-3 p-2.5 p-md-3 bg-white text-decoration-none text-center h-100 hover-scale">
                <div class="rounded-2 bg-primary-subtle text-primary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-id-card fs-5"></i>
                </div>
                <div class="fw-bold text-dark small">Digital ID Card</div>
                <div class="text-muted d-none d-sm-block" style="font-size: 0.72rem;">Print & download card</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('student.admission-form') }}" class="card border border-light-subtle shadow-sm rounded-3 p-2.5 p-md-3 bg-white text-decoration-none text-center h-100 hover-scale">
                <div class="rounded-2 bg-info-subtle text-info mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-file-contract fs-5"></i>
                </div>
                <div class="fw-bold text-dark small">Admission Form</div>
                <div class="text-muted d-none d-sm-block" style="font-size: 0.72rem;">View verified dossier</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('student.study-materials') }}" class="card border border-light-subtle shadow-sm rounded-3 p-2.5 p-md-3 bg-white text-decoration-none text-center h-100 hover-scale">
                <div class="rounded-2 bg-warning-subtle text-warning mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-book-bookmark fs-5"></i>
                </div>
                <div class="fw-bold text-dark small">Study Materials</div>
                <div class="text-muted d-none d-sm-block" style="font-size: 0.72rem;">Access PDF course notes</div>
            </a>
        </div>
    </div>

    <!-- 3. Course Details & Centre Information -->
    <div class="row g-4 mb-4">
        <!-- Course Name & Duration -->
        <div class="col-12 col-lg-7">
            <div class="card border border-light-subtle shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-graduation-cap text-primary me-2"></i> Enrolled Course Details
                    </h5>
                    <a href="{{ route('student.course') }}" class="small fw-semibold text-primary text-decoration-none">
                        View Curriculum <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($admission && $admission->course)
                        <div class="p-3 rounded-3 mb-3" style="background-color: #f0f9ff; border: 1px solid #bae6fd;">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">{{ $admission->course->course_name }}</h4>
                                    <span class="badge bg-white text-primary border border-primary-subtle fw-semibold font-monospace rounded-1">
                                        {{ $admission->course->course_code }}
                                    </span>
                                </div>
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-1 small">Active Enrolment</span>
                            </div>
                            <p class="text-muted small mt-2 mb-0">{{ Str::limit($admission->course->description, 140) }}</p>
                        </div>

                        <div class="row g-2 g-md-3">
                            <div class="col-6">
                                <div class="border rounded-2 p-2.5 p-md-3 h-100">
                                    <div class="text-muted small" style="font-size: 0.72rem;"><i class="fa-regular fa-clock me-1 text-primary"></i> Course Duration</div>
                                    <div class="fw-bold text-dark mt-1 small fs-md-6">{{ $admission->course->duration ?? 'Standard' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-2 p-2.5 p-md-3 h-100">
                                    <div class="text-muted small" style="font-size: 0.72rem;"><i class="fa-solid fa-users-line me-1 text-primary"></i> Assigned Batch</div>
                                    <div class="fw-bold text-dark mt-1 small fs-md-6">{{ $admission->batch?->batch_name ?? 'Regular Shift' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-2 p-2.5 p-md-3 h-100">
                                    <div class="text-muted small" style="font-size: 0.72rem;"><i class="fa-regular fa-calendar-check me-1 text-primary"></i> Enrolment Date</div>
                                    <div class="fw-bold text-dark mt-1 small fs-md-6">{{ $admission->admission_date ? $admission->admission_date->format('d M, Y') : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-2 p-2.5 p-md-3 h-100">
                                    <div class="text-muted small" style="font-size: 0.72rem;"><i class="fa-solid fa-shield-halved me-1 text-primary"></i> Student Status</div>
                                    <div class="fw-bold text-success mt-1 small fs-md-6">{{ ucfirst($student->status ?? 'Active') }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-book-open fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">No course enrolment record is linked with your profile yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Centre / Institute Details -->
        <div class="col-12 col-lg-5">
            <div class="card border border-light-subtle shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-building-columns text-primary me-2"></i> Centre / Institute
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($student->centre)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 rounded-2 bg-primary-subtle text-primary fw-bold fs-4">
                                <i class="fa-solid fa-landmark"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $student->centre->centre_name }}</h6>
                                <span class="badge bg-light text-muted font-monospace small rounded-1">CODE: {{ $student->centre->centre_code }}</span>
                            </div>
                        </div>

                        <ul class="list-unstyled mb-0 small">
                            <li class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
                                <i class="fa-solid fa-location-dot text-primary mt-1"></i>
                                <span class="text-muted">{{ $student->centre->address ?? 'Main Campus' }}, {{ $student->centre->city ?? '' }} {{ $student->centre->pincode ?? '' }}</span>
                            </li>
                            @if($student->centre->contact_number)
                                <li class="d-flex align-items-center gap-2 py-2 border-bottom border-light">
                                    <i class="fa-solid fa-phone text-primary"></i>
                                    <span class="text-muted">{{ $student->centre->contact_number }}</span>
                                </li>
                            @endif
                            @if($student->centre->email)
                                <li class="d-flex align-items-center gap-2 py-2">
                                    <i class="fa-solid fa-envelope text-primary"></i>
                                    <span class="text-muted">{{ $student->centre->email }}</span>
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-school fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">Head Office / Central Campus, {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Recent Payments List -->
    <div class="card border border-light-subtle shadow-sm rounded-3 mb-4 bg-white overflow-hidden" id="recent-payments">
        <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                <i class="fa-solid fa-receipt text-success me-2"></i> Recent Payments
            </h5>
            <a href="{{ route('student.fees.history') }}" class="small fw-semibold text-primary text-decoration-none">
                View All Ledger <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            @if($recentPayments->count() > 0)
                <div class="table-scroll-hint d-md-none">
                    <i class="fa-solid fa-arrows-left-right text-primary"></i>
                    <span>Scroll horizontally to view details</span>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table-custom table-hover align-middle mb-0" style="min-width: 620px;">
                    <thead>
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Receipt No</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td class="ps-4 text-nowrap small text-muted">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : $payment->created_at->format('d M, Y') }}
                                </td>
                                <td>
                                    <span class="font-monospace fw-bold small text-primary">{{ $payment->receipt_no }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small text-uppercase rounded-1">
                                        {{ str_replace('_', ' ', $payment->payment_mode) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6 text-nowrap">₹{{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $pStatus = strtolower($payment->payment_status ?? ($payment->status ?? 'paid'));
                                    @endphp
                                    @if(in_array($pStatus, ['paid', 'completed', 'success', 'verified']))
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-circle-check me-1"></i> Paid
                                        </span>
                                    @elseif($pStatus === 'pending')
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-1 small text-nowrap">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> {{ ucfirst($pStatus) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('student.fees.receipt', $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3 fw-semibold text-nowrap">
                                        <i class="fa-solid fa-print me-1"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                                    <div>No payment transactions recorded yet.</div>
                                    @if($totalDues > 0)
                                        <a href="{{ route('student.fees.pay') }}" class="btn btn-sm btn-primary rounded-2 mt-2 fw-semibold px-3 py-1.5">
                                            Make First Payment
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. Study Materials & Notifications Section -->
    <div class="row g-4">
        <!-- Study Materials -->
        <!-- <div class="col-12 col-lg-7">
            <div class="card border border-light-subtle shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark fs-6 fs-md-5">
                        <i class="fa-solid fa-folder-open text-primary me-2"></i> Study Materials & Notes
                    </h5>
                    <a href="{{ route('student.study-materials') }}" class="small fw-semibold text-primary text-decoration-none">
                        View All ({{ $studyMaterials->count() }}) <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="list-group list-group-flush">
                        @forelse($studyMaterials as $mat)
                            <div class="list-group-item px-0 py-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 border-light">
                                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 w-100">
                                    <div class="p-2.5 p-sm-3 rounded-2 bg-primary-subtle text-primary flex-shrink-0">
                                        <i class="fa-solid fa-file-pdf fs-5"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $mat->title }}">{{ $mat->title }}</h6>
                                        <div class="text-muted small">
                                            {{ Str::limit($mat->description ?? 'Course syllabus study material', 60) }}
                                            @if($mat->file_size) &bull; <span class="badge bg-light text-muted border rounded-1">{{ $mat->file_size }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('student.study-materials.download', $mat->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3 fw-semibold flex-shrink-0 align-self-stretch align-self-sm-auto text-center text-nowrap">
                                    <i class="fa-solid fa-download me-1"></i> Download
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
                                <p class="mb-0">No study materials published for this course yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Notifications & Announcements -->
        <div class="col-12 col-lg-5">
            <div class="card border border-light-subtle shadow-sm rounded-3 h-100 bg-white">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-bell text-warning me-2"></i> Recent Alerts
                        </h5>
                        @if($unreadNotificationsCount > 0)
                            <span class="badge rounded-pill bg-danger">{{ $unreadNotificationsCount }} New</span>
                        @endif
                    </div>
                    <a href="{{ route('student.notifications') }}" class="small fw-semibold text-primary text-decoration-none">
                        All Alerts <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @forelse($recentNotifications as $notif)
                            <div class="list-group-item px-0 py-3 border-light {{ is_null($notif->read_at) ? 'fw-semibold' : '' }}">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <div class="text-dark small mb-1">{{ $notif->title }}</div>
                                        <div class="text-muted small fw-normal">{{ Str::limit($notif->message, 80) }}</div>
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if(is_null($notif->read_at))
                                        <span class="badge bg-primary-subtle text-primary rounded-1" style="font-size: 0.65rem;">Unread</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fa-regular fa-bell-slash fa-2x mb-2 text-secondary opacity-50"></i>
                                <p class="mb-0">No new notifications at this time.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-scale {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.15) !important;
}
</style>
@endpush
