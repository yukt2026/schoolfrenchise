<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Course;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\PaymentReceipt;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Policies\CentrePolicy;
use App\Policies\FeePaymentPolicy;
use App\Policies\StudentPolicy;
use App\Services\FeeService;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class AuthAndManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist
        Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Franchise Director']);
        Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
    }

    public function test_super_admin_can_login_and_access_admin_dashboard(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin_test@drishticomputer.com'],
            [
                'name' => 'Super Admin Test',
                'password' => Hash::make('AdminTest@12345'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        $response = $this->post('/login', [
            'email' => 'admin_test@drishticomputer.com',
            'password' => 'AdminTest@12345',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($superAdmin);

        // Access dashboard
        $dashResponse = $this->actingAs($superAdmin)->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Super Admin Executive Dashboard');
    }

    public function test_centre_manager_can_login_and_access_centre_dashboard(): void
    {
        $centre = Centre::first();
        $this->assertNotNull($centre);

        $centreUser = User::updateOrCreate(
            ['email' => 'centre_director_test@drishticomputer.com'],
            [
                'name' => 'Director Test',
                'password' => Hash::make('CentreTest@12345'),
                'role' => 'centre',
                'centre_id' => $centre->id,
                'is_active' => true,
            ]
        );
        $centreUser->assignRole('centre');

        $response = $this->post('/login', [
            'email' => 'centre_director_test@drishticomputer.com',
            'password' => 'CentreTest@12345',
        ]);

        $response->assertRedirect('/centre/dashboard');
        $this->assertAuthenticatedAs($centreUser);

        // Access centre dashboard
        $dashResponse = $this->actingAs($centreUser)->get('/centre/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee($centre->centre_code);
    }

    public function test_centre_manager_cannot_access_admin_dashboard_receives_403(): void
    {
        $centre = Centre::first();
        $centreUser = User::where('email', 'centre_director_test@drishticomputer.com')->first();
        if (!$centreUser) {
            $centreUser = User::create([
                'name' => 'Director Test',
                'email' => 'centre_director_test2@drishticomputer.com',
                'password' => Hash::make('CentreTest@12345'),
                'role' => 'centre',
                'centre_id' => $centre->id,
                'is_active' => true,
            ]);
            $centreUser->assignRole('centre');
        }

        $response = $this->actingAs($centreUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_student_can_login_with_roll_number_and_access_student_dashboard(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $studentUser = User::updateOrCreate(
            ['email' => 'student_user_test@example.com'],
            [
                'name' => $student->full_name,
                'password' => Hash::make('Student@12345'),
                'role' => 'student',
                'is_active' => true,
            ]
        );
        $studentUser->assignRole('student');
        $student->update(['user_id' => $studentUser->id]);

        $response = $this->post('/student/login', [
            'login_id' => $student->roll_no,
            'password' => 'Student@12345',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($studentUser);

        // Access student dashboard
        $dashResponse = $this->actingAs($studentUser)->get('/student/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee($student->full_name);
        $dashResponse->assertSee($student->roll_no);
    }

    public function test_student_cannot_access_admin_or_centre_dashboard_receives_403(): void
    {
        $studentUser = User::create([
            'name' => 'Student Access Test',
            'email' => 'student_access_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $studentUser->assignRole('student');

        $responseAdmin = $this->actingAs($studentUser)->get('/admin/dashboard');
        $responseAdmin->assertStatus(403);

        $responseCentre = $this->actingAs($studentUser)->get('/centre/dashboard');
        $responseCentre->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->post('/logout'); // ensure logged out

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect('/login');

        $responseCentre = $this->get('/centre/dashboard');
        $responseCentre->assertRedirect('/login');

        $responseStudent = $this->get('/student/dashboard');
        $responseStudent->assertRedirect('/student');
    }

    public function test_deactivated_user_account_is_blocked_from_logging_in(): void
    {
        $deactivatedUser = User::updateOrCreate(
            ['email' => 'deactivated@example.com'],
            [
                'name' => 'Inactive User',
                'password' => Hash::make('Secret@12345'),
                'role' => 'centre',
                'is_active' => false,
            ]
        );

        $response = $this->post('/login', [
            'email' => 'deactivated@example.com',
            'password' => 'Secret@12345',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_suspended_centre_user_is_blocked_by_centre_active_middleware(): void
    {
        $suspendedCentre = Centre::create([
            'centre_code' => 'DCE-TEST-SUSPENDED',
            'centre_name' => 'Suspended Centre',
            'director_name' => 'Test Director',
            'email' => 'suspended_centre@example.com',
            'mobile' => '9899887766',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Test Address',
            'pincode' => '201301',
            'affiliation_date' => now(),
            'status' => 'suspended',
        ]);

        $user = User::create([
            'name' => 'Suspended Director',
            'email' => 'suspended_dir@example.com',
            'password' => Hash::make('Secret@12345'),
            'role' => 'centre',
            'centre_id' => $suspendedCentre->id,
            'is_active' => true,
        ]);
        $user->assignRole('centre');

        $response = $this->actingAs($user)->get('/centre/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }

    public function test_password_reset_flow_generates_token_and_resets_password(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        // 1. Request reset link
        $resp1 = $this->post('/password/email', ['email' => $user->email]);
        $resp1->assertSessionHas('success');

        $resetRecord = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($resetRecord);

        // 2. Perform reset with new password
        $rawToken = 'test-token-12345';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $rawToken),
                'created_at' => now(),
            ]
        );

        $resp2 = $this->post('/password/reset', [
            'token' => $rawToken,
            'email' => $user->email,
            'password' => 'NewSecretPassword@12345',
            'password_confirmation' => 'NewSecretPassword@12345',
        ]);

        $resp2->assertSessionHas('success');

        // Check password changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecretPassword@12345', $user->password));

        // Token deleted
        $this->assertNull(DB::table('password_reset_tokens')->where('email', $user->email)->first());
    }

    public function test_password_reset_accepts_roll_number_and_mobile(): void
    {
        $student = Student::whereNotNull('roll_no')->whereNotNull('user_id')->first();
        $this->assertNotNull($student);

        // 1. Request reset link using student roll number
        $resp1 = $this->post('/password/email', ['email' => $student->roll_no]);
        $resp1->assertSessionHas('success');

        // 2. Request reset link using student mobile
        if ($student->mobile) {
            $resp2 = $this->post('/password/email', ['email' => $student->mobile]);
            $resp2->assertSessionHas('success');
        }
    }

    public function test_student_policy_prevents_viewing_another_students_record_idor(): void
    {
        $policy = new StudentPolicy();

        $student1User = User::create([
            'name' => 'Student One',
            'email' => 'stud1_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $student2User = User::create([
            'name' => 'Student Two',
            'email' => 'stud2_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $student1 = Student::create([
            'user_id' => $student1User->id,
            'centre_id' => 1,
            'registration_no' => 'DCE/2026/TEST/' . rand(10000, 99999),
            'roll_no' => 'DCE26TEST' . rand(10000, 99999),
            'full_name' => 'Student One',
            'father_name' => 'Father One',
            'dob' => '2005-01-01',
            'gender' => 'male',
            'mobile' => '99' . rand(10000000, 99999999),
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Test Address 1',
            'pincode' => '201301',
            'status' => 'enrolled',
        ]);

        // Student 1 can view Student 1
        $this->assertTrue($policy->view($student1User, $student1));

        // Student 2 CANNOT view Student 1 (IDOR protection)
        $this->assertFalse($policy->view($student2User, $student1));
    }

    public function test_centre_policy_prevents_viewing_unauthorized_centre_idor(): void
    {
        $policy = new CentrePolicy();

        $centreA = Centre::first();
        $centreB = Centre::create([
            'centre_code' => 'DCE-TEST-ISOLATION-B',
            'centre_name' => 'Centre B Branch',
            'director_name' => 'Director B',
            'email' => 'director_b@example.com',
            'mobile' => '9900112233',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Patna',
            'address' => 'Patna Address',
            'pincode' => '800001',
            'affiliation_date' => now(),
            'status' => 'active',
        ]);

        $userCentreA = User::create([
            'name' => 'Centre A User',
            'email' => 'manager_a@example.com',
            'password' => Hash::make('password'),
            'role' => 'centre',
            'centre_id' => $centreA->id,
            'is_active' => true,
        ]);

        // Centre A Manager CAN view Centre A
        $this->assertTrue($policy->view($userCentreA, $centreA));

        // Centre A Manager CANNOT view Centre B (Isolation check)
        $this->assertFalse($policy->view($userCentreA, $centreB));
    }

    public function test_fee_service_collect_payment_executes_in_database_transaction(): void
    {
        $student = Student::first();
        $course = Course::first();
        $centre = Centre::first();

        // Create an admission with pending fee
        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $centre->id,
            'course_id' => $course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 10000.00,
            'discount_amount' => 0.00,
            'net_fee' => 10000.00,
            'total_paid' => 2000.00,
            'status' => 'active',
        ]);

        $initialPaymentsCount = FeePayment::count();
        $initialReceiptsCount = PaymentReceipt::count();

        // Collect payment of 3000 via FeeService
        $receipt = FeeService::collectPayment(
            admission: $admission,
            amount: 3000.00,
            paymentMode: 'cash',
            notes: 'Second installment collected via FeeService'
        );

        $this->assertNotNull($receipt);
        $this->assertEquals(3000.00, $receipt->amount);
        $this->assertEquals('cash', $receipt->payment_mode);
        $this->assertNotNull($receipt->qr_token);
        $this->assertStringStartsWith('RCP-', $receipt->receipt_no);

        // Admission total_paid updated to 5000
        $admission->refresh();
        $this->assertEquals(5000.00, $admission->total_paid);

        // Records created
        $this->assertEquals($initialPaymentsCount + 1, FeePayment::count());
        $this->assertEquals($initialReceiptsCount + 1, PaymentReceipt::count());
    }

    public function test_file_upload_service_rejects_executable_or_banned_extensions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $bannedFile = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');
        FileUploadService::uploadImage($bannedFile);
    }

    public function test_file_upload_service_accepts_valid_image_and_generates_safe_path(): void
    {
        $validImage = UploadedFile::fake()->image('passport_photo.jpg', 300, 300)->size(500);
        $storedPath = FileUploadService::uploadImage($validImage, 'uploads/test_photos');

        $this->assertNotNull($storedPath);
        $this->assertStringStartsWith('uploads/test_photos/', $storedPath);
        $this->assertStringEndsWith('.jpg', $storedPath);
    }

    public function test_audit_log_records_actions_properly(): void
    {
        $log = AuditLog::record('test_action', null, ['old' => '1'], ['new' => '2']);
        $this->assertNotNull($log->id);
        $this->assertEquals('test_action', $log->action);
        $this->assertEquals(['old' => '1'], $log->old_values);
        $this->assertEquals(['new' => '2'], $log->new_values);
    }
}
