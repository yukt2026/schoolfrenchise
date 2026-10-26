@extends('layouts.app')

@section('title', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . ' - Building Digital Skills for the Future')
@section('meta_description', 'TECH ERA COMPUTER, Sasbahana, Sheikhpura, Bihar - Leading Computer Training Institute offering ADCA, DCA, Typing, Tally Prime with GST, and Programming courses.')

@section('content')
    @if(isset($banners) && $banners->isNotEmpty())
        <!-- Promotional Graphic Banners Slider (Admin Managed) -->
        <section class="dce-promo-banner-section position-relative">
            <div id="dcePromoBannerSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4500">
                @if($banners->count() > 1)
                    <div class="carousel-indicators dce-slider-indicators">
                        @foreach($banners as $index => $banner)
                            <button type="button" data-bs-target="#dcePromoBannerSlider" data-bs-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Banner Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                <div class="carousel-inner">
                    @foreach($banners as $banner)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }} dce-graphic-banner-item">
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="{{ $banner->link_target ?? '_self' }}" class="d-block w-100 text-decoration-none">
                            @endif
                            <picture>
                                @if($banner->mobile_image_path)
                                    <source media="(max-width: 767.98px)" srcset="{{ $banner->mobile_image_url }}">
                                @endif
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="d-block w-100 dce-banner-graphic-img" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                            </picture>
                            @if($banner->link_url)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($banners->count() > 1)
                    <!-- Carousel Navigation Arrows (Desktop Only) -->
                    <button class="dce-slider-btn prev border-0 d-none d-lg-flex" type="button" data-bs-target="#dcePromoBannerSlider" data-bs-slide="prev" aria-label="Previous Banner">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="dce-slider-btn next border-0 d-none d-lg-flex" type="button" data-bs-target="#dcePromoBannerSlider" data-bs-slide="next" aria-label="Next Banner">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                @endif
            </div>
        </section>
    @endif

    <!-- 1. Hero Banner Slider Section (Original Section - Preserved) -->
    <section class="dce-slider-section position-relative">
        <div id="dceHeroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Slider Indicators (Bottom Center Dots/Pill) -->
            <div class="carousel-indicators dce-slider-indicators">
                <button type="button" data-bs-target="#dceHeroSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#dceHeroSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#dceHeroSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#dceHeroSlider" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>

            <div class="carousel-inner">
                <!-- Slide 1: General Skill & Career Education -->
                <div class="carousel-item active dce-slider-item" style="background-image: url('{{ asset('images/banners/banner1.jpg') }}');">
                    <div class="dce-slider-overlay"></div>
                    <div class="container dce-slider-content">
                        <div class="row align-items-center g-4">
                            <!-- Left Slide Content / Floating Card on Mobile -->
                            <div class="col-lg-8">
                                <div class="dce-slider-card">
                                    <div class="dce-slider-badge mb-2">
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 d-none d-lg-inline-block">SKILL EDUCATION</span>
                                        <span class="dce-mobile-badge-text">SKILL EDUCATION • PRACTICAL TRAINING • BETTER CAREERS</span>
                                    </div>
                                    <h1 class="dce-slider-title">
                                        Skill Education for <br><span class="dce-slider-highlight">A Brighter Tomorrow</span>
                                    </h1>
                                    <div class="dce-slider-tagline mb-2">
                                        <span>Learn</span> <span class="opacity-40">|</span>
                                        <span>Build Skills</span> <span class="opacity-40">|</span>
                                        <span>Get Certified</span> <span class="opacity-40">|</span>
                                        <span>Grow Your Career</span>
                                    </div>
                                    <p class="dce-slider-desc mb-3 mb-lg-4">
                                        Skill-based education for a better future. Build confidence, practical ability and career-ready knowledge with supportive training and digital student services in Sasbahana, Sheikhpura.
                                    </p>
                                    <div class="dce-slider-actions d-flex flex-wrap align-items-center gap-2 mb-3 mb-lg-4">
                                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-apply fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <span>Apply Now</span> <i class="fa-solid fa-arrow-right small ms-1"></i>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-explore fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <i class="fa-solid fa-book-open text-warning d-none d-lg-inline-block"></i> <span>Explore Courses</span>
                                        </a>
                                    </div>
                                    <!-- Micro Trust Badges Row (Desktop Only) -->
                                    <div class="dce-slider-micro-row d-none d-lg-flex flex-wrap align-items-center gap-2.5 pt-2 border-top border-white border-opacity-10">
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon"><i class="fa-solid fa-award"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Recognized</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Trusted & Certified</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8;"><i class="fa-solid fa-briefcase"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Job Oriented</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Practical Training</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;"><i class="fa-solid fa-users"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Better Career</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Brighter Future</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Side Course Shortcuts (Desktop Glass Box) -->
                            <div class="col-lg-4 d-none d-lg-block">
                                <div class="dce-slider-side-box">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                                        <span class="text-white small fw-bold text-uppercase" style="letter-spacing: 0.8px;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i> Fast Track Programs
                                        </span>
                                        <span class="badge bg-success-subtle text-success small px-2 py-0.5 rounded-pill">Open</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <span>Long Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-laptop-code"></i></div>
                                            <span>Short Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-award"></i></div>
                                            <span>NIELIT-Courses (Preparation)</span>
                                        </a>
                                        <a href="{{ route('admission.apply') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-file-signature"></i></div>
                                            <span>APPLY FOR ONLINE COURSE</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-microchip"></i></div>
                                            <span>HARDWARE COURSE</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: ADCA & DCA Advanced Computing -->
                <div class="carousel-item dce-slider-item" style="background-image: url('{{ asset('images/banners/banner2.jpg') }}');">
                    <div class="dce-slider-overlay"></div>
                    <div class="container dce-slider-content">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <div class="dce-slider-card">
                                    <div class="dce-slider-badge mb-2">
                                        <span class="badge bg-info text-white fw-bold px-2 py-0.5 d-none d-lg-inline-block">ADVANCED IT DIPLOMA</span>
                                        <span class="dce-mobile-badge-text">ADVANCED IT DIPLOMA • ADCA & DCA • PRACTICAL LAB</span>
                                    </div>
                                    <h1 class="dce-slider-title">
                                        Master Advanced Computing & <br><span class="dce-slider-highlight">Career Diploma (ADCA)</span>
                                    </h1>
                                    <div class="dce-slider-tagline mb-2">
                                        <span>MS Office</span> <span class="opacity-40">|</span>
                                        <span>Web Design</span> <span class="opacity-40">|</span>
                                        <span>C/C++</span> <span class="opacity-40">|</span>
                                        <span>Tally Prime</span>
                                    </div>
                                    <p class="dce-slider-desc mb-3 mb-lg-4">
                                        Comprehensive 12-month master diploma covering computer fundamentals, office automation, database management, and programming with individual workstation access.
                                    </p>
                                    <div class="dce-slider-actions d-flex flex-wrap align-items-center gap-2 mb-3 mb-lg-4">
                                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-apply fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <span>Enroll Now</span> <i class="fa-solid fa-arrow-right small ms-1"></i>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-explore fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <i class="fa-solid fa-list-check text-warning d-none d-lg-inline-block"></i> <span>View Syllabus</span>
                                        </a>
                                    </div>
                                    <div class="dce-slider-micro-row d-none d-lg-flex flex-wrap align-items-center gap-2.5 pt-2 border-top border-white border-opacity-10">
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon"><i class="fa-solid fa-certificate"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">ISO Certified</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Govt Recognized</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8;"><i class="fa-solid fa-desktop"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">1:1 Practical</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Dedicated Workstation</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;"><i class="fa-solid fa-qrcode"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Online Verified</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">QR Marksheet</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block">
                                <div class="dce-slider-side-box">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                                        <span class="text-white small fw-bold text-uppercase" style="letter-spacing: 0.8px;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i> Fast Track Programs
                                        </span>
                                        <span class="badge bg-success-subtle text-success small px-2 py-0.5 rounded-pill">Open</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <span>Long Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-laptop-code"></i></div>
                                            <span>Short Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-award"></i></div>
                                            <span>NIELIT-Courses (Preparation)</span>
                                        </a>
                                        <a href="{{ route('admission.apply') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-file-signature"></i></div>
                                            <span>APPLY FOR ONLINE COURSE</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-microchip"></i></div>
                                            <span>HARDWARE COURSE</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Bilingual Typing Lab -->
                <div class="carousel-item dce-slider-item" style="background-image: url('{{ asset('images/banners/banner3.jpg') }}');">
                    <div class="dce-slider-overlay"></div>
                    <div class="container dce-slider-content">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <div class="dce-slider-card">
                                    <div class="dce-slider-badge mb-2">
                                        <span class="badge bg-success text-white fw-bold px-2 py-0.5 d-none d-lg-inline-block">TYPING LAB & SPEED</span>
                                        <span class="dce-mobile-badge-text">BILINGUAL TYPING LAB • SPEED & ACCURACY • GOVT EXAMS</span>
                                    </div>
                                    <h1 class="dce-slider-title">
                                        High-Speed Bilingual <br><span class="dce-slider-highlight">Hindi & English Typing Lab</span>
                                    </h1>
                                    <div class="dce-slider-tagline mb-2">
                                        <span>Krutidev</span> <span class="opacity-40">|</span>
                                        <span>Mangal</span> <span class="opacity-40">|</span>
                                        <span>Live Speed Tracking</span> <span class="opacity-40">|</span>
                                        <span>Beltron & SSC</span>
                                    </div>
                                    <p class="dce-slider-desc mb-3 mb-lg-4">
                                        Specialized typing lab with dedicated software for real-time WPM speed and accuracy evaluation for state exams, court clerk, Beltron, and central govt job selections.
                                    </p>
                                    <div class="dce-slider-actions d-flex flex-wrap align-items-center gap-2 mb-3 mb-lg-4">
                                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-apply fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <span>Join Typing Lab</span> <i class="fa-solid fa-arrow-right small ms-1"></i>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-explore fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <i class="fa-solid fa-keyboard text-warning d-none d-lg-inline-block"></i> <span>Check Typing Batches</span>
                                        </a>
                                    </div>
                                    <div class="dce-slider-micro-row d-none d-lg-flex flex-wrap align-items-center gap-2.5 pt-2 border-top border-white border-opacity-10">
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon"><i class="fa-solid fa-gauge-high"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">45+ WPM</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Target Speed</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8;"><i class="fa-solid fa-chalkboard-user"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Expert Mentor</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Personal Attention</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;"><i class="fa-solid fa-file-circle-check"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Speed Certificate</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Govt Validated</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block">
                                <div class="dce-slider-side-box">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                                        <span class="text-white small fw-bold text-uppercase" style="letter-spacing: 0.8px;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i> Fast Track Programs
                                        </span>
                                        <span class="badge bg-success-subtle text-success small px-2 py-0.5 rounded-pill">Open</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <span>Long Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-laptop-code"></i></div>
                                            <span>Short Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-award"></i></div>
                                            <span>NIELIT-Courses (Preparation)</span>
                                        </a>
                                        <a href="{{ route('admission.apply') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-file-signature"></i></div>
                                            <span>APPLY FOR ONLINE COURSE</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-microchip"></i></div>
                                            <span>HARDWARE COURSE</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4: Tally Prime & Accounting -->
                <div class="carousel-item dce-slider-item" style="background-image: url('{{ asset('images/banners/banner4.jpg') }}');">
                    <div class="dce-slider-overlay"></div>
                    <div class="container dce-slider-content">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <div class="dce-slider-card">
                                    <div class="dce-slider-badge mb-2">
                                        <span class="badge bg-danger text-white fw-bold px-2 py-0.5 d-none d-lg-inline-block">FINANCIAL ACCOUNTING</span>
                                        <span class="dce-mobile-badge-text">FINANCIAL ACCOUNTING • TALLY PRIME • GST BILLING</span>
                                    </div>
                                    <h1 class="dce-slider-title">
                                        Professional Accounting with <br><span class="dce-slider-highlight">Tally Prime & GST Billing</span>
                                    </h1>
                                    <div class="dce-slider-tagline mb-2">
                                        <span>GST Invoicing</span> <span class="opacity-40">|</span>
                                        <span>TDS & Payroll</span> <span class="opacity-40">|</span>
                                        <span>Balance Sheet</span> <span class="opacity-40">|</span>
                                        <span>Bank Recon</span>
                                    </div>
                                    <p class="dce-slider-desc mb-3 mb-lg-4">
                                        Master computerized accounting on the latest Tally Prime software. Learn real voucher entry, inventory management, and tax filing with hands-on practice.
                                    </p>
                                    <div class="dce-slider-actions d-flex flex-wrap align-items-center gap-2 mb-3 mb-lg-4">
                                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-apply fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <span>Enroll in Tally</span> <i class="fa-solid fa-arrow-right small ms-1"></i>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-explore fw-bold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                                            <i class="fa-solid fa-calculator text-warning d-none d-lg-inline-block"></i> <span>Accounting Courses</span>
                                        </a>
                                    </div>
                                    <div class="dce-slider-micro-row d-none d-lg-flex flex-wrap align-items-center gap-2.5 pt-2 border-top border-white border-opacity-10">
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">GST Ready</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Real Billing Cases</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8;"><i class="fa-solid fa-building-user"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Job Ready</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Accountant Placements</div>
                                            </div>
                                        </div>
                                        <div class="dce-slider-micro-pill">
                                            <div class="dce-slider-micro-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;"><i class="fa-solid fa-stamp"></i></div>
                                            <div>
                                                <div class="text-white small fw-bold lh-1">Authorized Cert</div>
                                                <div class="text-white-50" style="font-size: 0.68rem;">Nationwide Valid</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block">
                                <div class="dce-slider-side-box">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-white border-opacity-10">
                                        <span class="text-white small fw-bold text-uppercase" style="letter-spacing: 0.8px;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i> Fast Track Programs
                                        </span>
                                        <span class="badge bg-success-subtle text-success small px-2 py-0.5 rounded-pill">Open</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                            <span>Long Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-laptop-code"></i></div>
                                            <span>Short Term Courses</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-award"></i></div>
                                            <span>NIELIT-Courses (Preparation)</span>
                                        </a>
                                        <a href="{{ route('admission.apply') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-file-signature"></i></div>
                                            <span>APPLY FOR ONLINE COURSE</span>
                                        </a>
                                        <a href="{{ route('courses.index') }}" class="dce-slider-side-card">
                                            <div class="dce-slider-side-icon"><i class="fa-solid fa-microchip"></i></div>
                                            <span>HARDWARE COURSE</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Navigation Arrows (Desktop Only) -->
            <button class="dce-slider-btn prev border-0 d-none d-lg-flex" type="button" data-bs-target="#dceHeroSlider" data-bs-slide="prev" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="dce-slider-btn next border-0 d-none d-lg-flex" type="button" data-bs-target="#dceHeroSlider" data-bs-slide="next" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- 2. Six Quick Service Cards Strip (Direct Match to Reference Screenshot) -->
    <section class="dce-quick-strip">
        <div class="container">
            <div class="row g-2.5 g-md-3 row-cols-2 row-cols-md-3 row-cols-lg-6">
                <!-- 1. Registration -->
                <div class="col">
                    <a href="{{ route('admission.apply') }}" class="dce-quick-ref-card border-red">
                        <div class="dce-quick-ref-icon bg-danger-subtle text-danger">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Registration</div>
                            <div class="dce-quick-ref-sub">Apply online</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow text-danger"></i>
                    </a>
                </div>
                <!-- 2. Admit Card -->
                <div class="col">
                    <a href="{{ route('student.login') }}" class="dce-quick-ref-card border-amber">
                        <div class="dce-quick-ref-icon bg-warning-subtle text-warning-emphasis">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Admit Card</div>
                            <div class="dce-quick-ref-sub">Download now</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow text-warning"></i>
                    </a>
                </div>
                <!-- 3. Student Verify -->
                <div class="col">
                    <a href="{{ route('verify.admission') }}" class="dce-quick-ref-card border-green">
                        <div class="dce-quick-ref-icon bg-success-subtle text-success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Student Verify</div>
                            <div class="dce-quick-ref-sub">Check record</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow text-success"></i>
                    </a>
                </div>
                <!-- 4. Marksheet -->
                <div class="col">
                    <a href="{{ route('verify.certificate') }}" class="dce-quick-ref-card border-purple">
                        <div class="dce-quick-ref-icon" style="background: rgba(139, 92, 246, 0.12); color: #7c3aed;">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Marksheet</div>
                            <div class="dce-quick-ref-sub">Verify online</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow" style="color: #7c3aed;"></i>
                    </a>
                </div>
                <!-- 5. Certificate -->
                <div class="col">
                    <a href="{{ route('verify.certificate') }}" class="dce-quick-ref-card border-orange">
                        <div class="dce-quick-ref-icon" style="background: rgba(234, 88, 12, 0.12); color: #ea580c;">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Certificate</div>
                            <div class="dce-quick-ref-sub">Verify online</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow text-warning"></i>
                    </a>
                </div>
                <!-- 6. Online Payment -->
                <div class="col">
                    <a href="{{ route('payment.index') }}" class="dce-quick-ref-card border-blue">
                        <div class="dce-quick-ref-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div class="dce-quick-ref-content">
                            <div class="dce-quick-ref-title">Online Payment</div>
                            <div class="dce-quick-ref-sub">Pay securely</div>
                        </div>
                        <i class="fa-solid fa-chevron-right dce-quick-ref-arrow text-primary"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- 4. Key Impact & Numerical Statistics Strip -->
    <section class="py-4 py-md-5 bg-light border-top border-bottom">
        <div class="container">
            <div class="row g-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card stat-blue h-100">
                        <div class="dce-stat-badge bg-primary-subtle text-primary">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="2500">2,500+</div>
                        <div class="dce-stat-lbl">Students Trained</div>
                        <div class="dce-stat-sub d-none d-sm-block">Job & Career Ready</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card stat-amber h-100">
                        <div class="dce-stat-badge bg-warning-subtle text-warning-emphasis">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="{{ $allCourses->count() }}">{{ $allCourses->count() }}</div>
                        <div class="dce-stat-lbl">Certified Courses</div>
                        <div class="dce-stat-sub d-none d-sm-block">Basic to Advanced</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card stat-info h-100">
                        <div class="dce-stat-badge bg-info-subtle text-info">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="50">50+</div>
                        <div class="dce-stat-lbl">Modern PCs</div>
                        <div class="dce-stat-sub d-none d-sm-block">1:1 Practical Ratio</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card stat-green h-100">
                        <div class="dce-stat-badge bg-success-subtle text-success">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="100">100%</div>
                        <div class="dce-stat-lbl">Verified Diplomas</div>
                        <div class="dce-stat-sub d-none d-sm-block">QR Code Enabled</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Academic Programs (12 Featured Job-Oriented Courses with Dynamic Images) -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <span class="dce-section-tag bg-primary-subtle text-primary mb-2">
                        <i class="fa-solid fa-graduation-cap me-1"></i> Academic Programs
                    </span>
                    <h2 class="dce-section-title mb-1">Featured Computer Courses</h2>
                    <p class="dce-section-subtitle text-muted mb-0">Explore 12 certified vocational, software, and accounting courses designed for government exams and career excellence.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary fw-bold rounded-2 px-4 shadow-sm">
                        View All {{ $allCourses->count() }} Courses <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4">
                @foreach($featuredCourses as $course)
                    @php
                        $courseImg = $course->thumbnail 
                            ? (file_exists(public_path($course->thumbnail)) 
                                ? asset($course->thumbnail) 
                                : (str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail))) 
                            : asset('images/courses/' . strtolower(\Illuminate\Support\Str::slug($course->course_code)) . '.svg');
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
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

            <!-- Bottom CTA to view complete syllabus directory -->
            <div class="text-center mt-4 pt-1">
                <a href="{{ route('courses.index') }}" class="dce-course-explore-btn">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Explore Complete Course Directory ({{ $allCourses->count() }} Programs)</span>
                    <i class="fa-solid fa-arrow-right small"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 6. Why Study With Us (Why Choose TECH ERA COMPUTER - 2-Column Mobile Optimized) -->
    <section class="py-5 dce-why-section border-top border-bottom">
        <div class="container">
            <div class="dce-section-header">
                <span class="dce-section-tag bg-primary-subtle text-primary">
                    <i class="fa-solid fa-award me-1"></i> Why Study With Us
                </span>
                <h2 class="dce-section-title">Why Choose TECH ERA COMPUTER?</h2>
                <p class="dce-section-subtitle">
                    Equipping students with modern technical skills for competitive exams, accounting roles, and high-growth IT careers with 100% practical lab training.
                </p>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4">
                <!-- Card 1: 1:1 PC -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #2563eb, #38bdf8);">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <h5 class="dce-why-title">1:1 Dedicated PC Access</h5>
                        <p class="dce-why-desc">Individual computer terminal time with high-speed internet and personal mentor guidance for every student.</p>
                        <span class="badge bg-primary-subtle text-primary dce-why-badge">
                            <i class="fa-solid fa-circle-check"></i> 100% Personal Workstation
                        </span>
                    </div>
                </div>

                <!-- Card 2: QR Verification -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #059669, #34d399);">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <h5 class="dce-why-title">QR Code Verification</h5>
                        <p class="dce-why-desc">Instant 24x7 online verification of student diplomas and marksheets via public QR scan verification engine.</p>
                        <span class="badge bg-success-subtle text-success dce-why-badge">
                            <i class="fa-solid fa-shield-halved"></i> Govt & Corporate Valid
                        </span>
                    </div>
                </div>

                <!-- Card 3: Bilingual Typing -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #d97706, #fbbf24);">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <h5 class="dce-why-title">Bilingual Typing Lab</h5>
                        <p class="dce-why-desc">Govt exam preparation: Hindi (Mangal, KrutiDev) and English typing software with live accuracy measurement.</p>
                        <span class="badge bg-warning-subtle text-warning-emphasis dce-why-badge">
                            <i class="fa-solid fa-bolt"></i> 45+ WPM Target
                        </span>
                    </div>
                </div>

                <!-- Card 4: Tally Prime & GST -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #4f46e5, #818cf8);">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h5 class="dce-why-title">Tally Prime & GST</h5>
                        <p class="dce-why-desc">Comprehensive computerized accounting: GST returns, e-way bills, TDS, bank reconciliation, and business ledgers.</p>
                        <span class="badge bg-info-subtle text-info dce-why-badge">
                            <i class="fa-solid fa-file-invoice"></i> Real Invoicing Cases
                        </span>
                    </div>
                </div>

                <!-- Card 5: Expert Mentors -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #9333ea, #c084fc);">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h5 class="dce-why-title">Expert Faculty Mentors</h5>
                        <p class="dce-why-desc">Experienced faculty instructors providing personal guidance from fundamentals to advanced projects.</p>
                        <span class="badge dce-why-badge" style="background: #faf5ff; color: #9333ea; border: 1px solid #f3e8ff;">
                            <i class="fa-solid fa-user-check"></i> Step-by-Step Mentoring
                        </span>
                    </div>
                </div>

                <!-- Card 6: Installments -->
                <div class="col-6 col-lg-4">
                    <div class="dce-why-card">
                        <div class="dce-why-squircle" style="background: linear-gradient(135deg, #0891b2, #22d3ee);">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h5 class="dce-why-title">Easy Installments</h5>
                        <p class="dce-why-desc">Transparent fee packages with zero hidden fees, flexible monthly installment options, and instant computerized receipts.</p>
                        <span class="badge bg-light text-secondary border dce-why-badge">
                            <i class="fa-solid fa-receipt"></i> Zero Hidden Fees
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Instant Verification CTA Banner -->
    <section class="py-4 py-md-5 bg-white">
        <div class="container">
            <div class="dce-verify-cta-banner">
                <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                    <div class="col-lg-8">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill mb-2.5 text-uppercase" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-shield-halved me-1"></i> Public Verification Portal
                        </span>
                        <h2 class="display-6 fw-extrabold text-white mb-2">Verify Student Diploma & Marksheet Online</h2>
                        <p class="text-white-50 mb-0" style="max-width: 650px; font-size: 0.96rem; line-height: 1.6;">
                            Our certificates feature automated SHA-256 cryptographic security verification. Scan the live QR-code on any physical certificate or enter the unique Certificate Number to verify authentic enrollment and grades 24x7.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                            <a href="{{ route('verify.certificate') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-4 rounded-2 shadow d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-qrcode fs-6"></i>
                                <span>Check Certificate / Marksheet</span>
                            </a>
                            <a href="{{ route('verify.admission') }}" class="btn btn-outline-light fw-bold py-2.5 px-4 rounded-2 d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-id-badge fs-6"></i>
                                <span>Verify Admission Status</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7.5. Certified Students & Campus Gallery Section -->
    @if(isset($galleries) && $galleries->isNotEmpty())
    <section class="py-5 bg-light position-relative" id="homeGallerySection">
        <div class="container">
            <div class="dce-section-header">
                <span class="dce-section-tag" style="background-color: #ede9fe; color: #6d28d9;">
                    <i class="fa-solid fa-images me-1"></i> Certified Students & Campus Life
                </span>
                <h2 class="dce-section-title">Students & Certificate Awards</h2>
                <p class="dce-section-subtitle">Real glimpses of our students receiving government-recognized diplomas, hands-on computer lab sessions, and annual convocation ceremonies.</p>
            </div>

            <!-- Filter Buttons & View All Link -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div class="d-flex flex-wrap gap-1.5" id="homeGalleryFilters">
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 home-gallery-filter active" data-filter="all">
                        <i class="fa-solid fa-border-all me-1"></i> All ({{ $galleries->count() }})
                    </button>
                    @php
                        $uniqueCategories = $galleries->pluck('category')->unique()->filter()->values();
                    @endphp
                    @foreach($uniqueCategories as $cat)
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 home-gallery-filter" data-filter="{{ Str::slug($cat) }}">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                <div>
                    <a href="{{ route('gallery') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5">
                        <span>View Full Gallery</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>
            </div>

            <!-- Gallery Grid (10-12 Cards) -->
            <div class="row g-2.5 g-sm-3 g-lg-4" id="homeGalleryGrid">
                @foreach($galleries as $gallery)
                    @php
                        $badgeClass = match($gallery->category) {
                            'Certificates' => 'bg-warning text-dark',
                            'Students'     => 'bg-success text-white',
                            'Computer Lab' => 'bg-primary text-white',
                            'Events'       => 'bg-info text-dark',
                            default        => 'bg-dark bg-opacity-75 text-white',
                        };
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3 home-gallery-item" data-category="{{ Str::slug($gallery->category) }}">
                        <div class="card border border-light-subtle shadow-sm rounded-4 overflow-hidden h-100 dce-gallery-card bg-white position-relative">
                            <div class="position-relative overflow-hidden" style="height: 180px; background-color: #0f172a; cursor: pointer;" onclick="showGalleryLightbox('{{ $gallery->image_url }}', '{{ addslashes($gallery->title) }}', '{{ addslashes($gallery->category) }}')">
                                <img src="{{ $gallery->image_url }}" 
                                     alt="{{ $gallery->title }}" 
                                     class="w-100 h-100 object-fit-cover dce-gallery-img"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='https://placehold.co/600x400?text=TECH+ERA+Gallery';">
                                
                                <span class="badge position-absolute top-0 start-0 m-2 shadow-sm {{ $badgeClass }}" style="font-size: 0.68rem; font-weight: 700; border-radius: 6px;">
                                    {{ $gallery->category }}
                                </span>

                                <div class="dce-gallery-zoom-overlay">
                                    <span class="btn btn-light btn-sm rounded-circle shadow" style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-magnifying-glass-plus text-primary fs-6"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-2.5 p-md-3 bg-white d-flex flex-column justify-content-between">
                                <h6 class="fw-bold text-dark mb-1 small text-truncate" title="{{ $gallery->title }}">
                                    {{ $gallery->title }}
                                </h6>
                                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
                                    <span><i class="fa-solid fa-circle-check text-success me-1"></i> Verified</span>
                                    <span class="text-primary fw-semibold cursor-pointer" onclick="showGalleryLightbox('{{ $gallery->image_url }}', '{{ addslashes($gallery->title) }}', '{{ addslashes($gallery->category) }}')">
                                        Zoom <i class="fa-solid fa-arrow-up-right-from-square ms-0.5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modal for Home Gallery Lightbox -->
    <div class="modal fade" id="homeGalleryLightboxModal" tabindex="-1" aria-labelledby="homeGalleryLightboxTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-dark text-white py-2.5 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark" id="homeGalleryModalBadge">Gallery</span>
                        <h6 class="modal-title fs-6 fw-bold text-white mb-0" id="homeGalleryLightboxTitle">Photo View</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-black text-center position-relative" style="min-height: 280px; display: flex; align-items: center; justify-content: center;">
                    <img id="homeGalleryModalImage" src="" alt="" class="img-fluid" style="max-height: 75vh; width: auto; object-fit: contain;">
                </div>
                <div class="modal-footer border-0 bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fa-solid fa-certificate text-warning me-1"></i> {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} &bull; Verified Campus Photo</small>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- [COMMENTED: Campus Facilities Section - redundant with 'Why Choose Us'. Uncomment if a dedicated lab infrastructure section is needed]
    <section class="py-5 bg-light">
        <div class="container">
            <div class="dce-section-header">
                <span class="dce-section-tag bg-info-subtle text-info">
                    <i class="fa-solid fa-microchip me-1"></i> Campus Infrastructure
                </span>
                <h2 class="dce-section-title">Smart Lab Facilities & Environment</h2>
                <p class="dce-section-subtitle">A high-tech, productive, and inspiring environment built specifically for practical IT excellence.</p>
            </div>
            <div class="row g-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100 p-3.5 p-md-4 rounded-4 shadow-sm border bg-white text-center">
                        <div class="dce-lab-badge bg-primary-subtle text-primary mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.35rem; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1 fs-6">Connected Network</h5>
                        <p class="text-muted small mb-0">High-speed gigabit Wi-Fi & LAN network for online tests and cloud software learning.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100 p-3.5 p-md-4 rounded-4 shadow-sm border bg-white text-center">
                        <div class="dce-lab-badge bg-warning-subtle text-warning mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.35rem; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1 fs-6">100% Power Backup</h5>
                        <p class="text-muted small mb-0">Uninterrupted dual inverter and silent generator power for continuous typing and coding practice.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}

    <!-- 8. Student Testimonials & Success Stories -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="dce-section-header">
                <span class="dce-section-tag bg-success-subtle text-success">
                    <i class="fa-solid fa-star me-1"></i> Student Reviews
                </span>
                <h2 class="dce-section-title">What Our Students Say</h2>
                <p class="dce-section-subtitle">Real feedback from students who transformed their technical skills and built successful careers with TECH ERA COMPUTER.</p>
            </div>

            <div class="row g-3 g-lg-4">
                <div class="col-md-4">
                    <div class="dce-testimonial-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-warning small">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary small">ADCA Graduate</span>
                        </div>
                        <p class="dce-testimonial-quote">
                            "The 1:1 computer ratio at TECH ERA helped me practice coding and advanced Excel without waiting for turns. I received my QR-verified diploma and got placed as a Computer Operator in Sheikhpura!"
                        </p>
                        <div class="dce-testimonial-user mt-auto pt-3 border-top">
                            <div class="dce-testimonial-avatar bg-primary text-white">AK</div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Amit Kumar</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Computer Operator, Sheikhpura</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dce-testimonial-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-warning small">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="badge bg-warning-subtle text-warning-emphasis small">Tally Prime & GST</span>
                        </div>
                        <p class="dce-testimonial-quote">
                            "Learning Tally Prime with live GST invoicing, e-way bills, and bank reconciliation gave me huge confidence. The teachers explain every doubt with great patience."
                        </p>
                        <div class="dce-testimonial-user mt-auto pt-3 border-top">
                            <div class="dce-testimonial-avatar bg-warning text-dark">PK</div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Priya Kumari</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Accountant, Sasbahana</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dce-testimonial-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-warning small">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success small">Typing Lab Expert</span>
                        </div>
                        <p class="dce-testimonial-quote">
                            "Their bilingual typing software helped me jump from 20 WPM to 48 WPM in Hindi Mangal and 52 WPM in English. Cleared my court clerk typing exam on the first attempt!"
                        </p>
                        <div class="dce-testimonial-user mt-auto pt-3 border-top">
                            <div class="dce-testimonial-avatar bg-success text-white">RS</div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Rahul Sharma</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Govt Selection Candidate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. About Institute & Location Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="dce-section-tag bg-primary-subtle text-primary mb-2">
                        <i class="fa-solid fa-building-columns me-1"></i> About The Institute
                    </span>
                    <h2 class="display-6 fw-extrabold text-dark mb-3">Welcome to TECH ERA COMPUTER</h2>
                    <p class="text-muted lead fs-6">
                        <strong>TECH ERA COMPUTER</strong> is an advanced technical education institute situated in <strong>Sasbahana, Sheikhpura, Bihar - 801105</strong>.
                    </p>
                    <p class="text-secondary" style="line-height: 1.7;">
                        Our mission is to empower youth and students across Sheikhpura and neighboring areas with high-demand digital skills, software literacy, accounting competence, and certified vocational training. From basic computer concepts to advanced programming, accounting with GST, and rapid bilingual typing, we guide each student toward real-world self-reliance and career success.
                    </p>
                    <div class="d-flex flex-wrap gap-2.5 mt-4">
                        <a href="{{ route('about') }}" class="btn btn-dce-primary rounded-2 px-4 shadow-sm">
                            <i class="fa-solid fa-circle-info me-1"></i> Learn More About Us
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary rounded-2 px-4 shadow-sm">
                            <i class="fa-solid fa-location-dot me-1"></i> Visit Campus Desk
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                                 alt="Tech Era Logo" 
                                 class="img-fluid" 
                                 style="max-height: 58px; width: auto;">
                            <div>
                                <h4 class="fw-bold text-dark mb-0">TECH ERA COMPUTER</h4>
                                <span class="text-muted small">Campus & Admissions Desk</span>
                            </div>
                        </div>

                        <div class="vstack gap-3 text-secondary small">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary-subtle text-primary p-2 rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Campus Address:</strong>
                                    {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-success-subtle text-success p-2 rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Helpline & WhatsApp:</strong>
                                    <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="text-decoration-none text-dark fw-bold">
                                        +91 {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-warning-subtle text-warning p-2 rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Working Hours:</strong>
                                    Monday – Saturday: 08:00 AM – 07:00 PM
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <a href="https://api.whatsapp.com/send?phone=91{{ $instituteSettings['contact_phone'] ?? '9162384660' }}&text=Hi%20Tech%20Era%20Computer" target="_blank" class="btn btn-success rounded-2 px-4 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-1.5">
                                <i class="fa-brands fa-whatsapp fs-5"></i>
                                <span class="fw-bold">Chat on WhatsApp</span>
                            </a>
                            <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-outline-primary rounded-2 px-4 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <i class="fa-solid fa-phone"></i>
                                <span class="fw-bold">Call</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function showGalleryLightbox(url, title, category) {
            const modalEl = document.getElementById('homeGalleryLightboxModal');
            if (!modalEl) return;
            const imgEl = document.getElementById('homeGalleryModalImage');
            const titleEl = document.getElementById('homeGalleryLightboxTitle');
            const badgeEl = document.getElementById('homeGalleryModalBadge');
            
            if (imgEl) imgEl.src = url;
            if (titleEl) titleEl.textContent = title;
            if (badgeEl) badgeEl.textContent = category;
            
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const filterBtns = document.querySelectorAll('.home-gallery-filter');
            const galleryItems = document.querySelectorAll('.home-gallery-item');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterBtns.forEach(b => {
                        b.classList.remove('active', 'btn-primary');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.add('active', 'btn-primary');
                    this.classList.remove('btn-outline-secondary');

                    const filter = this.getAttribute('data-filter');
                    galleryItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
    @endpush
@endsection
