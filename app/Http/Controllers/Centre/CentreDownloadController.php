<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Download;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Marksheet;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CentreDownloadController extends Controller
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
        $query = Download::where('status', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('title', 'like', "%{$s}%");
        }

        $downloads = $query->latest('id')->paginate(12)->withQueryString();
        $categories = Download::select('category')->distinct()->pluck('category');

        return view('centre.downloads.index', compact('centre', 'downloads', 'categories'));
    }

    public function certificate()
    {
        $centre = $this->getAuthCentre();
        $centre->load(['state', 'district']);

        return view('centre.downloads.certificate', compact('centre'));
    }

    public function studentCertificates(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Certificate::where('centre_id', $centre->id)
            ->with(['student', 'course', 'examResult']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('certificate_no', 'like', "%{$s}%")
                  ->orWhereHas('student', function ($sq) use ($s) {
                      $sq->where('full_name', 'like', "%{$s}%")
                        ->orWhere('roll_no', 'like', "%{$s}%")
                        ->orWhere('registration_no', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('id')->paginate(15)->withQueryString();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        $stats = [
            'total' => Certificate::where('centre_id', $centre->id)->count(),
            'active' => Certificate::where('centre_id', $centre->id)->where('status', 'issued')->count(),
            'revoked' => Certificate::where('centre_id', $centre->id)->where('status', 'revoked')->count(),
        ];

        return view('centre.downloads.student_certificates', compact('centre', 'certificates', 'courses', 'stats'));
    }

    public function createCertificate(Request $request)
    {
        $centre = $this->getAuthCentre();

        // 1. Eligible passed exam results for this centre without certificate yet
        $eligibleResults = ExamResult::with(['student.centre', 'course'])
            ->where('centre_id', $centre->id)
            ->where('result_status', 'passed')
            ->doesntHave('certificate')
            ->latest()
            ->get();

        // 2. Enrolled students of this centre who don't have a certificate yet (for direct certification)
        $eligibleStudents = Student::where('centre_id', $centre->id)
            ->whereDoesntHave('certificates')
            ->with(['centre', 'admissions.course.subjects'])
            ->orderBy('full_name')
            ->get();

        $selectedResult = null;
        if ($request->filled('exam_result_id')) {
            $selectedResult = ExamResult::with(['student.centre', 'course', 'subjectResults.subject'])
                ->where('centre_id', $centre->id)
                ->find($request->exam_result_id);
        }

        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::with(['centre', 'admissions.course.subjects'])
                ->where('centre_id', $centre->id)
                ->find($request->student_id);
        }

        $courses = Course::where('status', true)->with('subjects')->orderBy('course_name')->get();

        return view('centre.downloads.create_certificate', compact('centre', 'eligibleResults', 'eligibleStudents', 'selectedResult', 'selectedStudent', 'courses'));
    }

    public function storeCertificate(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'exam_result_id'  => 'nullable|exists:exam_results,id|unique:certificates,exam_result_id',
            'student_id'      => 'required_without:exam_result_id|nullable|exists:students,id',
            'course_id'       => 'nullable|exists:courses,id',
            'percentage'      => 'nullable|numeric|min:35|max:100',
            'grade'           => 'nullable|string|max:10',
            'certificate_no'  => 'nullable|string|max:60|unique:certificates,certificate_no',
            'marksheet_no'    => 'nullable|string|max:60|unique:marksheets,marksheet_no',
            'issue_date'      => 'nullable|date',
        ]);

        if (empty($validated['issue_date'])) {
            if (!empty($validated['student_id'])) {
                $candidate = Student::with(['admissions.course'])->find($validated['student_id']);
                $targetCourseId = !empty($validated['course_id']) ? $validated['course_id'] : null;
                $adm = $targetCourseId 
                    ? ($candidate?->admissions()->where('course_id', $targetCourseId)->latest('admission_date')->first() ?? $candidate?->admissions()->latest('admission_date')->first())
                    : $candidate?->admissions()->latest('admission_date')->first();

                $crs = ($targetCourseId ? Course::find($targetCourseId) : null) ?? $adm?->course;
                if ($adm && $adm->admission_date && $crs) {
                    $validated['issue_date'] = \Carbon\Carbon::parse($adm->admission_date)->addMonths($crs->duration_months)->toDateString();
                } else {
                    $validated['issue_date'] = now()->toDateString();
                }
            } else {
                $validated['issue_date'] = now()->toDateString();
            }
        }

        $year = date('Y', strtotime($validated['issue_date']));

        if (!empty($validated['exam_result_id'])) {
            $result = ExamResult::with(['student', 'course', 'centre'])
                ->where('centre_id', $centre->id)
                ->findOrFail($validated['exam_result_id']);
            $student = $result->student;
            $course = $result->course;
        } else {
            // Direct candidate certificate issuance for centre student
            $student = Student::with(['centre', 'admissions.course.subjects'])
                ->where('centre_id', $centre->id)
                ->findOrFail($validated['student_id']);
            $admission = $student->admissions()->latest()->first();
            $courseId = !empty($validated['course_id']) ? $validated['course_id'] : ($admission ? $admission->course_id : null);

            if (!$courseId) {
                return back()->withInput()->with('error', 'The selected candidate has no active course enrollment. Please assign a course first.');
            }

            $course = Course::with('subjects')->findOrFail($courseId);

            // Find or create standard examination session for this course
            $exam = Exam::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'exam_code' => 'CERT-EXAM-' . strtoupper($course->course_code) . '-' . $year,
                ],
                [
                    'exam_title'   => 'Board Certification Assessment - ' . $course->course_name,
                    'session_name' => ($year - 1) . '-' . $year,
                    'exam_date'    => $validated['issue_date'],
                    'status'       => 'published',
                ]
            );

            // Compute subject marks
            $pct = (float) (!empty($validated['percentage']) ? $validated['percentage'] : 75.0);
            $grade = !empty($validated['grade']) ? $validated['grade'] : ExamResult::calculateGrade($pct);

            $totalObtained = 0.0;
            $totalMax = 0.0;
            $subjectEntries = [];

            if ($course->subjects->count() > 0) {
                foreach ($course->subjects as $sub) {
                    $subMax = (float) ($sub->total_max ?: 100);
                    $subObt = round(($pct / 100) * $subMax, 1);
                    $thMax = (float) ($sub->theory_max ?: ($subMax * 0.7));
                    $prMax = (float) ($sub->practical_max ?: ($subMax * 0.3));
                    $thObt = round(($pct / 100) * $thMax, 1);
                    $prObt = round(($pct / 100) * $prMax, 1);

                    $totalObtained += $subObt;
                    $totalMax += $subMax;

                    $subjectEntries[] = [
                        'course_subject_id'  => $sub->id,
                        'theory_obtained'    => $thObt,
                        'practical_obtained' => $prObt,
                        'viva_obtained'      => 0.0,
                        'total_obtained'     => $subObt,
                        'max_marks'          => $subMax,
                        'grade'              => $grade,
                        'is_passed'          => true,
                    ];
                }
            } else {
                $totalMax = 100.0;
                $totalObtained = $pct;
            }

            $result = ExamResult::create([
                'exam_id'              => $exam->id,
                'student_id'           => $student->id,
                'centre_id'            => $centre->id,
                'course_id'            => $course->id,
                'total_marks_obtained' => $totalObtained,
                'total_max_marks'      => $totalMax,
                'percentage'           => $pct,
                'grade'                => $grade,
                'result_status'        => 'passed',
                'approved_by'          => Auth::id(),
                'approved_at'          => now(),
            ]);

            foreach ($subjectEntries as $entry) {
                $entry['exam_result_id'] = $result->id;
                ExamResultSubject::create($entry);
            }
        }

        if (empty($validated['certificate_no'])) {
            $lastId = Certificate::max('id') + 1;
            $validated['certificate_no'] = sprintf('TEC/CERT/%s/%04d', $year, $lastId);
        }

        if (empty($validated['marksheet_no'])) {
            $lastMsId = Marksheet::max('id') + 1;
            $validated['marksheet_no'] = sprintf('TEC/MS/%s/%04d', $year, $lastMsId);
        }

        $certificate = DB::transaction(function () use ($result, $student, $validated, $centre) {
            $certHash = hash('sha256', "{$validated['certificate_no']}-{$student->roll_no}-" . config('app.key'));
            $msHash = hash('sha256', "{$validated['marksheet_no']}-{$student->roll_no}-" . config('app.key'));
            $qrUrl = url('/verify/cert/' . urlencode($validated['certificate_no']));

            // Create Marksheet
            Marksheet::create([
                'exam_result_id' => $result->id,
                'student_id'     => $result->student_id,
                'centre_id'      => $centre->id,
                'course_id'      => $result->course_id,
                'marksheet_no'   => $validated['marksheet_no'],
                'issue_date'     => $validated['issue_date'],
                'security_hash'  => $msHash,
                'is_issued'      => true,
            ]);

            // Create Certificate
            $certificate = Certificate::create([
                'exam_result_id' => $result->id,
                'student_id'     => $result->student_id,
                'centre_id'      => $centre->id,
                'course_id'      => $result->course_id,
                'certificate_no' => $validated['certificate_no'],
                'issue_date'     => $validated['issue_date'],
                'security_hash'  => $certHash,
                'qr_code_url'    => $qrUrl,
                'status'         => 'issued',
            ]);

            AuditLog::record(
                'centre_certificate_issued',
                $certificate,
                [],
                $certificate->toArray()
            );

            // Dispatch Certificate Issuance Notification
            try {
                \App\Services\NotificationDispatchService::dispatchCertificateIssued($certificate);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for certificate {$certificate->certificate_no}: " . $e->getMessage());
            }

            return $certificate;
        });

        return redirect()->route('centre.downloads.student-certificates.show', $certificate->id)
            ->with('success', "Certificate #{$certificate->certificate_no} and Marksheet generated successfully for {$student->full_name}.");
    }

    public function showStudentCertificate(Certificate $certificate)
    {
        $centre = $this->getAuthCentre();
        if ($certificate->centre_id !== $centre->id) {
            abort(403, 'Unauthorized access to student certificate.');
        }

        $certificate->load(['student.user', 'course', 'centre', 'examResult.marksheet']);
        return view('centre.downloads.student_certificate_show', compact('centre', 'certificate'));
    }

    public function downloadFile(Download $download)
    {
        if (!$download->status) {
            abort(404, 'File is currently unavailable.');
        }

        $download->increment('downloads_count');

        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            return Storage::disk('public')->download($download->file_path, $download->title . '.' . pathinfo($download->file_path, PATHINFO_EXTENSION));
        }

        return back()->with('info', "Download for '{$download->title}' registered. Physical document package available at Central Directorate.");
    }
}
