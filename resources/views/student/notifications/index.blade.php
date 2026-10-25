@extends('layouts.student')

@section('title', 'Notifications - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" 
                 style="width: 44px; height: 44px; border-radius: 8px; background: linear-gradient(135deg, #0b1329 0%, #0284c7 100%);">
                <i class="fa-solid fa-bell fs-5"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold mb-0 text-dark">
                    Notifications & Bulletins
                </h2>
                <p class="text-muted small mb-0">Stay informed about upcoming exam timetables, fee due reminders, material uploads, and academic notices.</p>
            </div>
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('student.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-2 fw-semibold px-3 py-1.5">
                    <i class="fa-solid fa-check-double me-1"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Notifications List Card -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 bg-white">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold fs-6 mb-0 text-dark">
                <i class="fa-solid fa-inbox text-primary me-2"></i>System Inbox
            </h5>
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill px-2 py-1 small">
                    {{ $unreadCount }} Unread
                </span>
            @else
                <span class="badge bg-light text-muted border px-2 py-1 small">All Caught Up</span>
            @endif
        </div>
    </div>

    <div class="card-body p-0">
        @if($notifications->count() > 0)
            <div class="list-group list-group-flush divide-y">
                @foreach($notifications as $notif)
                    @php
                        $isUnread = is_null($notif->read_at);
                        $iconClass = match($notif->type) {
                            'fee'         => 'fa-solid fa-receipt text-success',
                            'exam'        => 'fa-solid fa-pen-nib text-primary',
                            'alert'       => 'fa-solid fa-triangle-exclamation text-danger',
                            'certificate' => 'fa-solid fa-award text-warning',
                            default       => 'fa-solid fa-circle-info text-info',
                        };
                        $bgSubtle = match($notif->type) {
                            'fee'         => 'bg-success-subtle',
                            'exam'        => 'bg-primary-subtle',
                            'alert'       => 'bg-danger-subtle',
                            'certificate' => 'bg-warning-subtle',
                            default       => 'bg-info-subtle',
                        };
                        $borderAccent = match($notif->type) {
                            'fee'         => '#10b981',
                            'exam'        => '#0284c7',
                            'alert'       => '#ef4444',
                            'certificate' => '#f59e0b',
                            default       => '#06b6d4',
                        };
                    @endphp
                    <div class="list-group-item p-3 p-md-4 {{ $isUnread ? 'bg-light bg-opacity-75' : '' }} transition-all" style="border-left: 3px solid {{ $isUnread ? $borderAccent : 'transparent' }};">
                        <div class="d-flex align-items-start gap-3">
                            <!-- Type Icon -->
                            <div class="rounded-2 p-3 {{ $bgSubtle }} flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="{{ $iconClass }} fs-5"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="fw-bold text-dark mb-0 {{ $isUnread ? 'fw-bolder' : '' }}">
                                        {{ $notif->title }}
                                    </h6>
                                    <span class="text-muted small" style="font-size: 0.72rem;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-muted small mb-2" style="line-height: 1.5;">
                                    {{ $notif->message }}
                                </p>

                                <div class="d-flex align-items-center gap-2">
                                    @if($notif->action_url)
                                        <a href="{{ $notif->action_url }}" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Details
                                        </a>
                                    @endif

                                    @if($isUnread)
                                        <form method="POST" action="{{ route('student.notifications.read', $notif->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted py-0 px-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-check me-1"></i> Mark as read
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-check-double text-primary me-1"></i> Read
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-5 px-3">
                <div class="rounded-3 mx-auto p-3 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background: #e0f2fe; color: #0284c7;">
                    <i class="fa-solid fa-bell-slash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Notifications Right Now</h5>
                <p class="text-muted small mb-4" style="max-width: 440px; margin: 0 auto; line-height: 1.6;">
                    You are completely up to date. Important announcements, fee payment reminders, and examination notices will appear here.
                </p>

                <!-- Quick Navigation Shortcuts -->
                <div class="d-flex flex-wrap justify-content-center gap-2" style="max-width: 460px; margin: 0 auto;">
                    <a href="{{ route('student.course') }}" class="btn btn-sm btn-outline-primary rounded-2 px-3 py-1.5 fw-semibold">
                        <i class="fa-solid fa-book-open me-1.5"></i> My Course
                    </a>
                    <a href="{{ route('student.fees') }}" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5 fw-semibold">
                        <i class="fa-solid fa-receipt me-1.5"></i> Fee Ledger
                    </a>
                    <a href="{{ route('student.exams') }}" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5 fw-semibold">
                        <i class="fa-solid fa-pen-nib me-1.5"></i> Exam Schedule
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
