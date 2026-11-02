<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Marksheet;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CentreExamController extends Controller
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
        $query = Exam::with('course')->withCount('results');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('exam_title', 'like', "%{$s}%")
                    ->orWhere('exam_code', 'like', "%{$s}%")
                    ->orWhere('session_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->orderBy('exam_date', 'desc')->paginate(15)->withQueryString();

        return view('centre.exams.index', compact('centre', 'exams'));
    }

    public function hallTicketsIndex(Request $request)
    {
        $centre = $this->getAuthCentre();

        $exams = Exam::with('course')->orderBy('exam_date', 'desc')->get();

        $selectedExam = null;
        if ($request->filled('exam_id')) {
            $selectedExam = Exam::with(['course.subjects'])->find($request->exam_id);
        } elseif ($exams->isNotEmpty()) {
            $selectedExam = $exams->first();
            $selectedExam->load(['course.subjects']);
        }

        $students = collect();
        if ($selectedExam) {
            $studentQuery = Student::where('centre_id', $centre->id)
                ->whereHas('admissions', function ($q) use ($selectedExam) {
                    $q->where('course_id', $selectedExam->course_id)
                      ->where('status', 'active');
                })
                ->with(['admissions' => function ($q) use ($selectedExam) {
                    $q->where('course_id', $selectedExam->course_id);
                }, 'admissions.batch']);

            if ($request->filled('search')) {
                $s = trim($request->search);
                $studentQuery->where(function ($q) use ($s) {
                    $q->where('full_name', 'like', "%{$s}%")
                      ->orWhere('roll_no', 'like', "%{$s}%")
                      ->orWhere('registration_no', 'like', "%{$s}%");
                });
            }

            $students = $studentQuery->orderBy('full_name')->get();
        }

        $stats = [
            'total_exams' => $exams->count(),
            'eligible_candidates' => $students->count(),
        ];

        return view('centre.exams.hall_tickets_index', compact('centre', 'exams', 'selectedExam', 'students', 'stats'));
    }

    public function hallTickets(Exam $exam)
    {
        $centre = $this->getAuthCentre();
        $exam->load('course.subjects');

        // Fetch students from this centre enrolled in the exam's course
        $students = Student::where('centre_id', $centre->id)
            ->whereHas('admissions', function ($q) use ($exam) {
                $q->where('course_id', $exam->course_id)
                  ->where('status', 'active');
            })
            ->with(['admissions' => function ($q) use ($exam) {
                $q->where('course_id', $exam->course_id);
            }, 'admissions.batch'])
            ->orderBy('full_name')
            ->get();

        return view('centre.exams.hall_tickets', compact('centre', 'exam', 'students'));
    }

    public function singleHallTicket(Exam $exam, Student $student)
    {
        $centre = $this->getAuthCentre();
        if ($student->centre_id !== $centre->id) {
            abort(403, 'Unauthorized access to student hall ticket.');
        }

        $exam->load('course.subjects');
        $student->load(['admissions' => function ($q) use ($exam) {
            $q->where('course_id', $exam->course_id);
        }, 'admissions.batch']);

        $students = collect([$student]);
        return view('centre.exams.hall_tickets', compact('centre', 'exam', 'students'));
    }

    public function marksIndex(Request $request)
    {
        $centre = $this->getAuthCentre();

        if ($request->filled('exam_id')) {
            $exam = Exam::findOrFail($request->exam_id);
            return $this->marksForm($request, $exam);
        }

        $exams = Exam::with(['course'])
            ->withCount(['results' => function ($q) use ($centre) {
                $q->where('centre_id', $centre->id);
            }])
            ->orderBy('exam_date', 'desc')
            ->get();

        foreach ($exams as $ex) {
            $ex->centre_candidates_count = Student::where('centre_id', $centre->id)
                ->whereHas('admissions', function ($q) use ($ex) {
                    $q->where('course_id', $ex->course_id);
                })
                ->count();
        }

        return view('centre.exams.marks_index', compact('centre', 'exams'));
    }

    public function marksForm(Request $request, Exam $exam)
    {
        $centre = $this->getAuthCentre();
        $exam->load(['course.subjects']);

        // Eligible students at this centre enrolled in this exam course
        $students = Student::where('centre_id', $centre->id)
            ->whereHas('admissions', function ($q) use ($exam) {
                $q->where('course_id', $exam->course_id);
            })
            ->with(['results' => function ($q) use ($exam) {
                $q->where('exam_id', $exam->id);
            }])
            ->orderBy('full_name')
            ->get();

        $selectedStudent = null;
        $existingResult = null;
        $existingSubjects = collect();

        if ($request->filled('student_id')) {
            $selectedStudent = Student::where('centre_id', $centre->id)->findOrFail($request->student_id);
            $existingResult = ExamResult::with('subjectResults')
                ->where('exam_id', $exam->id)
                ->where('student_id', $selectedStudent->id)
                ->first();
            if ($existingResult) {
                $existingSubjects = $existingResult->subjectResults->keyBy('course_subject_id');
            }
        }

        return view('centre.exams.marks', compact('centre', 'exam', 'students', 'selectedStudent', 'existingResult', 'existingSubjects'));
    }

    public function storeMarks(Request $request, Exam $exam)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subjects' => 'required|array|min:1',
            'subjects.*.id' => 'required|exists:course_subjects,id',
            'subjects.*.theory_obtained' => 'nullable|numeric|min:0',
            'subjects.*.practical_obtained' => 'nullable|numeric|min:0',
            'subjects.*.viva_obtained' => 'nullable|numeric|min:0',
        ]);

        $student = Student::where('centre_id', $centre->id)->findOrFail($validated['student_id']);

        // Check if student is admitted to the exam's course
        $isEnrolled = $student->admissions()->where('course_id', $exam->course_id)->exists();
        if (!$isEnrolled) {
            return back()->withInput()->with('error', "Candidate [{$student->full_name}] is not enrolled in the course associated with this examination.");
        }

        $course = Course::with('subjects')->findOrFail($exam->course_id);

        // Pre-validate subject ceiling limits
        foreach ($validated['subjects'] as $subInput) {
            $sub = CourseSubject::where('course_id', $exam->course_id)->find($subInput['id']);
            if (!$sub) {
                return back()->withInput()->with('error', "Invalid subject selection for this course curriculum.");
            }
            $th = (float) ($subInput['theory_obtained'] ?? 0);
            $pr = (float) ($subInput['practical_obtained'] ?? 0);
            $va = (float) ($subInput['viva_obtained'] ?? 0);

            if ($sub->theory_max > 0 && $th > (float)$sub->theory_max) {
                return back()->withInput()->with('error', "Theory marks for '{$sub->subject_name}' cannot exceed maximum of {$sub->theory_max}.");
            }
            if ($sub->practical_max > 0 && $pr > (float)$sub->practical_max) {
                return back()->withInput()->with('error', "Practical marks for '{$sub->subject_name}' cannot exceed maximum of {$sub->practical_max}.");
            }
            if ($sub->viva_max > 0 && $va > (float)$sub->viva_max) {
                return back()->withInput()->with('error', "Viva marks for '{$sub->subject_name}' cannot exceed maximum of {$sub->viva_max}.");
            }
            if (($th + $pr + $va) > (float)$sub->total_max) {
                return back()->withInput()->with('error', "Total obtained marks for '{$sub->subject_name}' cannot exceed subject total of {$sub->total_max}.");
            }
        }

        $result = DB::transaction(function () use ($validated, $student, $exam, $course, $centre) {
            $totalObtained = 0.0;
            $totalMax = 0.0;
            $allPassed = true;
            $subjectEntries = [];

            foreach ($validated['subjects'] as $subInput) {
                $sub = CourseSubject::where('course_id', $exam->course_id)->findOrFail($subInput['id']);
                $th = (float) ($subInput['theory_obtained'] ?? 0);
                $pr = (float) ($subInput['practical_obtained'] ?? 0);
                $va = (float) ($subInput['viva_obtained'] ?? 0);
                $subTot = $th + $pr + $va;
                $maxMarks = (float) ($sub->total_max ?: 100);
                $passMarks = (float) ($sub->min_pass_marks ?: ($maxMarks * 0.40));

                $passed = $subTot >= $passMarks;
                if (!$passed) {
                    $allPassed = false;
                }

                $totalObtained += $subTot;
                $totalMax += $maxMarks;

                $subPct = $maxMarks > 0 ? ($subTot / $maxMarks) * 100 : 0;
                $subGrade = ExamResult::calculateGrade($subPct);

                $subjectEntries[] = [
                    'course_subject_id'  => $sub->id,
                    'theory_obtained'    => $th,
                    'practical_obtained' => $pr,
                    'viva_obtained'      => $va,
                    'total_obtained'     => $subTot,
                    'max_marks'          => $maxMarks,
                    'grade'              => $subGrade,
                    'is_passed'          => $passed,
                ];
            }

            $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
            $grade = ExamResult::calculateGrade($percentage);
            $resultStatus = ($allPassed && $percentage >= 40) ? 'passed' : 'failed';

            $isNew = !ExamResult::where('exam_id', $exam->id)->where('student_id', $student->id)->exists();

            $result = ExamResult::updateOrCreate(
                [
                    'exam_id'    => $exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'centre_id'            => $centre->id,
                    'course_id'            => $course->id,
                    'total_marks_obtained' => $totalObtained,
                    'total_max_marks'      => $totalMax,
                    'percentage'           => $percentage,
                    'grade'                => $grade,
                    'result_status'        => $resultStatus,
                    'approved_by'          => Auth::id(),
                    'approved_at'          => now(),
                ]
            );

            // Replace subject items
            $result->subjectResults()->delete();
            foreach ($subjectEntries as $entry) {
                $entry['exam_result_id'] = $result->id;
                ExamResultSubject::create($entry);
            }

            // Auto-generate or update Marksheet & Certificate upon passing
            if ($resultStatus === 'passed') {
                $year = date('Y');

                // 1. Marksheet
                $existingMarksheet = Marksheet::where('exam_result_id', $result->id)->first();
                if (!$existingMarksheet) {
                    $lastMsId = Marksheet::max('id') + 1;
                    $msNo = sprintf('DCE/MS/%s/%04d', $year, $lastMsId);
                    $msHash = hash('sha256', "{$msNo}-{$student->roll_no}-" . config('app.key'));

                    Marksheet::create([
                        'exam_result_id' => $result->id,
                        'student_id'     => $student->id,
                        'centre_id'      => $centre->id,
                        'course_id'      => $course->id,
                        'marksheet_no'   => $msNo,
                        'issue_date'     => now()->toDateString(),
                        'security_hash'  => $msHash,
                        'is_issued'      => true,
                    ]);
                }

                // 2. Certificate
                $existingCert = Certificate::where('exam_result_id', $result->id)->first();
                if (!$existingCert) {
                    $lastId = Certificate::max('id') + 1;
                    $certNo = sprintf('DCE/CERT/%s/%04d', $year, $lastId);
                    $certHash = hash('sha256', "{$certNo}-{$student->roll_no}-" . config('app.key'));
                    $qrUrl = url('/verify/cert/' . urlencode($certNo));

                    $certificate = Certificate::create([
                        'exam_result_id' => $result->id,
                        'student_id'     => $student->id,
                        'centre_id'      => $centre->id,
                        'course_id'      => $course->id,
                        'certificate_no' => $certNo,
                        'issue_date'     => now()->toDateString(),
                        'security_hash'  => $certHash,
                        'qr_code_url'    => $qrUrl,
                        'status'         => 'issued',
                    ]);

                    try {
                        \App\Services\NotificationDispatchService::dispatchCertificateIssued($certificate);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for certificate: " . $e->getMessage());
                    }
                }
            }

            AuditLog::record($isNew ? 'centre_marks_submitted' : 'centre_marks_updated', $result, null, [
                'exam_id'    => $exam->id,
                'student_id' => $student->id,
                'percentage' => $percentage,
                'status'     => $resultStatus,
            ]);

            return $result;
        });

        return redirect()->route('centre.exams.results')
            ->with('success', "Exam evaluation saved successfully for {$student->full_name}. Result: " . strtoupper($result->result_status) . " ({$result->percentage}%, Grade {$result->grade}).");
    }

    public function results(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = ExamResult::where('centre_id', $centre->id)->with(['exam', 'student', 'course', 'subjectResults.subject']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->whereHas('student', function ($sq) use ($s) {
                $sq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('result_status')) {
            $query->where('result_status', $request->result_status);
        }

        $results = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => ExamResult::where('centre_id', $centre->id)->count(),
            'passed' => ExamResult::where('centre_id', $centre->id)->where('result_status', 'passed')->count(),
            'failed' => ExamResult::where('centre_id', $centre->id)->where('result_status', 'failed')->count(),
        ];

        $exams = Exam::orderBy('exam_date', 'desc')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('centre.exams.results', compact('centre', 'results', 'stats', 'exams', 'courses'));
    }

    public function marksheet(ExamResult $result)
    {
        $centre = $this->getAuthCentre();
        if ($result->centre_id !== $centre->id) {
            abort(403, 'Unauthorized access to student marksheet.');
        }

        $result->load([
            'student.user',
            'course',
            'exam',
            'centre',
            'subjectResults.subject',
            'marksheet',
        ]);

        return view('centre.exams.marksheet', compact('centre', 'result'));
    }
}
