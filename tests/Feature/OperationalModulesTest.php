<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\District;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\FranchiseApplication;
use App\Models\MarketingExpense;
use App\Models\Marksheet;
use App\Models\Question;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationalModulesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $centreUser;
    protected Centre $centre;
    protected User $studentUser;
    protected Student $student;
    protected Course $course;
    protected State $state;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        $this->state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);
        $this->district = District::where('state_id', $this->state->id)->first() ?? District::create(
            ['name' => 'Patna', 'state_id' => $this->state->id, 'status' => true]
        );

        // Super Admin
        $this->superAdmin = User::create([
            'name'      => 'Super Administrator',
            'email'     => 'admin_ops_' . uniqid() . '@drishti.com',
            'mobile'    => '9801' . rand(100000, 999999),
            'password'  => Hash::make('password123'),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        // Centre & Director
        $this->centreUser = User::create([
            'name'      => 'Director Operational',
            'email'     => 'centre_ops_' . uniqid() . '@drishti.com',
            'mobile'    => '9802' . rand(100000, 999999),
            'password'  => Hash::make('password123'),
            'role'      => 'centre',
            'is_active' => true,
        ]);
        $this->centreUser->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->centre = Centre::create([
            'user_id'          => $this->centreUser->id,
            'centre_code'      => 'DCE-OP-' . strtoupper(substr(uniqid(), -4)) . rand(100, 999),
            'centre_name'      => 'Drishti Operations Centre',
            'director_name'    => 'Director Operational',
            'email'            => $this->centreUser->email,
            'mobile'           => $this->centreUser->mobile,
            'address'          => 'Main Road, Boring Canal',
            'city'             => 'Patna',
            'pincode'          => '800001',
            'lab_capacity'     => 25,
            'affiliation_date' => now()->toDateString(),
            'valid_upto'       => now()->addYear()->toDateString(),
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'status'           => 'active',
        ]);
        $this->centreUser->update(['centre_id' => $this->centre->id]);

        // Course & Subject
        $this->course = Course::create([
            'course_name'        => 'Advanced Diploma in Computer Application',
            'course_code'        => 'ADCA-' . rand(100, 999),
            'slug'               => 'adca-ops-' . uniqid(),
            'duration'           => '12 Months',
            'duration_months'    => 12,
            'eligibility'        => '12th Pass',
            'default_course_fee' => 12000.00,
            'status'             => true,
        ]);

        CourseSubject::create([
            'course_id'       => $this->course->id,
            'subject_code'    => 'SUB-CF-' . rand(100, 999),
            'subject_name'    => 'Computer Fundamentals',
            'theory_max'      => 70,
            'practical_max'   => 20,
            'viva_max'        => 10,
            'pass_marks'      => 40,
            'total_max'       => 100,
        ]);

        // Student & User
        $this->studentUser = User::create([
            'name'      => 'Amit Sharma',
            'email'     => 'amit_ops_' . uniqid() . '@example.com',
            'mobile'    => '9803' . rand(100000, 999999),
            'password'  => Hash::make('password123'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        $this->studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $this->student = Student::create([
            'user_id'         => $this->studentUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/' . $this->centre->centre_code . '/099',
            'roll_no'         => 'DCE26' . rand(10000, 99999),
            'full_name'       => 'Amit Sharma',
            'father_name'     => 'Ramakant Sharma',
            'dob'             => '2004-05-15',
            'gender'          => 'male',
            'mobile'          => $this->studentUser->mobile,
            'state_id'        => $this->state->id,
            'district_id'     => $this->district->id,
            'city'            => 'Patna',
            'address'         => 'Rajendra Nagar',
            'pincode'         => '800016',
            'status'          => 'enrolled',
        ]);
    }

    // ==========================================
    // 1. FRANCHISE WORKFLOW & STATUS TESTS
    // ==========================================

    public function test_franchise_application_submission_generates_tracking_number_and_status_lookup(): void
    {
        $mobile = '98' . rand(10000000, 99999999);
        $payload = [
            'applicant_name'   => 'Deepak Jha',
            'centre_name'      => 'Drishti Study Centre Muzaffarpur',
            'mobile'           => $mobile,
            'email'            => 'deepak_' . uniqid() . '@gmail.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Muzaffarpur',
            'address'          => 'Station Road, Ward 12',
            'qualification'    => 'MCA, B.Sc IT',
            'computers_count'  => 15,
            'carpet_area_sqft' => 850,
            'remarks'          => 'Prime location commercial space with power backup.',
        ];

        // 1. Submit Franchise Application
        $response = $this->post(route('franchise.apply.submit'), $payload);
        $response->assertRedirect(route('franchise.status'));
        $response->assertSessionHas('success');

        $app = FranchiseApplication::where('mobile', $mobile)->first();
        $this->assertNotNull($app);
        $this->assertStringStartsWith('FR-' . date('Y') . '-', $app->application_no);
        $this->assertEquals('pending', $app->status);

        // 2. Public Status Lookup by Tracking ID
        $statusResp = $this->post(route('franchise.status.submit'), [
            'search_query' => $app->application_no,
        ]);
        $statusResp->assertStatus(200);
        $statusResp->assertSee($app->application_no);
        $statusResp->assertSee('Drishti Study Centre Muzaffarpur');
        $statusResp->assertSee('Deepak Jha');

        // 3. Search Approved Centres
        $searchResp = $this->get(route('franchise.search', ['keyword' => 'Drishti Operations Centre']));
        $searchResp->assertStatus(200);
        $searchResp->assertSee($this->centre->centre_name);
        $searchResp->assertSee($this->centre->centre_code);
    }

    public function test_admin_franchise_approval_provisions_centre_and_director_account(): void
    {
        $app = FranchiseApplication::create([
            'application_no'   => 'FR-2026-TESTAPP',
            'applicant_name'   => 'Suresh Kumar',
            'centre_name'      => 'Drishti Computer Centre Gaya',
            'mobile'           => '9812' . rand(100000, 999999),
            'email'            => 'suresh_' . uniqid() . '@drishti.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Gaya',
            'address'          => 'Civil Lines Road',
            'qualification'    => 'B.Tech CS',
            'computers_count'  => 20,
            'carpet_area_sqft' => 1200,
            'status'           => 'pending',
        ]);

        $approvePayload = [
            'centre_code'  => 'DCE-GY-' . rand(1000, 9999),
            'lab_capacity' => 20,
            'valid_upto'   => now()->addYear()->toDateString(),
            'remarks'      => 'Centre approved after premises inspection',
        ];

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.centres.applications.approve', $app->id),
            $approvePayload
        );

        $response->assertSessionHas('success');
        $app->refresh();
        $this->assertEquals('approved', $app->status);

        $this->assertDatabaseHas('centres', [
            'centre_code'   => $approvePayload['centre_code'],
            'director_name' => 'Suresh Kumar',
            'status'        => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $app->email,
            'role'  => 'centre',
        ]);
    }

    // ==========================================
    // 2. CERTIFICATE, MARKSHEET & VERIFICATION TESTS
    // ==========================================

    public function test_certificate_and_marksheet_generation_with_qr_and_privacy_protected_verification(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Diploma Final Examination 2026',
            'exam_code'    => 'EXM-OP-' . rand(1000, 9999),
            'session_name' => 'Session 2026',
            'exam_date'    => now()->subDays(15)->toDateString(),
            'status'       => 'published',
        ]);

        $examResult = ExamResult::create([
            'exam_id'              => $exam->id,
            'student_id'           => $this->student->id,
            'centre_id'            => $this->centre->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 88.00,
            'total_max_marks'      => 100.00,
            'percentage'           => 88.00,
            'grade'                => 'A+',
            'result_status'        => 'passed',
        ]);

        // 1. Generate Certificate & Marksheet
        $certPayload = [
            'exam_result_id' => $examResult->id,
            'issue_date'     => now()->toDateString(),
        ];

        $createResp = $this->actingAs($this->superAdmin)->post(route('admin.certificates.store'), $certPayload);
        $createResp->assertSessionHas('success');

        $cert = Certificate::where('exam_result_id', $examResult->id)->first();
        $this->assertNotNull($cert);
        $this->assertStringStartsWith('DCE/CERT/' . date('Y') . '/', $cert->certificate_no);
        $this->assertEquals('issued', $cert->status);

        $marksheet = Marksheet::where('exam_result_id', $examResult->id)->first();
        $this->assertNotNull($marksheet);
        $this->assertStringStartsWith('DCE/MS/' . date('Y') . '/', $marksheet->marksheet_no);

        // 2. Public Verification Search
        $verifyResp = $this->post(route('verify.certificate.submit'), [
            'registration' => $cert->certificate_no,
        ]);
        $verifyResp->assertStatus(200);
        $verifyResp->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');
        $verifyResp->assertSee('Amit Sharma');
        $verifyResp->assertSee($cert->certificate_no);

        // 3. Privacy check: Contact details (phone, email, full address) are NOT exposed on public verification
        $verifyResp->assertDontSee($this->student->mobile);
        $verifyResp->assertDontSee($this->studentUser->email);
        $verifyResp->assertDontSee($this->student->address);

        // 4. Revocation and Reissue Workflow with Audit Log
        $revokeResp = $this->actingAs($this->superAdmin)->post(route('admin.certificates.revoke', $cert->id), [
            'reason' => 'Candidate duplicate records check initiated',
        ]);
        $revokeResp->assertSessionHas('success');
        $cert->refresh();
        $this->assertEquals('revoked', $cert->status);

        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'certificate_revoked',
            'auditable_type' => Certificate::class,
            'auditable_id'   => $cert->id,
        ]);

        $reissueResp = $this->actingAs($this->superAdmin)->post(route('admin.certificates.reissue', $cert->id));
        $reissueResp->assertSessionHas('success');
        $cert->refresh();
        $this->assertEquals('issued', $cert->status);
    }

    // ==========================================
    // 3. EXAMS, ATTENDANCE & QUESTION BANK TESTS
    // ==========================================

    public function test_batch_schedule_attendance_corrections_and_question_bank(): void
    {
        // 1. Batch & Schedule
        $batch = Batch::create([
            'centre_id'  => $this->centre->id,
            'course_id'  => $this->course->id,
            'batch_name' => 'Evening DCA Batch Ops',
            'start_time' => '16:00',
            'end_time'   => '18:00',
            'days'       => 'Mon-Fri',
            'status'     => 'active',
        ]);

        $admission = Admission::create([
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'batch_id'       => $batch->id,
            'admission_date' => now()->toDateString(),
            'course_fee'     => 12000.00,
            'discount_amount'=> 0.00,
            'net_fee'        => 12000.00,
            'total_paid'     => 0.00,
            'status'         => 'active',
        ]);

        // 2. Mark Initial Attendance
        $today = now()->toDateString();
        $attResp1 = $this->actingAs($this->superAdmin)->post(route('admin.batches.attendance.mark', $batch->id), [
            'attendance_date' => $today,
            'attendance'      => [$this->student->id => 'absent'],
            'remarks'         => [$this->student->id => 'Uninformed absent'],
        ]);
        $attResp1->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'batch_id'   => $batch->id,
            'student_id' => $this->student->id,
            'status'     => 'absent',
        ]);

        // 3. Correct Attendance (Correction permission)
        $attResp2 = $this->actingAs($this->superAdmin)->post(route('admin.batches.attendance.mark', $batch->id), [
            'attendance_date' => $today,
            'attendance'      => [$this->student->id => 'present'],
            'remarks'         => [$this->student->id => 'Late arrival verified by instructor'],
        ]);
        $attResp2->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'batch_id'   => $batch->id,
            'student_id' => $this->student->id,
            'status'     => 'present',
            'remarks'    => 'Late arrival verified by instructor',
        ]);

        // 4. Question Bank Management
        $qPayload = [
            'course_id'      => $this->course->id,
            'question_text'  => 'Which unit of the computer performs arithmetic and logical operations?',
            'option_a'       => 'Control Unit (CU)',
            'option_b'       => 'Arithmetic Logic Unit (ALU)',
            'option_c'       => 'Random Access Memory (RAM)',
            'option_d'       => 'Hard Disk Drive (HDD)',
            'correct_option' => 'b',
            'marks'          => 2,
            'difficulty'     => 'easy',
            'status'         => true,
        ];

        $qResp = $this->actingAs($this->superAdmin)->post(route('admin.exams.questions.store'), $qPayload);
        $qResp->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'course_id'      => $this->course->id,
            'correct_option' => 'b',
            'difficulty'     => 'easy',
        ]);
    }

    public function test_result_publication_restrictions_block_student_from_unpublished_results(): void
    {
        $examOngoing = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Ongoing Semester Exam',
            'exam_code'    => 'EXM-ONGOING-' . rand(100, 999),
            'session_name' => '2026',
            'exam_date'    => now()->toDateString(),
            'status'       => 'ongoing', // NOT published
        ]);

        $evalResult = ExamResult::create([
            'exam_id'              => $examOngoing->id,
            'student_id'           => $this->student->id,
            'centre_id'            => $this->centre->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 85.00,
            'total_max_marks'      => 100.00,
            'percentage'           => 85.00,
            'grade'                => 'A',
            'result_status'        => 'passed',
        ]);

        // Student tries to view results list: unpublished exam result is filtered out
        $resListResp = $this->actingAs($this->studentUser)->get(route('student.results'));
        $resListResp->assertStatus(200);
        $resListResp->assertDontSee('Ongoing Semester Exam');

        // Direct marksheet access to unpublished exam throws 403
        $msResp = $this->actingAs($this->studentUser)->get(route('student.marksheet', $evalResult->id));
        $msResp->assertStatus(403);
    }

    // ==========================================
    // 4. STUDY MATERIALS TESTS
    // ==========================================

    public function test_study_material_upload_and_enrolled_course_download_restriction(): void
    {
        $fakeFile = UploadedFile::fake()->create('c_programming_notes.pdf', 1500, 'application/pdf');

        $matPayload = [
            'title'         => 'C Programming Comprehensive Handbook',
            'course_id'     => $this->course->id,
            'material_type' => 'pdf',
            'file'          => $fakeFile,
            'description'   => 'Core C syntax and pointer tutorials',
            'status'        => true,
        ];

        $uploadResp = $this->actingAs($this->superAdmin)->post(route('admin.materials.store'), $matPayload);
        $uploadResp->assertSessionHas('success');

        $material = StudyMaterial::where('title', 'C Programming Comprehensive Handbook')->first();
        $this->assertNotNull($material);
        $this->assertNotNull($material->file_path);

        // Enrolled student can view and download
        Admission::create([
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee'     => 12000.00,
            'discount_amount'=> 0.00,
            'net_fee'        => 12000.00,
            'total_paid'     => 0.00,
            'status'         => 'active',
        ]);

        $dlResp = $this->actingAs($this->studentUser)->get(route('student.study-materials.download', $material->id));
        $this->assertContains($dlResp->getStatusCode(), [200, 302]);

        // Unenrolled student receives 403
        $otherStudentUser = User::create([
            'name'      => 'Other Student',
            'email'     => 'other_' . uniqid() . '@example.com',
            'mobile'    => '9804' . rand(100000, 999999),
            'password'  => Hash::make('password123'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        $otherStudent = Student::create([
            'user_id'         => $otherStudentUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/OTHER/001',
            'roll_no'         => 'DCE26' . rand(10000, 99999),
            'full_name'       => 'Other Student',
            'father_name'     => 'Father',
            'dob'             => '2004-01-01',
            'gender'          => 'male',
            'mobile'          => $otherStudentUser->mobile,
            'state_id'        => $this->state->id,
            'district_id'     => $this->district->id,
            'city'            => 'Patna',
            'address'         => 'Danapur',
            'pincode'         => '801503',
            'status'          => 'enrolled',
        ]);

        $forbiddenResp = $this->actingAs($otherStudentUser)->get(route('student.study-materials.download', $material->id));
        $forbiddenResp->assertStatus(403);
    }

    // ==========================================
    // 5. HELP & SUPPORT STATUS TRANSITIONS & REPLIES
    // ==========================================

    public function test_support_ticket_creation_status_transitions_and_reply_notifications(): void
    {
        // 1. Student creates ticket
        $ticket = SupportTicket::create([
            'ticket_no'     => SupportTicket::generateTicketNo(),
            'user_id'       => $this->studentUser->id,
            'centre_id'     => $this->centre->id,
            'category'      => 'Fees & Payments',
            'priority'      => 'high',
            'subject'       => 'Incorrect receipt amount verification required',
            'description'   => 'Receipt shows ₹3000 but bank deducted ₹3500.',
            'status'        => 'open',
            'last_reply_at' => now(),
        ]);

        $this->assertEquals('open', $ticket->status);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_no);

        // 2. Staff replies -> transitions status to waiting_for_student & dispatches notification
        $staffReplyResp = $this->actingAs($this->superAdmin)->post(route('admin.support.reply', $ticket->id), [
            'message' => 'Please provide the UTR reference number or upload your bank mini-statement.',
        ]);
        $staffReplyResp->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals('waiting_for_student', $ticket->status);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'type'    => 'system',
        ]);

        // 3. Student replies -> transitions status to in_progress / open
        $studentReplyResp = $this->actingAs($this->studentUser)->post(route('student.support.reply', $ticket->id), [
            'message' => 'The UTR is UTR9988776655 from SBI UPI transaction.',
        ]);
        $studentReplyResp->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals('open', $ticket->status);

        // 4. Staff marks ticket resolved
        $resolveResp = $this->actingAs($this->superAdmin)->post(route('admin.support.status', $ticket->id), [
            'status' => 'resolved',
        ]);
        $resolveResp->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);

        // 5. IDOR check: another student cannot view this ticket
        $otherUser = User::create([
            'name'      => 'Intruder Student',
            'email'     => 'intruder_' . uniqid() . '@example.com',
            'mobile'    => '9805' . rand(100000, 999999),
            'password'  => Hash::make('password123'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        Student::create([
            'user_id'         => $otherUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/INT/001',
            'roll_no'         => 'DCE26' . rand(10000, 99999),
            'full_name'       => 'Intruder',
            'father_name'     => 'Father',
            'dob'             => '2004-01-01',
            'gender'          => 'male',
            'mobile'          => $otherUser->mobile,
            'state_id'        => $this->state->id,
            'district_id'     => $this->district->id,
            'city'            => 'Patna',
            'address'         => 'Danapur',
            'pincode'         => '801503',
            'status'          => 'enrolled',
        ]);

        $idorResp = $this->actingAs($otherUser)->get(route('student.support.show', $ticket->id));
        $idorResp->assertStatus(404);
    }

    // ==========================================
    // 6. EXPENSES, MARKETING & FINANCIAL STATEMENT
    // ==========================================

    public function test_expenses_marketing_and_financial_pnl_statement_calculations(): void
    {
        $today = now()->toDateString();

        // 1. Log Daily Operating Expense
        $expPayload = [
            'centre_id'    => $this->centre->id,
            'category'     => 'hardware',
            'title'        => 'Replacement of 5 Optical Keyboards & Mouse sets',
            'amount'       => 4500.00,
            'expense_date' => $today,
            'payment_mode' => 'upi',
            'reference_no' => 'UPI-HARDWARE-001',
            'notes'        => 'Lab workstation upgrades',
        ];

        $expResp = $this->actingAs($this->superAdmin)->post(route('admin.expenses.store'), $expPayload);
        $expResp->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'centre_id' => $this->centre->id,
            'category'  => 'hardware',
            'amount'    => 4500.00,
        ]);

        // 2. Log Marketing Expense
        $mktPayload = [
            'centre_id'         => $this->centre->id,
            'campaign_name'     => 'Diwali Admission Drive Meta Ads',
            'platform'          => 'meta_ads',
            'budget'            => 10000.00,
            'amount_spent'      => 6000.00,
            'start_date'        => $today,
            'leads_generated'   => 45,
            'conversions_count' => 12,
        ];

        $mktResp = $this->actingAs($this->superAdmin)->post(route('admin.expenses.marketing.store'), $mktPayload);
        $mktResp->assertSessionHas('success');

        $this->assertDatabaseHas('marketing_expenses', [
            'campaign_name' => 'Diwali Admission Drive Meta Ads',
            'amount_spent'  => 6000.00,
        ]);

        // 3. Create Fee Collections
        $admission = Admission::create([
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'admission_date' => $today,
            'course_fee'     => 12000.00,
            'discount_amount'=> 0.00,
            'net_fee'        => 12000.00,
            'total_paid'     => 20000.00,
            'status'         => 'active',
        ]);

        FeePayment::create([
            'admission_id'    => $admission->id,
            'student_id'      => $this->student->id,
            'centre_id'       => $this->centre->id,
            'receipt_no'      => 'RCP-FIN-TEST-001',
            'amount'          => 20000.00,
            'payment_mode'    => 'online',
            'payment_date'    => $today,
            'transaction_ref' => 'TXN-FIN-001',
        ]);

        // 4. Financial Statement Page (Net Calculations)
        $finResp = $this->actingAs($this->superAdmin)->get(route('admin.reports.financial', [
            'date_from' => $today,
            'date_to'   => $today,
            'centre_id' => $this->centre->id,
        ]));

        $finResp->assertStatus(200);
        $finResp->assertSee('Institutional Financial Statement');
        $finResp->assertSee('20,000.00'); // Gross collections
        $finResp->assertSee('4,500.00');  // Operating expenses
        $finResp->assertSee('6,000.00');  // Marketing spends
        // Net: 20000 - (4500 + 6000) = 9,500.00
        $finResp->assertSee('9,500.00');

        // 5. CSV Export Stream
        $csvResp = $this->actingAs($this->superAdmin)->get(route('admin.reports.financial.export', [
            'date_from' => $today,
            'date_to'   => $today,
            'centre_id' => $this->centre->id,
        ]));
        $csvResp->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResp->headers->get('content-type'));
    }
}
