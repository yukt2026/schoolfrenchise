@extends('layouts.admin')

@section('title', 'Website Content Management System (CMS)')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Website CMS</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-globe text-primary me-2"></i>Website Content Management (CMS)
            </h2>
            <p class="text-muted small mb-0">Control public announcements, marquee scrolls, photo galleries, leads, and institute statistics.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Site
            </a>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-warning text-dark fw-bold shadow-sm">
                <i class="fa-solid fa-images me-1"></i> Manage Hero Banners
            </a>
            <a href="{{ route('admin.cms.notices.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Post Announcement
            </a>
        </div>
    </div>

    <!-- KPI Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Announcements</div>
                            <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_notices']) }}</div>
                            <div class="text-muted small mt-1">{{ number_format($stats['active_notices']) }} active ({{ $stats['marquee_count'] }} marquee)</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                            <i class="fa-solid fa-bullhorn fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Photo Gallery</div>
                            <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_photos']) }}</div>
                            <div class="text-muted small mt-1">{{ number_format($stats['active_photos']) }} published photos</div>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                            <i class="fa-solid fa-images fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Public Enquiries</div>
                            <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_enquiries']) }}</div>
                            <div class="text-muted small mt-1"><span class="badge bg-warning text-dark">{{ $stats['new_enquiries'] }}</span> pending review</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                            <i class="fa-solid fa-envelope-open-text fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">CMS Content</div>
                            <div class="h4 fw-bold text-dark mb-0">Active & Sync'd</div>
                            <div class="text-muted small mt-1">Helpline, Stats & Profile</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                            <i class="fa-solid fa-sliders fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Hub Shortcuts -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.banners.index') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none bg-white h-100 hover-shadow transition">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="text-white p-3 rounded-3" style="background: linear-gradient(135deg, #0284c7, #4f46e5) !important;">
                        <i class="fa-solid fa-images fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Hero Banners</h6>
                        <p class="text-muted small mb-0">{{ $stats['active_banners'] ?? 0 }} active slider graphics</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.cms.notices') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none bg-white h-100 hover-shadow transition">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary text-white p-3 rounded-3">
                        <i class="fa-solid fa-scroll fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Notices & Marquee</h6>
                        <p class="text-muted small mb-0">Manage running tickers & circulars</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.cms.gallery') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none bg-white h-100 hover-shadow transition">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-info text-white p-3 rounded-3">
                        <i class="fa-solid fa-camera fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Campus Photo Gallery</h6>
                        <p class="text-muted small mb-0">Labs, events & celebrations</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.cms.enquiries') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none bg-white h-100 hover-shadow transition">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning text-dark p-3 rounded-3">
                        <i class="fa-solid fa-comments fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Enquiries & Leads</h6>
                        <p class="text-muted small mb-0">Contact messages & demo requests</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.cms.content') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none bg-white h-100 hover-shadow transition">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="bg-success text-white p-3 rounded-3">
                        <i class="fa-solid fa-gear fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Page Configurations</h6>
                        <p class="text-muted small mb-0">Stats, address, helpline & socials</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Feeds Grid -->
    <div class="row g-4">
        <!-- Recent Notices -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-bullhorn me-2 text-primary"></i>Recent Notices</h6>
                    <a href="{{ route('admin.cms.notices') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Notice Title</th>
                                    <th>Target</th>
                                    <th>Marquee</th>
                                    <th class="text-end pe-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentNotices as $n)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark">{{ Str::limit($n->title, 45) }}</div>
                                            @if($n->attachment_path)
                                                <span class="badge bg-light text-muted border small"><i class="fa-solid fa-paperclip me-1"></i>PDF Attached</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase">{{ $n->target_audience }}</span>
                                        </td>
                                        <td>
                                            @if($n->is_marquee)
                                                <span class="badge bg-danger"><i class="fa-solid fa-bolt me-1"></i>Yes</span>
                                            @else
                                                <span class="badge bg-light text-muted border">No</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 font-monospace small text-muted">
                                            {{ $n->published_at ? \Carbon\Carbon::parse($n->published_at)->format('d M, Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No notices published yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inquiries -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-envelope-open-text me-2 text-warning"></i>Recent Leads & Inquiries</h6>
                    <a href="{{ route('admin.cms.enquiries') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Sender</th>
                                    <th>Subject / Type</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnquiries as $e)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark">{{ $e->name }}</div>
                                            <div class="small text-muted font-monospace">{{ $e->mobile }}</div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark">{{ Str::limit($e->subject, 30) }}</div>
                                            <span class="badge bg-light text-muted border">{{ str_replace('_', ' ', $e->type) }}</span>
                                        </td>
                                        <td>
                                            @if($e->status == 'new')
                                                <span class="badge bg-danger">New</span>
                                            @elseif($e->status == 'contacted')
                                                <span class="badge bg-info">Contacted</span>
                                            @elseif($e->status == 'converted')
                                                <span class="badge bg-success">Converted</span>
                                            @else
                                                <span class="badge bg-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 font-monospace small text-muted">
                                            {{ $e->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No public enquiries logged yet.</td>
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
