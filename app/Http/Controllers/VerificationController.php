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
        $cleanedCertNo = str_replace('-', '/', $cert_no);

        $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject'])
            ->where('certificate_no', $cert_no)
            ->orWhere('certificate_no', $cleanedCertNo)
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
            $marksheet = Marksheet::with(['student.centre', 'course', 'result.subjectResults.subject'])
                ->where('marksheet_no', $cert_no)
                ->orWhere('marksheet_no', $cleanedCertNo)
                ->firstOrFail();

            $certificate = Certificate::with(['student.centre', 'course', 'result.subjectResults.subject'])
                ->where('exam_result_id', $marksheet->exam_result_id)
                ->first();
        }

        VerificationLog::create([
            'type' => 'certificate',
            'search_query' => "QR-Scan: {$cert_no}",
            'result_found' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('pages.check-certificate', [
            'searched' => true,
            'certificate' => $certificate,
            'marksheet' => $marksheet,
            'queryRegistration' => $certificate->certificate_no,
            'queryFather' => $certificate->student->father_name,
            'isDirectScan' => true,
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
}
