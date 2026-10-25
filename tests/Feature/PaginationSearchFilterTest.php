<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Centre;
use App\Models\Course;
use App\Models\District;
use App\Models\FeePayment;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationSearchFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $centreUser;
    protected Centre $centre;
    protected Course $course;
    protected State $state;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@drishticomputer.com'],
            ['name' => 'Super Administrator', 'password' => bcrypt('Admin@12345'), 'role' => 'super_admin', 'is_active' => true]
        );
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);
        $this->district = District::firstOrCreate(
            ['name' => 'Patna'],
            ['state_id' => $this->state->id, 'status' => true]
        );

        $this->centre = Centre::firstOrCreate(
            ['centre_code' => 'PAGTEST01'],
            [
                'centre_name' => 'Pagination Test Centre',
                'director_name' => 'Director Test',
                'contact_person' => 'Director Test',
                'mobile' => '9876500001',
                'email' => 'pagtest01@drishti.local',
                'address' => 'Test Area, Patna',
                'state_id' => $this->state->id,
                'district_id' => $this->district->id,
                'city' => 'Patna',
                'pincode' => '800001',
                'affiliation_date' => now()->toDateString(),
                'valid_upto' => now()->addYear()->toDateString(),
                'status' => 'active',
            ]
        );

        $this->centreUser = User::firstOrCreate(
            ['email' => 'pag_centre@drishti.local'],
            [
                'name' => 'Centre Pag Manager',
                'password' => bcrypt('Centre@12345'),
                'role' => 'centre',
                'centre_id' => $this->centre->id,
                'is_active' => true,
            ]
        );
        $this->centreUser->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'ADCA-PAG'],
            [
                'course_name' => 'Advance Diploma in Computer Apps (Pag)',
                'slug' => 'advance-diploma-computer-apps-pag',
                'duration' => '12 Months',
                'eligibility' => '10+2',
                'status' => true,
            ]
        );
    }

    /**
     * Test global paginator generates Bootstrap 5 HTML structure.
     */
    public function test_paginator_uses_bootstrap_five_markup(): void
    {
        $items = collect(range(1, 40));
        $paginator = new LengthAwarePaginator($items->slice(0, 10), 40, 10, 1, [
            'path' => url('/test-pagination'),
            'pageName' => 'page',
        ]);

        $rendered = (string) $paginator->links();

        $this->assertStringContainsString('pagination', $rendered);
        $this->assertStringContainsString('page-item', $rendered);
        $this->assertStringContainsString('page-link', $rendered);
        // Ensure no unconstrained raw svg tags in default render
        $this->assertStringNotContainsString('<svg class="w-5 h-5"', $rendered);
    }

    /**
     * Test admin students index search and status filtering.
     */
    public function test_admin_students_search_and_filter(): void
    {
        $studentA = Student::create([
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-PAG-A',
            'registration_no' => 'REG-PAG-A',
            'full_name' => 'Aarav Sharma Pagination',
            'father_name' => 'Suresh Sharma',
            'mother_name' => 'Sunita Sharma',
            'gender' => 'male',
            'dob' => '2004-05-10',
            'mobile' => '9988771122',
            'email' => 'aarav.pag@example.com',
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $studentB = Student::create([
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-PAG-B',
            'registration_no' => 'REG-PAG-B',
            'full_name' => 'Bhavna Kumari Pagination',
            'father_name' => 'Ramesh Kumar',
            'mother_name' => 'Rekha Devi',
            'gender' => 'female',
            'dob' => '2003-08-15',
            'mobile' => '9988773344',
            'email' => 'bhavna.pag@example.com',
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'completed',
        ]);

        // Filter by search name
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.students.index', ['search' => 'Aarav Sharma Pagination']));

        $response->assertStatus(200);
        $response->assertSee('Aarav Sharma Pagination');
        $response->assertDontSee('Bhavna Kumari Pagination');

        // Filter by status completed
        $responseStatus = $this->actingAs($this->superAdmin)
            ->get(route('admin.students.index', ['status' => 'completed', 'search' => 'Pagination']));

        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Bhavna Kumari Pagination');
        $responseStatus->assertDontSee('Aarav Sharma Pagination');
    }

    /**
     * Test centre portal students index filtering and search.
     */
    public function test_centre_students_search_and_filter(): void
    {
        $student = Student::create([
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-CTR-FILTER',
            'registration_no' => 'REG-CTR-FILTER',
            'full_name' => 'Chirag Singhania Centre',
            'father_name' => 'Dinesh Singhania',
            'mother_name' => 'Kavita Singhania',
            'gender' => 'male',
            'dob' => '2005-02-18',
            'mobile' => '9112233445',
            'email' => 'chirag.ctr@example.com',
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'ongoing',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 3000.00,
            'status' => 'active',
        ]);

        // Search in Centre Portal
        $res = $this->actingAs($this->centreUser)
            ->get(route('centre.students.index', ['search' => 'Chirag Singhania']));

        $res->assertStatus(200);
        $res->assertSee('Chirag Singhania Centre');
        $res->assertSee('ROLL-CTR-FILTER');

        // Filter by status ongoing
        $resStatus = $this->actingAs($this->centreUser)
            ->get(route('centre.students.index', ['status' => 'ongoing', 'search' => 'Chirag']));

        $resStatus->assertStatus(200);
        $resStatus->assertSee('Chirag Singhania Centre');

        // Non-existent search returns empty state
        $resEmpty = $this->actingAs($this->centreUser)
            ->get(route('centre.students.index', ['search' => 'NONEXISTENT_STUDENT_QUERY_XYZ']));

        $resEmpty->assertStatus(200);
        $resEmpty->assertSee('No students found');
    }

    /**
     * Test centre fees index and search.
     */
    public function test_centre_fees_search_and_query_strings(): void
    {
        $student = Student::create([
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-FEE-PAG',
            'registration_no' => 'REG-FEE-PAG',
            'full_name' => 'Divya Prakash Fee',
            'father_name' => 'Om Prakash',
            'mother_name' => 'Shanti Devi',
            'gender' => 'male',
            'dob' => '2004-11-20',
            'mobile' => '9876511223',
            'email' => 'divya.fee@example.com',
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'ongoing',
        ]);

        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 5000.00,
            'status' => 'active',
        ]);

        $payment = FeePayment::create([
            'centre_id' => $this->centre->id,
            'student_id' => $student->id,
            'admission_id' => $admission->id,
            'receipt_no' => 'RCP-FILTER-001',
            'amount' => 5000.00,
            'payment_date' => now()->toDateString(),
            'payment_mode' => 'upi',
            'transaction_ref' => 'UPI-FILTER-12345',
            'remarks' => 'First installment',
        ]);

        // Search by receipt number
        $res = $this->actingAs($this->centreUser)
            ->get(route('centre.fees.index', ['search' => 'RCP-FILTER-001']));

        $res->assertStatus(200);
        $res->assertSee('RCP-FILTER-001');
        $res->assertSee('Divya Prakash Fee');
    }

    /**
     * Test admin centres index search and status filtering.
     */
    public function test_admin_centres_search_and_filter(): void
    {
        $res = $this->actingAs($this->superAdmin)
            ->get(route('admin.centres.index', ['search' => 'PAGTEST01']));

        $res->assertStatus(200);
        $res->assertSee('PAGTEST01');
        $res->assertSee('Pagination Test Centre');

        $resStatus = $this->actingAs($this->superAdmin)
            ->get(route('admin.centres.index', ['status' => 'active', 'search' => 'Pagination Test']));

        $resStatus->assertStatus(200);
        $resStatus->assertSee('Pagination Test Centre');
    }

    /**
     * Test admin admissions index with tabs and filters.
     */
    public function test_admin_admissions_tabs_and_search(): void
    {
        // 1. Applications tab
        $resApp = $this->actingAs($this->superAdmin)
            ->get(route('admin.admissions.index', ['tab' => 'applications']));

        $resApp->assertStatus(200);
        $resApp->assertSee('Admission Applications');

        // 2. Active tab with search query string
        $resActive = $this->actingAs($this->superAdmin)
            ->get(route('admin.admissions.index', ['tab' => 'active', 'search' => 'NONEXISTENT_QUERY_TERM']));

        $resActive->assertStatus(200);
        $resActive->assertSee('Enrolled Student Course Admissions');
    }

    /**
     * Test student portal pagination and query parameters.
     */
    public function test_student_portal_materials_and_fees_history(): void
    {
        $studentUser = User::create([
            'name' => 'Student Pag Test',
            'email' => 'student.pag.' . uniqid() . '@example.com',
            'password' => bcrypt('Secret@123'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
        $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-ST-PAG',
            'registration_no' => 'REG-ST-PAG',
            'full_name' => 'Student Pag Test',
            'father_name' => 'Father Pag',
            'mother_name' => 'Mother Pag',
            'gender' => 'female',
            'dob' => '2005-01-01',
            'mobile' => '9112233499',
            'email' => $studentUser->email,
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 2000.00,
            'status' => 'active',
        ]);

        // Materials index
        $resMat = $this->actingAs($studentUser)
            ->get(route('student.study-materials'));
        $resMat->assertStatus(200);

        // Fees history
        $resFee = $this->actingAs($studentUser)
            ->get(route('student.fees.history'));
        $resFee->assertStatus(200);
        $resFee->assertSee('Payment History');

        // Attendance analytics
        $resAtt = $this->actingAs($studentUser)
            ->get(route('student.attendance'));
        $resAtt->assertStatus(200);
        $resAtt->assertSee('Attendance Record');
    }

    /**
     * Test admin fees dues roster pagination, search, and dynamic amounts.
     */
    public function test_admin_fees_dues_roster_pagination_and_search(): void
    {
        $studentDues = Student::create([
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-DUES-99',
            'registration_no' => 'REG-DUES-99',
            'full_name' => 'Dues Candidate Unique',
            'father_name' => 'Father Unique',
            'mother_name' => 'Mother Unique',
            'gender' => 'male',
            'dob' => '2003-01-01',
            'mobile' => '9988001122',
            'address' => 'Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $studentDues->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 12000.00,
            'discount_amount' => 0.00,
            'net_fee' => 12000.00,
            'total_paid' => 4000.00,
            'status' => 'active',
        ]);

        $res = $this->actingAs($this->superAdmin)
            ->get(route('admin.fees.dues', ['search' => 'Dues Candidate Unique']));

        $res->assertStatus(200);
        $res->assertSee('Dues Candidate Unique');
        $res->assertSee('ROLL-DUES-99');
        $res->assertSee('8,000.00'); // Due: 12000 - 4000 = 8000
    }
}
