<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCourseModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected CourseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@drishticomputer.com'],
            ['name' => 'Super Administrator', 'password' => bcrypt('Admin@12345'), 'role' => 'super_admin', 'is_active' => true]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->category = CourseCategory::firstOrCreate(
            ['slug' => 'software-development'],
            ['name' => 'Software Development', 'description' => 'Programming and software courses', 'status' => true]
        );
    }

    public function test_admin_can_view_courses_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.courses.index'));
        $response->assertStatus(200);
        $response->assertSee('Course Curriculum Management');
        $response->assertSee('Curriculum Directory');
    }

    public function test_admin_can_view_course_create_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.courses.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Curriculum Course');
    }

    public function test_admin_can_create_new_course(): void
    {
        $uniqueCode = 'TEST-' . rand(100, 999);
        $postData = [
            'course_code' => $uniqueCode,
            'course_name' => 'Full-Stack Web Development with Laravel',
            'category_id' => $this->category->id,
            'duration' => '6 Months',
            'eligibility' => '12th Pass or Equivalent',
            'default_course_fee' => 8500.00,
            'registration_fee' => 500.00,
            'exam_fee' => 300.00,
            'certificate_fee' => 200.00,
            'syllabus' => "Module 1: HTML, CSS, JavaScript\nModule 2: PHP & MySQL\nModule 3: Laravel Framework Architecture",
            'is_popular' => 1,
            'status' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.courses.store'), $postData);

        $this->assertDatabaseHas('courses', [
            'course_code' => $uniqueCode,
            'course_name' => 'Full-Stack Web Development with Laravel',
            'default_course_fee' => 8500.00,
            'status' => 1,
        ]);

        $course = Course::where('course_code', $uniqueCode)->first();
        $this->assertNotNull($course);
        $this->assertEquals(9500.00, $course->total_fee);

        // Check auto-provisioned fee structure
        $this->assertDatabaseHas('fee_structures', [
            'course_id' => $course->id,
            'total_fee' => 9500.00,
        ]);

        $response->assertRedirect(route('admin.courses.show', $course->id));
    }

    public function test_admin_can_view_course_dossier(): void
    {
        $course = Course::first();
        $this->assertNotNull($course);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.courses.show', $course->id));
        $response->assertStatus(200);
        $response->assertSee($course->course_code);
        $response->assertSee($course->course_name);
        $response->assertSee('Course Curriculum Dossier');
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::create([
            'category_id' => $this->category->id,
            'course_code' => 'UPDT-' . rand(100, 999),
            'course_name' => 'Original Course Name',
            'slug' => 'original-course-' . rand(100, 999),
            'duration' => '3 Months',
            'eligibility' => '10th Pass',
            'default_course_fee' => 3000.00,
            'registration_fee' => 0.00,
            'exam_fee' => 0.00,
            'certificate_fee' => 0.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)->put(
            route('admin.courses.update', $course->id),
            [
                'course_code' => $course->course_code,
                'course_name' => 'Updated Certified Course',
                'duration' => '4 Months',
                'eligibility' => '10th Pass',
                'default_course_fee' => 4000.00,
                'status' => 1,
            ]
        );

        $course->refresh();
        $this->assertEquals('Updated Certified Course', $course->course_name);
        $this->assertEquals('4 Months', $course->duration);
        $this->assertEquals(4000.00, (float) $course->default_course_fee);

        $response->assertRedirect(route('admin.courses.show', $course->id));
    }

    public function test_admin_can_soft_delete_course(): void
    {
        $course = Course::create([
            'category_id' => $this->category->id,
            'course_code' => 'DEL-' . rand(100, 999),
            'course_name' => 'Course to Delete',
            'slug' => 'course-to-delete-' . rand(100, 999),
            'duration' => '1 Month',
            'eligibility' => '10th',
            'default_course_fee' => 1000.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.courses.destroy', $course->id));
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $response->assertRedirect(route('admin.courses.index'));
    }

    public function test_admin_can_add_subject_with_marks_to_course(): void
    {
        $course = Course::first();
        $this->assertNotNull($course);

        $postData = [
            'subject_code'   => 'SUB-T1',
            'subject_name'   => 'Information Technology Fundamentals',
            'theory_max'     => 70,
            'practical_max'  => 20,
            'viva_max'       => 10,
            'min_pass_marks' => 40,
            'sort_order'     => 1,
        ];

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.courses.subjects.store', $course->id),
            $postData
        );

        $response->assertRedirect(route('admin.courses.show', $course->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('course_subjects', [
            'course_id'       => $course->id,
            'subject_code'    => 'SUB-T1',
            'subject_name'    => 'Information Technology Fundamentals',
            'theory_max'      => 70,
            'practical_max'   => 20,
            'viva_max'        => 10,
            'total_max'       => 100,
            'min_pass_marks'  => 40,
        ]);
    }

    public function test_admin_can_update_subject_and_marks(): void
    {
        $course = Course::first();
        $subject = $course->subjects()->create([
            'subject_code'   => 'OLD-01',
            'subject_name'   => 'Old Subject Name',
            'theory_max'     => 50,
            'practical_max'  => 50,
            'viva_max'       => 0,
            'total_max'      => 100,
            'min_pass_marks' => 35,
            'sort_order'     => 2,
        ]);

        $updateData = [
            'subject_code'   => 'NEW-01',
            'subject_name'   => 'Revised Subject Name',
            'theory_max'     => 60,
            'practical_max'  => 30,
            'viva_max'       => 10,
            'min_pass_marks' => 40,
            'sort_order'     => 3,
        ];

        $response = $this->actingAs($this->superAdmin)->put(
            route('admin.courses.subjects.update', [$course->id, $subject->id]),
            $updateData
        );

        $response->assertRedirect(route('admin.courses.show', $course->id));
        $response->assertSessionHas('success');

        $subject->refresh();
        $this->assertEquals('NEW-01', $subject->subject_code);
        $this->assertEquals('Revised Subject Name', $subject->subject_name);
        $this->assertEquals(60, $subject->theory_max);
        $this->assertEquals(30, $subject->practical_max);
        $this->assertEquals(10, $subject->viva_max);
        $this->assertEquals(100, $subject->total_max);
        $this->assertEquals(40, $subject->min_pass_marks);
    }

    public function test_admin_can_delete_course_subject(): void
    {
        $course = Course::first();
        $subject = $course->subjects()->create([
            'subject_code'   => 'DEL-SUB',
            'subject_name'   => 'Subject to be Deleted',
            'theory_max'     => 100,
            'practical_max'  => 0,
            'viva_max'       => 0,
            'total_max'      => 100,
            'min_pass_marks' => 40,
            'sort_order'     => 5,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(
            route('admin.courses.subjects.destroy', [$course->id, $subject->id])
        );

        $response->assertRedirect(route('admin.courses.show', $course->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('course_subjects', [
            'id' => $subject->id,
        ]);
    }

    public function test_subject_validation_rejects_pass_marks_exceeding_total(): void
    {
        $course = Course::first();

        $postData = [
            'subject_code'   => 'INV-01',
            'subject_name'   => 'Invalid Passing Marks',
            'theory_max'     => 50,
            'practical_max'  => 20,
            'viva_max'       => 0, // total_max = 70
            'min_pass_marks' => 80, // > 70
            'sort_order'     => 1,
        ];

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.courses.subjects.store', $course->id),
            $postData
        );

        $response->assertSessionHasErrors(['min_pass_marks']);
    }
}
