<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin ERP - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))</title>

    <!-- Google Fonts & Font Awesome 6 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ERP Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/drishti-admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <!-- TURQUOISE TOPBAR -->
    <nav class="navbar navbar-expand navbar-dark dce-admin-topbar px-2 px-sm-3">
        <div class="d-flex align-items-center gap-1 gap-sm-2 flex-grow-1 overflow-hidden me-2">
            <button class="btn btn-link text-white p-1 d-lg-none me-1 flex-shrink-0" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars fs-5"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none overflow-hidden me-1" href="{{ route('admin.dashboard') }}">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1 flex-shrink-0" style="height: 34px; width: auto; object-fit: contain;">
                <span class="fw-bold tracking-tight text-truncate" style="font-size: 0.95rem; max-width: 180px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} <span class="fw-light opacity-75">ERP</span></span>
            </a>
            <span class="topbar-badge d-none d-md-inline-block">
                {{ auth()->user()->isSuperAdmin() ? 'HEAD OFFICE' : (auth()->user()->centre?->centre_code ?? 'CENTRE PANEL') }}
            </span>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-light d-none d-sm-inline-flex align-items-center gap-1 text-decoration-none">
                <i class="fa-solid fa-arrow-up-right-from-square small"></i> Public Website
            </a>

            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle gap-2" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle bg-white text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.9rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-md-block text-start lh-1">
                        <div class="small fw-bold">{{ auth()->user()->name }}</div>
                        <span class="text-white-50" style="font-size: 11px;">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userMenuDropdown">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted small" style="font-size: 12px;">{{ auth()->user()->email }}</div>
                    </li>
                    <li><a class="dropdown-item py-2" href="{{ Route::has('admin.settings.index') ? route('admin.settings.index') : '#' }}"><i class="fa-solid fa-gear me-2 text-secondary"></i>Settings</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- WRAPPER: SIDEBAR + CONTENT -->
    <div class="dce-admin-wrapper">
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- LEFT SIDEBAR -->
        <aside class="dce-admin-sidebar" id="adminSidebar">
            <nav class="dce-sidebar-nav py-2">
                <!-- MAIN -->
                <div class="sidebar-heading">Core</div>
                <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>

                <!-- ACADEMICS -->
                <div class="sidebar-heading">Students & Academics</div>
                <a class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}" href="{{ Route::has('admin.students.index') ? route('admin.students.index') : '#' }}">
                    <i class="fa-solid fa-user-graduate"></i> Students
                </a>
                <a class="nav-link {{ request()->routeIs('admin.admissions*') ? 'active' : '' }}" href="{{ Route::has('admin.admissions.index') ? route('admin.admissions.index') : '#' }}">
                    <i class="fa-solid fa-id-card"></i> Admissions
                </a>
                <a class="nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : '#' }}">
                    <i class="fa-solid fa-book-bookmark"></i> Courses
                </a>
                <a class="nav-link {{ request()->routeIs('admin.batches*') ? 'active' : '' }}" href="{{ Route::has('admin.batches.index') ? route('admin.batches.index') : '#' }}">
                    <i class="fa-solid fa-calendar-check"></i> Batches & Attendance
                </a>
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.exams*') ? 'active' : '' }}" 
                   data-bs-toggle="collapse" href="#adminExamsSubmenu" role="button" 
                   aria-expanded="{{ request()->routeIs('admin.exams*') ? 'true' : 'false' }}">
                    <div>
                        <i class="fa-solid fa-pen-nib"></i> Examinations
                    </div>
                    <i class="fa-solid fa-chevron-down small text-muted" style="font-size: 0.72rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.exams*') ? 'show' : '' }}" id="adminExamsSubmenu">
                    <div class="py-1 ps-4" style="background: rgba(0,0,0,0.2);">
                        <a class="nav-link py-1 small {{ request()->routeIs('admin.exams.index') || request()->routeIs('admin.exams.create') || request()->routeIs('admin.exams.show') || request()->routeIs('admin.exams.edit') ? 'active fw-bold' : '' }}" href="{{ route('admin.exams.index') }}">
                            <i class="fa-solid fa-calendar-days me-2" style="font-size: 0.8rem;"></i> Exam Cycles
                        </a>
                        <a class="nav-link py-1 small {{ request()->routeIs('admin.exams.questions*') ? 'active fw-bold' : '' }}" href="{{ route('admin.exams.questions') }}">
                            <i class="fa-solid fa-circle-question me-2" style="font-size: 0.8rem;"></i> Question Bank
                        </a>
                        <a class="nav-link py-1 small {{ request()->routeIs('admin.exams.results*') && !request()->routeIs('admin.exams.results.create') ? 'active fw-bold' : '' }}" href="{{ route('admin.exams.results') }}">
                            <i class="fa-solid fa-square-poll-vertical me-2" style="font-size: 0.8rem;"></i> Exam Results
                        </a>
                        <a class="nav-link py-1 small {{ request()->routeIs('admin.exams.results.create') ? 'active fw-bold' : '' }}" href="{{ route('admin.exams.results.create') }}">
                            <i class="fa-solid fa-marker me-2" style="font-size: 0.8rem;"></i> Enter Marks
                        </a>
                    </div>
                </div>
                <a class="nav-link {{ request()->routeIs('admin.materials*') ? 'active' : '' }}" href="{{ Route::has('admin.materials.index') ? route('admin.materials.index') : '#' }}">
                    <i class="fa-solid fa-file-pdf"></i> Study Materials
                </a>
                <a class="nav-link {{ request()->routeIs('admin.certificates*') ? 'active' : '' }}" href="{{ Route::has('admin.certificates.index') ? route('admin.certificates.index') : '#' }}">
                    <i class="fa-solid fa-certificate"></i> Certificates
                </a>

                <!-- FINANCE -->
                <div class="sidebar-heading">Finance & Franchise</div>
                <a class="nav-link {{ request()->routeIs('admin.fees*') ? 'active' : '' }}" href="{{ Route::has('admin.fees.index') ? route('admin.fees.index') : '#' }}">
                    <i class="fa-solid fa-indian-rupee-sign"></i> Fees & Payments
                </a>
                <a class="nav-link {{ request()->routeIs('admin.centres*') ? 'active' : '' }}" href="{{ Route::has('admin.centres.index') ? route('admin.centres.index') : '#' }}">
                    <i class="fa-solid fa-building-columns"></i> Franchise / Centres
                </a>
                <a class="nav-link {{ request()->routeIs('admin.downloads*') ? 'active' : '' }}" href="{{ Route::has('admin.downloads.index') ? route('admin.downloads.index') : '#' }}">
                    <i class="fa-solid fa-box-archive"></i> Downloads & Orders
                </a>
                <a class="nav-link {{ request()->routeIs('admin.wallet*') ? 'active' : '' }}" href="{{ Route::has('admin.wallet.index') ? route('admin.wallet.index') : '#' }}">
                    <i class="fa-solid fa-wallet"></i> Wallet History
                </a>
                <a class="nav-link {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}" href="{{ Route::has('admin.expenses.index') ? route('admin.expenses.index') : '#' }}">
                    <i class="fa-solid fa-calculator"></i> Expenses
                </a>

                <!-- OPERATIONS -->
                <div class="sidebar-heading">Operations</div>
                <a class="nav-link {{ request()->routeIs('admin.support*') ? 'active' : '' }}" href="{{ Route::has('admin.support.index') ? route('admin.support.index') : '#' }}">
                    <i class="fa-solid fa-headset"></i> Help & Support
                </a>
                <a class="nav-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}" href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : '#' }}">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ Route::has('admin.reports.index') ? route('admin.reports.index') : '#' }}">
                    <i class="fa-solid fa-chart-column"></i> Reports
                </a>

                <!-- SYSTEM -->
                <div class="sidebar-heading">System & Config</div>
                <a class="nav-link {{ request()->routeIs('admin.cms*') ? 'active' : '' }}" href="{{ Route::has('admin.cms.index') ? route('admin.cms.index') : '#' }}">
                    <i class="fa-solid fa-globe"></i> Website CMS
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}">
                    <i class="fa-solid fa-users-gear"></i> User Management
                </a>
                <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ Route::has('admin.settings.index') ? route('admin.settings.index') : '#' }}">
                    <i class="fa-solid fa-sliders"></i> Settings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}" href="{{ Route::has('admin.audit-logs.index') ? route('admin.audit-logs.index') : '#' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Audit Logs
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="dce-admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please resolve the following input errors:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle & Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/drishti-admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
