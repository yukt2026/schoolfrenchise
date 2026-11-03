<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\FeeStructure;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAdmissionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'applications');
        $search = $request->input('search');
        $centreId = $request->input('centre_id');
        $status = $request->input('status');

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        if ($tab === 'active') {
            $query = Admission::with(['student', 'course', 'centre', 'batch']);

            if ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('roll_no', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            }

            if ($centreId) {
                $query->where('centre_id', $centreId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $admissions = $query->latest()->paginate(15)->withQueryString();
            $applications = null;
        } else {
            $query = AdmissionApplication::with(['course', 'centre', 'convertedStudent']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('applicant_name', 'like', "%{$search}%")
                      ->orWhere('application_no', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            }

            if ($centreId) {
                $query->where('centre_id', $centreId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $applications = $query->latest()->paginate(15)->withQueryString();
            $admissions = null;
        }

        $counts = [
            'pending_apps' => AdmissionApplication::where('status', 'pending')->count(),
            'under_review_apps' => AdmissionApplication::where('status', 'under_review')->count(),
            'approved_apps' => AdmissionApplication::where('status', 'approved')->count(),
            'rejected_apps' => AdmissionApplication::where('status', 'rejected')->count(),
            'active_admissions' => Admission::count(),
        ];

        return view('admin.admissions.index', compact('tab', 'applications', 'admissions', 'centres', 'counts'));
    }

    public function create()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $batches = Batch::where('status', 'active')->orderBy('batch_name')->get();
        $students = Student::where('status', 'enrolled')->orWhere('status', 'ongoing')->latest()->take(50)->get();

        return view('admin.admissions.create', compact('centres', 'courses', 'batches', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'centre_id' => 'required|exists:centres,id',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'admission_date' => 'required|date',
            'course_fee' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $courseFee = (float) $validated['course_fee'];
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $netFee = max(0, $courseFee - $discount);

            $admission = Admission::create([
                'student_id' => $validated['student_id'],
                'centre_id' => $validated['centre_id'],
                'course_id' => $validated['course_id'],
                'batch_id' => $validated['batch_id'] ?? null,
                'admission_date' => $validated['admission_date'],
                'course_fee' => $courseFee,
                'discount_amount' => $discount,
                'net_fee' => $netFee,
                'total_paid' => 0,
                'status' => 'active',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Assign Fee Package
            if ($netFee > 0) {
                FeeService::assignAdmissionFees($admission, $discount);
            }

            // Credit Franchise Commission for this admission
            \App\Services\WalletService::creditAdmissionCommission($admission);

            AuditLog::record('admission_created', $admission, null, [
                'student_id' => $admission->student_id,
                'course_id' => $admission->course_id,
                'net_fee' => $netFee,
            ]);

            return redirect()->route('admin.admissions.index', ['tab' => 'active'])
                ->with('success', "Admission recorded successfully for {$admission->student?->full_name} in {$admission->course?->title}.");
        });
    }

    public function showApplication(AdmissionApplication $application)
    {
        $application->load(['centre', 'course', 'state', 'district', 'convertedStudent']);
        $batches = Batch::where('status', 'active')
            ->where(function ($q) use ($application) {
                $q->where('centre_id', $application->centre_id)
                  ->orWhereNull('centre_id');
            })->get();

        $feeStructures = FeeStructure::where('course_id', $application->course_id)
            ->where('is_active', true)
            ->get();

        return view('admin.admissions.show_application', compact('application', 'batches', 'feeStructures'));
    }

    public function approveApplication(Request $request, AdmissionApplication $application)
    {
        if ($application->status === 'approved') {
            return back()->with('error', 'This application has already been approved.');
        }

        $validated = $request->validate([
            'course_fee'       => 'required|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'batch_id'         => 'nullable|exists:batches,id',
            'fee_structure_id' => 'nullable|exists:fee_structures,id',
            'remarks'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $application, $validated) {
            $centre = $application->centre;
            $year = date('Y');
            $randomSeq = str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
            $regNo = "DCE/{$year}/" . ($centre?->centre_code ?? 'HO') . "/{$randomSeq}";
            $rollNo = "DCE" . substr($year, -2) . rand(10000, 99999);

            // Create or Link Student User
            $userEmail = !empty($application->email) ? $application->email : "student_{$rollNo}@techeracomputer.in";
            $studentUser = User::firstOrCreate(
                ['mobile' => $application->mobile],
                [
                    'name'      => $application->applicant_name,
                    'email'     => $userEmail,
                    'password'  => Hash::make($application->mobile),
                    'role'      => 'student',
                    'is_active' => true,
                ]
            );

            $studentRole = Role::where('name', 'student')->first();
            if ($studentRole) {
                $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
            }

            // Create Student Profile
            $student = Student::firstOrCreate(
                ['mobile' => $application->mobile],
                [
                    'user_id'               => $studentUser->id,
                    'centre_id'             => $application->centre_id,
                    'registration_no'       => $regNo,
                    'roll_no'               => $rollNo,
                    'full_name'             => $application->applicant_name,
                    'father_name'           => $application->father_name,
                    'mother_name'           => $application->mother_name,
                    'dob'                   => $application->dob,
                    'gender'                => $application->gender,
                    'email'                 => $application->email,
                    'state_id'              => $application->state_id,
                    'district_id'           => $application->district_id,
                    'city'                  => $application->city,
                    'address'               => $application->address,
                    'pincode'               => $application->pincode,
                    'highest_qualification' => $application->highest_qualification,
                    'photo_path'            => $application->photo_path,
                    'id_proof_doc'          => $application->id_proof_doc,
                    'status'                => 'enrolled',
                ]
            );

            $courseFee = (float) $validated['course_fee'];
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $netFee = max(0, $courseFee - $discount);

            // Create Admission
            $admission = Admission::create([
                'student_id'      => $student->id,
                'centre_id'       => $application->centre_id,
                'course_id'       => $application->course_id,
                'batch_id'        => $validated['batch_id'] ?? null,
                'admission_date'  => now(),
                'course_fee'      => $courseFee,
                'discount_amount' => $discount,
                'net_fee'         => $netFee,
                'total_paid'      => 0,
                'status'          => 'active',
                'remarks'         => $validated['remarks'] ?? "Approved from Online Application: {$application->application_no}",
            ]);

            // Assign Fee Package or Fee Structure schedule
            if (!empty($validated['fee_structure_id'])) {
                $structure = FeeStructure::find($validated['fee_structure_id']);
                if ($structure) {
                    FeeService::assignFeeSchedule($admission, $structure);
                }
            } elseif ($netFee > 0 && $admission->course) {
                FeeService::assignAdmissionFees($admission, $discount);
            }

            // Credit Franchise Commission for this admission
            \App\Services\WalletService::creditAdmissionCommission($admission);

            // Update Application
            $application->update([
                'status'               => 'approved',
                'reviewed_by'          => auth()->id(),
                'converted_student_id' => $student->id,
            ]);

            AuditLog::record('application_approved', $application, null, [
                'application_no' => $application->application_no,
                'student_id'     => $student->id,
                'admission_id'   => $admission->id,
                'net_fee'        => $netFee,
            ]);

            // Dispatch Admission Approval Notification to Student
            try {
                \App\Services\NotificationDispatchService::dispatchAdmissionApproval($student);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for student {$student->roll_no}: " . $e->getMessage());
            }

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Application [{$application->application_no}] approved successfully! Student Roll No: {$student->roll_no}.");
        });
    }

    public function rejectApplication(Request $request, AdmissionApplication $application)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
        ]);

        AuditLog::record('application_rejected', $application, null, [
            'application_no' => $application->application_no,
            'reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('admin.admissions.index')
            ->with('success', "Application [{$application->application_no}] marked as rejected.");
    }

    public function edit(Admission $admission)
    {
        $admission->load(['student', 'course', 'centre', 'batch']);
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $batches = Batch::where('status', 'active')->orderBy('batch_name')->get();

        return view('admin.admissions.edit', compact('admission', 'courses', 'batches'));
    }

    public function update(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'status' => 'required|in:active,completed,cancelled',
            'remarks' => 'nullable|string|max:500',
        ]);

        $oldValues = $admission->toArray();
        $admission->update($validated);

        AuditLog::record('admission_updated', $admission, $oldValues, $admission->toArray());

        return redirect()->route('admin.admissions.index', ['tab' => 'active'])
            ->with('success', "Admission record updated successfully.");
    }

    public function destroy(Admission $admission)
    {
        $admission->delete(); // Soft delete

        AuditLog::record('admission_deleted', $admission, ['status' => 'deleted']);

        return redirect()->route('admin.admissions.index', ['tab' => 'active'])
            ->with('success', "Admission record has been soft-deleted.");
    }

    /**
     * Generate official printable admission form dossier.
     */
    public function printAdmissionForm(Admission $admission)
    {
        $admission->load([
            'student.state',
            'student.district',
            'student.centre',
            'course.subjects',
            'centre',
            'batch',
            'studentFees',
            'payments',
        ]);

        return view('admin.admissions.print', compact('admission'));
    }
}
