<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with(['centre', 'course'])->withCount(['admissions', 'attendances']);

        if ($search = $request->input('search')) {
            $query->where('batch_name', 'like', "%{$search}%");
        }

        if ($centreId = $request->input('centre_id')) {
            $query->where('centre_id', $centreId);
        }

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $batches = $query->latest()->paginate(15)->withQueryString();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        $counts = [
            'total' => Batch::count(),
            'active' => Batch::where('status', 'active')->count(),
            'completed' => Batch::where('status', 'completed')->count(),
            'total_attendances' => Attendance::count(),
        ];

        return view('admin.batches.index', compact('batches', 'centres', 'courses', 'counts'));
    }

    public function create()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        return view('admin.batches.create', compact('centres', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $batch = Batch::create($validated);

        AuditLog::record('batch_created', $batch, null, [
            'batch_name' => $batch->batch_name,
            'centre_id' => $batch->centre_id,
            'course_id' => $batch->course_id,
        ]);

        return redirect()->route('admin.batches.show', $batch->id)
            ->with('success', "Batch [{$batch->batch_name}] created successfully.");
    }

    public function show(Batch $batch)
    {
        $batch->load(['centre', 'course', 'admissions.student']);
        $recentAttendances = Attendance::with('student')
            ->where('batch_id', $batch->id)
            ->latest('attendance_date')
            ->take(30)
            ->get();

        return view('admin.batches.show', compact('batch', 'recentAttendances'));
    }

    public function edit(Batch $batch)
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        return view('admin.batches.edit', compact('batch', 'centres', 'courses'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $oldValues = $batch->toArray();
        $batch->update($validated);

        AuditLog::record('batch_updated', $batch, $oldValues, $batch->toArray());

        return redirect()->route('admin.batches.show', $batch->id)
            ->with('success', "Batch [{$batch->batch_name}] updated successfully.");
    }

    public function destroy(Batch $batch)
    {
        $name = $batch->batch_name;
        $batch->delete(); // Soft delete

        AuditLog::record('batch_deleted', $batch, ['status' => 'deleted']);

        return redirect()->route('admin.batches.index')
            ->with('success', "Batch [{$name}] soft-deleted successfully.");
    }

    public function enrollStudents(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $enrolledCount = 0;
        DB::transaction(function () use ($batch, $validated, &$enrolledCount) {
            foreach ($validated['student_ids'] as $studentId) {
                $admission = Admission::where('student_id', $studentId)
                    ->where('centre_id', $batch->centre_id)
                    ->latest('id')
                    ->first();

                if ($admission) {
                    $admission->update(['batch_id' => $batch->id]);
                    $enrolledCount++;
                }
            }

            AuditLog::record('admin_batch_students_enrolled', $batch, null, [
                'batch_id'       => $batch->id,
                'enrolled_count' => $enrolledCount,
                'student_ids'    => $validated['student_ids'],
            ]);
        });

        return back()->with('success', "Successfully enrolled {$enrolledCount} student(s) into batch [{$batch->batch_name}].");
    }

    public function unenrollStudent(Batch $batch, \App\Models\Student $student)
    {
        Admission::where('student_id', $student->id)
            ->where('batch_id', $batch->id)
            ->update(['batch_id' => null]);

        AuditLog::record('admin_batch_student_unenrolled', $batch, null, [
            'batch_id'   => $batch->id,
            'student_id' => $student->id,
        ]);

        return back()->with('success', "Student [{$student->full_name}] un-enrolled from batch.");
    }

    public function attendanceSheet(Request $request, Batch $batch)
    {
        $batch->load(['centre', 'course', 'admissions.student']);
        $attendanceDate = $request->input('date', date('Y-m-d'));

        // Load existing attendance for this batch on this date
        $existingRecords = Attendance::where('batch_id', $batch->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        $enrolledIds = $batch->admissions->pluck('student_id')->toArray();
        $availableStudents = \App\Models\Student::where('centre_id', $batch->centre_id)
            ->whereIn('status', ['enrolled', 'ongoing'])
            ->whereNotIn('id', $enrolledIds)
            ->with(['admissions.course', 'admissions.batch'])
            ->orderBy('full_name')
            ->get();

        return view('admin.batches.attendance_sheet', compact('batch', 'attendanceDate', 'existingRecords', 'availableStudents'));
    }

    public function markAttendance(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,leave,holiday',
            'remarks' => 'nullable|array',
        ]);

        $attendanceDate = $validated['attendance_date'];
        $attendanceData = $validated['attendance'];
        $remarksData = $validated['remarks'] ?? [];

        DB::transaction(function () use ($batch, $attendanceDate, $attendanceData, $remarksData) {
            foreach ($attendanceData as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'batch_id' => $batch->id,
                        'student_id' => $studentId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'centre_id' => $batch->centre_id,
                        'status' => $status,
                        'remarks' => $remarksData[$studentId] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            AuditLog::record('attendance_marked', $batch, null, [
                'batch_id' => $batch->id,
                'attendance_date' => $attendanceDate,
                'students_count' => count($attendanceData),
            ]);
        });

        return redirect()->route('admin.batches.attendance.sheet', ['batch' => $batch->id, 'date' => $attendanceDate])
            ->with('success', "Attendance marked successfully for " . count($attendanceData) . " students on {$attendanceDate}.");
    }

    public function attendanceReport(Request $request)
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $batches = Batch::where('status', 'active')->orderBy('batch_name')->get();

        $query = Attendance::with(['student', 'batch', 'centre']);

        if ($centreId = $request->input('centre_id')) {
            $query->where('centre_id', $centreId);
        }

        if ($batchId = $request->input('batch_id')) {
            $query->where('batch_id', $batchId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('attendance_date', '>=', $from);
        }

        if ($to = $request->input('to_date')) {
            $query->whereDate('attendance_date', '<=', $to);
        }

        $attendances = $query->latest('attendance_date')->paginate(20)->withQueryString();

        $summary = [
            'total' => Attendance::count(),
            'present' => Attendance::where('status', 'present')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'late' => Attendance::where('status', 'late')->count(),
            'leave' => Attendance::where('status', 'leave')->count(),
        ];

        return view('admin.batches.attendance_report', compact('attendances', 'centres', 'batches', 'summary'));
    }
}
