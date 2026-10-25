<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSupportController extends Controller
{
    use HasStudentContext;

    /**
     * List all support tickets created by the authenticated student.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent($request);
        $user = Auth::user();

        $tickets = SupportTicket::where('user_id', $user->id)
            ->withCount('replies')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student.support.index', compact('student', 'tickets'));
    }

    /**
     * Show form to raise a new support ticket.
     */
    public function create(Request $request)
    {
        $student = $this->getStudent($request);

        $categories = [
            'Academic & Syllabus',
            'Fees & Payments',
            'Exam & Results',
            'Certificate & Verification',
            'Batch & Attendance',
            'Technical & Portal Support',
            'General Enquiry',
        ];

        return view('student.support.create', compact('student', 'categories'));
    }

    /**
     * Store newly created support ticket.
     */
    public function store(Request $request)
    {
        $student = $this->getStudent($request);
        $user = Auth::user();

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'subject' => 'required|string|max:191',
            'description' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $ticket = new SupportTicket();
        $ticket->ticket_no = SupportTicket::generateTicketNo();
        $ticket->user_id = $user->id;
        $ticket->centre_id = $student->centre_id;
        $ticket->category = $validated['category'];
        $ticket->priority = $validated['priority'];
        $ticket->subject = $validated['subject'];
        $ticket->description = $validated['description'];
        $ticket->status = 'open';
        $ticket->save();

        if ($request->hasFile('attachment')) {
            $path = FileUploadService::uploadDocument($request->file('attachment'), 'tickets/' . $ticket->id);
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => 'Attachment uploaded with ticket creation.',
                'attachment_path' => $path,
                'is_staff_reply' => false,
            ]);
        }

        AuditLog::logAction('student_ticket_created', 'SupportTicket', $ticket->id, [
            'ticket_no' => $ticket->ticket_no,
            'category' => $ticket->category,
        ]);

        return redirect()->route('student.support.show', $ticket->id)
            ->with('success', "Ticket #{$ticket->ticket_no} created successfully. Our team will respond shortly.");
    }

    /**
     * Show ticket details and replies.
     */
    public function show(Request $request, $id)
    {
        $student = $this->getStudent($request);
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $id)
            ->where(function ($q) use ($user) {
                if ($user->hasRole('super_admin')) {
                    return; // Admin can inspect
                }
                $q->where('user_id', $user->id);
            })
            ->with(['replies.user', 'centre', 'assignee'])
            ->firstOrFail();

        return view('student.support.show', compact('student', 'ticket'));
    }

    /**
     * Post a reply to the ticket.
     */
    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('id', $id)
            ->where(function ($q) use ($user) {
                if ($user->hasRole('super_admin')) {
                    return;
                }
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string|max:4000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadService::uploadDocument($request->file('attachment'), 'tickets/' . $ticket->id);
        }

        $reply = SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'is_staff_reply' => false,
        ]);

        $ticket->last_reply_at = now();
        if (in_array($ticket->status, ['waiting_for_student', 'answered', 'closed', 'resolved'])) {
            $ticket->status = $ticket->assigned_to ? 'in_progress' : 'open';
        }
        $ticket->save();

        try {
            \App\Services\NotificationDispatchService::dispatchTicketReply($ticket, $reply);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for ticket {$ticket->ticket_no}: " . $e->getMessage());
        }

        return back()->with('success', 'Your reply has been posted successfully.');
    }
}
