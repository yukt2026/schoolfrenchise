<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Attendance;
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
     * Requirement 5: Redesigned Student ID Card (CR80 Portrait, Dynamic Branding & QR Code)
     */
    public function test_student_id_card_generation_and_access_control(): void
    {
        $studentA = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG/' . $this->centreA->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Aman Kumar Student A',
            'father_name' => 'Sohan Lal',
            'mother_name' => 'Geeta Devi',
            'dob' => '2004-04-04',
            'gender' => 'male',
            'mobile' => '98999' . rand(10000, 99999),
            'email' => 'student_a@example.com',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'address' => 'Boring Road',
            'pincode' => '800001',
            'status' => 'enrolled',
            'id_card_status' => 'valid',
        ]);

        $studentB = Student::create([
            'centre_id' => $this->centreB->id,
            'registration_no' => 'REG/' . $this->centreB->centre_code . '/' . rand(100, 999),
            'roll_no' => 'ROL' . rand(10000, 99999),
            'full_name' => 'Ravi Sharma Student B',
            'father_name' => 'Mohan Sharma',
            'mother_name' => 'Radha Sharma',
            'dob' => '2003-05-15',
            'gender' => 'male',
            'mobile' => '98777' . rand(10000, 99999),
            'email' => 'student_b@example.com',
            'state_id' => $this->stateUP->id,
            'district_id' => $this->districtLucknow->id,
            'city' => 'Lucknow',
            'address' => 'Hazratganj',
            'pincode' => '226001',
            'status' => 'enrolled',
            'id_card_status' => 'valid',
        ]);

        Admission::create([
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 5000.00,
            'discount_amount' => 0.00,
            'net_fee' => 5000.00,
            'total_paid' => 5000.00,
            'status' => 'active',
        ]);

        Admission::create([
            'student_id' => $studentB->id,
            'centre_id' => $this->centreB->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 5000.00,
            'discount_amount' => 0.00,
            'net_fee' => 5000.00,
            'total_paid' => 5000.00,
            'status' => 'active',
        ]);

        // 1. Franchise A generates ID Card: Franchise A name appears & TECH ERA COMPUTER authorization branding
        $responseA = $this->actingAs($this->centreUserA)->get(route('centre.students.id-card', $studentA->id));
        $responseA->assertStatus(200);
        $responseA->assertSee($this->centreA->centre_name);
        $responseA->assertSee('AMAN KUMAR STUDENT A');
        $responseA->assertSee($studentA->roll_no);
        $responseA->assertSee('Authorized by: TECH ERA COMPUTER');
        $responseA->assertSee('TERMS AND CONDITIONS');
        $responseA->assertSee('H.O. Signature');
        $responseA->assertSee('Authorized Signatory');

        // 2. Franchise B generates ID Card: Franchise B name appears & TECH ERA COMPUTER authorization branding
        $responseB = $this->actingAs($this->centreUserB)->get(route('centre.students.id-card', $studentB->id));
        $responseB->assertStatus(200);
        $responseB->assertSee($this->centreB->centre_name);
        $responseB->assertSee('RAVI SHARMA STUDENT B');
        $responseB->assertSee('Authorized by: TECH ERA COMPUTER');

        // 3. Unique QR tokens: Students have distinct unique verification tokens and QR URLs
        $this->assertNotEmpty($studentA->verification_token);
        $this->assertNotEmpty($studentB->verification_token);
        $this->assertNotEquals($studentA->verification_token, $studentB->verification_token);
        $this->assertNotEquals($studentA->qr_code_data_uri, $studentB->qr_code_data_uri);

        // 4. Cross-franchise ID card access is strictly blocked (IDOR Protection)
        $crossRes = $this->actingAs($this->centreUserB)->get(route('centre.students.id-card', $studentA->id));
        $crossRes->assertStatus(403);

        // 5. Online Verification: Valid Student
        $verifyValid = $this->get(route('verify.student', $studentA->verification_token));
        $verifyValid->assertStatus(200);
        $verifyValid->assertSee('VALID STUDENT CREDENTIAL');
        $verifyValid->assertSee($studentA->full_name);
        $verifyValid->assertSee($this->centreA->centre_name);
        $verifyValid->assertSee('TECH ERA COMPUTER');

        // 6. Online Verification: Expired Student
        $studentA->id_card_expires_at = now()->subDays(10)->toDateString();
        $studentA->save();
        $verifyExpired = $this->get(route('verify.student', $studentA->verification_token));
        $verifyExpired->assertStatus(200);
        $verifyExpired->assertSee('ID CARD EXPIRED');

        // 7. Online Verification: Revoked Student
        $studentA->id_card_status = 'revoked';
        $studentA->save();
        $verifyRevoked = $this->get(route('verify.student', $studentA->verification_token));
        $verifyRevoked->assertStatus(200);
        $verifyRevoked->assertSee('ID CARD REVOKED / SUSPENDED');

        // 8. Online Verification: Invalid / Non-existent Token
        $verifyInvalid = $this->get(route('verify.student', 'INVALID-NONEXISTENT-TOKEN-999'));
        $verifyInvalid->assertStatus(200);
        $verifyInvalid->assertSee('INVALID VERIFICATION RECORD');
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

    /**
     * Requirement: Dynamic Franchise-Specific Branding & Fixed Tech Era Authorization on Documents
     */
    public function test_dynamic_franchise_branding_on_certificate_and_marksheet(): void
    {
        // 1. Create Student & Exam Result under Centre A
        $studentA = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG-ALP-' . rand(1000, 9999),
            'roll_no' => 'ROLL-ALP-' . rand(1000, 9999),
            'full_name' => 'Aarav Kumar',
            'father_name' => 'Sanjay Kumar',
            'dob' => '2004-05-12',
            'gender' => 'male',
            'mobile' => '9871112233',
            'email' => 'aarav@example.com',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'pincode' => '800001',
            'address' => 'Alpha Colony, Patna',
            'status' => 'completed',
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Final Examination 2026',
            'exam_code' => 'EX-' . rand(1000, 9999),
            'session_name' => '2026-Session',
            'exam_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'total_marks' => 100,
            'pass_marks' => 40,
            'status' => 'published',
        ]);

        $resultA = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 88.00,
            'total_max_marks' => 100.00,
            'percentage' => 88.00,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        $certA = Certificate::create([
            'exam_result_id' => $resultA->id,
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'CERT-ALP-2026-001',
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        $marksheetA = Marksheet::create([
            'exam_result_id' => $resultA->id,
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'marksheet_no' => 'MS-ALP-2026-001',
            'issue_date' => now()->toDateString(),
            'is_issued' => true,
        ]);

        // 2. View Certificate under Franchise A
        $certResA = $this->actingAs($this->centreUserA)->get(route('centre.downloads.student-certificates.show', $certA->id));
        $certResA->assertStatus(200);
        $certResA->assertSee($this->centreA->centre_name);
        $certResA->assertSee('AUTHORIZED BY: TECH ERA COMPUTER');
        $certResA->assertSee($studentA->full_name);
        $certResA->assertSee($certA->certificate_no);
        $certResA->assertSee('Scan to Verify');

        // 3. View Marksheet under Franchise A
        $msResA = $this->actingAs($this->centreUserA)->get(route('centre.exams.results.marksheet', $resultA->id));
        $msResA->assertStatus(200);
        $msResA->assertSee($this->centreA->centre_name);
        $msResA->assertSee('AUTHORIZED BY: TECH ERA COMPUTER');
        $msResA->assertSee($studentA->full_name);
        $msResA->assertSee('STATEMENT OF MARKS');
        $msResA->assertSee('88'); // Percentage / marks

        // 4. Create Student & Exam Result under Centre B
        $studentB = Student::create([
            'centre_id' => $this->centreB->id,
            'registration_no' => 'REG-BET-' . rand(1000, 9999),
            'roll_no' => 'ROLL-BET-' . rand(1000, 9999),
            'full_name' => 'Bhavna Sharma',
            'father_name' => 'Rajesh Sharma',
            'dob' => '2005-02-18',
            'gender' => 'female',
            'mobile' => '9872223344',
            'email' => 'bhavna@example.com',
            'state_id' => $this->stateUP->id,
            'district_id' => $this->districtLucknow->id,
            'city' => 'Lucknow',
            'pincode' => '226001',
            'address' => 'Beta Avenue, Lucknow',
            'status' => 'completed',
        ]);

        $resultB = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $studentB->id,
            'centre_id' => $this->centreB->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 76.00,
            'total_max_marks' => 100.00,
            'percentage' => 76.00,
            'grade' => 'A',
            'result_status' => 'passed',
        ]);

        $certB = Certificate::create([
            'exam_result_id' => $resultB->id,
            'student_id' => $studentB->id,
            'centre_id' => $this->centreB->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'CERT-BET-2026-002',
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        // 5. View Certificate under Franchise B -> Displays Centre B branding & Fixed Tech Era Computer authorization
        $certResB = $this->actingAs($this->centreUserB)->get(route('centre.downloads.student-certificates.show', $certB->id));
        $certResB->assertStatus(200);
        $certResB->assertSee($this->centreB->centre_name);
        $certResB->assertDontSee($this->centreA->centre_name);
        $certResB->assertSee('AUTHORIZED BY: TECH ERA COMPUTER');
        $certResB->assertSee($studentB->full_name);
    }

    /**
     * Requirement: Real QR-based Online Verification & Privacy Masking
     */
    public function test_online_document_verification_via_qr_token(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG-QR-' . rand(1000, 9999),
            'roll_no' => 'ROLL-QR-' . rand(1000, 9999),
            'full_name' => 'Rohan Varma',
            'father_name' => 'Mahesh Varma',
            'dob' => '2003-10-10',
            'gender' => 'male',
            'mobile' => '9899988877',
            'email' => 'rohan.secret@example.com',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'pincode' => '800001',
            'address' => 'Private House 123',
            'status' => 'completed',
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Evaluation Exam',
            'exam_code' => 'EX-' . rand(1000, 9999),
            'session_name' => '2026-Session',
            'exam_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'total_marks' => 100,
            'pass_marks' => 40,
            'status' => 'published',
        ]);

        $result = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 91.00,
            'total_max_marks' => 100.00,
            'percentage' => 91.00,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        $cert = Certificate::create([
            'exam_result_id' => $result->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'CERT-VERIFY-101',
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        $marksheet = Marksheet::create([
            'exam_result_id' => $result->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'marksheet_no' => 'MS-VERIFY-101',
            'issue_date' => now()->toDateString(),
            'is_issued' => true,
        ]);

        // 1. Verify Certificate Token URL
        $this->assertNotEmpty($cert->verification_token);
        $verifyRes = $this->get(route('verify.certificate.token', ['token' => $cert->verification_token]));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');
        $verifyRes->assertSee('OFFICIALLY VERIFIED & ACTIVE');
        $verifyRes->assertSee($student->full_name);
        $verifyRes->assertSee($this->centreA->centre_name);
        $verifyRes->assertSee('AUTHORIZED BY: TECH ERA COMPUTER');
        $verifyRes->assertSee($cert->certificate_no);

        // Assert sensitive privacy fields are NOT exposed on public verification
        $verifyRes->assertDontSee($student->mobile);
        $verifyRes->assertDontSee($student->email);

        // 2. Verify Marksheet Token URL
        $this->assertNotEmpty($marksheet->verification_token);
        $msVerifyRes = $this->get(route('verify.marksheet.token', ['token' => $marksheet->verification_token]));
        $msVerifyRes->assertStatus(200);
        $msVerifyRes->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');
        $msVerifyRes->assertSee($student->full_name);
        $msVerifyRes->assertSee($marksheet->marksheet_no);

        // 3. Revoked Certificate displays Revoked status
        $cert->update(['status' => 'revoked']);
        $revokedRes = $this->get(route('verify.certificate.token', ['token' => $cert->verification_token]));
        $revokedRes->assertStatus(200);
        $revokedRes->assertSee('REVOKED');

        // 4. Unknown token displays No Genuine Record Found
        $invalidRes = $this->get(route('verify.certificate.token', ['token' => 'NON-EXISTENT-TOKEN']));
        $invalidRes->assertStatus(200);
        $invalidRes->assertSee('No Genuine Record Found');
    }

    /**
     * Requirement: Cross-Franchise Document Access Isolation (IDOR Protection)
     */
    public function test_cross_franchise_document_access_is_blocked(): void
    {
        $studentA = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'REG-ISO-' . rand(1000, 9999),
            'roll_no' => 'ROLL-ISO-' . rand(1000, 9999),
            'full_name' => 'Isolation Test Student',
            'father_name' => 'Parent Name',
            'dob' => '2004-01-01',
            'gender' => 'male',
            'mobile' => '9888877777',
            'email' => 'iso@example.com',
            'state_id' => $this->stateBihar->id,
            'district_id' => $this->districtPatna->id,
            'city' => 'Patna',
            'pincode' => '800001',
            'address' => 'Alpha Street',
            'status' => 'completed',
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Isolation Exam',
            'exam_code' => 'EX-' . rand(1000, 9999),
            'session_name' => '2026-Session',
            'exam_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'total_marks' => 100,
            'pass_marks' => 40,
            'status' => 'published',
        ]);

        $resultA = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 85.00,
            'total_max_marks' => 100.00,
            'percentage' => 85.00,
            'grade' => 'A',
            'result_status' => 'passed',
        ]);

        $certA = Certificate::create([
            'exam_result_id' => $resultA->id,
            'student_id' => $studentA->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'CERT-ISO-001',
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        // Franchise User B attempting to access Franchise A's certificate -> 403 Forbidden
        $resCert = $this->actingAs($this->centreUserB)->get(route('centre.downloads.student-certificates.show', $certA->id));
        $resCert->assertStatus(403);

        // Franchise User B attempting to access Franchise A's marksheet -> 403 Forbidden
        $resMs = $this->actingAs($this->centreUserB)->get(route('centre.exams.results.marksheet', $resultA->id));
        $resMs->assertStatus(403);
    }

    /**
     * Test Franchise Wallet Dynamic UPI QR Generation, UTR & Screenshot Request Submission,
     * and Admin Verification & Approval Workflow.
     */
    public function test_franchise_wallet_dynamic_upi_qr_recharge_and_admin_approval_workflow(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // Configure Admin UPI Settings
        \App\Models\Setting::set('admin_upi_id', 'techeracomputer@upi', 'payment');
        \App\Models\Setting::set('admin_upi_name', 'TECH ERA COMPUTER', 'payment');

        $wallet = CentreWallet::firstOrCreate(
            ['centre_id' => $this->centreA->id],
            ['balance' => 3000.00, 'total_credited' => 3000.00, 'status' => 'active']
        );
        $wallet->update(['balance' => 3000.00]);
        $initialBalance = 3000.00;

        // 1. Franchise visits Top-up Page
        $topupPageRes = $this->actingAs($this->centreUserA)->get(route('centre.wallet.topup'));
        $topupPageRes->assertStatus(200);
        $topupPageRes->assertSee('techeracomputer@upi');
        $topupPageRes->assertSee('TECH ERA COMPUTER');
        $topupPageRes->assertSee('Transaction / UTR Reference ID');
        $topupPageRes->assertSee('Upload Payment Screenshot / Slip');

        // 2. Dynamic QR AJAX Endpoint for ₹2500
        $qrJsonRes = $this->actingAs($this->centreUserA)->getJson(route('centre.wallet.qr', ['amount' => 2500]));
        $qrJsonRes->assertStatus(200);
        $qrData = $qrJsonRes->json();
        $this->assertEquals(2500, $qrData['amount']);
        $this->assertStringContainsString('techeracomputer%40upi', $qrData['upi_uri']);
        $this->assertStringContainsString('am=2500.00', $qrData['upi_uri']);
        $this->assertStringContainsString('<svg', $qrData['qr_svg']);

        // 3. Franchise makes payment and submits request with screenshot & UTR
        $fakeScreenshot = \Illuminate\Http\UploadedFile::fake()->image('phonepe_success.png', 600, 800);
        $submitRes = $this->actingAs($this->centreUserA)->post(route('centre.wallet.topup.store'), [
            'amount' => 2500.00,
            'payment_mode' => 'upi',
            'reference_no' => 'UPI-UTR-9988776611',
            'payment_slip' => $fakeScreenshot,
            'notes' => 'Topup for 10 new student kits',
        ]);

        $submitRes->assertRedirect(route('centre.wallet.index'));
        $submitRes->assertSessionHas('success');

        // Assert transaction was recorded as PENDING
        $txn = CentreWalletTransaction::where('reference_no', 'UPI-UTR-9988776611')->first();
        $this->assertNotNull($txn);
        $this->assertEquals('pending', $txn->status);
        $this->assertEquals(2500.00, (float) $txn->amount);
        $this->assertNotNull($txn->payment_slip);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($txn->payment_slip);

        // CRUCIAL: Wallet balance must NOT be credited automatically!
        $wallet->refresh();
        $this->assertEquals($initialBalance, (float) $wallet->balance, 'Wallet must NOT be credited before Admin approval!');

        // 4. Admin views Wallet Index and sees pending request with screenshot preview
        $adminIndexRes = $this->actingAs($this->superAdmin)->get(route('admin.wallet.index'));
        $adminIndexRes->assertStatus(200);
        $adminIndexRes->assertSee('UPI-UTR-9988776611');
        $adminIndexRes->assertSee('₹2,500.00');
        $adminIndexRes->assertSee($this->centreA->centre_name);

        // 5. Admin Approves the request
        $approveRes = $this->actingAs($this->superAdmin)->post(route('admin.wallet.transactions.approve', $txn), [
            'remarks' => 'Verified with SBI current a/c credit statement',
        ]);
        $approveRes->assertRedirect();

        // Assert transaction is approved and wallet balance is credited
        $txn->refresh();
        $this->assertEquals('approved', $txn->status);
        $this->assertEquals($this->superAdmin->id, $txn->approved_by);
        $this->assertEquals(5500.00, (float) $txn->balance_after);

        $wallet->refresh();
        $this->assertEquals(5500.00, (float) $wallet->balance);

        // 6. Test Admin Rejection Flow on another request
        $rejectScreenshot = \Illuminate\Http\UploadedFile::fake()->image('invalid_slip.png', 400, 400);
        $this->actingAs($this->centreUserA)->post(route('centre.wallet.topup.store'), [
            'amount' => 1500.00,
            'payment_mode' => 'upi',
            'reference_no' => 'FAKE-UTR-000000',
            'payment_slip' => $rejectScreenshot,
        ]);

        $failTxn = CentreWalletTransaction::where('reference_no', 'FAKE-UTR-000000')->first();
        $this->assertEquals('pending', $failTxn->status);

        // Admin rejects request with reason
        $rejectRes = $this->actingAs($this->superAdmin)->post(route('admin.wallet.transactions.reject', $failTxn), [
            'reason' => 'Payment not credited to Head Office SBI bank account.',
        ]);
        $rejectRes->assertRedirect();

        $failTxn->refresh();
        $this->assertEquals('rejected', $failTxn->status);
        $this->assertStringContainsString('Payment not credited', $failTxn->admin_remarks);

        // Balance should still be 5500.00 (unchanged)
        $wallet->refresh();
        $this->assertEquals(5500.00, (float) $wallet->balance);
    }

    /**
     * Test franchise date-wise attendance report, view modes, and CSV export.
     */
    public function test_franchise_date_wise_attendance_report_and_csv_export(): void
    {
        $batch = Batch::create([
            'centre_id'   => $this->centreA->id,
            'course_id'   => $this->course->id,
            'batch_name'  => 'Morning Coding Batch',
            'start_time'  => '08:00:00',
            'end_time'    => '10:00:00',
            'max_students'=> 30,
            'status'      => 'active',
        ]);

        $student = Student::create([
            'centre_id'       => $this->centreA->id,
            'registration_no' => 'REG-ATT-' . rand(1000, 9999),
            'roll_no'         => 'ROL-ATT-' . rand(1000, 9999),
            'full_name'       => 'Attendance Test Student',
            'father_name'     => 'Father Test',
            'mobile'          => '9888812345',
            'gender'          => 'male',
            'dob'             => '2004-01-01',
            'state_id'        => $this->stateBihar->id,
            'district_id'     => $this->districtPatna->id,
            'city'            => 'Patna',
            'address'         => 'Test St',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        Admission::create([
            'student_id'      => $student->id,
            'centre_id'       => $this->centreA->id,
            'course_id'       => $this->course->id,
            'batch_id'        => $batch->id,
            'admission_date'  => now()->toDateString(),
            'course_fee'      => 4000.00,
            'discount_amount' => 0.00,
            'net_fee'         => 4000.00,
            'total_paid'      => 4000.00,
            'status'          => 'active',
        ]);

        // Record attendance on 2 distinct dates
        Attendance::create([
            'centre_id'       => $this->centreA->id,
            'batch_id'        => $batch->id,
            'student_id'      => $student->id,
            'attendance_date' => now()->subDay()->toDateString(),
            'status'          => 'present',
            'remarks'         => 'On time',
        ]);

        Attendance::create([
            'centre_id'       => $this->centreA->id,
            'batch_id'        => $batch->id,
            'student_id'      => $student->id,
            'attendance_date' => now()->toDateString(),
            'status'          => 'absent',
            'remarks'         => 'Sick leave',
        ]);

        // 1. Test Daily Log view mode
        $logRes = $this->actingAs($this->centreUserA)->get(route('centre.batches.attendance.report', [
            'batch_id'  => $batch->id,
            'date_from' => now()->subDays(2)->toDateString(),
            'date_to'   => now()->toDateString(),
            'view'      => 'daily_log',
        ]));
        $logRes->assertStatus(200);
        $logRes->assertSee('Attendance Test Student');
        $logRes->assertSee('Present');
        $logRes->assertSee('Absent');
        $logRes->assertSee('On time');

        // 2. Test CSV export
        $exportRes = $this->actingAs($this->centreUserA)->get(route('centre.batches.attendance.report.export', [
            'batch_id'  => $batch->id,
            'date_from' => now()->subDays(2)->toDateString(),
            'date_to'   => now()->toDateString(),
        ]));
        $exportRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $exportRes->headers->get('Content-Type'));
        $content = $exportRes->streamedContent();
        $this->assertStringContainsString('Attendance Test Student', $content);
        $this->assertStringContainsString('PRESENT', $content);
        $this->assertStringContainsString('ABSENT', $content);
    }
}
