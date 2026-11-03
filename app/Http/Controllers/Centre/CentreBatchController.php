<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CentreBatchController extends Controller
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
        $query = Batch::where('centre_id', $centre->id)->with(['course'])->withCount(['admissions', 'attendances']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('batch_name', 'like', "%{$s}%");
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->latest('id')->paginate(15)->withQueryString();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        $stats = [
            'total' => Batch::where('centre_id', $centre->id)->count(),
            'active' => Batch::where('centre_id', $centre->id)->where('status', 'active')->count(),
            'completed' => Batch::where('centre_id', $centre->id)->where('status', 'completed')->count(),
        ];

        return view('centre.batches.index', compact('centre', 'batches', 'courses', 'stats'));
    }

    public function create()
    {
        $centre = $this->getAuthCentre();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('centre.batches.create', compact('centre', 'courses'));
    }

    public function store(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $validated['centre_id'] = $centre->id;

        $batch = Batch::create($validated);

        AuditLog::record('centre_batch_created', $batch, null, [
            'batch_name' => $batch->batch_name,
            'centre_id' => $centre->id,
            'course_id' => $batch->course_id,
        ]);

        return redirect()->route('centre.batches.index')
            ->with('success', "Batch [{$batch->batch_name}] created successfully.");
    }

    public function edit(Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('centre.batches.edit', compact('centre', 'batch', 'courses'));
    }

    public function update(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $oldValues = $batch->toArray();
        $batch->update($validated);

        AuditLog::record('centre_batch_updated', $batch, $oldValues, $batch->toArray());

        return redirect()->route('centre.batches.index')
            ->with('success', "Batch [{$batch->batch_name}] updated successfully.");
    }

    /**
     * Dedicated Daily Attendance Hub for Franchise
     */
    public function dailyAttendanceHub(Request $request)
    {
        $centre = $this->getAuthCentre();
        $attendanceDate = $request->input('date', date('Y-m-d'));

        $batches = Batch::where('centre_id', $centre->id)
            ->with(['course', 'admissions.student'])
            ->withCount(['admissions', 'attendances'])
            ->orderBy('batch_name')
            ->get();

        // Check attendance counts for this date
        $attendancesByBatch = Attendance::where('centre_id', $centre->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->groupBy('batch_id');

        // All active centre students for batch enrollment modal
        $allStudents = Student::where('centre_id', $centre->id)
            ->whereIn('status', ['enrolled', 'ongoing'])
            ->with(['admissions.course', 'admissions.batch'])
            ->orderBy('full_name')
            ->get();

        $stats = [
            'total_batches'       => $batches->count(),
            'total_students'      => Student::where('centre_id', $centre->id)->count(),
            'enrolled_in_batches' => Admission::where('centre_id', $centre->id)->whereNotNull('batch_id')->count(),
            'marked_today'        => Attendance::where('centre_id', $centre->id)->whereDate('attendance_date', $attendanceDate)->count(),
        ];

        return view('centre.batches.daily_hub', compact('centre', 'batches', 'attendanceDate', 'attendancesByBatch', 'allStudents', 'stats'));
    }

    /**
     * Enroll students into this batch
     */
    public function enrollStudents(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $validated = $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $enrolledCount = 0;
        DB::transaction(function () use ($batch, $centre, $validated, &$enrolledCount) {
            foreach ($validated['student_ids'] as $studentId) {
                // Find student admission under this centre
                $admission = Admission::where('student_id', $studentId)
                    ->where('centre_id', $centre->id)
                    ->latest('id')
                    ->first();

                if ($admission) {
                    $admission->update(['batch_id' => $batch->id]);
                    $enrolledCount++;
                }
            }

            AuditLog::record('centre_batch_students_enrolled', $batch, null, [
                'batch_id'       => $batch->id,
                'centre_id'      => $centre->id,
                'enrolled_count' => $enrolledCount,
                'student_ids'    => $validated['student_ids'],
            ]);
        });

        return back()->with('success', "Successfully enrolled {$enrolledCount} student(s) into batch [{$batch->batch_name}]! You can now mark their daily attendance.");
    }

    /**
     * Unenroll a single student from this batch
     */
    public function unenrollStudent(Batch $batch, Student $student)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        Admission::where('student_id', $student->id)
            ->where('centre_id', $centre->id)
            ->where('batch_id', $batch->id)
            ->update(['batch_id' => null]);

        AuditLog::record('centre_batch_student_unenrolled', $batch, null, [
            'batch_id'   => $batch->id,
            'student_id' => $student->id,
        ]);

        return back()->with('success', "Student [{$student->full_name}] un-enrolled from batch [{$batch->batch_name}].");
    }

    public function attendanceSheet(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $batch->load(['course', 'admissions.student']);
        $attendanceDate = $request->input('date', date('Y-m-d'));

        // Load existing records for this batch on date
        $existingRecords = Attendance::where('batch_id', $batch->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        // All centre students who can be enrolled into this batch
        $enrolledIds = $batch->admissions->pluck('student_id')->toArray();
        $availableStudents = Student::where('centre_id', $centre->id)
            ->whereIn('status', ['enrolled', 'ongoing'])
            ->whereNotIn('id', $enrolledIds)
            ->with(['admissions.course', 'admissions.batch'])
            ->orderBy('full_name')
            ->get();

        return view('centre.batches.attendance', compact('centre', 'batch', 'attendanceDate', 'existingRecords', 'availableStudents'));
    }

    public function markAttendance(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,leave,holiday',
            'remarks' => 'nullable|array',
        ]);

        $attendanceDate = $validated['attendance_date'];
        $attendanceData = $validated['attendance'];
        $remarksData = $validated['remarks'] ?? [];

        DB::transaction(function () use ($batch, $centre, $attendanceDate, $attendanceData, $remarksData) {
            foreach ($attendanceData as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'batch_id' => $batch->id,
                        'student_id' => $studentId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'centre_id' => $centre->id,
                        'status' => $status,
                        'remarks' => $remarksData[$studentId] ?? null,
                    ]
                );
            }

            AuditLog::record('centre_attendance_marked', $batch, null, [
                'batch_id' => $batch->id,
                'centre_id' => $centre->id,
                'date' => $attendanceDate,
                'student_count' => count($attendanceData),
            ]);
        });

        return redirect()->route('centre.batches.attendance', [$batch->id, 'date' => $attendanceDate])
            ->with('success', "Attendance marked successfully for " . count($attendanceData) . " students on {$attendanceDate}.");
    }

    public function attendanceReport(Request $request)
    {
        $centre = $this->getAuthCentre();
        $batches = Batch::where('centre_id', $centre->id)->orderBy('batch_name')->get();

        $selectedBatchId = $request->input('batch_id');
        $month = $request->input('month', date('Y-m'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');
        $viewMode = $request->input('view', 'daily_log'); // 'daily_log', 'summary', 'matrix'

        // Determine date range
        if ($dateFrom && $dateTo) {
            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateTo)->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
            $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth();
            $dateFrom = $startDate->toDateString();
            $dateTo = min(now()->toDateString(), $endDate->toDateString());
        }

        // 1. Query raw attendance logs for date-wise ledger
        $logQuery = Attendance::where('centre_id', $centre->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['student', 'batch']);

        if ($selectedBatchId) {
            $logQuery->where('batch_id', $selectedBatchId);
        }

        if ($status && in_array($status, ['present', 'absent', 'late', 'excused', 'leave'])) {
            $logQuery->where('status', $status);
        }

        $attendanceLogs = (clone $logQuery)
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        // 2. Summary stats for the queried date range
        $totalSessions = (clone $logQuery)->count();
        $presentCount = (clone $logQuery)->where('status', 'present')->count();
        $absentCount = (clone $logQuery)->where('status', 'absent')->count();
        $lateCount = (clone $logQuery)->whereIn('status', ['late', 'excused', 'leave'])->count();
        $overallRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 0;

        // 3. Batch-wise student regularities & monthly matrix
        $batchReport = null;
        if ($selectedBatchId) {
            $batch = Batch::where('centre_id', $centre->id)->find($selectedBatchId);
            if ($batch) {
                $students = Student::whereHas('admissions', function ($q) use ($selectedBatchId) {
                    $q->where('batch_id', $selectedBatchId);
                })->orderBy('full_name')->get();

                $attendances = Attendance::where('batch_id', $selectedBatchId)
                    ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->get()
                    ->groupBy('student_id');

                $batchReport = [
                    'batch'       => $batch,
                    'students'    => $students,
                    'attendances' => $attendances,
                ];
            }
        }

        // Days list for matrix grid view
        $daysInPeriod = [];
        $periodCursor = $startDate->copy();
        $limitDate = min($endDate->copy(), now());
        while ($periodCursor->lte($limitDate)) {
            $daysInPeriod[] = $periodCursor->copy();
            $periodCursor->addDay();
        }

        return view('centre.batches.attendance_report', compact(
            'centre',
            'batches',
            'selectedBatchId',
            'month',
            'dateFrom',
            'dateTo',
            'status',
            'viewMode',
            'attendanceLogs',
            'totalSessions',
            'presentCount',
            'absentCount',
            'lateCount',
            'overallRate',
            'batchReport',
            'daysInPeriod'
        ));
    }

    /**
     * Export date-wise attendance report as CSV spreadsheet.
     */
    public function exportAttendanceReport(Request $request)
    {
        $centre = $this->getAuthCentre();
        $month = $request->input('month', date('Y-m'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');
        $selectedBatchId = $request->input('batch_id');

        if ($dateFrom && $dateTo) {
            $startDate = \Carbon\Carbon::parse($dateFrom)->toDateString();
            $endDate = \Carbon\Carbon::parse($dateTo)->toDateString();
        } else {
            $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth()->toDateString();
            $endDate = \Carbon\Carbon::parse($month . '-01')->endOfMonth()->toDateString();
        }

        $query = Attendance::where('centre_id', $centre->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->with(['student', 'batch'])
            ->latest('attendance_date')
            ->latest('id');

        if ($selectedBatchId) {
            $query->where('batch_id', $selectedBatchId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $records = $query->get();

        $filename = "attendance_report_{$centre->centre_code}_{$startDate}_to_{$endDate}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Day', 'Roll No', 'Registration No', 'Student Name', 'Batch', 'Status', 'Remarks']);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->attendance_date ? $r->attendance_date->format('Y-m-d') : 'N/A',
                    $r->attendance_date ? $r->attendance_date->format('l') : 'N/A',
                    $r->student?->roll_no ?? 'N/A',
                    $r->student?->registration_no ?? 'N/A',
                    $r->student?->full_name ?? 'N/A',
                    $r->batch?->batch_name ?? 'N/A',
                    strtoupper($r->status),
                    $r->remarks ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
