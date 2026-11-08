<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseSubject;
use Illuminate\Http\Request;

class AdminCourseSubjectController extends Controller
{
    /**
     * Store a newly created subject in storage for a given course.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'subject_code'    => 'required|string|max:50',
            'subject_name'    => 'required|string|max:191',
            'theory_max'      => 'required|integer|min:0|max:1000',
            'practical_max'   => 'nullable|integer|min:0|max:1000',
            'viva_max'        => 'nullable|integer|min:0|max:1000',
            'min_pass_marks'  => 'required|integer|min:0|max:1000',
            'sort_order'      => 'nullable|integer|min:1',
        ]);

        $theoryMax = (int) $validated['theory_max'];
        $practicalMax = (int) ($validated['practical_max'] ?? 0);
        $vivaMax = (int) ($validated['viva_max'] ?? 0);
        $totalMax = $theoryMax + $practicalMax + $vivaMax;

        if ($validated['min_pass_marks'] > $totalMax && $totalMax > 0) {
            return back()->withInput()->withErrors([
                'min_pass_marks' => 'Minimum pass marks (' . $validated['min_pass_marks'] . ') cannot be greater than total maximum marks (' . $totalMax . ').'
            ]);
        }

        $sortOrder = $validated['sort_order'] ?? (($course->subjects()->max('sort_order') ?? 0) + 1);

        $subject = $course->subjects()->create([
            'subject_code'    => strtoupper(trim($validated['subject_code'])),
            'subject_name'    => trim($validated['subject_name']),
            'theory_max'      => $theoryMax,
            'practical_max'   => $practicalMax,
            'viva_max'        => $vivaMax,
            'total_max'       => $totalMax,
            'min_pass_marks'  => (int) $validated['min_pass_marks'],
            'sort_order'      => $sortOrder,
        ]);

        AuditLog::record('course_subject_created', $subject, null, [
            'course_id'    => $course->id,
            'course_code'  => $course->course_code,
            'subject_code' => $subject->subject_code,
            'subject_name' => $subject->subject_name,
            'total_max'    => $totalMax,
        ]);

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', "Subject [{$subject->subject_code} - {$subject->subject_name}] added successfully to course.");
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Course $course, CourseSubject $subject)
    {
        if ($subject->course_id !== $course->id) {
            abort(404, 'Subject does not belong to this course.');
        }

        $validated = $request->validate([
            'subject_code'    => 'required|string|max:50',
            'subject_name'    => 'required|string|max:191',
            'theory_max'      => 'required|integer|min:0|max:1000',
            'practical_max'   => 'nullable|integer|min:0|max:1000',
            'viva_max'        => 'nullable|integer|min:0|max:1000',
            'min_pass_marks'  => 'required|integer|min:0|max:1000',
            'sort_order'      => 'nullable|integer|min:1',
        ]);

        $theoryMax = (int) $validated['theory_max'];
        $practicalMax = (int) ($validated['practical_max'] ?? 0);
        $vivaMax = (int) ($validated['viva_max'] ?? 0);
        $totalMax = $theoryMax + $practicalMax + $vivaMax;

        if ($validated['min_pass_marks'] > $totalMax && $totalMax > 0) {
            return back()->withInput()->withErrors([
                'min_pass_marks' => 'Minimum pass marks (' . $validated['min_pass_marks'] . ') cannot be greater than total maximum marks (' . $totalMax . ').'
            ]);
        }

        $oldValues = $subject->toArray();

        $subject->update([
            'subject_code'    => strtoupper(trim($validated['subject_code'])),
            'subject_name'    => trim($validated['subject_name']),
            'theory_max'      => $theoryMax,
            'practical_max'   => $practicalMax,
            'viva_max'        => $vivaMax,
            'total_max'       => $totalMax,
            'min_pass_marks'  => (int) $validated['min_pass_marks'],
            'sort_order'      => $validated['sort_order'] ?? $subject->sort_order,
        ]);

        AuditLog::record('course_subject_updated', $subject, $oldValues, $subject->toArray());

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', "Subject [{$subject->subject_code}] updated successfully.");
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Course $course, CourseSubject $subject)
    {
        if ($subject->course_id !== $course->id) {
            abort(404, 'Subject does not belong to this course.');
        }

        // Safety check: Prevent deletion if student exam results already exist for this subject
        if ($subject->examResults()->exists()) {
            return redirect()->route('admin.courses.show', $course->id)
                ->with('error', "Cannot delete subject [{$subject->subject_code}] because student examination marks/results are already recorded against it.");
        }

        $code = $subject->subject_code;
        $name = $subject->subject_name;

        AuditLog::record('course_subject_deleted', $subject, $subject->toArray(), ['status' => 'deleted']);

        $subject->delete();

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', "Subject [{$code} - {$name}] was successfully removed from course curriculum.");
    }
}
