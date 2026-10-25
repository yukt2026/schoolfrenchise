<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\District;
use App\Models\State;
use App\Models\StudyMaterial;
use App\Models\SystemNotification;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentPortalController extends Controller
{
    use HasStudentContext;

    /**
     * Student Portal Home Dashboard.
     */
    public function dashboard()
    {
        $student = $this->getStudent();
        $student->load(['centre', 'state', 'district']);

        $admission = $this->getActiveAdmission($student);

        // Financial Ledger Data
        $totalFees = $admission ? (float) $admission->net_fee : 0.00;
        $totalPaid = $admission ? (float) $admission->total_paid : 0.00;
        $totalDues = max(0.00, $totalFees - $totalPaid);

        // Recent Payments (Latest 5)
        $recentPayments = $student->payments()
            ->with('centre')
            ->latest('payment_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Relevant Study Materials (Latest 4)
        $studyMaterials = collect();
        if ($admission && $admission->course_id) {
            $studyMaterials = StudyMaterial::where('course_id', $admission->course_id)
                ->where(function ($q) use ($student) {
                    $q->whereNull('centre_id')
                        ->orWhere('centre_id', $student->centre_id);
                })
                ->where('status', true)
                ->latest()
                ->take(4)
                ->get();
        }

        // Notifications
        $user = Auth::user();
        $recentNotifications = SystemNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
            ->latest()
            ->take(4)
            ->get();

        $unreadNotificationsCount = SystemNotification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
            ->whereNull('read_at')
            ->count();

        // Attendance Overview
        $totalSessions = Attendance::where('student_id', $student->id)->count();
        $presentSessions = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 1) : 0;

        // Certification
        $certificate = Certificate::where('student_id', $student->id)->where('status', 'issued')->first();

        return view('student.dashboard', compact(
            'student',
            'admission',
            'totalFees',
            'totalPaid',
            'totalDues',
            'recentPayments',
            'studyMaterials',
            'recentNotifications',
            'unreadNotificationsCount',
            'totalSessions',
            'presentSessions',
            'attendanceRate',
            'certificate'
        ));
    }

    /**
     * Student Profile Dossier.
     */
    public function profile()
    {
        $student = $this->getStudent();
        $student->load(['centre', 'state', 'district', 'admissions.course', 'admissions.batch']);
        $admission = $this->getActiveAdmission($student);

        $states = State::where('status', true)->orderBy('name')->get();
        $districts = District::where('state_id', $student->state_id)->orderBy('name')->get();

        return view('student.profile', compact('student', 'admission', 'states', 'districts'));
    }

    /**
     * Update Student Profile (Contact & Photo).
     */
    public function updateProfile(Request $request)
    {
        $student = $this->getStudent();

        $validated = $request->validate([
            'father_name'      => ['nullable', 'string', 'max:191'],
            'mother_name'      => ['nullable', 'string', 'max:191'],
            'date_of_birth'    => ['nullable', 'date'],
            'gender'           => ['nullable', 'in:male,female,other'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'mobile'           => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:500'],
            'city'             => ['nullable', 'string', 'max:100'],
            'pincode'          => ['nullable', 'string', 'max:10'],
            'state_id'         => ['nullable', 'exists:states,id'],
            'district_id'      => ['nullable', 'exists:districts,id'],
            'photo'            => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = FileUploadService::uploadImage($request->file('photo'), 'uploads/students/photos');
            $student->photo_path = $photoPath;
        }

        if (isset($validated['father_name'])) {
            $student->father_name = $validated['father_name'];
        }
        if (isset($validated['mother_name'])) {
            $student->mother_name = $validated['mother_name'];
        }
        if (!empty($validated['date_of_birth'])) {
            $student->dob = $validated['date_of_birth'];
        }
        if (!empty($validated['gender'])) {
            $student->gender = $validated['gender'];
        }

        $student->alternate_mobile = $validated['alternate_mobile'] ?? $student->alternate_mobile;
        if (!empty($validated['phone']) || !empty($validated['mobile'])) {
            $student->mobile = $validated['phone'] ?? $validated['mobile'];
        }
        $student->address = $validated['address'] ?? $student->address;
        $student->city = $validated['city'] ?? $student->city;
        $student->pincode = $validated['pincode'] ?? $student->pincode;
        $student->state_id = $validated['state_id'] ?? $student->state_id;
        $student->district_id = $validated['district_id'] ?? $student->district_id;
        $student->save();

        return back()->with('success', 'Your contact profile and personal particulars have been updated successfully.');
    }

    /**
     * Official Printable Admission Form Dossier.
     */
    public function admissionForm()
    {
        $student = $this->getStudent();
        $student->load(['centre.state', 'centre.district', 'state', 'district']);
        $admission = $this->getActiveAdmission($student);

        return view('student.admission_form', compact('student', 'admission'));
    }

    /**
     * Enrolled Course Details & Syllabus.
     */
    public function course()
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        $course = $admission ? $admission->course : null;
        $subjects = $course ? $course->subjects : collect();

        return view('student.course', compact('student', 'admission', 'course', 'subjects'));
    }

    /**
     * Student Digital Identity Card.
     */
    public function idCard()
    {
        $student = $this->getStudent();
        $student->load(['centre', 'admissions.course', 'admissions.batch']);
        $admission = $this->getActiveAdmission($student);

        return view('student.id_card', compact('student', 'admission'));
    }

    /**
     * Batch Schedule & Timetable.
     */
    public function schedule()
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);
        $batch = $admission ? $admission->batch : null;

        return view('student.schedule', compact('student', 'admission', 'batch'));
    }

    /**
     * Attendance Record & Analytics.
     */
    public function attendance()
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        $attendances = Attendance::where('student_id', $student->id)
            ->with('batch')
            ->latest('attendance_date')
            ->paginate(20)
            ->withQueryString();

        $totalSessions = Attendance::where('student_id', $student->id)->count();
        $presentSessions = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $absentSessions = Attendance::where('student_id', $student->id)->where('status', 'absent')->count();
        $lateSessions = Attendance::where('student_id', $student->id)->where('status', 'late')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 1) : 0;

        return view('student.attendance', compact(
            'student',
            'admission',
            'attendances',
            'totalSessions',
            'presentSessions',
            'absentSessions',
            'lateSessions',
            'attendanceRate'
        ));
    }

    /**
     * Show Change Password Form.
     */
    public function changePasswordForm()
    {
        $student = $this->getStudent();
        return view('student.change_password', compact('student'));
    }

    /**
     * Execute Password Change.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The provided current password does not match our records.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Your password has been changed successfully. Use your new password for future logins.');
    }
}
