@extends('layouts.student')

@section('title', 'Help & Support - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Support</li>
            </ol>
        </nav>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-headset text-primary me-2"></i>Help & Support Desk
        </h2>
        <p class="text-muted small mb-0">Submit academic queries, fee assistance requests, or portal issues to the centre administrative staff.</p>
    </div>
    <a href="{{ route('student.support.create') }}" class="btn btn-primary rounded-2 btn-sm fw-semibold">
        <i class="fa-solid fa-plus me-1"></i> Raise New Ticket
    </a>
</div>

<!-- Support Summary Cards -->
@php
    $totalTickets = $tickets->total();
    $openTickets = $tickets->where('status', 'open')->count();
    $resolvedTickets = $tickets->whereIn('status', ['resolved', 'closed'])->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-primary">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-primary-subtle text-primary">
                    <i class="fa-solid fa-ticket fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Total Tickets</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $totalTickets }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-warning-subtle text-warning">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Open Queries</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ $openTickets }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-2 p-3 bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold">Resolved</div>
                    <div class="h4 fw-bold text-success mb-0">{{ $resolvedTickets }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tickets Table Card -->
<div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-4 bg-white">
    <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold fs-6 mb-0 text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i>My Support Tickets
            </h5>
            <span class="badge bg-primary-subtle text-primary border px-2 py-1 small rounded-1">
                Total: {{ $tickets->total() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($tickets->count() > 0)
            <div class="table-scroll-hint d-md-none">
                <i class="fa-solid fa-arrows-left-right text-primary"></i>
                <span>Scroll horizontally to view ticket progress & responses</span>
            </div>
            <div class="table-responsive">
                <table class="table-custom table-hover align-middle mb-0" style="min-width: 680px;">
                    <thead>
                        <tr>
                            <th class="ps-4">Ticket ID & Subject</th>
                            <th>Category</th>
                            <th class="text-center">Priority</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Responses</th>
                            <th>Date</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $ticket->subject }}</div>
                                    <div class="small text-muted font-monospace">#{{ $ticket->ticket_no }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-1 text-nowrap">{{ $ticket->category }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($ticket->priority === 'high')
                                        <span class="badge bg-danger-subtle text-danger text-uppercase px-2 py-1 rounded-1" style="font-size: 0.7rem;">High</span>
                                    @elseif($ticket->priority === 'medium')
                                        <span class="badge bg-warning-subtle text-warning text-uppercase px-2 py-1 rounded-1" style="font-size: 0.7rem;">Medium</span>
                                    @else
                                        <span class="badge bg-light text-muted text-uppercase px-2 py-1 rounded-1" style="font-size: 0.7rem;">Low</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($ticket->status === 'open')
                                        <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-clock me-1"></i> Open
                                        </span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-spinner me-1"></i> In Progress
                                        </span>
                                    @elseif($ticket->status === 'waiting_for_student')
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-hourglass-half me-1"></i> Waiting for You
                                        </span>
                                    @elseif($ticket->status === 'answered')
                                        <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-reply me-1"></i> Answered
                                        </span>
                                    @elseif($ticket->status === 'resolved')
                                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Resolved
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-1">
                                            <i class="fa-solid fa-lock me-1"></i> Closed
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-primary-subtle text-primary rounded-1 px-2 py-1">
                                        <i class="fa-regular fa-comment me-1"></i> {{ $ticket->replies_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-muted small text-nowrap">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i>
                                    {{ $ticket->created_at->format('d M, Y') }}
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a href="{{ route('student.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-2 fw-semibold text-nowrap">
                                        <i class="fa-regular fa-eye me-1"></i> View Thread
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-headset text-muted fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Support Tickets Raised</h5>
                <p class="text-muted small mb-3" style="max-width: 400px; margin: 0 auto;">
                    Have questions about your fee receipt, syllabus, examinations, or batch timing? You can raise a support query directly with your centre coordinators.
                </p>
                <a href="{{ route('student.support.create') }}" class="btn btn-primary btn-sm rounded-2 fw-semibold px-3">
                    <i class="fa-solid fa-plus me-1"></i> Raise Your First Ticket
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
