<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . ' | Building Digital Skills for the Future')</title>
    <meta name="description" content="@yield('meta_description', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . ' - Building Digital Skills for the Future. Professional computer education, programming, accounting, and typing in Sasbahana, Sheikhpura, Bihar.')">
    <meta name="keywords" content="TECH ERA COMPUTER, Sheikhpura Computer Institute, ADCA, DCA, Typing, Tally Prime, Web Development, Certificate Verification, Bihar">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="@yield('title', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))">
    <meta property="og:description" content="@yield('meta_description', 'Building Digital Skills for the Future - Premier Computer Institute in Sheikhpura, Bihar.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Brand CSS -->
    <link rel="stylesheet" href="{{ asset('css/drishti-custom.css') }}">

    @stack('styles')
</head>
<body>

    <!-- Header (Sticky Topbar & Navbar) -->
    <header class="sticky-top">
        <!-- 1. Top Header Bar (Desktop & Mobile) -->
        <div class="dce-topbar py-1 py-lg-2">
            <div class="container">
                <!-- Desktop Header Topbar (>= 992px) -->
                <div class="row align-items-center d-none d-lg-flex">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-4">
                            <span><i class="fa-solid fa-location-dot text-info me-2"></i>{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</span>
                            <span><i class="fa-solid fa-phone text-warning me-2"></i><a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</a></span>
                        </div>
                    </div>
                    <div class="col-lg-5 text-end">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('student.login') }}" class="btn btn-sm btn-info text-white fw-bold px-3 shadow-sm dce-topbar-btn">
                                <i class="fa-solid fa-user-graduate me-1"></i> Student Login
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark fw-bold px-3 shadow-sm dce-topbar-btn">
                                <i class="fa-solid fa-building-columns me-1"></i> Franchise Login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile & Tablet Header Topbar (< 992px) -->
                <div class="d-flex align-items-center justify-content-between gap-1.5 gap-sm-2 d-lg-none py-1">
                    <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="text-white text-decoration-none small d-flex align-items-center text-nowrap py-1" title="Helpline">
                        <i class="fa-solid fa-phone text-warning me-1"></i>
                        <span class="d-none d-sm-inline fw-semibold">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</span>
                        <span class="d-inline d-sm-none fw-semibold" style="font-size: 0.75rem;">Help</span>
                    </a>
                    <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-nowrap">
                        <a href="{{ route('student.login') }}" class="btn btn-sm btn-info text-white fw-bold d-inline-flex align-items-center justify-content-center gap-1 px-2.5 py-1 text-nowrap shadow-sm dce-topbar-btn" title="Student Login">
                            <i class="fa-solid fa-user-graduate"></i> <span>Student Login</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark fw-bold d-inline-flex align-items-center justify-content-center gap-1 px-2.5 py-1 text-nowrap shadow-sm dce-topbar-btn" title="Franchise Login">
                            <i class="fa-solid fa-building-columns"></i> <span>Franchise Login</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Main Navigation Bar -->
        <nav class="navbar navbar-expand-lg dce-navbar py-2 py-lg-3">
            <div class="container d-flex align-items-center justify-content-between flex-nowrap w-100">
                <!-- Brand / Logo -->
                <a class="dce-brand-logo d-flex align-items-center gap-2 text-decoration-none text-nowrap" href="{{ route('home') }}" style="min-width: 0;">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="dce-brand-img img-fluid flex-shrink-0" 
                         style="max-height: 42px; width: auto; object-fit: contain;">
                    <div class="dce-brand-text text-truncate">
                        <div class="dce-brand-title fw-bold text-nowrap">
                            <span style="color: #0284c7;">TECH</span> <span style="color: #ea580c;">ERA</span> <span class="dce-brand-suffix" style="color: #0f172a;">COMPUTER</span>
                        </div>
                        <div class="dce-brand-sub d-none d-md-block text-truncate">
                            {{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}
                        </div>
                    </div>
                </a>

                <!-- Mobile Offcanvas Hamburger Menu Toggle -->
                <div class="d-flex align-items-center d-lg-none flex-shrink-0 ms-2">
                    <button class="navbar-toggler border-0 shadow-none rounded-3 bg-light d-flex align-items-center justify-content-center p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#dceOffcanvasNav" aria-controls="dceOffcanvasNav" aria-expanded="false" aria-label="Open Navigation Menu" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-bars fs-5 text-dark"></i>
                    </button>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="collapse navbar-collapse d-none d-lg-flex" id="dceNavbarContent">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>

                        <!-- About Us Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('about-us*') || request()->is('company*') || request()->is('chairman*') || request()->is('mission*') || request()->is('corporate-members*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                About Us
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                <li><a class="dropdown-item py-2" href="{{ route('about') }}"><i class="fa-solid fa-circle-info text-primary me-2"></i>About Us</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('company') }}"><i class="fa-solid fa-id-card text-info me-2"></i>Company Profile</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('chairman') }}"><i class="fa-solid fa-user-tie text-success me-2"></i>Chairman's Message</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('mission') }}"><i class="fa-solid fa-bullseye text-danger me-2"></i>Mission & Vision</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('members') }}"><i class="fa-solid fa-users text-warning me-2"></i>Corporate Members</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('facilities') }}"><i class="fa-solid fa-chalkboard-user text-purple me-2"></i>Our Facilities</a></li>
                            </ul>
                        </li>

                        <!-- Courses Listing -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a>
                        </li>

                        <!-- Student Zone Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('check-*') || request()->is('online-payment*') || request()->is('admission-enquiry*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Student Zone
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                <li><a class="dropdown-item py-2" href="{{ route('verify.certificate') }}"><i class="fa-solid fa-certificate text-warning me-2"></i>Check Certificate / Marksheet</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('verify.admission') }}"><i class="fa-solid fa-id-badge text-primary me-2"></i>Check Admission Status</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('payment.index') }}"><i class="fa-solid fa-credit-card text-success me-2"></i>Online Fee Payment</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('student.login') }}"><i class="fa-solid fa-graduation-cap text-info me-2"></i>Student Login</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('enquiry.admission') }}"><i class="fa-solid fa-clipboard-question text-danger me-2"></i>Admission Enquiry</a></li>
                            </ul>
                        </li>

                        <!-- Franchise Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('franchise*') || request()->is('frenchise*') || request()->is('search_centre*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Franchise
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                <li><a class="dropdown-item py-2" href="{{ route('franchise.apply') }}"><i class="fa-solid fa-file-signature text-primary me-2"></i>Apply for New Franchise</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('franchise.search') }}"><i class="fa-solid fa-magnifying-glass-location text-success me-2"></i>Search Franchise Centre</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('franchise.status') }}"><i class="fa-solid fa-hourglass-half text-warning me-2"></i>Franchise Application Status</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('login') }}"><i class="fa-solid fa-door-open text-info me-2"></i>Franchise Login</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Slide-Out Mobile Navigation Drawer (Offcanvas) -->
        <div class="offcanvas offcanvas-end d-lg-none dce-mobile-drawer" tabindex="-1" id="dceOffcanvasNav" aria-labelledby="dceOffcanvasNavLabel">
            <div class="offcanvas-header bg-dark text-white p-3 align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="rounded bg-white p-1" 
                         style="height: 38px; width: auto; object-fit: contain;">
                    <div>
                        <div class="fw-bold fs-6 lh-1" id="dceOffcanvasNavLabel">
                            <span style="color: #38bdf8;">TECH</span> <span style="color: #fb923c;">ERA</span> COMPUTER
                        </div>
                        <span class="text-white-50" style="font-size: 0.68rem;">Building Digital Skills</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0 d-flex flex-column justify-content-between">
                <div>
                    <!-- Quick Mobile Action Buttons Bar -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('student.login') }}" class="btn btn-info text-white w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-1.5" style="min-height: 44px;">
                                    <i class="fa-solid fa-graduation-cap"></i> Student Login
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('login') }}" class="btn btn-warning text-dark w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-1.5" style="min-height: 44px;">
                                    <i class="fa-solid fa-building-columns"></i> Franchise Login
                                </a>
                            </div>
                            <div class="col-12 mt-2">
                                <a href="{{ route('enquiry.admission') }}" class="btn btn-primary w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="min-height: 44px;">
                                    <i class="fa-solid fa-paper-plane"></i> Online Admission Enquiry
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navigation Menu -->
                    <ul class="list-unstyled mb-0 dce-mobile-nav-list">
                        <li>
                            <a href="{{ route('home') }}" class="dce-mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="fa-solid fa-house me-3 text-primary"></i> Home
                            </a>
                        </li>

                        <!-- About Us Accordion -->
                        <li class="dce-mobile-accordion-group">
                            <a class="dce-mobile-nav-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileAboutCollapse" role="button" aria-expanded="false">
                                <span><i class="fa-solid fa-circle-info me-3 text-info"></i> About Institute</span>
                                <i class="fa-solid fa-chevron-down small text-muted"></i>
                            </a>
                            <div class="collapse {{ request()->is('about-us*') || request()->is('company*') || request()->is('chairman*') || request()->is('mission*') || request()->is('corporate-members*') ? 'show' : '' }}" id="mobileAboutCollapse">
                                <div class="ps-4 bg-light py-1 border-start border-3 border-info ms-3">
                                    <a href="{{ route('about') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>About Us</a>
                                    <a href="{{ route('company') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>Company Profile</a>
                                    <a href="{{ route('chairman') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>Chairman's Message</a>
                                    <a href="{{ route('mission') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>Mission & Vision</a>
                                    <a href="{{ route('members') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>Corporate Members</a>
                                    <a href="{{ route('facilities') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-angle-right me-2"></i>Our Facilities</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <a href="{{ route('courses.index') }}" class="dce-mobile-nav-item {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-bookmark me-3 text-success"></i> Courses Directory
                            </a>
                        </li>

                        <!-- Student Zone Accordion -->
                        <li class="dce-mobile-accordion-group">
                            <a class="dce-mobile-nav-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileStudentCollapse" role="button" aria-expanded="false">
                                <span><i class="fa-solid fa-user-graduate me-3 text-warning"></i> Student Zone</span>
                                <i class="fa-solid fa-chevron-down small text-muted"></i>
                            </a>
                            <div class="collapse {{ request()->is('check-*') || request()->is('online-payment*') || request()->is('admission-enquiry*') ? 'show' : '' }}" id="mobileStudentCollapse">
                                <div class="ps-4 bg-light py-1 border-start border-3 border-warning ms-3">
                                    <a href="{{ route('verify.certificate') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-certificate me-2 text-warning"></i>Check Certificate / Marksheet</a>
                                    <a href="{{ route('verify.admission') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-id-badge me-2 text-primary"></i>Check Admission Status</a>
                                    <a href="{{ route('payment.index') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-credit-card me-2 text-success"></i>Online Fee Payment</a>
                                    <a href="{{ route('student.login') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-graduation-cap me-2 text-info"></i>Student Login</a>
                                    <a href="{{ route('enquiry.admission') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-clipboard-question me-2 text-danger"></i>Admission Enquiry</a>
                                </div>
                            </div>
                        </li>

                        <!-- Franchise Accordion -->
                        <li class="dce-mobile-accordion-group">
                            <a class="dce-mobile-nav-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileFranchiseCollapse" role="button" aria-expanded="false">
                                <span><i class="fa-solid fa-building-columns me-3 text-primary"></i> Franchise Panel</span>
                                <i class="fa-solid fa-chevron-down small text-muted"></i>
                            </a>
                            <div class="collapse {{ request()->is('franchise*') || request()->is('frenchise*') || request()->is('search_centre*') ? 'show' : '' }}" id="mobileFranchiseCollapse">
                                <div class="ps-4 bg-light py-1 border-start border-3 border-primary ms-3">
                                    <a href="{{ route('franchise.apply') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-file-signature me-2 text-primary"></i>Apply for Franchise</a>
                                    <a href="{{ route('franchise.search') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-magnifying-glass-location me-2 text-success"></i>Search Study Centre</a>
                                    <a href="{{ route('franchise.status') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-hourglass-half me-2 text-warning"></i>Franchise Status</a>
                                    <a href="{{ route('login') }}" class="dce-mobile-sub-item"><i class="fa-solid fa-door-open me-2 text-info"></i>Franchise Login</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <a href="{{ route('gallery') }}" class="dce-mobile-nav-item {{ request()->routeIs('gallery') ? 'active' : '' }}">
                                <i class="fa-solid fa-images me-3 text-purple"></i> Campus Gallery
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('contact') }}" class="dce-mobile-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                                <i class="fa-solid fa-envelope me-3 text-danger"></i> Contact Us
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Drawer Contact Footer -->
                <div class="p-3 bg-dark text-white border-top">
                    <div class="small fw-semibold mb-2 text-white-50"><i class="fa-solid fa-headset me-1 text-warning"></i> Need Direct Help?</div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-sm btn-outline-light w-50 d-flex align-items-center justify-content-center gap-1.5" style="min-height: 40px;">
                            <i class="fa-solid fa-phone text-warning"></i> Call Us
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=91{{ $instituteSettings['contact_phone'] ?? '9162384660' }}&text=Hi%20Tech%20Era%20Computer" target="_blank" class="btn btn-sm btn-success w-50 d-flex align-items-center justify-content-center gap-1.5" style="min-height: 40px;">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                    <div class="text-white-50" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-location-dot me-1 text-info"></i> {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Animated Notice Ticker / Marquee -->
        @php
            $marqueeNotices = \App\Models\Notice::where('is_marquee', true)->where('status', true)->latest()->take(5)->get();
        @endphp
        @if($marqueeNotices->isNotEmpty())
        <div class="dce-marquee-container">
            <div class="container d-flex align-items-center">
                <span class="dce-marquee-label me-3"><i class="fa-solid fa-bullhorn me-1"></i> Latest Updates</span>
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    @foreach($marqueeNotices as $notice)
                        <span class="me-4"><i class="fa-solid fa-star text-light me-1"></i> {{ $notice->title }}</span>
                    @endforeach
                </marquee>
            </div>
        </div>
        @endif
    </header>

    <!-- 4. Flash Notifications & Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong class="d-block mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Please check the form errors:</strong>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- 5. Main Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- 6. Need Assistance Helpline Strip -->
    <section class="py-4 text-white text-center" style="background: linear-gradient(90deg, #0a192f 0%, #0f172a 50%, #0284c7 100%);">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-1"><i class="fa-solid fa-headset text-warning me-2"></i> Need Admission Guidance or Information?</h3>
                    <p class="mb-0 text-light opacity-75">Connect directly with TECH ERA COMPUTER Helpdesk for prompt assistance.</p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-warning text-dark btn-lg fw-bold px-4 rounded-2 shadow-sm">
                        <i class="fa-solid fa-phone-volume me-2"></i> {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Comprehensive Footer -->
    <footer class="dce-footer">
        <div class="container">
            <div class="row g-4">
                <!-- About Col -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                             alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                             class="img-fluid bg-white p-1 rounded-2 shadow-sm" 
                             style="max-height: 52px; width: auto; object-fit: contain;">
                        <div>
                            <h4 class="text-white fw-bold mb-0" style="font-size: 1.25rem;">
                                <span style="color: #38bdf8;">TECH</span> <span style="color: #fb923c;">ERA</span> COMPUTER
                            </h4>
                            <div class="text-secondary small">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                        </div>
                    </div>
                    <p class="text-secondary small mb-3">
                        {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} is a premier computer technology institute offering career-oriented IT programs, programming, accounting, and typing courses designed to build job-ready digital skills.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://api.whatsapp.com/send?phone=91{{ $instituteSettings['contact_phone'] ?? '9162384660' }}&text=Hi%20Tech%20Era%20Computer" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width:36px; height:36px;"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-outline-light btn-sm rounded-circle" style="width:36px; height:36px;"><i class="fa-solid fa-phone"></i></a>
                    </div>
                </div>

                <!-- Fast Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5>Quick Links</h5>
                    <ul class="dce-footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('about') }}">About Institute</a></li>
                        <li><a href="{{ route('courses.index') }}">Courses Directory</a></li>
                        <li><a href="{{ route('gallery') }}">Campus Gallery</a></li>
                        <li><a href="{{ route('facilities') }}">Lab Facilities</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Student & Franchise Links -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h5>Student & Verification</h5>
                    <ul class="dce-footer-links">
                        <li><a href="{{ route('verify.certificate') }}">Check Certificate / Marksheet</a></li>
                        <li><a href="{{ route('verify.admission') }}">Check Admission Status</a></li>
                        <li><a href="{{ route('payment.index') }}">Online Fee Payment</a></li>
                        <li><a href="{{ route('student.login') }}">Student Login</a></li>
                        <li><a href="{{ route('enquiry.admission') }}">Admission Enquiry</a></li>
                        <li><a href="{{ route('franchise.search') }}">Search Centre Locator</a></li>
                    </ul>
                </div>

                <!-- Contact & Corporate Office -->
                <div class="col-lg-3 col-md-6">
                    <h5>Institute Campus</h5>
                    <ul class="dce-footer-links text-secondary small">
                        <li class="mb-2"><i class="fa-solid fa-location-dot text-info me-2"></i> {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</li>
                        <li class="mb-2"><i class="fa-solid fa-phone text-warning me-2"></i> +91 {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</li>
                        @if(!empty($instituteSettings['contact_email']))
                        <li class="mb-2"><i class="fa-solid fa-envelope text-info me-2"></i> {{ $instituteSettings['contact_email'] }}</li>
                        @endif
                        <li class="mb-2"><i class="fa-solid fa-clock text-success me-2"></i> Mon - Sat: 08:00 AM - 07:00 PM</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Legal Bar -->
            <div class="dce-footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    &copy; {{ date('Y') }} <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong>. All Rights Reserved.
                </div>
                <div class="d-flex gap-3 small">
                    <a href="{{ route('privacy') }}" class="text-secondary text-decoration-none">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="text-secondary text-decoration-none">Terms & Conditions</a>
                    <a href="{{ route('refund') }}" class="text-secondary text-decoration-none">Refund & Cancellation</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- 8. Floating Quick-Contact Badges -->
    <a href="https://api.whatsapp.com/send?phone=91{{ $instituteSettings['contact_phone'] ?? '9162384660' }}&text=Hi%20Tech%20Era%20Computer,%20I%20have%20an%20enquiry" target="_blank" class="dce-float-whatsapp" title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <div class="dce-float-helpline d-none d-md-flex">
        <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" title="Call Helpline"><i class="fa-solid fa-phone"></i></a>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom Brand JS -->
    <script src="{{ asset('js/drishti-custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
