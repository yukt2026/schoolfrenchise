@extends('layouts.admin')

@section('title', 'Photo Gallery & Campus Visuals')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-camera text-primary me-2"></i>Campus Visuals & Photo Gallery
            </h2>
            <p class="text-muted small mb-0">Showcase computer labs, convocation ceremonies, franchise meets, and banners.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> CMS Hub
            </a>
            <a href="{{ route('admin.cms.gallery.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Photo
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.cms.gallery') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search caption / title..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Visibility</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hidden (Inactive)</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.cms.gallery') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row g-4 mb-4">
        @forelse($items as $item)
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white overflow-hidden">
                    <div class="position-relative" style="height: 180px; background: #f8f9fa;">
                        @if($item->image_path)
                            <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->title }}" onerror="this.onerror=null;this.src='https://placehold.co/600x400?text=Photo';">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="fa-solid fa-image fa-2x opacity-25"></i>
                            </div>
                        @endif

                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75">
                            {{ $item->category }}
                        </span>

                        <span class="position-absolute top-0 end-0 m-2">
                            <form action="{{ route('admin.cms.gallery.toggle-status', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="badge border-0 {{ $item->status ? 'bg-success' : 'bg-secondary' }}" style="cursor: pointer;">
                                    {{ $item->status ? 'Active' : 'Hidden' }}
                                </button>
                            </form>
                        </span>
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                            <div class="small text-muted">Sort Priority: <span class="fw-semibold">{{ $item->sort_order }}</span></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small text-muted font-monospace">#{{ $item->id }}</span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.cms.gallery.edit', $item) }}" class="btn btn-light border text-dark" title="Edit Caption">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.cms.gallery.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this gallery photo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light border text-danger" title="Delete Photo">
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
                <div class="card border-0 shadow-sm rounded-3 bg-white text-center py-5">
                    <i class="fa-solid fa-images fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-dark">No Gallery Photos Found</h6>
                    <p class="text-muted small mb-3">Upload high-resolution photographs to showcase your computer labs and events.</p>
                    <div>
                        <a href="{{ route('admin.cms.gallery.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload First Photo
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
