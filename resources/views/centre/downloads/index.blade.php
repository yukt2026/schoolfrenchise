@extends('layouts.centre')

@section('title', 'Resources & Downloads - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-folder-open text-primary me-2"></i>Resources & Downloads Library
        </h3>
        <p class="text-muted small mb-0">Official syllabi, course prospectuses, branding flex banners, and administrative form templates.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-primary btn-sm rounded-2 px-3 fw-semibold">
            <i class="fa-solid fa-graduation-cap me-1"></i>Student Certificates
        </a>
        <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark btn-sm fw-bold rounded-2 px-3 shadow-sm">
            <i class="fa-solid fa-certificate me-1"></i>Centre Affiliation Certificate
        </a>
    </div>
</div>

<!-- Category Filter Strip -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('centre.downloads.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-2 px-3">
                All Resources
            </a>
            @foreach($categories as $cat)
            @if(!empty($cat))
            <a href="{{ route('centre.downloads.index', ['category' => $cat]) }}" class="btn btn-sm {{ request('category') == $cat ? 'btn-primary' : 'btn-outline-secondary' }} rounded-2 px-3 text-capitalize">
                {{ str_replace('_', ' ', $cat) }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Downloads Grid -->
<div class="row g-3 g-md-4">
    <!-- Featured Resource: Centre Affiliation Certificate -->
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-2">Official</span>
                    <i class="fa-solid fa-certificate text-warning fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Affiliation Certificate</h6>
                <p class="small text-muted mb-3 d-none d-sm-block" style="font-size: 0.75rem;">Official affiliation certificate issued by Central Directorate with validity and accreditation details.</p>
                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.certificate') }}" class="btn btn-warning text-dark btn-sm fw-bold rounded-2 w-100" target="_blank">
                        <i class="fa-solid fa-print me-1"></i>Print Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Resource: Student Certificates Registry -->
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary-subtle text-primary fw-bold rounded-2">Registry</span>
                    <i class="fa-solid fa-graduation-cap text-primary fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Student Certificates</h6>
                <p class="small text-muted mb-3 d-none d-sm-block" style="font-size: 0.75rem;">Verify and reprint verified diplomas and certificates for candidates enrolled at your centre.</p>
                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-primary btn-sm fw-semibold rounded-2 w-100">
                        <i class="fa-solid fa-award me-1"></i>Browse Certificates
                    </a>
                </div>
            </div>
        </div>
    </div>

    @forelse($downloads as $item)
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 shadow-sm h-100">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-secondary-subtle text-secondary text-capitalize rounded-2">{{ str_replace('_', ' ', $item->category ?? 'Resource') }}</span>
                    <i class="fa-solid fa-file-arrow-down text-info fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->title }}</h6>
                <p class="small text-muted mb-3 d-none d-sm-block" style="font-size: 0.75rem;">{{ Str::limit($item->description, 65) }}</p>

                <div class="d-flex justify-content-between align-items-center small text-muted mb-3 d-none d-sm-flex" style="font-size: 0.7rem;">
                    <span><i class="fa-solid fa-database me-1"></i>{{ $item->file_size ?? 'Document' }}</span>
                    <span><i class="fa-solid fa-download me-1"></i>{{ $item->downloads_count }} DLs</span>
                </div>

                <div class="mt-auto">
                    <a href="{{ route('centre.downloads.file', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-2 w-100 fw-semibold">
                        <i class="fa-solid fa-download me-1"></i>Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-6 col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 shadow-sm h-100">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info-subtle text-info rounded-2">Curriculum</span>
                    <i class="fa-solid fa-book text-info fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Standard Syllabi</h6>
                <p class="small text-muted mb-3 d-none d-sm-block" style="font-size: 0.75rem;">Approved curriculum booklet for ADCA, DCA, PGDCA and Tally Prime programs.</p>
                <div class="mt-auto">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-info btn-sm rounded-2 w-100 fw-semibold">
                        <i class="fa-solid fa-eye me-1"></i>View Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($downloads->hasPages())
<div class="mt-4">
    {{ $downloads->links() }}
</div>
@endif
@endsection
