@extends('layouts.app')

@section('title', 'Courses Directory | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Explore industry-demand computer courses: ADCA, DCA, BCC, CCC, Tally Prime with GST, Typing, Python, and Graphic Design with practical lab sessions and digital certificate verification at TECH ERA COMPUTER.')

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Our Certified IT & Vocational Courses</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Courses</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <!-- Filter Bar & Search -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-light">
                <form action="{{ route('courses.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by course name, code or keyword..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dce-primary w-100 fw-bold">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Courses Grid -->
            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted fs-1 mb-3"><i class="fa-solid fa-folder-open"></i></div>
                    <h4 class="fw-bold text-dark">No courses found matching your criteria</h4>
                    <p class="text-muted">Try adjusting your search keyword or clearing category filters.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-2">View All Courses</a>
                </div>
            @else
                <div class="row g-2.5 g-sm-3 g-lg-4">
                    @foreach($courses as $course)
                        @php
                            $courseImg = $course->thumbnail_url;
                        @endphp
                        <div class="col-6 col-md-6 col-lg-4">
                            <div class="dce-course-grid-card">
                                <!-- Course Thumbnail Image with Floating Badges -->
                                <div class="dce-course-img-wrap">
                                    <a href="{{ route('courses.show', $course->slug) }}" class="d-block w-100 h-100">
                                        <img src="{{ $courseImg }}" 
                                             alt="{{ $course->course_name }}" 
                                             class="dce-course-img"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset('images/courses/default-course.svg') }}';">
                                    </a>
                                    <span class="dce-course-code-badge">{{ $course->course_code }}</span>
                                    <span class="dce-course-cat-badge">{{ $course->category->name ?? 'Certified' }}</span>
                                </div>

                                <!-- Course Body -->
                                <div class="dce-course-body">
                                    <a href="{{ route('courses.show', $course->slug) }}" 
                                       class="dce-course-title-clamp" 
                                       title="{{ $course->course_name }}">
                                        {{ ucwords(strtolower($course->course_name)) }}
                                    </a>

                                    <div class="dce-course-meta-row">
                                        <span class="dce-course-pill">
                                            <i class="fa-regular fa-clock text-primary"></i> {{ $course->duration }}
                                        </span>
                                        <span class="dce-course-pill d-none d-sm-inline-flex">
                                            <i class="fa-solid fa-laptop-code text-success"></i> Practical
                                        </span>
                                    </div>

                                    <div class="dce-course-footer">
                                        <div class="dce-course-pricing">
                                            <span class="dce-course-price-lbl">Fee</span>
                                            <span class="dce-course-price">₹{{ number_format($course->default_course_fee ?? $course->course_fee ?? 0, 0) }}</span>
                                        </div>
                                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary dce-course-btn">
                                            <span>View Details</span>
                                            <i class="fa-solid fa-arrow-right small ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-5 d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
