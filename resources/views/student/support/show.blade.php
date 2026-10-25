@extends('layouts.student')

@section('title', 'Ticket #' . $ticket->ticket_no . ' - ' . $ticket->subject)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.support') }}" class="text-decoration-none">Support</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $ticket->ticket_no }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h2 class="h4 fw-bold mb-0 text-dark">Ticket #{{ $ticket->ticket_no }}</h2>
            @if($ticket->status === 'open')
                <span class="badge bg-warning-subtle text-warning px-2 py-1">Open</span>
            @elseif($ticket->status === 'in_progress')
                <span class="badge bg-info-subtle text-info px-2 py-1">In Progress</span>
            @elseif($ticket->status === 'waiting_for_student')
                <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i>Waiting for Student Action</span>
            @elseif($ticket->status === 'answered')
                <span class="badge bg-primary-subtle text-primary px-2 py-1">Answered</span>
            @elseif($ticket->status === 'resolved')
                <span class="badge bg-success-subtle text-success px-2 py-1">Resolved</span>
            @else
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Closed</span>
            @endif
        </div>
    </div>
    <a href="{{ route('student.support') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Tickets
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Conversation Thread -->
    <div class="col-12 col-lg-8">
        <!-- Original Ticket Body -->
        <div class="card border border-light-subtle shadow-sm rounded-3 mb-4 overflow-hidden bg-white">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="student-avatar bg-primary text-white" style="width: 34px; height: 34px; font-size: 0.8rem;">
                            {{ strtoupper(substr($student->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">{{ $student->full_name }} <span class="badge bg-light text-muted border ms-1 rounded-1">Author</span></div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $ticket->created_at->format('d M, Y \a\t h:i A') }}</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-1">{{ $ticket->category }}</span>
                </div>
            </div>
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">{{ $ticket->subject }}</h5>
                <div class="text-dark" style="white-space: pre-line; line-height: 1.6;">
                    {{ $ticket->description }}
                </div>
            </div>
        </div>

        <!-- Timeline of Replies -->
        @if($ticket->replies && $ticket->replies->count() > 0)
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-regular fa-comments text-primary me-2"></i>Conversation Activity ({{ $ticket->replies->count() }})
                </h6>

                <div class="d-flex flex-column gap-3">
                    @foreach($ticket->replies as $reply)
                        @php
                            $isStaff = $reply->is_staff_reply || ($reply->user && $reply->user->role !== 'student');
                        @endphp
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white {{ $isStaff ? 'border-start border-4 border-primary' : '' }}">
                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white {{ $isStaff ? 'bg-primary' : 'bg-secondary' }}" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        @if($isStaff)
                                            <i class="fa-solid fa-headset"></i>
                                        @else
                                            <i class="fa-solid fa-user"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="fw-bold small text-dark">{{ $reply->user->name ?? ($isStaff ? 'Support Staff' : 'You') }}</span>
                                        @if($isStaff)
                                            <span class="badge bg-primary text-white ms-1 rounded-1" style="font-size: 0.65rem;">Centre Staff</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="text-dark small" style="white-space: pre-line; line-height: 1.6;">
                                    {{ $reply->message }}
                                </div>

                                @if($reply->attachment_path)
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="{{ asset('storage/' . $reply->attachment_path) }}" target="_blank" class="btn btn-sm btn-light border rounded-2 text-primary fw-semibold">
                                            <i class="fa-solid fa-paperclip me-1"></i> View Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Reply Form Box -->
        <div class="card border border-light-subtle shadow-sm rounded-3 p-4 bg-white">
            <h6 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-reply text-primary me-2"></i>Post a Reply
            </h6>
            <form action="{{ route('student.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Type your follow-up message or response here..." required></textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Attach File / Screenshot (Optional)</label>
                    <input type="file" name="attachment" class="form-control form-control-sm @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-2 btn-sm px-4 fw-semibold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Ticket Meta Info -->
    <div class="col-12 col-lg-4">
        <div class="card border border-light-subtle shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Ticket Metadata</h6>
            <div class="d-flex flex-column gap-3 small">
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Status</div>
                    <div class="fw-bold">
                        @if($ticket->status === 'open')
                            <span class="text-warning"><i class="fa-solid fa-circle-dot me-1"></i> Open</span>
                        @elseif($ticket->status === 'in_progress')
                            <span class="text-info"><i class="fa-solid fa-spinner me-1"></i> In Progress</span>
                        @elseif($ticket->status === 'resolved')
                            <span class="text-success"><i class="fa-solid fa-check me-1"></i> Resolved</span>
                        @else
                            <span class="text-secondary"><i class="fa-solid fa-lock me-1"></i> Closed</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Priority</div>
                    <div class="fw-bold text-dark text-capitalize">{{ $ticket->priority }}</div>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Category</div>
                    <div class="fw-bold text-dark">{{ $ticket->category }}</div>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Assigned Centre</div>
                    <div class="fw-bold text-dark">{{ $ticket->centre->centre_name ?? 'Head Office' }}</div>
                </div>
                @if($ticket->assignee)
                    <div>
                        <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Handled By</div>
                        <div class="fw-bold text-dark">{{ $ticket->assignee->name }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Created On</div>
                    <div class="fw-bold text-dark">{{ $ticket->created_at->format('d M, Y h:i A') }}</div>
                </div>
            </div>
        </div>

        <div class="card border border-light-subtle shadow-sm rounded-3 p-4 bg-light">
            <div class="small text-muted">
                <i class="fa-solid fa-circle-info text-primary me-2"></i><strong>Support Tip:</strong> Replying to a closed or resolved ticket will automatically reopen the inquiry for staff review.
            </div>
        </div>
    </div>
</div>
@endsection
