<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\District;
use App\Models\FeePayment;
use App\Models\FranchiseApplication;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCentreController extends Controller
{
    public function index(Request $request)
    {
        $query = Centre::with(['state', 'district', 'wallet'])
            ->withCount(['students', 'admissions', 'batches']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('centre_code', 'like', "%{$s}%")
                    ->orWhere('centre_name', 'like', "%{$s}%")
                    ->orWhere('director_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        $centres = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_centres'        => Centre::count(),
            'active_centres'       => Centre::where('status', 'active')->count(),
            'pending_applications' => FranchiseApplication::where('status', 'pending')->count(),
            'total_students'       => Student::count(),
            'total_lab_capacity'   => (int) Centre::sum('lab_capacity'),
        ];

        $states = State::where('status', true)->orderBy('name')->get();

        return view('admin.centres.index', compact('centres', 'stats', 'states'));
    }

    public function applications(Request $request)
    {
        $query = FranchiseApplication::with(['state', 'district']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('application_no', 'like', "%{$s}%")
                    ->orWhere('applicant_name', 'like', "%{$s}%")
                    ->orWhere('centre_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total'    => FranchiseApplication::count(),
            'pending'  => FranchiseApplication::where('status', 'pending')->count(),
            'reviewed' => FranchiseApplication::where('status', 'reviewed')->count(),
            'approved' => FranchiseApplication::where('status', 'approved')->count(),
            'rejected' => FranchiseApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.centres.applications', compact('applications', 'stats'));
    }

    public function showApplication(FranchiseApplication $application)
    {
        $application->load(['state', 'district']);

        return view('admin.centres.show_application', compact('application'));
    }

    public function approveApplication(Request $request, FranchiseApplication $application)
    {
        $validated = $request->validate([
            'centre_code'  => 'nullable|string|max:50|unique:centres,centre_code',
            'lab_capacity' => 'nullable|integer|min:0',
            'valid_upto'   => 'nullable|date',
            'remarks'      => 'nullable|string|max:500',
        ]);

        $centre = DB::transaction(function () use ($application, $validated) {
            // Generate unique Centre Code if not provided
            $centreCode = !empty($validated['centre_code']) ? strtoupper(trim($validated['centre_code'])) : null;
            if (!$centreCode) {
                $stateCode = $application->state ? strtoupper(substr($application->state->code ?? $application->state->name, 0, 2)) : 'DL';
                do {
                    $centreCode = 'DCE-' . $stateCode . rand(100, 999);
                } while (Centre::where('centre_code', $centreCode)->exists());
            }

            // Provision User Account for Franchise Owner
            $user = User::where('email', $application->email)
                ->orWhere('mobile', $application->mobile)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $application->applicant_name,
                    'email'     => $application->email,
                    'mobile'    => $application->mobile,
                    'password'  => Hash::make($application->mobile),
                    'role'      => 'centre',
                    'is_active' => true,
                ]);

                $centreRole = Role::where('name', 'centre')->first();
                if ($centreRole) {
                    $user->roles()->syncWithoutDetaching([$centreRole->id]);
                }
            } else {
                $user->update([
                    'role'      => 'centre',
                    'is_active' => true,
                ]);
            }

            // Create Centre Profile
            $newCentre = Centre::create([
                'user_id'          => $user->id,
                'centre_code'      => $centreCode,
                'centre_name'      => $application->centre_name,
                'director_name'    => $application->applicant_name,
                'email'            => $application->email,
                'mobile'           => $application->mobile,
                'state_id'         => $application->state_id,
                'district_id'      => $application->district_id,
                'city'             => $application->city,
                'address'          => $application->address,
                'pincode'          => '110001',
                'lab_capacity'     => $validated['lab_capacity'] ?? $application->computers_count ?? 10,
                'affiliation_date' => now()->toDateString(),
                'valid_upto'       => $validated['valid_upto'] ?? now()->addYear()->toDateString(),
                'status'           => 'active',
            ]);

            // Link Centre to User
            $user->update(['centre_id' => $newCentre->id]);

            // Initialize Wallet
            CentreWallet::firstOrCreate(
                ['centre_id' => $newCentre->id],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            // Update Application Record
            $application->update([
                'status'  => 'approved',
                'remarks' => $validated['remarks'] ?? 'Application approved by Central Board. Centre allocated: ' . $centreCode,
            ]);

            AuditLog::record('franchise_application_approved', $newCentre, null, [
                'application_id' => $application->id,
                'centre_code'    => $centreCode,
                'director_name'  => $application->applicant_name,
            ]);

            return $newCentre;
        });

        return redirect()->route('admin.centres.show', $centre)
            ->with('success', "Franchise Application #{$application->application_no} approved successfully! Centre {$centre->centre_code} created.");
    }

    public function rejectApplication(Request $request, FranchiseApplication $application)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $application->update([
            'status'  => 'rejected',
            'remarks' => $validated['reason'],
        ]);

        AuditLog::record('franchise_application_rejected', $application, null, [
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('admin.centres.applications')
            ->with('success', "Franchise application #{$application->application_no} marked as rejected.");
    }

    public function create()
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $suggestedCode = 'DCE-' . date('y') . '-' . sprintf('%03d', Centre::withTrashed()->count() + 1);

        return view('admin.centres.create', compact('states', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_code'        => 'required|string|max:50|unique:centres,centre_code',
            'centre_name'        => 'required|string|max:191',
            'director_name'      => 'required|string|max:191',
            'email'              => 'required|email|max:191',
            'mobile'             => 'required|string|max:20',
            'alternate_mobile'   => 'nullable|string|max:20',
            'state_id'           => 'required|exists:states,id',
            'district_id'        => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city'               => 'required|string|max:100',
            'address'            => 'required|string',
            'pincode'            => 'required|string|max:10',
            'lab_capacity'       => 'nullable|integer|min:0',
            'affiliation_date'   => 'required|date',
            'valid_upto'         => 'nullable|date|after_or_equal:affiliation_date',
            'status'             => 'required|in:active,pending,suspended,rejected',
            'password'           => 'nullable|string|min:6',
            'centre_logo'        => 'nullable|image|max:2048',
            'director_photo'     => 'nullable|image|max:2048',
            'director_signature' => 'nullable|image|max:2048',
        ]);

        $centre = DB::transaction(function () use ($request, $validated) {
            // Handle file uploads
            $logoPath = $request->hasFile('centre_logo')
                ? FileUploadService::uploadImage($request->file('centre_logo'), 'uploads/centres/logos')
                : null;
            $photoPath = $request->hasFile('director_photo')
                ? FileUploadService::uploadImage($request->file('director_photo'), 'uploads/centres/directors')
                : null;
            $sigPath = $request->hasFile('director_signature')
                ? FileUploadService::uploadImage($request->file('director_signature'), 'uploads/centres/signatures')
                : null;

            // Provision User Account
            $user = User::where('email', $validated['email'])
                ->orWhere('mobile', $validated['mobile'])
                ->first();

            $plainPassword = $validated['password'] ?? $validated['mobile'];

            if (!$user) {
                $user = User::create([
                    'name'      => $validated['director_name'],
                    'email'     => $validated['email'],
                    'mobile'    => $validated['mobile'],
                    'password'  => Hash::make($plainPassword),
                    'role'      => 'centre',
                    'is_active' => $validated['status'] === 'active',
                ]);

                $centreRole = Role::where('name', 'centre')->first();
                if ($centreRole) {
                    $user->roles()->syncWithoutDetaching([$centreRole->id]);
                }
            } else {
                $user->update([
                    'role'      => 'centre',
                    'is_active' => $validated['status'] === 'active',
                ]);
            }

            // Create Centre
            $newCentre = Centre::create([
                'user_id'            => $user->id,
                'centre_code'        => strtoupper(trim($validated['centre_code'])),
                'centre_name'        => $validated['centre_name'],
                'director_name'      => $validated['director_name'],
                'email'              => $validated['email'],
                'mobile'             => $validated['mobile'],
                'alternate_mobile'   => $validated['alternate_mobile'] ?? null,
                'state_id'           => $validated['state_id'],
                'district_id'        => $validated['district_id'],
                'city'               => $validated['city'],
                'address'            => $validated['address'],
                'pincode'            => $validated['pincode'],
                'lab_capacity'       => $validated['lab_capacity'] ?? 0,
                'centre_logo'        => $logoPath,
                'director_photo'     => $photoPath,
                'director_signature' => $sigPath,
                'affiliation_date'   => $validated['affiliation_date'],
                'valid_upto'         => $validated['valid_upto'] ?? null,
                'status'             => $validated['status'],
            ]);

            $user->update(['centre_id' => $newCentre->id]);

            // Initialize Wallet
            CentreWallet::firstOrCreate(
                ['centre_id' => $newCentre->id],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            AuditLog::record('centre_created', $newCentre, null, $newCentre->toArray());

            return $newCentre;
        });

        return redirect()->route('admin.centres.show', $centre)
            ->with('success', "Centre {$centre->centre_name} ({$centre->centre_code}) registered successfully.");
    }

    public function show(Centre $centre)
    {
        $centre->load(['state', 'district', 'wallet', 'staffUsers', 'user']);

        $recentStudents = Student::where('centre_id', $centre->id)->latest('id')->take(8)->get();
        $recentAdmissions = $centre->admissions()->with(['student', 'course'])->latest('id')->take(8)->get();
        $batches = $centre->batches()->with('course')->withCount('students')->get();

        $stats = [
            'total_students'       => Student::where('centre_id', $centre->id)->count(),
            'total_admissions'     => $centre->admissions()->count(),
            'total_batches'        => $batches->count(),
            'total_collected_fees' => (float) FeePayment::where('centre_id', $centre->id)->sum('amount'),
            'wallet_balance'       => (float) ($centre->wallet?->balance ?? 0.00),
        ];

        return view('admin.centres.show', compact('centre', 'recentStudents', 'recentAdmissions', 'batches', 'stats'));
    }

    public function edit(Centre $centre)
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $districts = District::where('state_id', $centre->state_id)->where('status', true)->orderBy('name')->get();

        return view('admin.centres.edit', compact('centre', 'states', 'districts'));
    }

    public function update(Request $request, Centre $centre)
    {
        $validated = $request->validate([
            'centre_code'        => ['required', 'string', 'max:50', Rule::unique('centres', 'centre_code')->ignore($centre->id)],
            'centre_name'        => 'required|string|max:191',
            'director_name'      => 'required|string|max:191',
            'email'              => 'required|email|max:191',
            'mobile'             => 'required|string|max:20',
            'alternate_mobile'   => 'nullable|string|max:20',
            'state_id'           => 'required|exists:states,id',
            'district_id'        => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city'               => 'required|string|max:100',
            'address'            => 'required|string',
            'pincode'            => 'required|string|max:10',
            'lab_capacity'       => 'nullable|integer|min:0',
            'affiliation_date'   => 'required|date',
            'valid_upto'         => 'nullable|date|after_or_equal:affiliation_date',
            'status'             => 'required|in:active,pending,suspended,rejected',
            'centre_logo'        => 'nullable|image|max:2048',
            'director_photo'     => 'nullable|image|max:2048',
            'director_signature' => 'nullable|image|max:2048',
        ]);

        $oldData = $centre->toArray();

        if ($request->hasFile('centre_logo')) {
            $validated['centre_logo'] = FileUploadService::uploadImage($request->file('centre_logo'), 'uploads/centres/logos');
        }

        if ($request->hasFile('director_photo')) {
            $validated['director_photo'] = FileUploadService::uploadImage($request->file('director_photo'), 'uploads/centres/directors');
        }

        if ($request->hasFile('director_signature')) {
            $validated['director_signature'] = FileUploadService::uploadImage($request->file('director_signature'), 'uploads/centres/signatures');
        }

        $validated['centre_code'] = strtoupper(trim($validated['centre_code']));

        $centre->update($validated);

        if ($centre->user) {
            $centre->user->update([
                'name'      => $validated['director_name'],
                'email'     => $validated['email'],
                'mobile'    => $validated['mobile'],
                'is_active' => $validated['status'] === 'active',
            ]);
        }

        AuditLog::record('centre_updated', $centre, $oldData, $centre->toArray());

        return redirect()->route('admin.centres.show', $centre)
            ->with('success', "Centre particulars for {$centre->centre_code} updated successfully.");
    }

    public function toggleStatus(Request $request, Centre $centre)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,pending,suspended,rejected',
        ]);

        $oldStatus = $centre->status;
        $centre->update(['status' => $validated['status']]);

        if ($centre->user) {
            $centre->user->update(['is_active' => $validated['status'] === 'active']);
        }

        AuditLog::record('centre_status_changed', $centre, ['status' => $oldStatus], ['status' => $validated['status']]);

        return back()->with('success', "Centre {$centre->centre_code} status updated to " . ucfirst($validated['status']) . ".");
    }

    public function certificate(Centre $centre)
    {
        $centre->load(['state', 'district', 'user']);

        return view('admin.centres.certificate', compact('centre'));
    }

    public function destroy(Centre $centre)
    {
        $oldData = $centre->toArray();
        $centre->delete();

        AuditLog::record('centre_deleted', $centre, $oldData, null);

        return redirect()->route('admin.centres.index')
            ->with('success', "Centre {$centre->centre_name} ({$centre->centre_code}) has been archived successfully.");
    }
}
