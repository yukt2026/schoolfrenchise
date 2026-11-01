<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\District;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminStudentModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected Centre $centre;
    protected State $state;
    protected District $district;

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
            'centre_code' => 'TEST101',
            'centre_name' => 'Test Centre',
            'contact_person' => 'Manager',
            'mobile' => '9876543210',
            'email' => 'centre_test@drishticomputer.com',
            'status' => 'active',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Patna',
            'address' => 'Test Address',
        ]);

        $this->state = State::first() ?? State::create(['name' => 'Bihar', 'code' => 'BR']);
        $this->district = District::first() ?? District::create(['state_id' => $this->state->id, 'name' => 'Patna']);
    }

    public function test_admin_can_view_student_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertSee('Student Directory');
        $response->assertSee('Students Roster');
    }

    public function test_admin_can_view_create_student_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.students.create'));
        $response->assertStatus(200);
        $response->assertSee('Register New Student');
    }

    public function test_admin_can_register_new_student(): void
    {
        $uniqueMobile = '98' . rand(10000000, 99999999);
        $postData = [
            'centre_id' => $this->centre->id,
            'full_name' => 'Kavita Kumari',
            'father_name' => 'Rameshwar Singh',
            'mother_name' => 'Shanti Devi',
            'dob' => '2004-05-15',
            'gender' => 'female',
            'mobile' => $uniqueMobile,
            'alternate_mobile' => '9876543211',
            'email' => "kavita_{$uniqueMobile}@gmail.com",
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Boring Road, Patna',
            'pincode' => '800001',
            'highest_qualification' => '12th Standard',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.students.store'), $postData);

        $this->assertDatabaseHas('students', [
            'full_name' => 'Kavita Kumari',
            'mobile' => $uniqueMobile,
            'centre_id' => $this->centre->id,
        ]);

        $createdStudent = Student::where('mobile', $uniqueMobile)->first();
        $this->assertNotNull($createdStudent);
        $this->assertNotEmpty($createdStudent->roll_no);
        $this->assertNotEmpty($createdStudent->registration_no);

        // Verify user account created
        $this->assertDatabaseHas('users', [
            'mobile' => $uniqueMobile,
            'role' => 'student',
        ]);

        $response->assertRedirect(route('admin.students.show', $createdStudent->id));
    }

    public function test_admin_can_view_student_dossier(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.students.show', $student->id));
        $response->assertStatus(200);
        $response->assertSee($student->full_name);
        $response->assertSee($student->roll_no);
    }

    public function test_admin_can_update_student(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $response = $this->actingAs($this->superAdmin)->put(route('admin.students.update', $student->id), [
            'centre_id' => $student->centre_id,
            'full_name' => 'Updated ' . $student->full_name,
            'father_name' => $student->father_name,
            'dob' => '2003-01-01',
            'gender' => $student->gender ?? 'male',
            'mobile' => $student->mobile,
            'state_id' => $student->state_id,
            'district_id' => $student->district_id,
            'city' => $student->city,
            'address' => $student->address,
            'pincode' => $student->pincode,
            'status' => 'ongoing',
        ]);

        $response->assertRedirect(route('admin.students.show', $student->id));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'ongoing',
        ]);
    }

    public function test_admin_can_soft_delete_student(): void
    {
        $student = Student::create([
            'centre_id' => $this->centre->id,
            'registration_no' => 'DCE/2026/TEST/' . rand(1000, 9999),
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Temp Student To Delete',
            'father_name' => 'Father',
            'dob' => '2000-01-01',
            'gender' => 'male',
            'mobile' => '91' . rand(10000000, 99999999),
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Some address',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.students.destroy', $student->id));
        $response->assertRedirect(route('admin.students.index'));

        // Soft deleted
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_admin_can_export_students_csv(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.students.export'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_admin_can_view_student_id_card(): void
    {
        $student = Student::create([
            'centre_id' => $this->centre->id,
            'registration_no' => 'DCE/2026/TEST/' . rand(1000, 9999),
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'ID Card Test Student',
            'father_name' => 'Father Test',
            'dob' => '2001-05-10',
            'gender' => 'male',
            'mobile' => '91' . rand(10000000, 99999999),
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Sheikhpura',
            'address' => 'Some address',
            'pincode' => '801105',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.students.id-card', $student->id));
        $response->assertStatus(200);
        $response->assertSee('STUDENT IDENTITY CARD');
        $response->assertSee($student->full_name);
        $response->assertSee($student->roll_no);
    }
}
