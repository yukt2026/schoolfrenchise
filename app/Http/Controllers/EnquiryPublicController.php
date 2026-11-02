<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\State;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryPublicController extends Controller
{
    public function storeDemo(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:191',
            'mobile'      => 'required|string|max:20',
            'email'       => 'nullable|email|max:191',
            'state_id'    => 'required|exists:states,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'subject'     => 'required|string|max:191',
        ]);

        $validated['type'] = 'demo_request';
        $validated['status'] = 'new';

        Enquiry::create($validated);

        return back()->with('success', 'Thank you! Your request for Live Demo has been submitted. Our executive will reach out to you shortly.');
    }

    public function admissionEnquiryIndex()
    {
        $courses = Course::where('status', true)->get();
        $states = State::where('status', true)->orderBy('name')->get();
        return view('pages.admission-enquiry', compact('courses', 'states'));
    }

    public function storeAdmissionEnquiry(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:191',
            'mobile'      => 'required|string|max:20',
            'email'       => 'nullable|email|max:191',
            'state_id'    => 'required|exists:states,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'course_id'   => 'nullable|exists:courses,id',
            'subject'     => 'required|string|max:191',
            'message'     => 'nullable|string',
        ]);

        $validated['type'] = 'admission_enquiry';
        $validated['status'] = 'new';

        Enquiry::create($validated);

        return back()->with('success', 'Thank you for your admission enquiry! Our academic counseling desk will contact you with course details and nearest centre info.');
    }

    /**
     * Display public online student admission application form.
     */
    public function admissionApplyIndex()
    {
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $states = State::where('status', true)->orderBy('name')->get();

        return view('pages.admission-apply', compact('courses', 'centres', 'states'));
    }

    /**
     * Submit public admission application with file validation and unique application number.
     */
    public function storeAdmissionApplication(Request $request)
    {
        $validated = $request->validate([
            'centre_id'              => 'required|exists:centres,id',
            'course_id'              => 'required|exists:courses,id',
            'applicant_name'         => 'required|string|max:191',
            'father_name'            => 'required|string|max:191',
            'mother_name'            => 'nullable|string|max:191',
            'dob'                    => 'required|date|before:today',
            'gender'                 => 'required|in:male,female,other',
            'mobile'                 => 'required|string|max:20',
            'email'                  => 'nullable|email|max:191',
            'state_id'               => 'required|exists:states,id',
            'district_id'            => [
                'required',
                Rule::exists('districts', 'id')->where('state_id', $request->state_id),
            ],
            'city'                   => 'required|string|max:100',
            'address'                => 'required|string|max:1000',
            'pincode'                => 'required|string|max:10',
            'highest_qualification'  => 'nullable|string|max:100',
            'photo'                  => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'id_proof'               => 'nullable|file|mimes:pdf,jpeg,png,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = FileUploadService::uploadImage($request->file('photo'), 'admissions/photos');
        }

        $idProofPath = null;
        if ($request->hasFile('id_proof')) {
            $idProofPath = FileUploadService::uploadDocument($request->file('id_proof'), 'admissions/docs');
        }

        $applicationNo = AdmissionApplication::generateApplicationNumber();

        $application = AdmissionApplication::create([
            'application_no'        => $applicationNo,
            'centre_id'             => $validated['centre_id'],
            'course_id'             => $validated['course_id'],
            'applicant_name'        => $validated['applicant_name'],
            'father_name'           => $validated['father_name'],
            'mother_name'           => $validated['mother_name'] ?? null,
            'dob'                   => $validated['dob'],
            'gender'                => $validated['gender'],
            'mobile'                => $validated['mobile'],
            'email'                 => $validated['email'] ?? null,
            'state_id'              => $validated['state_id'],
            'district_id'           => $validated['district_id'],
            'city'                  => $validated['city'],
            'address'               => $validated['address'],
            'pincode'               => $validated['pincode'],
            'highest_qualification' => $validated['highest_qualification'] ?? null,
            'photo_path'            => $photoPath,
            'id_proof_doc'          => $idProofPath,
            'status'                => 'pending',
        ]);

        return redirect()->route('admission.status', ['application_no' => $applicationNo])
            ->with('success', "Your admission application has been successfully submitted! Application No: {$applicationNo}. Keep this number for tracking.");
    }

    /**
     * Public page to track admission application status.
     */
    public function admissionStatusIndex(Request $request)
    {
        $application = null;
        if ($request->filled('application_no')) {
            $application = AdmissionApplication::with(['course', 'centre', 'convertedStudent'])
                ->where('application_no', trim($request->application_no))
                ->orWhere('mobile', trim($request->application_no))
                ->latest()
                ->first();
        }

        return view('pages.admission-status', compact('application'));
    }

    /**
     * POST handler for admission application lookup.
     */
    public function checkAdmissionStatus(Request $request)
    {
        $validated = $request->validate([
            'application_no' => 'required|string|max:60',
        ]);

        return redirect()->route('admission.status', ['application_no' => trim($validated['application_no'])]);
    }

    /**
     * Generate printable Admission Application Acknowledgement Slip.
     */
    public function printAcknowledgement(string $application_no)
    {
        $application = AdmissionApplication::with([
            'course.subjects',
            'centre.state',
            'centre.district',
            'state',
            'district',
            'convertedStudent.admissions.batch',
        ])
        ->where('application_no', trim($application_no))
        ->firstOrFail();

        return view('pages.admission-acknowledgement', compact('application'));
    }
}
