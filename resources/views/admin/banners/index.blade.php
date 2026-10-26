@extends('layouts.admin')

@section('title', 'Hero Banners & Home Slider Management - TECH ERA COMPUTER ERP')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hero Banners</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-images text-primary me-2"></i>Hero Banners & Slider Manager
            </h2>
            <p class="text-muted small mb-0">Upload, publish, re-order, and manage graphic banners displayed on the homepage slider.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Site
            </a>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Add New Banner
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Banners</div>
                            <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</div>
                            <div class="text-muted small mt-1">Configured in system</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                            <i class="fa-solid fa-images fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Active On Slider</div>
                            <div class="h3 fw-bold text-success mb-0">{{ number_format($stats['active']) }}</div>
                            <div class="text-muted small mt-1">Live & visible to visitors</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                            <i class="fa-solid fa-circle-check fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Hidden / Inactive</div>
                            <div class="h3 fw-bold text-secondary mb-0">{{ number_format($stats['inactive']) }}</div>
                            <div class="text-muted small mt-1">Saved as drafts / off-season</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-circle">
                            <i class="fa-solid fa-eye-slash fa-fw fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.banners.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by banner title, caption or URL..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Visibility Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only (Live)</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only (Hidden)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-light border text-muted" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Banners Grid -->
    <div class="row g-4 mb-4">
        @forelse($banners as $banner)
            <div class="col-md-6 col-xl-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white overflow-hidden banner-admin-card">
                    <!-- Image Preview Header -->
                    <div class="position-relative bg-dark" style="height: 220px; overflow: hidden;">
                        <img src="{{ $banner->image_url }}" 
                             class="w-100 h-100 object-fit-cover" 
                             alt="{{ $banner->title }}"
                             onerror="this.onerror=null;this.src='https://placehold.co/1200x500?text=Banner+Graphic';">

                        <!-- Badges Overlay -->
                        <span class="position-absolute top-0 start-0 m-2.5 badge bg-dark bg-opacity-80 px-2 py-1 shadow-sm">
                            <i class="fa-solid fa-arrow-down-1-9 me-1 text-warning"></i> Order: {{ $banner->sort_order }}
                        </span>

                        <span class="position-absolute top-0 end-0 m-2.5">
                            <form action="{{ route('admin.banners.toggle-status', $banner) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="badge border-0 {{ $banner->status ? 'bg-success' : 'bg-secondary' }} px-2 py-1 shadow-sm" style="cursor: pointer;" title="Click to toggle status">
                                    <i class="fa-solid {{ $banner->status ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1"></i>
                                    {{ $banner->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </span>

                        @if($banner->mobile_image_path)
                            <span class="position-absolute bottom-0 start-0 m-2.5 badge bg-info bg-opacity-90 shadow-sm">
                                <i class="fa-solid fa-mobile-screen me-1"></i> Mobile Image Set
                            </span>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-1 fs-6 text-truncate" title="{{ $banner->title }}">
                                {{ $banner->title }}
                            </h5>
                            @if($banner->subtitle)
                                <p class="text-muted small mb-2 text-truncate">{{ $banner->subtitle }}</p>
                            @endif

                            <div class="small text-muted d-flex align-items-center gap-1 mb-2">
                                <i class="fa-solid fa-link text-primary"></i>
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" target="{{ $banner->link_target }}" class="text-primary text-decoration-none text-truncate" style="max-width: 320px;">
                                        {{ $banner->link_url }}
                                    </a>
                                @else
                                    <span class="text-muted fst-italic">No redirect link configured (Display only)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small text-muted font-monospace">#ID: {{ $banner->id }}</span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-light border text-dark" title="Edit Banner Details">
                                    <i class="fa-solid fa-pen-to-square me-1 text-primary"></i> Edit
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this banner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light border text-danger" title="Delete Banner">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white">
                    <div class="text-muted mb-3">
                        <i class="fa-solid fa-images fa-3x opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Banners Found</h5>
                    <p class="text-muted small mx-auto" style="max-width: 420px;">
                        No custom hero banners are currently uploaded. You can add high-impact graphic banners to showcase admission open announcements, courses, and franchise invitations.
                    </p>
                    <div>
                        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-plus-circle me-1"></i> Upload Your First Banner
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($banners->hasPages())
        <div class="d-flex justify-content-center">
            {{ $banners->links() }}
        </div>
    @endif
</div>
@endsection
