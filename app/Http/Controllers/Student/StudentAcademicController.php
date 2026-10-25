<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdmitCard;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentAcademicController extends Controller
{
    use HasStudentContext;

    /**
     * Display accessible study materials for the enrolled student.
     */
    public function studyMaterials(Request $request)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $query = StudyMaterial::query()->where('status', true);

        // Course filter: materials matching the student's active course or shared/global materials
        if ($admission && $admission->course_id) {
            $query->where(function ($q) use ($admission) {
                $q->where('course_id', $admission->course_id)
                  ->orWhereNull('course_id');
            });
        }

        // Centre filter: materials matching student's centre or global materials
        if ($student->centre_id) {
            $query->where(function ($q) use ($student) {
                $q->where('centre_id', $student->centre_id)
                  ->orWhereNull('centre_id');
            });
        }

        if ($request->filled('type')) {
            $query->where('material_type', $request->query('type'));
        }

        if ($request->filled('search')) {
            $term = '%' . trim($request->query('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        $materials = $query->with(['course', 'subject'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('student.academic.materials', compact('student', 'admission', 'materials'));
    }

    /**
     * Download or redirect to a study material with IDOR protection.
     */
    public function downloadMaterial(Request $request, $id)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $material = StudyMaterial::findOrFail($id);

        if (!$material->status) {
            abort(404, 'Material is not currently available.');
        }

        // Enforce authorization
        if ($material->course_id && (!$admission || $material->course_id !== $admission->course_id)) {
            abort(403, 'Unauthorized access to this course material.');
        }

        if ($material->centre_id && $material->centre_id !== $student->centre_id) {
            abort(403, 'Unauthorized access to this centre material.');
        }

        if ($material->external_url && empty($material->file_path)) {
            return redirect()->away($material->external_url);
        }

        if ($material->file_path) {
            if (Storage::disk('public')->exists($material->file_path)) {
                return Storage::disk('public')->download(
                    $material->file_path,
                    basename($material->file_path)
                );
            }

            if (file_exists(public_path($material->file_path))) {
                return response()->download(public_path($material->file_path));
            }

            if (file_exists(storage_path('app/public/' . $material->file_path))) {
                return response()->download(storage_path('app/public/' . $material->file_path));
            }

            if (file_exists(public_path('storage/' . $material->file_path))) {
                return response()->download(public_path('storage/' . $material->file_path));
            }
        }

        return back()->with('error', 'The requested file could not be located on the server.');
    }

    /**
     * Display examination schedule, admit cards, and upcoming exams.
     */
    public function exams(Request $request)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $courseId = $admission?->course_id;

        $examsQuery = Exam::query();
        if ($courseId) {
            $examsQuery->where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)
                  ->orWhereNull('course_id');
            });
        } else {
            $examsQuery->whereNull('course_id');
        }

        $exams = $examsQuery->with('course')
            ->orderBy('exam_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $admitCards = AdmitCard::where('student_id', $student->id)
            ->pluck('id', 'exam_id')
            ->toArray();

        $results = ExamResult::where('student_id', $student->id)
            ->pluck('id', 'exam_id')
            ->toArray();

        return view('student.academic.exams', compact('student', 'admission', 'exams', 'admitCards', 'results'));
    }

    /**
     * Display/print admit card for an examination.
     */
    public function admitCard(Request $request, $exam_id)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $exam = Exam::with('course')->findOrFail($exam_id);

        // Security check: must belong to student's course or be universal
        if ($exam->course_id && (!$admission || $exam->course_id !== $admission->course_id)) {
            abort(403, 'You are not enrolled in the course for this examination.');
        }

        $admitCard = AdmitCard::where('student_id', $student->id)
            ->where('exam_id', $exam_id)
            ->with(['centre', 'student.user'])
            ->first();

        return view('student.academic.admit_card', compact('student', 'admission', 'exam', 'admitCard'));
    }

    /**
     * Display exam results list.
     */
    public function results(Request $request)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $results = ExamResult::where('student_id', $student->id)
            ->where('result_status', '!=', 'withheld')
            ->where(function ($q) {
                $q->whereNull('exam_id')
                  ->orWhereHas('exam', function ($eq) {
                      $eq->whereIn('status', ['published', 'evaluated']);
                  });
            })
            ->with(['exam', 'course', 'centre'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student.academic.results', compact('student', 'admission', 'results'));
    }

    /**
     * Display or print detailed marksheet.
     */
    public function marksheet(Request $request, $id)
    {
        $student = $this->getStudent($request);

        $result = ExamResult::where('id', $id)
            ->where('student_id', $student->id)
            ->with(['exam', 'course', 'centre', 'student.user', 'subjectResults.subject', 'marksheet'])
            ->firstOrFail();

        if ($result->result_status === 'withheld') {
            abort(403, 'Your examination result has been withheld. Please contact your study centre administration.');
        }

        if ($result->exam && !in_array($result->exam->status, ['published', 'evaluated'])) {
            abort(403, 'Examination results are awaiting official publication by the central examination board.');
        }

        return view('student.academic.marksheet', compact('student', 'result'));
    }

    /**
     * Display issued certificates.
     */
    public function certificates(Request $request)
    {
        $student = $this->getStudent($request);
        $admission = $this->getActiveAdmission($student);

        $certificates = Certificate::where('student_id', $student->id)
            ->with(['course', 'centre', 'result'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student.academic.certificates', compact('student', 'admission', 'certificates'));
    }
}
