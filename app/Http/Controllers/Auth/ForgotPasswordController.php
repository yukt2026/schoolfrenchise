<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $input = trim((string) ($request->input('email') ?? $request->input('identity') ?? ''));

        if (empty($input)) {
            return back()->withInput()->with('error', 'Please enter your registered Email, Roll Number, Registration Number, or Mobile.');
        }

        $user = null;

        // 1. Direct match on users table by email
        $user = User::where('email', $input)->first();

        // 2. If not found, search in Student records (Roll No, Registration No, Mobile, or Email)
        if (!$user) {
            $student = Student::where('roll_no', $input)
                ->orWhere('registration_no', $input)
                ->orWhere('mobile', $input)
                ->orWhere('alternate_mobile', $input)
                ->orWhere('email', $input)
                ->first();

            if ($student) {
                if ($student->user_id) {
                    $user = User::find($student->user_id);
                }
                if (!$user && $student->email) {
                    $user = User::where('email', $student->email)->first();
                }
            }
        }

        // 3. If not found, search in Centre records (Centre Code, Mobile, or Email)
        if (!$user) {
            $centre = Centre::where('centre_code', $input)
                ->orWhere('mobile', $input)
                ->orWhere('alternate_mobile', $input)
                ->orWhere('email', $input)
                ->first();

            if ($centre) {
                if ($centre->user_id) {
                    $user = User::find($centre->user_id);
                }
                if (!$user && $centre->email) {
                    $user = User::where('email', $centre->email)->first();
                }
            }
        }

        // 4. Fallback search by mobile substring on Student or Centre
        if (!$user && strlen($input) >= 10 && is_numeric($input)) {
            $last10 = substr($input, -10);
            $student = Student::where('mobile', 'like', "%{$last10}%")->first();
            if ($student && $student->user_id) {
                $user = User::find($student->user_id);
            }
        }

        if (!$user) {
            return back()->withInput()->with('error', 'We could not find an active account associated with that email, roll number, or mobile number. Please verify your credentials or contact your branch.');
        }

        if (!$user->is_active) {
            return back()->withInput()->with('error', 'This account has been deactivated. Please contact the Head Office administration.');
        }

        // Generate secure 64-character token
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset_requested',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Attempt sending email if mailer is configured
        try {
            if (config('mail.default') !== 'log' && config('mail.mailers.smtp.host')) {
                // Production email send logic
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $maskedEmail = Str::mask($user->email, '*', 3, -4);
        $message = "Password reset link generated for <strong>" . e($user->name) . "</strong> (" . e($maskedEmail) . ").<div class='mt-3'><a href='{$resetUrl}' class='btn btn-success btn-sm fw-bold text-white shadow-sm'><i class='fa-solid fa-key me-1'></i> Click here to reset your password</a></div>";

        return back()->with('success', $message);
    }
}
