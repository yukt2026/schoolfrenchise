@extends('layouts.app')

@section('title', $course->course_name . ' (' . $course->course_code . ') | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Learn ' . $course->course_name . ' at ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . '. Duration: ' . $course->duration . '. Eligibility: ' . $course->eligibility . '. Check syllabus, fees, and online admission enquiry.')

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">{{ $course->category->name ?? 'Course' }}</span>
            <h1 class="display-6">{{ $course->course_name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $course->course_code }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Main Course Content -->
                <div class="col-lg-8">
                    <!-- Key Highlights Ribbon -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                        <div class="row g-3 text-center">
                            <div class="col-sm-3 col-6 border-end">
                                <small class="text-muted d-block uppercase fw-bold">Course Code</small>
                                <strong class="fs-5 text-primary">{{ $course->course_code }}</strong>
                            </div>
                            <div class="col-sm-3 col-6 border-end">
                                <small class="text-muted d-block uppercase fw-bold">Duration</small>
                                <strong class="fs-5 text-dark">{{ $course->duration }}</strong>
                            </div>
                            <div class="col-sm-3 col-6 border-end">
                                <small class="text-muted d-block uppercase fw-bold">Eligibility</small>
                                <strong class="fs-5 text-dark">{{ $course->eligibility }}</strong>
                            </div>
                            <div class="col-sm-3 col-6">
                                <small class="text-muted d-block uppercase fw-bold">Standard Fee</small>
                                <strong class="fs-5 text-success">₹{{ number_format($course->default_course_fee, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Syllabus Content -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                        <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-list-check me-2"></i>Detailed Course Curriculum</h3>
                        <div class="course-syllabus-content bg-light p-4 rounded-3 text-secondary" style="white-space: pre-line; line-height: 1.8;">
                            {{ $course->syllabus }}
                        </div>
                    </div>

                    <!-- Examination Papers & Subject Breakdown -->
                    @if($course->subjects->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                        <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-file-lines me-2"></i>Subject Papers & Examination Marks</h3>
                        <p class="text-muted small">Standardized board marks breakdown across theory, practical lab evaluation, and viva voce.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Paper Code</th>
                                        <th>Subject Name</th>
                                        <th class="text-center">Theory</th>
                                        <th class="text-center">Practical</th>
                                        <th class="text-center">Viva</th>
                                        <th class="text-center">Max Marks</th>
                                        <th class="text-center">Pass Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->subjects as $subj)
                                        <tr>
                                            <td><code>{{ $subj->subject_code }}</code></td>
                                            <td><strong>{{ $subj->subject_name }}</strong></td>
                                            <td class="text-center">{{ $subj->theory_max }}</td>
                                            <td class="text-center">{{ $subj->practical_max }}</td>
                                            <td class="text-center">{{ $subj->viva_max }}</td>
                                            <td class="text-center fw-bold">{{ $subj->total_max }}</td>
                                            <td class="text-center text-danger">{{ $subj->min_pass_marks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Certification Guarantee -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary-subtle border-start border-primary border-5">
                        <h5 class="fw-bold text-primary mb-2"><i class="fa-solid fa-award me-2"></i>Authorized ISO 9001:2015 Certification</h5>
                        <p class="text-secondary small mb-0">
                            Upon successful completion of the course syllabus and clearing the central board examination, students receive an official Certificate and Marksheet with high-security anti-counterfeit QR code verifiable across India.
                        </p>
                    </div>
                </div>

                <!-- Right Sidebar: Quick Admission Enquiry Form -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 p-4 sticky-top" style="top: 100px;">
                        <div class="text-center mb-3">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">Admissions Open</span>
                            <h4 class="fw-bold text-dark mb-0">Enquire for {{ $course->course_code }}</h4>
                            <small class="text-muted">Get fee discounts and batch timings</small>
                        </div>

                        <form action="{{ route('enquiry.admission.submit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <input type="hidden" name="subject" value="Admission Enquiry for {{ $course->course_name }} ({{ $course->course_code }})">

                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email (Optional)</label>
                                <input type="email" name="email" class="form-control" placeholder="your@email.com">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select name="state_id" class="form-select dce-state-select" data-district-target="course_district_id" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $st)
                                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">District <span class="text-danger">*</span></label>
                                    <select name="district_id" id="course_district_id" class="form-select" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Query / Message</label>
                                <textarea name="message" class="form-control" rows="2" placeholder="Ask about batch timings, fees, nearest center..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-dce-accent w-100 py-2 fw-bold">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Enquiry
                            </button>
                        </form>

                        <div class="text-center mt-3 pt-3 border-top">
                            <small class="text-muted">Or call our helpline directly:</small>
                            <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="fw-bold text-dark d-block fs-5 mt-1 text-decoration-none">
                                <i class="fa-solid fa-phone text-warning me-1"></i> {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
