<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Marksheet;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['student.centre', 'course', 'result']);

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

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('issue_date')->paginate(15)->withQueryString();

        $stats = [
            'total_certificates' => Certificate::count(),
            'issued_count'       => Certificate::where('status', 'issued')->count(),
            'revoked_count'      => Certificate::where('status', 'revoked')->count(),
            'total_marksheets'   => Marksheet::count(),
            'pending_results'    => ExamResult::where('result_status', 'passed')->doesntHave('certificate')->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.certificates.index', compact('certificates', 'stats', 'courses', 'centres'));
    }

    public function create(Request $request)
    {
        // 1. Eligible passed exam results without certificate yet
        $eligibleResults = ExamResult::with(['student.centre', 'course'])
            ->where('result_status', 'passed')
            ->doesntHave('certificate')
            ->latest()
            ->get();

        // 2. Enrolled students who don't have a certificate yet (for direct certification)
        $eligibleStudents = Student::whereDoesntHave('certificates')
            ->with(['centre', 'admissions.course.subjects'])
            ->orderBy('full_name')
            ->get();

        $selectedResult = null;
        if ($request->filled('exam_result_id')) {
            $selectedResult = ExamResult::with(['student.centre', 'course', 'subjectResults.subject'])->find($request->exam_result_id);
        }

        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::with(['centre', 'admissions.course.subjects'])->find($request->student_id);
        }

        $courses = Course::where('status', true)->with('subjects')->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.certificates.create', compact('eligibleResults', 'eligibleStudents', 'selectedResult', 'selectedStudent', 'courses', 'centres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_result_id'  => 'nullable|exists:exam_results,id|unique:certificates,exam_result_id',
            'student_id'      => 'required_without:exam_result_id|nullable|exists:students,id',
            'course_id'       => 'nullable|exists:courses,id',
            'percentage'      => 'nullable|numeric|min:35|max:100',
            'grade'           => 'nullable|string|max:10',
            'certificate_no'  => 'nullable|string|max:60|unique:certificates,certificate_no',
            'marksheet_no'    => 'nullable|string|max:60|unique:marksheets,marksheet_no',
            'issue_date'      => 'required|date',
        ]);

        $year = date('Y', strtotime($validated['issue_date']));

        if (!empty($validated['exam_result_id'])) {
            $result = ExamResult::with(['student', 'course', 'centre'])->findOrFail($validated['exam_result_id']);
            $student = $result->student;
            $course = $result->course;
            $centre = $result->centre;
        } else {
            // Direct candidate certificate issuance
            $student = Student::with(['centre', 'admissions.course.subjects'])->findOrFail($validated['student_id']);
            $admission = $student->admissions()->latest()->first();
            $courseId = !empty($validated['course_id']) ? $validated['course_id'] : ($admission ? $admission->course_id : null);

            if (!$courseId) {
                return back()->withInput()->with('error', 'The selected candidate has no active course enrollment. Please assign a course first.');
            }

            $course = Course::with('subjects')->findOrFail($courseId);
            $centre = $student->centre ?: Centre::first();

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

        $certificate = DB::transaction(function () use ($result, $student, $validated) {
            $certHash = hash('sha256', "{$validated['certificate_no']}-{$student->roll_no}-" . config('app.key'));
            $msHash = hash('sha256', "{$validated['marksheet_no']}-{$student->roll_no}-" . config('app.key'));

            // Create Marksheet
            Marksheet::create([
                'exam_result_id' => $result->id,
                'student_id'     => $result->student_id,
                'centre_id'      => $result->centre_id,
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
                'centre_id'      => $result->centre_id,
                'course_id'      => $result->course_id,
                'certificate_no' => $validated['certificate_no'],
                'issue_date'     => $validated['issue_date'],
                'security_hash'  => $certHash,
                'status'         => 'issued',
            ]);

            AuditLog::record(
                'certificate_issued',
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

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', "Certificate #{$certificate->certificate_no} and Marksheet generated successfully.");
    }

    public function show(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result.subjectResults.subject',
        ]);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function print(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result',
        ]);

        return view('admin.certificates.print', compact('certificate'));
    }

    public function marksheet(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result.subjectResults.subject',
        ]);

        $marksheet = Marksheet::where('exam_result_id', $certificate->exam_result_id)->first();

        return view('admin.certificates.marksheet', compact('certificate', 'marksheet'));
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $oldStatus = $certificate->status;
        $certificate->status = 'revoked';
        $certificate->save();

        AuditLog::record(
            'certificate_revoked',
            $certificate,
            ['status' => $oldStatus],
            ['status' => 'revoked', 'reason' => $request->reason ?? 'Administrative order']
        );

        return back()->with('success', "Certificate #{$certificate->certificate_no} has been revoked.");
    }

    public function reissue(Certificate $certificate)
    {
        $oldStatus = $certificate->status;
        $certificate->status = 'issued';
        $certificate->save();

        AuditLog::record(
            'certificate_reissued',
            $certificate,
            ['status' => $oldStatus],
            ['status' => 'issued']
        );

        return back()->with('success', "Certificate #{$certificate->certificate_no} has been reinstated to active status.");
    }
}
