<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationController extends Controller
{
    use HasStudentContext;

    /**
     * Display all system notifications for the authenticated student.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent($request);
        $user = Auth::user();

        $notifications = SystemNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = SystemNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('student.notifications.index', compact('student', 'notifications', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read and optionally navigate to action URL.
     */
    public function markRead(Request $request, $id)
    {
        $user = Auth::user();

        $notification = SystemNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        SystemNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
