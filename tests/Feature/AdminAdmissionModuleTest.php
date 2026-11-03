<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAdmissionModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected Centre $centre;
    protected Course $course;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@drishticomputer.com'],
            ['name' => 'Super Administrator', 'password' => bcrypt('Admin@12345'), 'role' => 'super_admin', 'is_active' => true]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code' => 'TEST102',
            'centre_name' => 'Admissions Test Centre',
            'contact_person' => 'Director',
            'mobile' => '9876543220',
            'email' => 'centre_adm@drishticomputer.com',
            'status' => 'active',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Test Address',
        ]);

        $this->course = Course::first() ?? Course::create([
            'course_code' => 'DCA',
            'course_name' => 'Diploma in Computer Application',
            'slug' => 'dca-diploma-in-computer-application',
            'duration' => '6 Months',
            'default_course_fee' => 4500.00,
            'status' => 'active',
        ]);

        $this->student = Student::first() ?? Student::create([
            'centre_id' => $this->centre->id,
            'registration_no' => 'DCE/2026/TEST/9991',
            'roll_no' => 'DCE2699991',
            'full_name' => 'Admitted Student Test',
            'father_name' => 'Father Test',
            'dob' => '2001-01-01',
            'gender' => 'male',
            'mobile' => '9888877771',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Noida Sec 62',
            'pincode' => '201301',
            'status' => 'enrolled',
        ]);
    }

    public function test_admin_can_view_admissions_index_applications_tab(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.admissions.index', ['tab' => 'applications']));
        $response->assertStatus(200);
        $response->assertSee('Admissions & Applications Management', false);
        $response->assertSee('Online Admission Applications');
    }

    public function test_admin_can_view_admissions_index_active_tab(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.admissions.index', ['tab' => 'active']));
        $response->assertStatus(200);
        $response->assertSee('Enrolled Student Course Admissions');
    }

    public function test_admin_can_view_direct_admission_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.admissions.create'));
        $response->assertStatus(200);
        $response->assertSee('Direct Course Admission');
        $response->assertSee($this->course->course_name);
        $response->assertSee('Select Course (');
    }

    public function test_admin_can_store_direct_admission(): void
    {
        $postData = [
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => date('Y-m-d'),
            'course_fee' => 4500,
            'discount_amount' => 500,
            'remarks' => 'Direct institutional enrollment test',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.admissions.store'), $postData);
        $response->assertRedirect(route('admin.admissions.index', ['tab' => 'active']));

        $this->assertDatabaseHas('admissions', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'net_fee' => 4000.00,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_review_application(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-TEST1',
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'applicant_name' => 'Applicant Review Test',
            'father_name' => 'Parent Name',
            'dob' => '2004-02-02',
            'gender' => 'male',
            'mobile' => '9777766661',
            'email' => 'applicant_test@gmail.com',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Sample Address',
            'pincode' => '201301',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.admissions.application.show', $application->id));
        $response->assertStatus(200);
        $response->assertSee('Review Admission Application');
        $response->assertSee($application->application_no);
        $response->assertSee('Applicant Review Test');
    }

    public function test_admin_can_approve_application_and_convert_to_student(): void
    {
        $uniquePhone = '97' . rand(10000000, 99999999);
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-TEST2',
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'applicant_name' => 'To Be Approved',
            'father_name' => 'Father Name',
            'dob' => '2003-03-03',
            'gender' => 'female',
            'mobile' => $uniquePhone,
            'email' => "candidate_{$uniquePhone}@gmail.com",
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Sample Address',
            'pincode' => '201301',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.admissions.application.approve', $application->id),
            [
                'course_fee' => 4500,
                'discount_amount' => 500,
            ]
        );

        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertNotNull($application->converted_student_id);

        $student = Student::find($application->converted_student_id);
        $this->assertNotNull($student);
        $this->assertEquals('To Be Approved', $student->full_name);
        $this->assertNotEmpty($student->roll_no);

        // Admission created
        $this->assertDatabaseHas('admissions', [
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'net_fee' => 4000.00,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.students.show', $student->id));
    }

    public function test_admin_can_reject_application_with_reason(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-REJECT',
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'applicant_name' => 'To Be Rejected',
            'father_name' => 'Father Name',
            'dob' => '2003-03-03',
            'gender' => 'male',
            'mobile' => '96' . rand(10000000, 99999999),
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Sample Address',
            'pincode' => '201301',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.admissions.application.reject', $application->id),
            [
                'rejection_reason' => 'Marksheet scan was illegible and incomplete.',
            ]
        );

        $application->refresh();
        $this->assertEquals('rejected', $application->status);
        $this->assertEquals('Marksheet scan was illegible and incomplete.', $application->rejection_reason);
        $response->assertRedirect(route('admin.admissions.index'));
    }

    public function test_admin_can_update_active_admission(): void
    {
        $admission = Admission::create([
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => date('Y-m-d'),
            'course_fee' => 4500,
            'discount_amount' => 0,
            'net_fee' => 4500,
            'total_paid' => 0,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(
            route('admin.admissions.update', $admission->id),
            [
                'status' => 'completed',
                'remarks' => 'Batch started and student attending regular sessions.',
            ]
        );

        $admission->refresh();
        $this->assertEquals('completed', $admission->status);
        $this->assertEquals('Batch started and student attending regular sessions.', $admission->remarks);
        $response->assertRedirect(route('admin.admissions.index', ['tab' => 'active']));
    }

    public function test_admin_can_soft_delete_admission(): void
    {
        $admission = Admission::create([
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => date('Y-m-d'),
            'course_fee' => 4500,
            'discount_amount' => 0,
            'net_fee' => 4500,
            'total_paid' => 0,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.admissions.destroy', $admission->id));
        $this->assertSoftDeleted('admissions', ['id' => $admission->id]);
        $response->assertRedirect(route('admin.admissions.index', ['tab' => 'active']));
    }
}
