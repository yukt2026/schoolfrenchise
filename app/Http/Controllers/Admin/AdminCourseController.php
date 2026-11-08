<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FeeStructure;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('category')->withCount(['admissions', 'batches']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('duration', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', (bool) $request->input('status'));
        }

        $courses = $query->orderBy('course_code')->paginate(15)->withQueryString();
        $categories = CourseCategory::orderBy('name')->get();

        $counts = [
            'total' => Course::count(),
            'active' => Course::where('status', 1)->count(),
            'popular' => Course::where('is_popular', 1)->count(),
            'categories' => CourseCategory::count(),
        ];

        return view('admin.courses.index', compact('courses', 'categories', 'counts'));
    }

    public function create()
    {
        $categories = CourseCategory::orderBy('name')->get();
        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code',
            'course_name' => 'required|string|max:191',
            'category_id' => 'nullable|exists:course_categories,id',
            'duration' => 'required|string|max:50',
            'eligibility' => 'required|string|max:100',
            'default_course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'exam_fee' => 'nullable|numeric|min:0',
            'certificate_fee' => 'nullable|numeric|min:0',
            'syllabus' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $slug = Str::slug($validated['course_code'] . '-' . $validated['course_name']);
        if (Course::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(100, 999);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = FileUploadService::uploadImage($request->file('thumbnail'), 'uploads/courses');
        }

        $course = Course::create([
            'category_id' => $validated['category_id'] ?? null,
            'course_code' => strtoupper(trim($validated['course_code'])),
            'course_name' => $validated['course_name'],
            'slug' => $slug,
            'duration' => $validated['duration'],
            'eligibility' => $validated['eligibility'],
            'default_course_fee' => (float) $validated['default_course_fee'],
            'registration_fee' => (float) ($validated['registration_fee'] ?? 0),
            'exam_fee' => (float) ($validated['exam_fee'] ?? 0),
            'certificate_fee' => (float) ($validated['certificate_fee'] ?? 0),
            'syllabus' => $validated['syllabus'] ?? null,
            'thumbnail' => $thumbnailPath,
            'is_popular' => $request->boolean('is_popular'),
            'status' => $request->has('status') ? $request->boolean('status') : true,
        ]);

        // Auto-provision standard fee structure
        $totalFee = $course->total_fee;
        if ($totalFee > 0) {
            $admFee = min($totalFee, 1000.00);
            FeeStructure::firstOrCreate(
                ['course_id' => $course->id],
                [
                    'name' => "Standard {$course->course_code} Fee Package",
                    'centre_id' => null,
                    'total_fee' => $totalFee,
                    'admission_fee' => $admFee,
                    'tuition_fee' => max(0, $totalFee - $admFee),
                    'installments_count' => max(1, min(6, $course->duration_months)),
                    'is_active' => true,
                ]
            );
        }

        AuditLog::record('course_created', $course, null, [
            'course_code' => $course->course_code,
            'course_name' => $course->course_name,
            'total_fee' => $totalFee,
        ]);

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', "Course [{$course->course_code} - {$course->course_name}] created successfully. You can now configure curriculum subjects and marking scheme below.");
    }

    public function show(Course $course)
    {
        $course->load(['category', 'subjects', 'batches', 'feeStructures', 'admissions.student']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:50', Rule::unique('courses')->ignore($course->id)],
            'course_name' => 'required|string|max:191',
            'category_id' => 'nullable|exists:course_categories,id',
            'duration' => 'required|string|max:50',
            'eligibility' => 'required|string|max:100',
            'default_course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'exam_fee' => 'nullable|numeric|min:0',
            'certificate_fee' => 'nullable|numeric|min:0',
            'syllabus' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'is_popular' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $oldValues = $course->toArray();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = FileUploadService::uploadImage($request->file('thumbnail'), 'uploads/courses');
        }

        $validated['course_code'] = strtoupper(trim($validated['course_code']));
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['status'] = $request->boolean('status');

        $course->update($validated);

        AuditLog::record('course_updated', $course, $oldValues, $course->toArray());

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', "Course [{$course->course_code}] updated successfully.");
    }

    public function destroy(Course $course)
    {
        $code = $course->course_code;
        $course->delete(); // Soft delete

        AuditLog::record('course_deleted', $course, ['status' => 'deleted']);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course [{$code}] has been soft-deleted and archived.");
    }
}
