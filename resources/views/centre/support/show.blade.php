@extends('layouts.centre')

@section('title', 'Ticket #' . $ticket->ticket_no . ' - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.support.index') }}" class="btn btn-outline-secondary btn-sm rounded-2 mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Support Desk
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ $ticket->subject }}</h3>
            <span class="badge bg-secondary-subtle text-dark font-monospace fw-bold rounded-2">{{ $ticket->ticket_no }}</span>
            <span class="badge bg-light text-muted border text-capitalize rounded-2">{{ str_replace('_', ' ', $ticket->category) }}</span>
            <span class="badge {{ $ticket->priority === 'urgent' ? 'bg-danger' : ($ticket->priority === 'high' ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary') }} text-uppercase rounded-2">
                {{ $ticket->priority }}
            </span>
        </div>
        <div>
            <span class="badge fs-6 {{ $ticket->status === 'resolved' ? 'bg-success' : ($ticket->status === 'in_progress' ? 'bg-info' : 'bg-warning text-dark') }} rounded-2">
                Status: {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Original Inquiry Card -->
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="fw-bold text-dark">
                    <i class="fa-solid fa-user-tie text-primary me-2"></i>{{ $centre->director_name ?? 'Centre Director' }}
                    <span class="small text-muted font-monospace">({{ $centre->centre_code }})</span>
                </div>
                <span class="small text-muted">{{ $ticket->created_at->format('d M, Y h:i A') }}</span>
            </div>
            <div class="card-body p-4">
                <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.6;">{{ $ticket->description }}</p>
            </div>
        </div>

        <!-- Replies Conversation Stream -->
        @if($ticket->replies->isNotEmpty())
        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-comments text-info me-2"></i>Discussion Thread</h6>
        @foreach($ticket->replies as $reply)
        <div class="card border-0 rounded-3 shadow-sm mb-3 {{ $reply->is_staff_reply ? 'border-start border-4 border-primary bg-light' : '' }}">
            <div class="card-header bg-transparent py-2 px-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    @if($reply->is_staff_reply)
                    <span class="badge bg-primary me-1 rounded-2"><i class="fa-solid fa-shield-halved me-1"></i>Head Office Staff</span>
                    <span class="fw-bold text-dark">{{ $reply->user?->name ?? 'Central Support' }}</span>
                    @else
                    <span class="badge bg-secondary me-1 rounded-2">Centre Reply</span>
                    <span class="fw-bold text-dark">{{ $reply->user?->name ?? $centre->director_name }}</span>
                    @endif
                </div>
                <span class="small text-muted">{{ $reply->created_at->format('d M, Y h:i A') }}</span>
            </div>
            <div class="card-body p-4">
                <p class="mb-2 text-dark" style="white-space: pre-line;">{{ $reply->message }}</p>

                @if($reply->attachment_path)
                <div class="mt-2 pt-2 border-top">
                    <a href="{{ asset('storage/' . $reply->attachment_path) }}" class="btn btn-sm btn-outline-secondary rounded-2" target="_blank">
                        <i class="fa-solid fa-paperclip me-1"></i>View Attachment
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
        @endif

        <!-- Reply Form -->
        <div class="card border-0 rounded-3 shadow-sm mt-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-reply text-success me-2"></i>Post Reply</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('centre.support.reply', $ticket->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <textarea name="message" class="form-control rounded-2" rows="3" placeholder="Write your response or follow-up question here..." required></textarea>
                    </div>

                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <input type="file" name="attachment" class="form-control form-control-sm rounded-2" accept=".pdf,image/*,.doc,.docx">
                        </div>
                        <div class="col-md-5 text-md-end">
                            <button type="submit" class="btn btn-primary rounded-2 px-4 fw-bold">
                                <i class="fa-solid fa-paper-plane me-1"></i>Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info text-info me-2"></i>Ticket Information</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Ticket Number:</span>
                        <span class="fw-bold font-monospace">{{ $ticket->ticket_no }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Category:</span>
                        <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $ticket->category) }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Priority:</span>
                        <span class="fw-bold text-uppercase text-{{ $ticket->priority === 'urgent' ? 'danger' : 'dark' }}">{{ $ticket->priority }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge {{ $ticket->status === 'resolved' ? 'bg-success' : 'bg-warning text-dark' }} rounded-2">{{ ucfirst($ticket->status) }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Submitted Date:</span>
                        <span>{{ $ticket->created_at->format('d M, Y') }}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Assigned Handler:</span>
                        <span class="fw-semibold">{{ $ticket->assignee?->name ?? 'Central Desk' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
