@extends('layouts.centre')

@section('title', 'Help & Support Desk - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-headset text-info me-2"></i>Head Office Support Desk
        </h3>
        <p class="text-muted small mb-0">Direct communication channel with Central Board administration, examination wing, and accounts desk.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.support.create') }}" class="btn btn-primary rounded-2 px-3 fw-semibold shadow-sm">
            <i class="fa-solid fa-circle-plus me-1"></i>Open New Support Ticket
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-dark rounded-3 shadow-sm">
            <div class="text-muted small fw-semibold text-uppercase">Total Tickets</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total']) }}</div>
            <span class="small text-muted">All inquiries</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-warning rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Open / Pending</div>
            <div class="fs-4 fw-bold text-warning-emphasis my-1">{{ number_format($stats['open']) }}</div>
            <span class="small text-muted">Awaiting response</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-info rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">In Progress</div>
            <div class="fs-4 fw-bold text-info my-1">{{ number_format($stats['in_progress']) }}</div>
            <span class="small text-muted">Under investigation</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card p-3 bg-white border-start border-4 border-success rounded-3">
            <div class="text-muted small fw-semibold text-uppercase">Resolved</div>
            <div class="fs-4 fw-bold text-success my-1">{{ number_format($stats['resolved']) }}</div>
            <span class="small text-muted">Successfully settled</span>
        </div>
    </div>
</div>

<!-- Tickets Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <div class="table-scroll-hint d-md-none text-muted small px-3 py-1 bg-light border-bottom">
                <i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally to view complete list
            </div>
            <table class="table table-custom align-middle mb-0" style="min-width: 680px;">
                <thead>
                    <tr>
                        <th>Ticket No</th>
                        <th>Subject & Category</th>
                        <th>Priority</th>
                        <th>Last Activity</th>
                        <th>Status</th>
                        <th class="text-end">View Thread</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark font-monospace fw-bold rounded-2">{{ $ticket->ticket_no }}</span>
                        </td>
                        <td>
                            <a href="{{ route('centre.support.show', $ticket->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $ticket->subject }}
                            </a>
                            <span class="badge bg-light text-muted border text-capitalize rounded-2">{{ str_replace('_', ' ', $ticket->category) }}</span>
                            <span class="small text-muted ms-2"><i class="fa-solid fa-comments me-1"></i>{{ $ticket->replies->count() }} messages</span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->priority === 'urgent' ? 'bg-danger' : ($ticket->priority === 'high' ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary') }} text-uppercase rounded-2">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $ticket->updated_at->diffForHumans() }}</div>
                            <div class="small text-muted">{{ $ticket->created_at->format('d M, Y') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->status === 'resolved' ? 'bg-success' : ($ticket->status === 'in_progress' ? 'bg-info' : 'bg-warning text-dark') }} rounded-2">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('centre.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">
                                <i class="fa-solid fa-arrow-right me-1"></i>Open Thread
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-headset fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Support Tickets Opened</h5>
                            <p class="small text-muted mb-3">Need assistance with wallet recharges, certificates, or examinations? Raise a ticket.</p>
                            <a href="{{ route('centre.support.create') }}" class="btn btn-primary rounded-2 px-4">
                                <i class="fa-solid fa-circle-plus me-1"></i>Open First Ticket
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-3 border-top">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
