@extends('layouts.student')

@section('title', 'Study Materials - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Study Materials</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-folder-open text-primary me-2"></i>Study Materials & E-Books
        </h2>
        <p class="text-muted small mb-0">Access approved notes, syllabi, practical guides, and reference documents for your enrolled course.</p>
    </div>
    @if($admission && $admission->course)
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-1 fs-7">
            <i class="fa-solid fa-graduation-cap me-1"></i> {{ $admission->course->course_name }}
        </span>
    @endif
</div>

<!-- Search & Filter Bar -->
<div class="card border border-light-subtle shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('student.study-materials') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search title or topics..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="type" class="form-select form-select-sm bg-light">
                    <option value="">All Material Types</option>
                    <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="notes" {{ request('type') == 'notes' ? 'selected' : '' }}>Lecture Notes</option>
                    <option value="assignment" {{ request('type') == 'assignment' ? 'selected' : '' }}>Assignment / Lab Sheet</option>
                    <option value="syllabus" {{ request('type') == 'syllabus' ? 'selected' : '' }}>Syllabus / Guide</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video / Tutorial Link</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-2 w-100 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('type'))
                    <a href="{{ route('student.study-materials') }}" class="btn btn-sm btn-outline-secondary rounded-2" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Materials Grid -->
@if($materials->count() > 0)
    <div class="row g-2 g-md-4">
        @foreach($materials as $material)
            <div class="col-6 col-md-6 col-lg-4">
                <div class="card h-100 border border-light-subtle shadow-sm rounded-3 hover-shadow transition-all bg-white">
                    <div class="card-body p-2.5 p-sm-3 p-md-4 d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between mb-2 mb-md-3">
                            <div class="rounded-2 p-2 p-md-3 {{ $material->material_type === 'video' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                @if($material->material_type === 'video')
                                    <i class="fa-brands fa-youtube fs-5 fs-md-4"></i>
                                @elseif($material->material_type === 'assignment')
                                    <i class="fa-solid fa-tasks fs-5 fs-md-4"></i>
                                @elseif($material->material_type === 'syllabus')
                                    <i class="fa-solid fa-book-bookmark fs-5 fs-md-4"></i>
                                @else
                                    <i class="fa-solid fa-file-pdf fs-5 fs-md-4"></i>
                                @endif
                            </div>
                            <span class="badge {{ $material->material_type === 'video' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} text-uppercase px-1.5 px-md-2 py-0.5 py-md-1 rounded-1" style="font-size: 0.65rem;">
                                {{ $material->material_type ?? 'Document' }}
                            </span>
                        </div>

                        <h6 class="fw-bold fs-6 text-dark mb-1 mb-md-2 line-clamp-2 small fs-md-6" title="{{ $material->title }}">
                            {{ $material->title }}
                        </h6>

                        @if($material->description)
                            <p class="text-muted small mb-2 mb-md-3 flex-grow-1 line-clamp-2 d-none d-sm-block" style="font-size: 0.75rem;">
                                {{ Str::limit($material->description, 90) }}
                            </p>
                        @else
                            <div class="flex-grow-1"></div>
                        @endif

                        <div class="border-top pt-2 pt-md-3 mt-auto">
                            <div class="d-flex align-items-center justify-content-between text-muted small mb-2 mb-md-3" style="font-size: 0.7rem;">
                                <span><i class="fa-regular fa-calendar me-1"></i> {{ $material->created_at->format('d M, Y') }}</span>
                                @if($material->subject)
                                    <span class="text-truncate d-none d-md-inline" style="max-width: 140px;" title="{{ $material->subject->subject_name }}">
                                        <i class="fa-solid fa-layer-group me-1"></i> {{ $material->subject->subject_name }}
                                    </span>
                                @endif
                            </div>

                            <div class="d-grid">
                                @if($material->external_url && empty($material->file_path))
                                    <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Link
                                    </a>
                                @else
                                    <a href="{{ route('student.study-materials.download', $material->id) }}" class="btn btn-sm btn-primary rounded-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-cloud-arrow-down me-1"></i> Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $materials->links() }}
    </div>
@else
    <div class="card border border-light-subtle shadow-sm rounded-3 text-center py-5 bg-white">
        <div class="card-body">
            <div class="bg-light rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fa-regular fa-folder-open text-muted fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">No Study Materials Found</h5>
            <p class="text-muted small mb-3" style="max-width: 420px; margin: 0 auto;">
                @if(request('search') || request('type'))
                    No study materials matched your search criteria. Try resetting filters.
                @else
                    Study materials, e-books, and lab assignments for your course will appear here once published by your centre instructor.
                @endif
            </p>
            @if(request('search') || request('type'))
                <a href="{{ route('student.study-materials') }}" class="btn btn-sm btn-primary rounded-2 fw-semibold px-3">
                    <i class="fa-solid fa-rotate-left me-1"></i> View All Materials
                </a>
            @endif
        </div>
    </div>
@endif
@endsection
