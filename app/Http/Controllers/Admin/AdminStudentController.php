<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\District;
use App\Models\Enquiry;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['centre', 'state', 'district', 'admissions.course']);

        // Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%")
                  ->orWhere('registration_no', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Centre Filter
        if ($centreId = $request->input('centre_id')) {
            $query->where('centre_id', $centreId);
        }

        // Status Filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        $counts = [
            'all' => Student::count(),
            'enrolled' => Student::where('status', 'enrolled')->count(),
            'ongoing' => Student::where('status', 'ongoing')->count(),
            'completed' => Student::where('status', 'completed')->count(),
            'dropped' => Student::where('status', 'dropped')->count(),
            'enquiries' => Enquiry::where('type', 'admission_enquiry')->count(),
        ];

        return view('admin.students.index', compact('students', 'centres', 'counts'));
    }

    public function create()
    {
        $centres = Centre::where('status', 'active')->get();
        $states = State::orderBy('name')->get();
        return view('admin.students.create', compact('centres', 'states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'full_name' => 'required|string|max:191',
            'father_name' => 'required|string|max:191',
            'mother_name' => 'nullable|string|max:191',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|string|size:10',
            'alternate_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'highest_qualification' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $centre = Centre::findOrFail($validated['centre_id']);
            $year = date('Y');
            $randomSeq = str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
            $regNo = "DCE/{$year}/{$centre->centre_code}/{$randomSeq}";
            $rollNo = "DCE" . substr($year, -2) . rand(10000, 99999);

            // Handle Secure File Uploads
            $photoPath = $request->hasFile('photo') ? FileUploadService::uploadImage($request->file('photo'), 'uploads/students/photos') : null;
            $sigPath = $request->hasFile('signature') ? FileUploadService::uploadImage($request->file('signature'), 'uploads/students/signatures') : null;
            $docPath = $request->hasFile('id_proof') ? FileUploadService::uploadDocument($request->file('id_proof'), 'uploads/students/docs') : null;

            // Create Login User for Student
            $userEmail = !empty($validated['email']) ? $validated['email'] : "student_{$rollNo}@techeracomputer.in";
            $studentUser = User::create([
                'name' => $validated['full_name'],
                'email' => $userEmail,
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['mobile']), // Default password is mobile number
                'role' => 'student',
                'is_active' => true,
            ]);

            $studentRole = Role::where('name', 'student')->first();
            if ($studentRole) {
                $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
            }

            // Create Student Profile
            $student = Student::create([
                'user_id' => $studentUser->id,
                'centre_id' => $validated['centre_id'],
                'registration_no' => $regNo,
                'roll_no' => $rollNo,
                'full_name' => $validated['full_name'],
                'father_name' => $validated['father_name'],
                'mother_name' => $validated['mother_name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'mobile' => $validated['mobile'],
                'alternate_mobile' => $validated['alternate_mobile'],
                'email' => $validated['email'],
                'state_id' => $validated['state_id'],
                'district_id' => $validated['district_id'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'pincode' => $validated['pincode'],
                'highest_qualification' => $validated['highest_qualification'],
                'photo_path' => $photoPath,
                'signature_path' => $sigPath,
                'id_proof_doc' => $docPath,
                'status' => 'enrolled',
            ]);

            AuditLog::record('student_registered', $student, null, [
                'registration_no' => $regNo,
                'roll_no' => $rollNo,
                'centre_id' => $validated['centre_id'],
            ]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student [{$student->full_name}] registered successfully with Roll No: {$rollNo}. Default login password set to mobile number.");
        });
    }

    public function show(Student $student)
    {
        $student->load(['centre', 'state', 'district', 'admissions.course', 'fees', 'payments', 'attendances', 'certificates', 'marksheets']);
        return view('admin.students.show', compact('student'));
    }

    public function idCard(Student $student)
    {
        $student->load(['centre.state', 'centre.district', 'state', 'district', 'admissions.course', 'admissions.batch']);
        $centre = $student->centre ?? Centre::where('status', 'active')->first();
        $admission = $student->admissions->first();

        return view('centre.students.id_card', compact('student', 'centre', 'admission'));
    }

    public function edit(Student $student)
    {
        $centres = Centre::where('status', 'active')->get();
        $states = State::orderBy('name')->get();
        $districts = District::where('state_id', $student->state_id)->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'centres', 'states', 'districts'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'full_name' => 'required|string|max:191',
            'father_name' => 'required|string|max:191',
            'mother_name' => 'nullable|string|max:191',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|string|size:10',
            'alternate_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'highest_qualification' => 'nullable|string|max:100',
            'status' => 'required|in:enrolled,ongoing,completed,dropped',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $oldValues = $student->toArray();

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = FileUploadService::uploadImage($request->file('photo'), 'uploads/students/photos');
        }
        if ($request->hasFile('signature')) {
            $validated['signature_path'] = FileUploadService::uploadImage($request->file('signature'), 'uploads/students/signatures');
        }
        if ($request->hasFile('id_proof')) {
            $validated['id_proof_doc'] = FileUploadService::uploadDocument($request->file('id_proof'), 'uploads/students/docs');
        }

        $student->update($validated);

        AuditLog::record('student_updated', $student, $oldValues, $student->toArray());

        return redirect()->route('admin.students.show', $student->id)
            ->with('success', "Student record [{$student->full_name}] updated successfully.");
    }

    public function destroy(Student $student)
    {
        $name = $student->full_name;
        $student->delete(); // Soft delete

        AuditLog::record('student_deleted', $student, ['status' => 'deleted']);

        return redirect()->route('admin.students.index')
            ->with('success', "Student [{$name}] has been soft-deleted and archived.");
    }

    public function export(Request $request): StreamedResponse
    {
        $students = Student::with('centre')->latest()->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_export_' . date('Y-m-d_His') . '.csv"',
        ];

        AuditLog::record('students_exported_csv');

        return response()->stream(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Roll No', 'Registration No', 'Full Name', 'Father Name', 'Mobile', 'Email', 'Centre Code', 'Centre Name', 'City', 'Status', 'Date Joined']);

            foreach ($students as $s) {
                fputcsv($handle, [
                    $s->roll_no,
                    $s->registration_no,
                    $s->full_name,
                    $s->father_name,
                    $s->mobile,
                    $s->email,
                    $s->centre?->centre_code,
                    $s->centre?->centre_name,
                    $s->city,
                    ucfirst($s->status),
                    $s->created_at->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
