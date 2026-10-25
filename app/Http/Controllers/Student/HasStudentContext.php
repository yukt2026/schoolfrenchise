<?php

namespace App\Http\Controllers\Student;

use App\Models\Admission;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

trait HasStudentContext
{
    /**
     * Retrieve the authenticated student record with multi-tenant security.
     */
    protected function getStudent(mixed $request = null): Student
    {
        $user = Auth::user();

        // Convenience fallback for Super Admin reviewing specific student
        if ($user && $user->isSuperAdmin() && request()->filled('student_id')) {
            $specificStudent = Student::find(request('student_id'));
            if ($specificStudent) {
                return $specificStudent;
            }
        }

        if ($user->student) {
            return $user->student;
        }

        $student = Student::where('user_id', $user->id)->first();
        if ($student) {
            return $student;
        }

        // Convenience fallback for Super Admin reviewing student portal
        if ($user->isSuperAdmin()) {
            $firstStudent = Student::first();
            if ($firstStudent) {
                return $firstStudent;
            }
        }

        abort(403, 'No student record found linked to your account. Please contact your study centre.');
    }

    /**
     * Retrieve active course enrollment for the student.
     */
    protected function getActiveAdmission(Student $student): ?Admission
    {
        return $student->admissions()->with(['course.subjects', 'batch', 'centre'])->latest()->first();
    }
}
