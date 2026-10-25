<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --dce-primary: #0284c7;
            --dce-primary-dark: #0369a1;
            --dce-primary-light: #38bdf8;
            --dce-navy: #0b1329;
            --dce-navy-surface: #0f172a;
            --dce-accent: #ea580c;
            --dce-accent-green: #10b981;
            --dce-text-dark: #0f172a;
            --dce-text-muted: #64748b;
            --dce-bg-page: #f8fafc;
            --dce-card-bg: #ffffff;
            --dce-border-subtle: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--dce-bg-page);
            color: var(--dce-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        .btn, .btn-sm, .btn-lg, .btn.rounded-pill, .rounded-pill.btn {
            border-radius: 6px !important;
        }

        .btn {
            font-size: 0.81rem;
            font-weight: 600;
            padding: 0.3rem 0.72rem;
            line-height: 1.4;
            transition: all 0.18s ease;
        }

        .btn-sm {
            padding: 0.2rem 0.55rem !important;
            font-size: 0.75rem !important;
            line-height: 1.35 !important;
            border-radius: 5px !important;
        }

        .btn-lg {
            padding: 0.45rem 1rem !important;
            font-size: 0.88rem !important;
            border-radius: 6px !important;
        }

        /* Top Brand Navy Header */
        .student-header {
            background: linear-gradient(135deg, #070d1e 0%, #0b1530 50%, #0284c7 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .student-hamburger-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .student-hamburger-btn:hover, .student-hamburger-btn:focus {
            background: rgba(255, 255, 255, 0.2);
            color: #38bdf8;
            outline: none;
        }

        .student-action-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .student-action-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #38bdf8;
        }

        .student-profile-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 30px;
            padding: 3px 12px 3px 4px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .student-profile-pill:hover, .student-profile-pill:focus {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .student-brand-logo {
            height: 38px;
            object-fit: contain;
        }

        .student-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Desktop Sub-Navbar */
        .student-subnav {
            background: #ffffff;
            border-bottom: 1px solid var(--dce-border-subtle);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .student-subnav-scroll {
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .student-subnav-scroll::-webkit-scrollbar {
            display: none;
        }

        .student-subnav .nav-link {
            color: #475569;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 0.45rem 0.85rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            border: 1px solid transparent;
        }

        .student-subnav .nav-link:hover {
            color: var(--dce-primary);
            background: #f0f9ff;
            border-color: #e0f2fe;
        }

        .student-subnav .nav-link.active {
            color: var(--dce-primary);
            background: #e0f2fe;
            border-color: #bae6fd;
            font-weight: 700;
        }

        /* Enhanced Card Design (Admin ERP Aligned) */
        .card,
        .card.border-0,
        .stat-card,
        .dce-stat-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05) !important;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .card:hover,
        .stat-card:hover,
        .dce-stat-card:hover {
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08) !important;
        }

        /* KPI Card Colored Left Borders */
        .card.border-start.border-4.border-primary,
        .stat-card.border-start.border-4.border-primary,
        .stat-card.border-start.border-primary,
        .dce-stat-card.border-start.border-primary {
            border-left: 4px solid var(--dce-primary, #0284c7) !important;
        }
        .card.border-start.border-4.border-success,
        .stat-card.border-start.border-4.border-success,
        .stat-card.border-start.border-success,
        .dce-stat-card.border-start.border-success {
            border-left: 4px solid #10b981 !important;
        }
        .card.border-start.border-4.border-danger,
        .stat-card.border-start.border-4.border-danger,
        .stat-card.border-start.border-danger,
        .dce-stat-card.border-start.border-danger {
            border-left: 4px solid #ef4444 !important;
        }
        .card.border-start.border-4.border-warning,
        .stat-card.border-start.border-4.border-warning,
        .stat-card.border-start.border-warning,
        .dce-stat-card.border-start.border-warning {
            border-left: 4px solid #f59e0b !important;
        }
        .card.border-start.border-4.border-info,
        .stat-card.border-start.border-4.border-info,
        .stat-card.border-start.border-info,
        .dce-stat-card.border-start.border-info {
            border-left: 4px solid #06b6d4 !important;
        }
        .card.border-start.border-4.border-dark,
        .stat-card.border-start.border-4.border-dark,
        .stat-card.border-start.border-dark,
        .dce-stat-card.border-start.border-dark {
            border-left: 4px solid #1e293b !important;
        }

        .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0.85rem 1.15rem;
            font-weight: 700;
            color: #0f172a;
        }

        .card-body {
            padding: 1.15rem 1.25rem;
        }

        /* Form controls inside cards */
        .card .form-control,
        .card .form-select,
        .form-control,
        .form-select {
            border-radius: 6px !important;
            font-size: 0.84rem;
            border: 1px solid #cbd5e1;
            padding: 0.42rem 0.75rem;
        }

        .card .form-control:focus,
        .card .form-select:focus,
        .form-control:focus,
        .form-select:focus {
            border-color: var(--dce-primary, #0284c7);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        /* Badge Customization */
        .badge-lavender {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }

        .badge-success-subtle {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        .badge-danger-subtle {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
        }

        /* Modern Enterprise Table Styling (Admin ERP Aligned) */
        .table-custom,
        .table {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table-custom thead th,
        .table thead th,
        .table th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 0.65rem 0.85rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
        }

        .table-custom tbody tr,
        .table tbody tr {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:hover td,
        .table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        .table-custom tbody td,
        .table tbody td {
            padding: 0.65rem 0.85rem !important;
            vertical-align: middle !important;
            color: #334155;
            font-size: 0.82rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .table-custom .badge,
        .table .badge {
            font-size: 0.72rem !important;
            font-weight: 600 !important;
            padding: 0.25rem 0.55rem !important;
            border-radius: 4px !important;
        }

        .table-custom .btn,
        .table .btn {
            padding: 0.2rem 0.55rem !important;
            font-size: 0.75rem !important;
            border-radius: 5px !important;
            white-space: nowrap !important;
        }

        /* Sleek Table Responsive Container */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
            border-radius: 0 0 8px 8px;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .table-scroll-hint {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            font-size: 0.7rem;
            color: #64748b;
            padding: 0.35rem 0.85rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 500;
        }
        .table-scroll-hint i {
            color: var(--dce-primary, #0284c7);
        }

        /* Mobile Responsive Adjustments for Tables */
        @media (max-width: 767.98px) {
            .table-custom thead th,
            .table thead th {
                padding: 0.55rem 0.65rem !important;
                font-size: 0.66rem !important;
            }
            
            .table-custom tbody td,
            .table tbody td {
                padding: 0.55rem 0.65rem !important;
                font-size: 0.78rem !important;
            }

            .table-custom .btn-sm,
            .table .btn-sm {
                padding: 0.2rem 0.5rem !important;
                font-size: 0.72rem !important;
                white-space: nowrap !important;
            }

            .table-custom .badge,
            .table .badge {
                font-size: 0.66rem !important;
                padding: 0.25rem 0.5rem !important;
                white-space: nowrap !important;
            }
        }

        /* ==========================================================================
           PAGINATION SYSTEM (Bootstrap 5 & SVG Guard)
           ========================================================================== */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .pagination .page-item .page-link {
            border-radius: 6px !important;
            padding: 0.25rem 0.6rem;
            font-size: 0.78rem;
            color: #334155;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .pagination .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: var(--dce-primary, #0284c7);
            border-color: #cbd5e1;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--dce-primary, #0284c7) !important;
            border-color: var(--dce-primary, #0284c7) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);
        }

        .pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Hard guard against giant runaway SVGs in pagination */
        .pagination svg,
        .page-item svg,
        nav[role="navigation"] svg,
        .pagination-wrapper svg,
        nav svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block;
            vertical-align: middle;
        }

        /* Offcanvas Drawer for Mobile Navigation */
        .student-offcanvas {
            max-width: 310px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
        }

        .student-offcanvas-header {
            background: linear-gradient(135deg, #0b1329 0%, #0f172a 100%);
            color: #ffffff;
            padding: 1.25rem 1rem;
        }

        .student-drawer-section-title {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            padding: 0.75rem 1rem 0.35rem;
        }

        .student-drawer-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.65rem 1rem;
            color: #334155;
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 600;
            border-radius: 6px;
            margin: 2px 8px;
            transition: all 0.2s ease;
        }

        .student-drawer-link:hover {
            background: #f0f9ff;
            color: #0284c7;
        }

        .student-drawer-link.active {
            background: #e0f2fe;
            color: #0284c7;
            font-weight: 700;
        }

        .student-drawer-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Fixed Mobile Bottom Navigation */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.06);
            z-index: 1040;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 0.5rem;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            flex: 1;
            padding: 0.35rem 0;
            position: relative;
            transition: color 0.2s ease;
            border: none;
            background: transparent;
        }

        .mobile-nav-item i {
            font-size: 1.15rem;
            margin-bottom: 2px;
        }

        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: #0284c7;
            font-weight: 700;
        }

        /* Mobile Card & Container Formatting (< 768px) */
        @media (max-width: 767.98px) {
            .student-header {
                min-height: 54px;
                padding: 6px 10px !important;
            }

            .student-hamburger-btn {
                width: 32px;
                height: 32px;
            }

            .student-action-btn {
                width: 32px;
                height: 32px;
            }

            .student-avatar {
                width: 32px !important;
                height: 32px !important;
            }

            /* Modern Mobile Cards Across Student Portal */
            .card {
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05) !important;
                margin-bottom: 1rem !important;
            }

            .card-header {
                padding: 0.75rem 0.95rem !important;
                background-color: #ffffff !important;
                border-bottom: 1px solid #f1f5f9 !important;
                border-top-left-radius: 12px !important;
                border-top-right-radius: 12px !important;
            }

            .card-header h5,
            .card-header h6,
            .card-header .h5,
            .card-header .h6 {
                font-size: 0.88rem !important;
                font-weight: 700 !important;
            }

            .card-body,
            .card-body.p-4,
            .card-body.p-3 {
                padding: 0.95rem 1rem !important;
            }

            .card-footer {
                padding: 0.75rem 0.95rem !important;
                border-bottom-left-radius: 12px !important;
                border-bottom-right-radius: 12px !important;
            }

            .card .form-label {
                font-size: 0.78rem !important;
                font-weight: 700 !important;
                color: #334155 !important;
                margin-bottom: 0.25rem !important;
            }

            .card .form-control,
            .card .form-select {
                font-size: 0.83rem !important;
                padding: 0.45rem 0.7rem !important;
                border-radius: 7px !important;
                border: 1px solid #cbd5e1 !important;
            }

            .card .form-text {
                font-size: 0.7rem !important;
                margin-top: 0.25rem !important;
                color: #64748b !important;
            }

            .student-main-content h3,
            .student-main-content .h3,
            .student-main-content h2,
            .student-main-content .h2,
            .student-main-content h4,
            .student-main-content .h4 {
                font-size: 1.15rem !important;
                line-height: 1.3 !important;
            }

            /* Mobile Action Headers Stacking */
            .student-main-content > .d-flex.justify-content-between,
            .student-main-content > .d-flex.flex-wrap.justify-content-between,
            .student-main-content .container > .d-flex.justify-content-between,
            .student-main-content .container > .d-flex.flex-column.flex-md-row.justify-content-between,
            .student-main-content .container > .d-flex.flex-wrap.justify-content-between {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
                margin-bottom: 1rem !important;
            }

            .student-main-content > .d-flex.justify-content-between > .d-flex,
            .student-main-content > .d-flex.flex-wrap.justify-content-between > .d-flex,
            .student-main-content .container > .d-flex > .d-flex {
                width: 100% !important;
                flex-wrap: wrap !important;
                gap: 6px !important;
            }

            .student-main-content > .d-flex.justify-content-between > .d-flex .btn,
            .student-main-content > .d-flex.flex-wrap.justify-content-between > .d-flex .btn,
            .student-main-content .container > .d-flex > .d-flex .btn {
                flex: 1 1 auto !important;
                text-align: center !important;
                justify-content: center !important;
                font-size: 0.78rem !important;
                padding: 0.35rem 0.65rem !important;
            }

            .student-main-content {
                padding-bottom: 88px !important;
            }
        }

        /* Print formatting */
        @media print {
            .student-header,
            .student-subnav,
            .mobile-bottom-nav,
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $authUser = auth()->user();
        $authStudent = $authUser ? ($authUser->student ?? \App\Models\Student::where('user_id', $authUser->id)->first()) : null;
        if (!$authStudent && $authUser && $authUser->isSuperAdmin()) {
            $authStudent = \App\Models\Student::first();
        }

        $unreadCount = 0;
        if ($authUser) {
            $unreadCount = \App\Models\SystemNotification::where(function($q) use ($authUser) {
                $q->where('user_id', $authUser->id)->orWhereNull('user_id');
            })->whereNull('read_at')->count();
        }
    @endphp

    <!-- TOP NAVY/ELECTRIC BLUE BRAND HEADER -->
    <header class="student-header py-1 py-sm-2 text-white shadow-sm position-sticky top-0 z-3">
        <div class="container px-2 px-sm-3 d-flex align-items-center justify-content-between gap-1">
            <!-- Left: Mobile Menu Toggle & Brand Logo/Title -->
            <div class="d-flex align-items-center flex-grow-1 overflow-hidden me-1">
                <!-- Mobile Drawer Hamburger Button -->
                <button class="student-hamburger-btn d-md-none me-2 flex-shrink-0" 
                        type="button" 
                        data-bs-toggle="offcanvas" 
                        data-bs-target="#studentOffcanvasNav" 
                        aria-controls="studentOffcanvasNav" 
                        aria-label="Open Student Navigation Menu">
                    <i class="fa-solid fa-bars fs-5"></i>
                </button>

                <!-- Brand Link (Desktop & Mobile) -->
                <a href="{{ route('student.dashboard') }}" class="d-flex align-items-center gap-2 gap-sm-3 text-white text-decoration-none overflow-hidden">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="rounded-2 bg-white p-1 shadow-sm flex-shrink-0" 
                         style="height: 34px; width: auto; object-fit: contain;">
                    <div class="text-start overflow-hidden lh-1">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <span class="fw-bold text-white text-uppercase text-truncate" style="font-size: 0.9rem; letter-spacing: 0.4px; max-width: 160px;">
                                {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                            </span>
                            <span class="badge rounded-pill px-2 py-0.5 fw-semibold text-uppercase d-none d-lg-inline-block" style="background: rgba(56, 189, 248, 0.2); border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.65rem; color: #38bdf8;">
                                <i class="fa-solid fa-graduation-cap me-1"></i> Student Portal
                            </span>
                        </div>
                        <div style="font-size: 0.65rem; letter-spacing: 0.3px;" class="mt-0.5 text-truncate">
                            <span class="d-none d-md-inline text-white-50">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</span>
                            <span class="d-md-none fw-semibold" style="color: #38bdf8;">Student Portal</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Right Controls: Notifications & Student Profile -->
            <div class="d-flex align-items-center gap-2 gap-sm-3 justify-content-end flex-shrink-0">
                <!-- Notification Bell Icon -->
                <a href="{{ route('student.notifications') }}" class="student-action-btn position-relative text-white text-decoration-none" title="Notifications">
                    <i class="fa-regular fa-bell fs-5"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem; margin-top: 4px; margin-left: -4px;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle student-profile-pill" id="studentMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="student-avatar shadow-sm position-relative">
                            @if($authStudent && $authStudent->photo_path && file_exists(public_path('storage/' . $authStudent->photo_path)))
                                <img src="{{ asset('storage/' . $authStudent->photo_path) }}" alt="{{ $authStudent->full_name }}" class="w-100 h-100 rounded-circle object-fit-cover">
                            @else
                                {{ $authStudent ? strtoupper(substr($authStudent->full_name, 0, 1)) : 'S' }}
                            @endif
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="d-none d-sm-block text-start pe-1">
                            <div class="small fw-bold lh-1 text-white text-truncate" style="max-width: 140px;">{{ $authStudent ? $authStudent->full_name : 'Student' }}</div>
                            <div class="font-monospace mt-0.5" style="font-size: 0.65rem; color: #93c5fd;">{{ $authStudent->roll_no ?? 'ID Card' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-2" aria-labelledby="studentMenuDropdown" style="min-width: 240px;">
                        <li class="px-3 py-2 border-bottom bg-light rounded-top-3">
                            <div class="fw-bold text-dark fs-6">{{ $authStudent->full_name ?? 'Student' }}</div>
                            <div class="small text-muted font-monospace">{{ $authStudent->roll_no ?? '' }}</div>
                            @if($authStudent && $authStudent->centre)
                                <div class="badge bg-primary-subtle text-primary rounded-1 small mt-1 font-sans">{{ $authStudent->centre->centre_name }}</div>
                            @endif
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.profile') }}">
                                <i class="fa-solid fa-user me-2 text-primary"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.id-card') }}">
                                <i class="fa-solid fa-id-card me-2 text-info"></i> Digital ID Card
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.admission-form') }}">
                                <i class="fa-solid fa-file-contract me-2 text-secondary"></i> My Admission Form
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.change-password') }}">
                                <i class="fa-solid fa-key me-2 text-warning"></i> Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 small text-danger fw-semibold">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- OFFCANVAS MOBILE NAVIGATION DRAWER -->
    <div class="offcanvas offcanvas-start student-offcanvas shadow-lg" tabindex="-1" id="studentOffcanvasNav" aria-labelledby="studentOffcanvasNavLabel">
        <div class="offcanvas-header student-offcanvas-header border-bottom border-light border-opacity-10">
            <div class="d-flex align-items-center gap-2" id="studentOffcanvasNavLabel">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                     alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA' }}" 
                     class="rounded-2 bg-white p-1" 
                     style="height: 32px; width: auto; object-fit: contain;">
                <div>
                    <div class="fw-bold small text-white text-uppercase lh-1" style="letter-spacing: 0.5px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                    <span class="badge rounded-1 px-1.5 py-0.5 fw-bold text-uppercase mt-1" style="background: rgba(2, 132, 199, 0.3); border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.65rem; color: #38bdf8;">
                        Student Portal
                    </span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Student Quick Profile Banner -->
        <div class="p-3 border-bottom bg-light">
            <div class="d-flex align-items-center gap-3">
                <div class="student-avatar" style="width: 44px; height: 44px; font-size: 1.1rem;">
                    @if($authStudent && $authStudent->photo_path && file_exists(public_path('storage/' . $authStudent->photo_path)))
                        <img src="{{ asset('storage/' . $authStudent->photo_path) }}" alt="{{ $authStudent->full_name }}" class="w-100 h-100 rounded-circle object-fit-cover">
                    @else
                        {{ $authStudent ? strtoupper(substr($authStudent->full_name, 0, 1)) : 'S' }}
                    @endif
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-dark text-truncate">{{ $authStudent->full_name ?? 'Student' }}</div>
                    <div class="text-muted font-monospace small" style="font-size: 0.72rem;">Roll: {{ $authStudent->roll_no ?? 'Unassigned' }}</div>
                    @if($authStudent && $authStudent->centre)
                        <div class="text-truncate text-primary small" style="font-size: 0.68rem;"><i class="fa-solid fa-location-dot me-1"></i>{{ $authStudent->centre->centre_name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="offcanvas-body px-0 py-2">
            <!-- 1. ACADEMICS -->
            <div class="student-drawer-section-title">
                Academics
            </div>
            <ul class="list-unstyled mb-2">
                <li>
                    <a href="{{ route('student.dashboard') }}" class="student-drawer-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-house"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.course') }}" class="student-drawer-link {{ request()->routeIs('student.course*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-info-subtle text-info">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <span>My Course & Syllabus</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.study-materials') }}" class="student-drawer-link {{ request()->routeIs('student.study-materials*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <span>Study Materials</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.schedule') }}" class="student-drawer-link {{ request()->routeIs('student.schedule*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-secondary-subtle text-secondary">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <span>Batch Timetable</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.attendance') }}" class="student-drawer-link {{ request()->routeIs('student.attendance*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-success-subtle text-success">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <span>Attendance Log</span>
                    </a>
                </li>
            </ul>

            <!-- 2. EXAMINATIONS & RECORDS -->
            <div class="student-drawer-section-title">
                Examinations & Records
            </div>
            <ul class="list-unstyled mb-2">
                <li>
                    <a href="{{ route('student.exams') }}" class="student-drawer-link {{ request()->routeIs('student.exams*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <span>Exams & Hall Tickets</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.results') }}" class="student-drawer-link {{ request()->routeIs('student.results*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-info-subtle text-info">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                        </div>
                        <span>Marksheets & Grades</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.certificates') }}" class="student-drawer-link {{ request()->routeIs('student.certificates*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <span>Certificates</span>
                    </a>
                </li>
            </ul>

            <!-- 3. FINANCE & IDENTITY -->
            <div class="student-drawer-section-title">
                Finance & Identity
            </div>
            <ul class="list-unstyled mb-2">
                <li>
                    <a href="{{ route('student.fees') }}" class="student-drawer-link {{ request()->routeIs('student.fees*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-success-subtle text-success">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span>Fee Ledger & Receipts</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.id-card') }}" class="student-drawer-link {{ request()->routeIs('student.id-card*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <span>Digital ID Card</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.admission-form') }}" class="student-drawer-link {{ request()->routeIs('student.admission-form*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-secondary-subtle text-secondary">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <span>Admission Form</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.profile') }}" class="student-drawer-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-info-subtle text-info">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span>My Profile</span>
                    </a>
                </li>
            </ul>

            <!-- 4. SUPPORT & SECURITY -->
            <div class="student-drawer-section-title">
                Support & Account
            </div>
            <ul class="list-unstyled mb-3">
                <li>
                    <a href="{{ route('student.notifications') }}" class="student-drawer-link {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <span class="flex-grow-1">Notifications & Bulletins</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.support') }}" class="student-drawer-link {{ request()->routeIs('student.support*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <span>Helpdesk & Support</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.change-password') }}" class="student-drawer-link {{ request()->routeIs('student.change-password*') ? 'active' : '' }}">
                        <div class="student-drawer-icon bg-secondary-subtle text-secondary">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <span>Change Password</span>
                    </a>
                </li>
            </ul>

            <div class="px-3 pt-2 border-top">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-2 text-start px-3 py-2">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- DESKTOP SUB-NAVBAR -->
    <nav class="student-subnav d-none d-md-block py-2">
        <div class="container">
            <div class="student-subnav-scroll">
                <ul class="nav flex-nowrap gap-1 py-0.5">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="fa-solid fa-house me-1.5"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.course*') ? 'active' : '' }}" href="{{ route('student.course') }}">
                            <i class="fa-solid fa-book-open me-1.5"></i> My Course
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.fees*') ? 'active' : '' }}" href="{{ route('student.fees') }}">
                            <i class="fa-solid fa-receipt me-1.5"></i> Fees & Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.study-materials*') ? 'active' : '' }}" href="{{ route('student.study-materials') }}">
                            <i class="fa-solid fa-folder-open me-1.5"></i> Study Materials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.schedule*') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
                            <i class="fa-solid fa-calendar-days me-1.5"></i> Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.attendance*') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                            <i class="fa-solid fa-clipboard-user me-1.5"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.exams*') ? 'active' : '' }}" href="{{ route('student.exams') }}">
                            <i class="fa-solid fa-pen-nib me-1.5"></i> Exams & Hall Tickets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.results*') ? 'active' : '' }}" href="{{ route('student.results') }}">
                            <i class="fa-solid fa-square-poll-vertical me-1.5"></i> Marksheets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.certificates*') ? 'active' : '' }}" href="{{ route('student.certificates') }}">
                            <i class="fa-solid fa-award me-1.5"></i> Certificates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.support*') ? 'active' : '' }}" href="{{ route('student.support') }}">
                            <i class="fa-solid fa-headset me-1.5"></i> Support
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN BODY CONTENT -->
    <main class="student-main-content py-4 flex-grow-1">
        <div class="container">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-5 me-2 text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fs-5 me-2 text-danger"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Please resolve the following errors:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- MOBILE FIXED BOTTOM NAVIGATION -->
    <nav class="mobile-bottom-nav d-md-none" aria-label="Mobile Navigation">
        <!-- 1. Home -->
        <a href="{{ route('student.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>

        <!-- 2. Course -->
        <a href="{{ route('student.course') }}" class="mobile-nav-item {{ request()->routeIs('student.course*') || request()->routeIs('student.study-materials*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i>
            <span>Course</span>
        </a>

        <!-- 3. Fees -->
        <a href="{{ route('student.fees') }}" class="mobile-nav-item {{ request()->routeIs('student.fees*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span>Fees</span>
        </a>

        <!-- 4. Notices -->
        <a href="{{ route('student.notifications') }}" class="mobile-nav-item {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell position-relative">
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                @endif
            </i>
            <span>Notices</span>
        </a>

        <!-- 5. All Menu (Opens Drawer) -->
        <button type="button" class="mobile-nav-item border-0 bg-transparent" data-bs-toggle="offcanvas" data-bs-target="#studentOffcanvasNav" aria-controls="studentOffcanvasNav" aria-label="Open Full Navigation Menu">
            <i class="fa-solid fa-bars"></i>
            <span>Menu</span>
        </button>
    </nav>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-detect overflowing tables on mobile and insert swipe hint
            function checkStudentTableScrollHints() {
                if (window.innerWidth < 992) {
                    document.querySelectorAll('.table-responsive').forEach(function (el) {
                        if (el.scrollWidth > el.clientWidth && !el.previousElementSibling?.classList.contains('table-scroll-hint')) {
                            const hint = document.createElement('div');
                            hint.className = 'table-scroll-hint d-lg-none';
                            hint.innerHTML = '<i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Swipe table horizontally';
                            el.parentNode.insertBefore(hint, el);
                        }
                    });
                }
            }
            checkStudentTableScrollHints();
            window.addEventListener('resize', checkStudentTableScrollHints);

            // Auto-dismiss alert banners after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert.alert-dismissible:not(.alert-important)').forEach(function (alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                });
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
