@extends('layouts.admin')

@section('title', 'Franchise & Centre Management')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Centres & Franchises</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-building-columns text-primary me-2"></i>Franchise & Branch Management
            </h2>
            <p class="text-muted small mb-0">Oversee affiliated institute centres, track infrastructure capacity, and manage franchise partnerships.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.centres.applications') }}" class="btn btn-outline-warning position-relative shadow-sm">
                <i class="fa-solid fa-file-signature me-1"></i> Franchise Applications
                @if($stats['pending_applications'] > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $stats['pending_applications'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.centres.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Register New Centre
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Total Centres</span>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_centres']) }}</h3>
                    <div class="text-muted small mt-1">Affiliated branch networks</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Active Franchises</span>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-success mb-0">{{ number_format($stats['active_centres']) }}</h3>
                    <div class="text-muted small mt-1">Operational institutes</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Pending Applications</span>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-warning mb-0">{{ number_format($stats['pending_applications']) }}</h3>
                    <div class="text-muted small mt-1">Awaiting verification</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Combined Lab PCs</span>
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 text-info">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-info mb-0">{{ number_format($stats['total_lab_capacity']) }}</h3>
                    <div class="text-muted small mt-1">Installed computer terminals</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.centres.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search code, centre, director, mobile..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="state_id" class="form-select form-select-sm">
                        <option value="">All States</option>
                        @foreach($states as $st)
                            <option value="{{ $st->id }}" {{ request('state_id') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'state_id', 'status']))
                        <a href="{{ route('admin.centres.index') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Centres Table Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Affiliated Centres Directory <span class="badge bg-light text-dark fw-normal border ms-1">{{ $centres->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Centre Info</th>
                        <th>Director Particulars</th>
                        <th>Location</th>
                        <th class="text-center">Lab PCs</th>
                        <th class="text-center">Enrolled</th>
                        <th>Affiliation Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($centres as $centre)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if($centre->centre_logo)
                                        <img src="{{ $centre->centre_logo_url ?? asset('storage/' . $centre->centre_logo) }}" alt="Logo" class="rounded border" width="36" height="36" style="object-fit: contain;" onerror="this.onerror=null;this.src='https://placehold.co/72x72?text=Logo';">
                                    @else
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 36px; height: 36px;">
                                            <i class="fa-solid fa-building-columns"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.centres.show', $centre) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $centre->centre_name }}
                                        </a>
                                        <div class="small text-primary font-monospace fw-semibold">{{ $centre->centre_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $centre->director_name }}</div>
                                <div class="small text-muted">
                                    <i class="fa-solid fa-phone me-1 small"></i>{{ $centre->mobile }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $centre->city }}</div>
                                <div class="small text-muted">{{ $centre->state?->name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-monospace">
                                    <i class="fa-solid fa-desktop me-1 text-muted"></i>{{ $centre->lab_capacity }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary font-monospace">
                                    <i class="fa-solid fa-user-graduate me-1"></i>{{ $centre->students_count }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $centre->affiliation_date?->format('d M, Y') ?? 'N/A' }}</div>
                                @if($centre->valid_upto)
                                    <div class="small text-muted">Valid till {{ $centre->valid_upto->format('M Y') }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($centre->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-check me-1 small"></i>Active
                                    </span>
                                @elseif($centre->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-ban me-1 small"></i>Suspended
                                    </span>
                                @elseif($centre->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-clock me-1 small"></i>Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        {{ ucfirst($centre->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.centres.show', $centre) }}" class="btn btn-light border text-primary" title="View Dossier">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.centres.certificate', $centre) }}" target="_blank" class="btn btn-light border text-warning" title="Franchise Certificate">
                                        <i class="fa-solid fa-certificate"></i>
                                    </a>
                                    <a href="{{ route('admin.centres.edit', $centre) }}" class="btn btn-light border text-dark" title="Edit Centre">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-light border dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <form action="{{ route('admin.centres.toggle-status', $centre) }}" method="POST">
                                                @csrf
                                                @if($centre->status === 'active')
                                                    <input type="hidden" name="status" value="suspended">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa-solid fa-ban me-2"></i> Suspend Centre
                                                    </button>
                                                @else
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="fa-solid fa-circle-check me-2"></i> Activate Centre
                                                    </button>
                                                @endif
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.centres.destroy', $centre) }}" method="POST" onsubmit="return confirm('Archive this centre? Students and records will be retained in history.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Archive Centre
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-building-columns fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Affiliated Centres Found</h6>
                                    <p class="small text-muted mb-3">No centre records matched your current query or filter criteria.</p>
                                    <a href="{{ route('admin.centres.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Register New Centre
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($centres->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $centres->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
