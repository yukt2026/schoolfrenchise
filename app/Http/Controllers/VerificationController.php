<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Certificate;
use App\Models\Marksheet;
use App\Models\Student;
use App\Models\VerificationLog;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function certificateIndex()
    {
        return view('pages.check-certificate');
    }

    public function verifyCertificate(Request $request)
    {
        $request->validate([
            'registration' => 'required|string|max:60',
            'father' => 'nullable|string|max:191',
        ]);

        $queryStr = trim($request->registration);
        $fatherName = trim($request->father ?? '');

        // Search in certificates table first (either by certificate_no or via student relationship)
        $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject'])
            ->where(function ($q) use ($queryStr) {
                $q->where('certificate_no', $queryStr)
                  ->orWhereHas('student', function ($sq) use ($queryStr) {
                      $sq->where('registration_no', $queryStr)
                        ->orWhere('roll_no', $queryStr);
                  });
            })
            ->when(!empty($fatherName), function ($q) use ($fatherName) {
                $q->whereHas('student', function ($sq) use ($fatherName) {
                    $sq->where('father_name', 'like', "%{$fatherName}%");
                });
            })
            ->first();

        // Also search marksheet
        $marksheet = null;
        if ($certificate) {
            $marksheet = Marksheet::where('exam_result_id', $certificate->exam_result_id)->first();
        } else {
            $marksheet = Marksheet::with(['student.centre', 'course', 'result.subjectResults.subject'])
                ->where('marksheet_no', $queryStr)
                ->first();
            if ($marksheet) {
                $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject'])
                    ->where('exam_result_id', $marksheet->exam_result_id)
                    ->first();
            }
        }

        // Log verification audit
        VerificationLog::create([
            'type' => 'certificate',
            'search_query' => $queryStr . ($fatherName ? " / Father: {$fatherName}" : ''),
            'result_found' => (bool)$certificate,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('pages.check-certificate', [
            'searched' => true,
            'certificate' => $certificate,
            'marksheet' => $marksheet,
            'queryRegistration' => $queryStr,
            'queryFather' => $fatherName,
        ]);
    }

    public function directVerifyCert($cert_no)
    {
        $cert_no = trim($cert_no);
        $cleanedCertNo = str_replace('-', '/', $cert_no);

        $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
            ->where('verification_token', $cert_no)
            ->orWhere('certificate_no', $cert_no)
            ->orWhere('certificate_no', $cleanedCertNo)
            ->orWhere('serial_no', $cert_no)
            ->orWhereHas('student', function ($sq) use ($cert_no, $cleanedCertNo) {
                $sq->where('roll_no', $cert_no)
                  ->orWhere('registration_no', $cert_no)
                  ->orWhere('registration_no', $cleanedCertNo);
            })
            ->first();

        $marksheet = null;
        if ($certificate) {
            $marksheet = Marksheet::where('exam_result_id', $certificate->exam_result_id)->first();
        } else {
            $marksheet = Marksheet::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
                ->where('verification_token', $cert_no)
                ->orWhere('marksheet_no', $cert_no)
                ->orWhere('marksheet_no', $cleanedCertNo)
                ->orWhere('serial_no', $cert_no)
                ->first();

            if ($marksheet) {
                $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
                    ->where('exam_result_id', $marksheet->exam_result_id)
                    ->first();
            }
        }

        try {
            VerificationLog::create([
                'type' => 'certificate',
                'search_query' => "QR-Cert-Scan: {$cert_no}",
                'result_found' => (bool)$certificate,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return view('pages.check-certificate', [
            'searched' => true,
            'certificate' => $certificate,
            'marksheet' => $marksheet,
            'queryRegistration' => $certificate ? $certificate->certificate_no : $cert_no,
            'queryFather' => $certificate ? ($certificate->student->father_name ?? '') : '',
            'isDirectScan' => true,
            'status' => $certificate ? ($certificate->status === 'revoked' ? 'revoked' : 'valid') : 'invalid',
        ]);
    }

    public function directVerifyMarksheet($token)
    {
        $token = trim($token);
        $cleanedToken = str_replace('-', '/', $token);

        $marksheet = Marksheet::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
            ->where('verification_token', $token)
            ->orWhere('marksheet_no', $token)
            ->orWhere('marksheet_no', $cleanedToken)
            ->orWhere('serial_no', $token)
            ->orWhereHas('student', function ($sq) use ($token, $cleanedToken) {
                $sq->where('roll_no', $token)
                  ->orWhere('registration_no', $token)
                  ->orWhere('registration_no', $cleanedToken);
            })
            ->first();

        $certificate = null;
        if ($marksheet) {
            $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
                ->where('exam_result_id', $marksheet->exam_result_id)
                ->first();
        } else {
            $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject', 'centre.state', 'centre.district'])
                ->where('verification_token', $token)
                ->orWhere('certificate_no', $token)
                ->orWhere('certificate_no', $cleanedToken)
                ->first();

            if ($certificate) {
                $marksheet = Marksheet::where('exam_result_id', $certificate->exam_result_id)->first();
            }
        }

        try {
            VerificationLog::create([
                'type' => 'marksheet',
                'search_query' => "QR-MS-Scan: {$token}",
                'result_found' => (bool)$marksheet,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return view('pages.check-certificate', [
            'searched' => true,
            'certificate' => $certificate,
            'marksheet' => $marksheet,
            'queryRegistration' => $marksheet ? $marksheet->marksheet_no : $token,
            'queryFather' => $marksheet ? ($marksheet->student->father_name ?? '') : '',
            'isDirectScan' => true,
            'status' => $marksheet ? ($certificate && $certificate->status === 'revoked' ? 'revoked' : 'valid') : 'invalid',
        ]);
    }

    public function admissionIndex()
    {
        return view('pages.check-admission');
    }

    public function verifyAdmission(Request $request)
    {
        $request->validate([
            'search_key' => 'required|string|max:60',
            'dob' => 'nullable|date',
        ]);

        $searchKey = trim($request->search_key);
        $dob = $request->dob;

        $student = Student::with(['centre', 'admissions.course', 'admissions.batch'])
            ->where(function ($q) use ($searchKey) {
                $q->where('registration_no', $searchKey)
                  ->orWhere('roll_no', $searchKey);
            })
            ->when($dob, function ($q) use ($dob) {
                $q->where('dob', $dob);
            })
            ->first();

        VerificationLog::create([
            'type' => 'admission',
            'search_query' => $searchKey . ($dob ? " / DOB: {$dob}" : ''),
            'result_found' => (bool)$student,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('pages.check-admission', [
            'searched' => true,
            'student' => $student,
            'searchKey' => $searchKey,
            'dob' => $dob,
        ]);
    }

    /**
     * Dedicated Student ID Card Online QR Verification Endpoint.
     * Validates cryptographic verification token against database.
     */
    public function verifyStudentCard(Request $request, $token)
    {
        $token = trim($token);

        $student = Student::withoutGlobalScopes()
            ->with(['centre.state', 'centre.district', 'admissions.course', 'admissions.batch', 'state', 'district'])
            ->where('verification_token', $token)
            ->orWhere('registration_no', $token)
            ->orWhere('roll_no', $token)
            ->first();

        $status = 'invalid';
        if ($student) {
            $status = $student->effective_id_card_status;
        }

        try {
            VerificationLog::create([
                'type'         => 'student_id_card',
                'search_query' => "ID-Card-QR: {$token}",
                'result_found' => (bool) $student,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Ignore log creation errors
        }

        $admission = $student ? $student->admissions->first() : null;
        $course = $admission ? $admission->course : null;
        $centre = $student ? $student->centre : null;

        return view('pages.verify-student', [
            'student'   => $student,
            'status'    => $status,
            'token'     => $token,
            'admission' => $admission,
            'course'    => $course,
            'centre'    => $centre,
        ]);
    }
}
