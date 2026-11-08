<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\District;
use App\Models\FeeStructure;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Services\FeeService;
use App\Services\FileUploadService;
use App\Services\NotificationDispatchService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CentreStudentController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Student::where('centre_id', $centre->id)->with(['admissions.course', 'state', 'district']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('admissions', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest('id')->paginate(15)->withQueryString();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $batches = Batch::where('centre_id', $centre->id)->where('status', 'active')->get();

        $stats = [
            'total' => Student::where('centre_id', $centre->id)->count(),
            'enrolled' => Student::where('centre_id', $centre->id)->where('status', 'enrolled')->count(),
            'ongoing' => Student::where('centre_id', $centre->id)->where('status', 'ongoing')->count(),
            'completed' => Student::where('centre_id', $centre->id)->where('status', 'completed')->count(),
        ];

        return view('centre.students.index', compact('centre', 'students', 'courses', 'batches', 'stats'));
    }

    public function create()
    {
        $centre = $this->getAuthCentre();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $batches = Batch::where('centre_id', $centre->id)->where('status', 'active')->orderBy('batch_name')->get();
        $states = State::orderBy('name')->get();

        $walletBalance = WalletService::getCentreBalance($centre->id);
        $requiredAdmissionFee = WalletService::getRequiredAdmissionFee();
        $hasSufficientBalance = ($walletBalance >= $requiredAdmissionFee);

        return view('centre.students.create', compact(
            'centre', 'courses', 'batches', 'states',
            'walletBalance', 'requiredAdmissionFee', 'hasSufficientBalance'
        ));
    }

    public function store(Request $request)
    {
        $centre = $this->getAuthCentre();

        // 1. Enforce sufficient wallet balance for admission fee deduction
        $requiredAdmissionFee = WalletService::getRequiredAdmissionFee();
        $walletBalance = WalletService::getCentreBalance($centre->id);

        if ($requiredAdmissionFee > 0 && $walletBalance < $requiredAdmissionFee) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Insufficient Wallet Balance! Student admission requires ₹" . number_format($requiredAdmissionFee, 2) . " wallet deduction, but your current balance is ₹" . number_format($walletBalance, 2) . ". Please recharge your wallet to proceed with admission.");
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:191',
            'father_name' => 'required|string|max:191',
            'mother_name' => 'nullable|string|max:191',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|string|size:10',
            'alternate_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'state_id' => 'required|exists:states,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'highest_qualification' => 'nullable|string|max:100',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'course_fee' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated, $centre) {
                $year = date('Y');
                $randomSeq = str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
                $regNo = "DCE/{$year}/{$centre->centre_code}/{$randomSeq}";
                $rollNo = "DCE" . substr($year, -2) . rand(10000, 99999);

                $photoPath = $request->hasFile('photo') ? FileUploadService::uploadImage($request->file('photo'), 'uploads/students/photos') : null;
                $sigPath = $request->hasFile('signature') ? FileUploadService::uploadImage($request->file('signature'), 'uploads/students/signatures') : null;
                $docPath = $request->hasFile('id_proof') ? FileUploadService::uploadDocument($request->file('id_proof'), 'uploads/students/docs') : null;

                // 1. Create or Find User Login
                $userEmail = !empty($validated['email']) ? $validated['email'] : "student_{$rollNo}@techeracomputer.in";
                $studentUser = User::create([
                    'name' => $validated['full_name'],
                    'email' => $userEmail,
                    'mobile' => $validated['mobile'],
                    'password' => Hash::make($validated['mobile']),
                    'role' => 'student',
                    'is_active' => true,
                ]);

                $studentRole = Role::where('name', 'student')->first();
                if ($studentRole) {
                    $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
                }

                // 2. Create Student Profile
                $student = Student::create([
                    'user_id' => $studentUser->id,
                    'centre_id' => $centre->id,
                    'registration_no' => $regNo,
                    'roll_no' => $rollNo,
                    'full_name' => $validated['full_name'],
                    'father_name' => $validated['father_name'],
                    'mother_name' => $validated['mother_name'] ?? null,
                    'dob' => $validated['dob'],
                    'gender' => $validated['gender'],
                    'mobile' => $validated['mobile'],
                    'alternate_mobile' => $validated['alternate_mobile'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'state_id' => $validated['state_id'],
                    'district_id' => $validated['district_id'],
                    'city' => $validated['city'],
                    'address' => $validated['address'],
                    'pincode' => $validated['pincode'],
                    'highest_qualification' => $validated['highest_qualification'] ?? null,
                    'photo_path' => $photoPath,
                    'signature_path' => $sigPath,
                    'id_proof_doc' => $docPath,
                    'status' => 'enrolled',
                ]);

                // 3. Create Admission Record
                $courseFee = (float) $validated['course_fee'];
                $discount = (float) ($validated['discount_amount'] ?? 0);
                $netFee = max(0, $courseFee - $discount);

                $admission = Admission::create([
                    'student_id' => $student->id,
                    'centre_id' => $centre->id,
                    'course_id' => $validated['course_id'],
                    'batch_id' => $validated['batch_id'] ?? null,
                    'admission_date' => now()->toDateString(),
                    'course_fee' => $courseFee,
                    'discount_amount' => $discount,
                    'net_fee' => $netFee,
                    'total_paid' => 0.00,
                    'status' => 'active',
                    'remarks' => "Direct admission registered by Centre [{$centre->centre_code}]",
                ]);

                // 4. Assign Fee Schedule
                if ($netFee > 0) {
                    FeeService::assignAdmissionFees($admission, $discount);
                }

                // 5. Strictly Deduct Franchise Admission Fee from Wallet (Rolls back if balance insufficient)
                WalletService::processAdmissionFeeDeduction($admission, true);

                // 6. Audit Log
                AuditLog::record('centre_student_registered', $student, null, [
                    'registration_no' => $regNo,
                    'roll_no' => $rollNo,
                    'centre_id' => $centre->id,
                    'course_id' => $admission->course_id,
                    'net_fee' => $netFee,
                ]);

                // 7. Notification Dispatch
                try {
                    NotificationDispatchService::dispatchAdmissionApproval($student);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for centre student {$rollNo}: " . $e->getMessage());
                }

                return redirect()->route('centre.students.show', $student->id)
                    ->with('success', "Candidate {$student->full_name} admitted successfully! Roll No: {$rollNo}, Reg No: {$regNo}. Default portal password set to mobile number.");
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($student)
    {
        $centre = $this->getAuthCentre();
        $studentModel = $student instanceof Student ? $student : Student::withoutGlobalScope('centre_isolation')->findOrFail($student);

        if ($studentModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This student is enrolled with another study centre.');
        }

        $studentModel->load([
            'centre', 'state', 'district', 'user',
            'admissions.course.subjects',
            'admissions.batch',
            'admissions.studentFees',
            'fees', 'payments', 'attendances', 'certificates', 'marksheets', 'results'
        ]);

        return view('centre.students.show', ['centre' => $centre, 'student' => $studentModel]);
    }

    public function idCard($student)
    {
        $centre = $this->getAuthCentre();
        $studentModel = $student instanceof Student ? $student : Student::withoutGlobalScope('centre_isolation')->findOrFail($student);

        if ($studentModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This student is enrolled with another study centre.');
        }

        $studentModel->load(['centre.state', 'centre.district', 'admissions.course', 'admissions.batch', 'state', 'district']);
        $admission = $studentModel->admissions->first();

        return view('centre.students.id_card', ['centre' => $centre, 'student' => $studentModel, 'admission' => $admission]);
    }

    public function admissionForm($student)
    {
        $centre = $this->getAuthCentre();
        $studentModel = $student instanceof Student ? $student : Student::withoutGlobalScope('centre_isolation')->findOrFail($student);

        if ($studentModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This student is enrolled with another study centre.');
        }

        $studentModel->load(['centre', 'state', 'district', 'admissions.course', 'admissions.batch', 'admissions.studentFees']);
        $admission = $studentModel->admissions->first();

        return view('centre.students.admission_form', ['centre' => $centre, 'student' => $studentModel, 'admission' => $admission]);
    }

    public function edit($student)
    {
        $centre = $this->getAuthCentre();
        $studentModel = $student instanceof Student ? $student : Student::withoutGlobalScope('centre_isolation')->findOrFail($student);

        // Strict franchise tenant authorization
        if ($studentModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: You can only edit student profiles assigned to your franchise.');
        }

        $studentModel->load(['user', 'state', 'district', 'admissions.course', 'admissions.batch']);
        $states = State::orderBy('name')->get();
        $districts = District::where('state_id', $studentModel->state_id)->where('status', true)->orderBy('name')->get();
        $batches = Batch::where('centre_id', $centre->id)->orderBy('batch_name')->get();

        return view('centre.students.edit', [
            'centre'    => $centre,
            'student'   => $studentModel,
            'states'    => $states,
            'districts' => $districts,
            'batches'   => $batches,
        ]);
    }

    public function update(Request $request, $student)
    {
        $centre = $this->getAuthCentre();
        $studentModel = $student instanceof Student ? $student : Student::withoutGlobalScope('centre_isolation')->findOrFail($student);

        // Strict franchise tenant authorization
        if ($studentModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: You can only edit student profiles assigned to your franchise.');
        }

        $validated = $request->validate([
            'full_name'             => 'required|string|max:191',
            'father_name'           => 'required|string|max:191',
            'mother_name'           => 'nullable|string|max:191',
            'dob'                   => 'required|date',
            'gender'                => 'required|in:male,female,other',
            'mobile'                => 'required|string|size:10',
            'alternate_mobile'      => 'nullable|string|max:20',
            'email'                 => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('users', 'email')->ignore($studentModel->user_id),
                Rule::unique('students', 'email')->ignore($studentModel->id),
            ],
            'password'              => 'nullable|string|min:6|confirmed',
            'state_id'              => 'required|exists:states,id',
            'district_id'           => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city'                  => 'required|string|max:100',
            'address'               => 'required|string',
            'pincode'               => 'required|string|max:10',
            'highest_qualification' => 'nullable|string|max:100',
            'batch_id'              => 'nullable|exists:batches,id',
            'status'                => 'required|in:enrolled,ongoing,completed,dropped',
            'photo'                 => 'nullable|image|max:2048',
            'signature'             => 'nullable|image|max:2048',
            'id_proof'              => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $oldValues = $studentModel->toArray();

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = FileUploadService::uploadImage($request->file('photo'), 'uploads/students/photos');
        }
        if ($request->hasFile('signature')) {
            $validated['signature_path'] = FileUploadService::uploadImage($request->file('signature'), 'uploads/students/signatures');
        }
        if ($request->hasFile('id_proof')) {
            $validated['id_proof_doc'] = FileUploadService::uploadDocument($request->file('id_proof'), 'uploads/students/docs');
        }

        return DB::transaction(function () use ($studentModel, $validated, $oldValues, $centre, $request) {
            $studentModel->update($validated);

            // Update student batch assignment if provided
            if ($request->has('batch_id')) {
                $admission = $studentModel->admissions()->where('centre_id', $centre->id)->latest('id')->first();
                if ($admission) {
                    $admission->update(['batch_id' => $request->batch_id ?: null]);
                }
            }

            // Update associated User credentials (email, mobile, password, name)
            if ($studentModel->user) {
                $userUpdates = [
                    'name'   => $validated['full_name'],
                    'mobile' => $validated['mobile'],
                ];
                if (!empty($validated['email'])) {
                    $userUpdates['email'] = $validated['email'];
                }
                if (!empty($validated['password'])) {
                    $userUpdates['password'] = Hash::make($validated['password']);
                }
                $studentModel->user->update($userUpdates);
            } elseif (!empty($validated['email']) || !empty($validated['password'])) {
                $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
                $newUser = User::create([
                    'name'      => $validated['full_name'],
                    'email'     => $validated['email'] ?? "student_{$studentModel->roll_no}@domain.com",
                    'mobile'    => $validated['mobile'],
                    'password'  => Hash::make($validated['password'] ?? $validated['mobile']),
                    'role'      => 'student',
                    'is_active' => true,
                ]);
                $newUser->roles()->syncWithoutDetaching([$studentRole->id]);
                $studentModel->update(['user_id' => $newUser->id]);
            }

            AuditLog::record('centre_student_updated', $studentModel, $oldValues, [
                'centre_id'  => $centre->id,
                'student_id' => $studentModel->id,
                'roll_no'    => $studentModel->roll_no,
            ]);

            return redirect()->route('centre.students.show', $studentModel->id)
                ->with('success', "Student dossier for [{$studentModel->full_name}] updated successfully.");
        });
    }
}
