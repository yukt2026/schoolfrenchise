<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\District;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Marksheet;
use App\Models\OnlineTransaction;
use App\Models\Role;
use App\Models\Setting;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Services\PaymentGatewayService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FranchiseEnhancementsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $centreUserA;
    protected Centre $centreA;
    protected User $centreUserB;
    protected Centre $centreB;
    protected User $superAdmin;
    protected Course $course;
    protected State $stateBihar;
    protected District $districtPatna;
    protected State $stateUP;
    protected District $districtLucknow;

    protected function setUp(): void
    {
        parent::setUp();

        $centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        $this->stateBihar = State::firstOrCreate(['name' => 'Bihar Test State'], ['status' => true]);
        $this->districtPatna = District::firstOrCreate(
            ['name' => 'Patna Test District', 'state_id' => $this->stateBihar->id],
            ['status' => true]
        );

        $this->stateUP = State::firstOrCreate(['name' => 'UP Test State'], ['status' => true]);
        $this->districtLucknow = District::firstOrCreate(
            ['name' => 'Lucknow Test District', 'state_id' => $this->stateUP->id],
            ['status' => true]
        );

        // Centre A
        $this->centreUserA = User::create([
            'name' => 'Franchise Director Alpha',
            'email' => 'director_alpha_' . uniqid() . '@example.com',
            'mobile' => '98111' . rand(10000, 99999),
            'password' => Hash::make('password123'),
            'role' => 'centre',
            'is_active' => true,
        ]);
        $this->centreUserA->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->centreA = Centre::create([
            'user_id' => $this->centreUserA->id,
            'centre_code' => 'FR-ALP-' . rand(1000, 9999),
            'centre_name' => 'Alpha Learning Centre',
            'director_name' => 'Ramesh Kumar',
            'email' => $this->centreUserA->email,
            'mobile' => $this->centreUserA->mobile,
            'address' => 'Station Road Alpha',
            'city' => 'Patna',
            'pincode' => '800001',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'lab_capacity' => 25,
            'status' => 'active',
        ]);
        $this->centreUserA->update(['centre_id' => $this->centreA->id]);

        CentreWallet::create([
            'centre_id' => $this->centreA->id,
            'balance' => 1000.00,
            'total_credited' => 1000.00,
            'total_debited' => 0.00,
            'status' => 'active',
        ]);

        // Centre B (Different Franchise for cross-tenant IDOR tests)
        $this->centreUserB = User::create([
            'name' => 'Franchise Director Beta',
            'email' => 'director_beta_' . uniqid() . '@example.com',
            'mobile' => '98222' . rand(10000, 99999),
            'password' => Hash::make('password123'),
            'role' => 'centre',
            'is_active' => true,
        ]);
        $this->centreUserB->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->centreB = Centre::create([
            'user_id' => $this->centreUserB->id,
            'centre_code' => 'FR-BET-' . rand(1000, 9999),
            'centre_name' => 'Beta Skill Academy',
            'director_name' => 'Suresh Singh',
            'email' => $this->centreUserB->email,
            'mobile' => $this->centreUserB->mobile,
            'address' => 'Main Market Beta',
            'city' => 'Lucknow',
            'pincode' => '226001',
            'state_id' => $this->stateUP->id,
            'district_id' => $this->districtLucknow->id,
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'lab_capacity' => 20,
            'status' => 'active',
        ]);
        $this->centreUserB->update(['centre_id' => $this->centreB->id]);

        CentreWallet::create([
            'centre_id' => $this->centreB->id,
            'balance' => 500.00,
            'total_credited' => 500.00,
            'total_debited' => 0.00,
            'status' => 'active',
        ]);

        // Admin User
        $this->superAdmin = User::create([
            'name' => 'Main Admin',
            'email' => 'main_admin_' . uniqid() . '@example.com',
            'mobile' => '98999' . rand(10000, 99999),
            'password' => Hash::make('adminpassword'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        // Sample Course
        $this->course = Course::create([
            'course_name' => 'Diploma in Computer Application',
            'course_code' => 'DCA-' . rand(1000, 9999),
            'slug' => 'dca-' . uniqid(),
            'duration' => '6 Months',
            'eligibility' => '10th Standard',
            'fee' => 5000.00,
            'status' => 1,
        ]);
    }

    /**
     * Requirement 1: Student Profile Editing, Email/Password update & Tenant Isolation
     */
    public function test_franchise_can_edit_student_profile_and_update_credentials(): void
    {
        $studentUser = User::create([
            'name' => 'Original Student Name',
            'email' => 'student_' . uniqid() . '@example.com',
            'mobile' => '98333' . rand(10000, 99999),
            'password' => Hash::make('oldpassword'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Original Student Name',
            'father_name' => 'Ram Prasad',
            'mother_name' => 'Kausalya Devi',
            'dob' => '2003-05-15',
            'gender' => 'male',
            'mobile' => $studentUser->mobile,
            'email' => $studentUser->email,
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Old Address Lane 1',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        // 1. Franchise can view edit form
        $response = $this->actingAs($this->centreUserA)->get(route('centre.students.edit', $student->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Student Profile');
        $response->assertSee('Original Student Name');

        // 2. Franchise updates profile and credentials
        $newEmail = 'updated_student_' . uniqid() . '@example.com';
        $updatePayload = [
            'full_name' => 'Updated Student Name',
            'father_name' => 'Updated Father Name',
            'mother_name' => 'Updated Mother Name',
            'dob' => '2003-05-15',
            'gender' => 'male',
            'mobile' => '9844455555',
            'email' => $newEmail,
            'password' => 'NewSecretPass123',
            'password_confirmation' => 'NewSecretPass123',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna City',
            'address' => 'New Street 45, Near Temple',
            'pincode' => '800004',
            'qualification' => 'Graduation',
            'status' => 'enrolled',
        ];

        $updateResponse = $this->actingAs($this->centreUserA)->put(route('centre.students.update', $student->id), $updatePayload);
        $updateResponse->assertSessionHas('success');
        $updateResponse->assertRedirect(route('centre.students.show', $student->id));

        // Assert Student updated
        $student->refresh();
        $this->assertEquals('Updated Student Name', $student->full_name);
        $this->assertEquals('Updated Father Name', $student->father_name);
        $this->assertEquals($newEmail, $student->email);
        $this->assertEquals('9844455555', $student->mobile);

        // Assert User credentials synchronized & hashed
        $studentUser->refresh();
        $this->assertEquals('Updated Student Name', $studentUser->name);
        $this->assertEquals($newEmail, $studentUser->email);
        $this->assertTrue(Hash::check('NewSecretPass123', $studentUser->password));
    }

    /**
     * Requirement 1 & IDOR: Franchise cannot edit or update students of another franchise
     */
    public function test_cross_franchise_student_edit_is_blocked(): void
    {
        $studentB = Student::create([
            'centre_id' => $this->centreB->id,
            'registration_no' => 'REG/' . $this->centreB->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Beta Student',
            'father_name' => 'Beta Father',
            'dob' => '2004-01-01',
            'gender' => 'male',
            'mobile' => '98555' . rand(10000, 99999),
            'email' => 'beta_student_' . uniqid() . '@example.com',
            'state_id' => $this->stateUP->id,
            'district_id' => $this->districtLucknow->id,
            'city' => 'Lucknow',
            'address' => 'Beta Campus',
            'pincode' => '226001',
            'status' => 'enrolled',
        ]);

        // Centre A attempts to access Centre B's student edit form
        $resEdit = $this->actingAs($this->centreUserA)->get(route('centre.students.edit', $studentB->id));
        $resEdit->assertStatus(403);

        // Centre A attempts to PUT update Centre B's student
        $resUpdate = $this->actingAs($this->centreUserA)->put(route('centre.students.update', $studentB->id), [
            'full_name' => 'Hacked Name',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
        ]);
        $resUpdate->assertStatus(403);
    }

    /**
     * Requirement 2: Dynamic District loading API and strict mismatched state/district rejection
     */
    public function test_state_district_api_and_mismatched_validation_prevention(): void
    {
        // 1. Dynamic API returns districts belonging to state
        $apiResponse = $this->get('/api/districts/' . $this->stateBihar->id);
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment(['name' => 'Patna Test District']);
        $apiResponse->assertJsonMissing(['name' => 'Lucknow Test District']);

        // 2. Submit student edit with mismatched state and district (Bihar state with UP district)
        $studentA = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'District Check Student',
            'father_name' => 'Father',
            'dob' => '2004-02-02',
            'gender' => 'female',
            'mobile' => '98666' . rand(10000, 99999),
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Street 1',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $invalidPayload = [
            'full_name' => 'District Check Student',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtLucknow->id, // Mismatched! Lucknow belongs to UP, not Bihar
            'mobile' => $studentA->mobile,
            'dob' => '2004-02-02',
            'gender' => 'female',
            'father_name' => 'Father',
            'city' => 'Patna',
            'address' => 'Street 1',
            'pincode' => '800001',
        ];

        $resMismatched = $this->actingAs($this->centreUserA)->put(route('centre.students.update', $studentA->id), $invalidPayload);
        $resMismatched->assertSessionHasErrors(['district_id']);
    }

    /**
     * Requirement 3: Student Results & Marks Entry, Boundary Validation, Auto Marksheet & Certificate
     */
    public function test_student_marks_entry_validation_and_certificate_generation(): void
    {
        $subject = CourseSubject::create([
            'course_id' => $this->course->id,
            'subject_code' => 'CS-' . rand(100, 999),
            'subject_name' => 'Web Development Fundamentals',
            'theory_max' => 70,
            'practical_max' => 20,
            'viva_max' => 10,
            'min_pass_marks' => 40,
            'total_max' => 100,
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Certificate Exam 2026',
            'exam_code' => 'EX-' . rand(1000, 9999),
            'session_name' => '2026-Session-1',
            'exam_date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Mark Testing Student',
            'father_name' => 'Prakash Test',
            'dob' => '2004-03-03',
            'gender' => 'male',
            'mobile' => '98777' . rand(10000, 99999),
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Exam Test Address',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 5000.00,
            'discount_amount' => 0.00,
            'net_fee' => 5000.00,
            'total_paid' => 5000.00,
            'status' => 'active',
        ]);

        // 1. Submit marks exceeding max theory ceiling (75 > 70)
        $invalidMarksPayload = [
            'student_id' => $student->id,
            'subjects' => [
                [
                    'id' => $subject->id,
                    'theory_obtained' => 75, // Invalid! Exceeds 70
                    'practical_obtained' => 10,
                    'viva_obtained' => 5,
                ],
            ],
        ];

        $resInvalid = $this->actingAs($this->centreUserA)->post(route('centre.exams.marks.store', $exam->id), $invalidMarksPayload);
        $resInvalid->assertSessionHas('error');

        // 2. Submit valid passing marks
        $validMarksPayload = [
            'student_id' => $student->id,
            'subjects' => [
                [
                    'id' => $subject->id,
                    'theory_obtained' => 60,
                    'practical_obtained' => 18,
                    'viva_obtained' => 8,
                ],
            ],
        ];

        $resValid = $this->actingAs($this->centreUserA)->post(route('centre.exams.marks.store', $exam->id), $validMarksPayload);
        $resValid->assertSessionHas('success');
        $resValid->assertRedirect(route('centre.exams.results'));

        // Assert Result created
        $result = ExamResult::where('student_id', $student->id)->where('exam_id', $exam->id)->first();
        $this->assertNotNull($result);
        $this->assertEquals('passed', $result->result_status);
        $this->assertEquals(86.00, $result->total_marks_obtained);
        $this->assertEquals('A+', $result->grade);

        // Assert Marksheet automatically created & linked
        $marksheet = Marksheet::where('student_id', $student->id)->first();
        $this->assertNotNull($marksheet);
        $this->assertNotNull($marksheet->marksheet_no);

        // Assert Certificate automatically generated & linked
        $certificate = Certificate::where('student_id', $student->id)->first();
        $this->assertNotNull($certificate);
        $this->assertNotNull($certificate->certificate_no);
        $this->assertNotNull($certificate->security_hash);
        $this->assertEquals($this->centreA->id, $certificate->centre_id);
    }

    /**
     * Requirement 4: Franchise Wallet Admission Commission & Duplicate Prevention
     */
    public function test_franchise_wallet_commission_on_admission_and_idempotency(): void
    {
        // 1. Ensure commission is set to 200 in settings
        Setting::updateOrCreate(
            ['key' => 'franchise_admission_commission'],
            ['value' => '200', 'group' => 'financial']
        );

        $wallet = CentreWallet::where('centre_id', $this->centreA->id)->first();
        $initialBalance = (float) $wallet->balance;

        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Commission Test Student',
            'father_name' => 'Father',
            'dob' => '2003-01-01',
            'gender' => 'male',
            'mobile' => '98888' . rand(10000, 99999),
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Sample Address',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 5000.00,
            'discount_amount' => 0.00,
            'net_fee' => 5000.00,
            'total_paid' => 5000.00,
            'status' => 'active',
            'commission_credited' => false,
        ]);

        // 2. Trigger commission credit
        $walletService = app(WalletService::class);
        $credited = $walletService->creditAdmissionCommission($admission);
        $this->assertTrue($credited);

        $admission->refresh();
        $wallet->refresh();

        $this->assertTrue((bool) $admission->commission_credited);
        $this->assertEquals(200.00, (float) $admission->commission_amount);
        $this->assertEquals($initialBalance - 200.00, (float) $wallet->balance);

        // Assert transaction logged as debit (fee cut from wallet)
        $this->assertDatabaseHas('centre_wallet_transactions', [
            'centre_id' => $this->centreA->id,
            'type' => 'debit',
            'amount' => 200.00,
        ]);

        // 3. Duplicate Call: Idempotency check prevents double debit
        $secondAttempt = $walletService->creditAdmissionCommission($admission);
        $this->assertFalse($secondAttempt);

        $wallet->refresh();
        // Balance remains exactly -200, NOT -400!
        $this->assertEquals($initialBalance - 200.00, (float) $wallet->balance);
    }

    /**
     * Requirement 5: Student ID Card Generation with Branding & Signatures
     */
    public function test_student_id_card_generation_and_access_control(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'ID Card Candidate',
            'father_name' => 'Sohan Lal',
            'dob' => '2004-04-04',
            'gender' => 'male',
            'mobile' => '98999' . rand(10000, 99999),
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Boring Road',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 5000.00,
            'discount_amount' => 0.00,
            'net_fee' => 5000.00,
            'total_paid' => 5000.00,
            'status' => 'active',
        ]);

        // 1. Authorized franchise accesses ID card
        $response = $this->actingAs($this->centreUserA)->get(route('centre.students.id-card', $student->id));
        $response->assertStatus(200);
        $response->assertSee('ID Card Candidate');
        $response->assertSee($student->roll_no);
        $response->assertSee($student->registration_no);
        $response->assertSee('TECH ERA COMPUTER');
        $response->assertSee('Student Sign');
        $response->assertSee('Director Sign');

        // 2. Cross-franchise access is blocked
        $crossRes = $this->actingAs($this->centreUserB)->get(route('centre.students.id-card', $student->id));
        $crossRes->assertStatus(403);
    }

    /**
     * Requirement 6: Certificate Verification & Sensitive Data Privacy Protection
     */
    public function test_certificate_verification_and_privacy_masking(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/9999',
            'roll_no' => 'ROL99999',
            'full_name' => 'Verified Student Name',
            'father_name' => 'Public Father Name',
            'dob' => '2003-08-10',
            'gender' => 'female',
            'mobile' => '9876501234',
            'email' => 'private_student_email@secret.com',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Private House No 42, Secret Street',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Final Exam 2026',
            'exam_code' => 'EX-' . rand(1000, 9999),
            'session_name' => '2026-Session',
            'exam_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $result = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 88.00,
            'total_max_marks' => 100.00,
            'percentage' => 88.00,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        $certNo = 'TEC/CERT/2026/' . rand(10000, 99999);
        $certificate = Certificate::create([
            'certificate_no' => $certNo,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'exam_result_id' => $result->id,
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
            'security_hash' => hash('sha256', $certNo . $student->id),
        ]);

        // 1. Direct Certificate Verification page via QR link
        $verifyRes = $this->get(route('verify.cert.direct', ['cert_no' => $certNo]));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee($certNo);
        $verifyRes->assertSee('Verified Student Name');
        $verifyRes->assertSee('Alpha Learning Centre');
        $verifyRes->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');

        // Privacy verification: Sensitive contact details must NOT be displayed
        $verifyRes->assertDontSee('private_student_email@secret.com');
        $verifyRes->assertDontSee('Private House No 42, Secret Street');
        $verifyRes->assertDontSee('9876501234');

        // 2. Verification via POST form search
        $postVerify = $this->post(route('verify.certificate.submit'), [
            'registration' => $certNo,
        ]);
        $postVerify->assertStatus(200);
        $postVerify->assertSee('Verified Student Name');
    }

    /**
     * Requirement 7: Franchise Profile Permissions (Blocked for Franchise, Allowed for Admin)
     */
    public function test_franchise_cannot_update_profile_but_admin_can(): void
    {
        // 1. Franchise user attempts to update own profile -> 403 Forbidden
        $franchiseRes = $this->actingAs($this->centreUserA)->post(route('centre.profile.update'), [
            'centre_name' => 'Modified Centre Name Hack',
            'director_name' => 'Unauthorized Edit',
        ]);
        $franchiseRes->assertStatus(403);

        // 2. Main Admin updates centre profile -> Allowed (302 redirect with success)
        $adminRes = $this->actingAs($this->superAdmin)->put(route('admin.centres.update', $this->centreA->id), [
            'centre_name' => 'Authorized Updated Name',
            'centre_code' => $this->centreA->centre_code,
            'director_name' => 'Official Director Name',
            'email' => $this->centreA->email,
            'mobile' => $this->centreA->mobile,
            'address' => 'Updated Admin Address',
            'city' => 'Patna',
            'pincode' => '800001',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'affiliation_date' => $this->centreA->affiliation_date ?? now()->toDateString(),
            'status' => 'active',
        ]);
        $adminRes->assertSessionHas('success');

        $this->centreA->refresh();
        $this->assertEquals('Authorized Updated Name', $this->centreA->centre_name);
        $this->assertEquals('Official Director Name', $this->centreA->director_name);
    }

    /**
     * Requirement 8: Franchise Wallet Recharge, Signature Verification & Duplicate Prevention
     */
    public function test_franchise_wallet_recharge_flow_and_signature_verification(): void
    {
        $wallet = CentreWallet::where('centre_id', $this->centreA->id)->first();
        $startingBalance = (float) $wallet->balance;

        // 1. Initiate Recharge Order via JSON request
        $initiateRes = $this->actingAs($this->centreUserA)->postJson(route('centre.wallet.recharge.initiate'), [
            'amount' => 1500,
        ]);
        $initiateRes->assertStatus(200);
        $initiateData = $initiateRes->json();
        $this->assertNotEmpty($initiateData['order_id']);

        $orderId = $initiateData['order_id'];

        // Assert pending transaction created
        $this->assertDatabaseHas('online_transactions', [
            'order_id' => $orderId,
            'centre_id' => $this->centreA->id,
            'amount' => 1500.00,
            'status' => 'pending',
        ]);

        // 2. Tampered signature attempt -> rejected with error
        $fakePaymentId = 'pay_test_' . rand(10000, 99999);
        $tamperedRes = $this->actingAs($this->centreUserA)->post(route('centre.wallet.recharge.verify'), [
            'order_id' => $orderId,
            'gateway_payment_id' => $fakePaymentId,
            'gateway_signature' => 'invalid_tampered_signature_string',
        ]);
        $tamperedRes->assertRedirect(route('centre.wallet.index'));
        $tamperedRes->assertSessionHas('error');

        $this->assertDatabaseHas('online_transactions', [
            'order_id' => $orderId,
            'status' => 'failed',
        ]);

        // 3. Valid Server-Generated Signature (re-attempt with valid signature)
        // Reset transaction back to pending for valid test attempt
        OnlineTransaction::where('order_id', $orderId)->update(['status' => 'pending']);

        $gateway = app(PaymentGatewayService::class);
        $validSignature = $gateway->generateSignature($orderId, $fakePaymentId);

        $verifyRes = $this->actingAs($this->centreUserA)->post(route('centre.wallet.recharge.verify'), [
            'order_id' => $orderId,
            'gateway_payment_id' => $fakePaymentId,
            'gateway_signature' => $validSignature,
        ]);
        $verifyRes->assertRedirect(route('centre.wallet.index'));
        $verifyRes->assertSessionHas('success');

        // Assert transaction marked successful
        $this->assertDatabaseHas('online_transactions', [
            'order_id' => $orderId,
            'gateway_payment_id' => $fakePaymentId,
            'status' => 'success',
        ]);

        // Assert wallet balance credited
        $wallet->refresh();
        $this->assertEquals($startingBalance + 1500.00, (float) $wallet->balance);

        // Assert CentreWalletTransaction logged
        $this->assertDatabaseHas('centre_wallet_transactions', [
            'centre_id' => $this->centreA->id,
            'type' => 'credit',
            'amount' => 1500.00,
        ]);

        // 4. Duplicate verify request (Idempotency) -> returns success without double-crediting
        $duplicateRes = $this->actingAs($this->centreUserA)->post(route('centre.wallet.recharge.verify'), [
            'order_id' => $orderId,
            'gateway_payment_id' => $fakePaymentId,
            'gateway_signature' => $validSignature,
        ]);
        $duplicateRes->assertRedirect(route('centre.wallet.index'));

        $wallet->refresh();
        // Balance must remain initial + 1500, NOT + 3000!
        $this->assertEquals($startingBalance + 1500.00, (float) $wallet->balance);
    }
}
