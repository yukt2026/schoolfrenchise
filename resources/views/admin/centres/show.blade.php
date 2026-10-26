@extends('layouts.admin')

@section('title', 'Centre Dossier: ' . $centre->centre_name)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.index') }}">Centres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $centre->centre_code }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h2 class="h4 fw-bold mb-0 text-dark">{{ $centre->centre_name }}</h2>
                <span class="badge bg-primary font-monospace">{{ $centre->centre_code }}</span>
                @if($centre->status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                @elseif($centre->status === 'suspended')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Suspended</span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">{{ ucfirst($centre->status) }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.centres.certificate', $centre) }}" target="_blank" class="btn btn-outline-warning shadow-sm">
                <i class="fa-solid fa-certificate me-1"></i> Affiliation Certificate
            </a>
            <a href="{{ route('admin.centres.edit', $centre) }}" class="btn btn-outline-primary shadow-sm">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Particulars
            </a>
            <a href="{{ route('admin.centres.index') }}" class="btn btn-light border shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> All Centres
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Centre Performance KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center h-100">
                <div class="text-muted small fw-bold text-uppercase">Students</div>
                <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_students']) }}</div>
                <div class="small text-muted">Registered</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center h-100">
                <div class="text-muted small fw-bold text-uppercase">Admissions</div>
                <div class="h4 fw-bold text-primary mb-0">{{ number_format($stats['total_admissions']) }}</div>
                <div class="small text-muted">Enrolled Courses</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center h-100">
                <div class="text-muted small fw-bold text-uppercase">Study Batches</div>
                <div class="h4 fw-bold text-info mb-0">{{ number_format($stats['total_batches']) }}</div>
                <div class="small text-muted">Active Shifts</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center h-100">
                <div class="text-muted small fw-bold text-uppercase">Fees Collected</div>
                <div class="h4 fw-bold text-success mb-0">₹{{ number_format($stats['total_collected_fees'], 2) }}</div>
                <div class="small text-muted">Total Collections</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center h-100">
                <div class="text-muted small fw-bold text-uppercase">Wallet Balance</div>
                <div class="h4 fw-bold text-warning mb-0">₹{{ number_format($stats['wallet_balance'], 2) }}</div>
                <div class="small text-muted">Credits Available</div>
            </div>
        </div>
    </div>

    <!-- Tabbed Dossier Sections -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs mx-3 mt-2" id="centreTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                        <i class="fa-solid fa-id-badge me-1"></i> Centre Particulars
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">
                        <i class="fa-solid fa-user-graduate me-1"></i> Enrolled Students ({{ $stats['total_students'] }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="batches-tab" data-bs-toggle="tab" data-bs-target="#batches" type="button" role="tab">
                        <i class="fa-solid fa-calendar-check me-1"></i> Batches & Shifts ({{ $stats['total_batches'] }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab">
                        <i class="fa-solid fa-users-gear me-1"></i> Staff Logins ({{ $centre->staffUsers->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="centreTabsContent">
                <!-- TAB 1: Profile & Particulars -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Institutional Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Affiliation Code</span>
                                    <div class="font-monospace fw-bold text-primary">{{ $centre->centre_code }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Centre Name</span>
                                    <div class="fw-semibold text-dark">{{ $centre->centre_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Affiliation Date</span>
                                    <div class="text-dark">{{ $centre->affiliation_date?->format('d M, Y') ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Affiliation Valid Upto</span>
                                    <div class="text-dark">{{ $centre->valid_upto?->format('d M, Y') ?? 'Permanent' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Lab Hardware Capacity</span>
                                    <div class="text-dark"><i class="fa-solid fa-desktop text-muted me-1"></i>{{ $centre->lab_capacity }} Workstations</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Centre Director / Head</span>
                                    <div class="fw-semibold text-dark">{{ $centre->director_name }}</div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Communication & Address</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Primary Mobile</span>
                                    <div class="text-dark">{{ $centre->mobile }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Alternate Mobile</span>
                                    <div class="text-dark">{{ $centre->alternate_mobile ?? 'None' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">Email Address</span>
                                    <div class="text-dark">{{ $centre->email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold">City / District / State</span>
                                    <div class="text-dark">{{ $centre->city }}, {{ $centre->district?->name }}, {{ $centre->state?->name }} - {{ $centre->pincode }}</div>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted small fw-bold">Physical Facility Address</span>
                                    <div class="p-3 bg-light rounded text-dark mt-1">{{ $centre->address }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border bg-light rounded-3 p-3 text-center mb-3">
                                <span class="text-muted small fw-bold mb-2">Director Photograph</span>
                                @if($centre->director_photo)
                                    <img src="{{ $centre->director_photo_url ?? asset('storage/' . $centre->director_photo) }}" alt="Director" class="rounded-circle mx-auto border shadow-sm mb-2" width="100" height="100" style="object-fit: cover;" onerror="this.onerror=null;this.src='https://placehold.co/120x120?text=Photo';">
                                @else
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 100px; height: 100px;">
                                        <i class="fa-solid fa-user-tie fa-3x"></i>
                                    </div>
                                @endif
                                <div class="fw-bold text-dark">{{ $centre->director_name }}</div>
                                <div class="small text-muted">Centre Director / Head</div>
                                @if($centre->director_signature)
                                    <div class="mt-3 pt-2 border-top">
                                        <span class="text-muted small fw-bold d-block mb-1">Director Signature</span>
                                        <img src="{{ $centre->director_signature_url ?? asset('storage/' . $centre->director_signature) }}" alt="Signature" class="border bg-white p-1 rounded" height="40" style="max-width: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/140x40?text=Signature';">
                                    </div>
                                @endif
                            </div>

                            <div class="card border rounded-3 p-3">
                                <span class="text-muted small fw-bold mb-2">Portal Access Status</span>
                                @if($centre->user)
                                    <div class="small mb-1"><strong>Login Email:</strong> {{ $centre->user->email }}</div>
                                    <div class="small mb-2"><strong>Status:</strong>
                                        @if($centre->user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Disabled</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">Role: <span class="badge bg-secondary font-monospace">{{ $centre->user->role }}</span></div>
                                @else
                                    <div class="small text-muted">No linked portal user found.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Students -->
                <div class="tab-pane fade" id="students" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Recently Registered Students</h6>
                        <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-user-plus me-1"></i> Add Student
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Roll / Reg No</th>
                                    <th>Student Name</th>
                                    <th>Contact</th>
                                    <th>Admitted Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $st)
                                    <tr>
                                        <td>
                                            <span class="font-monospace fw-bold text-primary">{{ $st->roll_no }}</span>
                                            <div class="small text-muted font-monospace">{{ $st->registration_no }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $st->full_name }}</div>
                                            <div class="small text-muted">S/D of {{ $st->father_name }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $st->mobile }}</div>
                                            <div class="small text-muted">{{ $st->email }}</div>
                                        </td>
                                        <td>{{ $st->created_at?->format('d M, Y') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ ucfirst($st->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.students.show', $st) }}" class="btn btn-sm btn-light border text-primary">
                                                <i class="fa-solid fa-eye"></i> Dossier
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No students currently enrolled under this centre.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: Batches & Shifts -->
                <div class="tab-pane fade" id="batches" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Study Batches & Class Shifts</h6>
                        <a href="{{ route('admin.batches.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-plus-circle me-1"></i> Add Batch
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Batch Title</th>
                                    <th>Course</th>
                                    <th>Shift Timings</th>
                                    <th>Days</th>
                                    <th class="text-center">Enrolled</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $bt)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $bt->batch_name }}</td>
                                        <td>{{ $bt->course?->course_name ?? 'All Courses' }}</td>
                                        <td class="font-monospace small">{{ $bt->start_time }} - {{ $bt->end_time }}</td>
                                        <td class="small">{{ $bt->days_schedule }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $bt->students_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ ucfirst($bt->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.batches.show', $bt) }}" class="btn btn-sm btn-light border text-primary">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No study batches currently created for this centre.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 4: Staff Users -->
                <div class="tab-pane fade" id="staff" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Authorized Centre Staff Accounts</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Email / Login</th>
                                    <th>Mobile</th>
                                    <th>Assigned Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($centre->staffUsers as $su)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $su->name }}</td>
                                        <td>{{ $su->email }}</td>
                                        <td>{{ $su->mobile ?? 'N/A' }}</td>
                                        <td><span class="badge bg-secondary font-monospace">{{ $su->role }}</span></td>
                                        <td>
                                            @if($su->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Disabled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Only primary director account ({{ $centre->email }}) is currently registered for this branch.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
