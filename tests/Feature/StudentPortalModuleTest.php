<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\AdmitCard;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\FeePayment;
use App\Models\Marksheet;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudyMaterial;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentPortalModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $studentUser;
    protected Student $student;
    protected User $studentUserB;
    protected Student $studentB;
    protected Centre $centre;
    protected Course $course;
    protected Course $otherCourse;
    protected Admission $admission;
    protected Batch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        // 2. State & Centre
        $state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);
        $this->centre = Centre::firstOrCreate(
            ['centre_code' => 'DCE-SP-01'],
            [
                'centre_name'      => 'Drishti Main Campus',
                'director_name'    => 'Rajesh Sharma',
                'email'            => 'maincampus@drishti.com',
                'mobile'           => '9876543210',
                'address'          => 'Station Road, Fraser Road Area',
                'city'             => 'Patna',
                'pincode'          => '800001',
                'lab_capacity'     => 25,
                'affiliation_date' => now()->toDateString(),
                'state_id'         => $state->id,
                'district_id'      => 1,
                'status'           => 'active',
            ]
        );

        // 3. Courses
        $this->course = Course::firstOrCreate(
            ['course_code' => 'ADCA-SP-2026'],
            [
                'course_name'        => 'Advanced Diploma in Computer Applications',
                'slug'               => 'adca-sp-2026',
                'duration'           => '12 Months',
                'eligibility'        => '10+2 Pass',
                'default_course_fee' => 12000.00,
                'course_fee'         => 12000.00,
                'status'             => true,
            ]
        );

        $this->otherCourse = Course::firstOrCreate(
            ['course_code' => 'DCA-OTHER-SP'],
            [
                'course_name'        => 'Diploma in Computer Applications Other',
                'slug'               => 'dca-other-sp',
                'duration'           => '6 Months',
                'eligibility'        => '10th Pass',
                'default_course_fee' => 6000.00,
                'course_fee'         => 6000.00,
                'status'             => true,
            ]
        );

        // 4. Batch
        $this->batch = Batch::firstOrCreate(
            ['batch_name' => 'Morning IT Master Batch', 'centre_id' => $this->centre->id],
            [
                'course_id'   => $this->course->id,
                'start_time'  => '09:00:00',
                'end_time'    => '11:00:00',
                'days'        => 'Mon, Wed, Fri',
                'status'      => 'active',
            ]
        );

        // 5. Primary Student & User
        $this->studentUser = User::create([
            'name'      => 'Kavita Kumari',
            'email'     => 'kavita.sp.' . uniqid() . '@example.com',
            'password'  => Hash::make('StudentSecret@2026'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        $this->studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $this->student = Student::create([
            'user_id'         => $this->studentUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/' . rand(10000, 99999),
            'roll_no'         => 'DCE' . rand(100000, 999999),
            'full_name'       => 'Kavita Kumari',
            'father_name'     => 'Mahesh Prasad',
            'mother_name'     => 'Sunita Devi',
            'dob'             => '2004-03-25',
            'gender'          => 'female',
            'mobile'          => '981122' . rand(1000, 9999),
            'state_id'        => $state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Kankarbagh, Near Old Bypass',
            'pincode'         => '800020',
            'status'          => 'enrolled',
        ]);

        // 6. Primary Admission & Fees
        $this->admission = Admission::create([
            'student_id'      => $this->student->id,
            'centre_id'       => $this->centre->id,
            'course_id'       => $this->course->id,
            'batch_id'        => $this->batch->id,
            'admission_date'  => now()->toDateString(),
            'course_fee'      => 12000.00,
            'discount_amount' => 2000.00,
            'net_fee'         => 10000.00,
            'total_paid'      => 3000.00,
            'status'          => 'active',
        ]);

        StudentFee::create([
            'student_id'     => $this->student->id,
            'admission_id'   => $this->admission->id,
            'centre_id'      => $this->centre->id,
            'installment_no' => 1,
            'title'          => '1st Quarter Fee',
            'amount'         => 3000.00,
            'paid_amount'    => 3000.00,
            'due_date'       => now()->subDays(30),
            'status'         => 'paid',
        ]);

        StudentFee::create([
            'student_id'     => $this->student->id,
            'admission_id'   => $this->admission->id,
            'centre_id'      => $this->centre->id,
            'installment_no' => 2,
            'title'          => '2nd Quarter Fee',
            'amount'         => 3500.00,
            'paid_amount'    => 0.00,
            'due_date'       => now()->addDays(30),
            'status'         => 'pending',
        ]);

        StudentFee::create([
            'student_id'     => $this->student->id,
            'admission_id'   => $this->admission->id,
            'centre_id'      => $this->centre->id,
            'installment_no' => 3,
            'title'          => '3rd Quarter Fee',
            'amount'         => 3500.00,
            'paid_amount'    => 0.00,
            'due_date'       => now()->addDays(90),
            'status'         => 'pending',
        ]);

        // 7. Secondary Student (For IDOR Protection Tests)
        $this->studentUserB = User::create([
            'name'      => 'Ravi Shankar',
            'email'     => 'ravi.sp.' . uniqid() . '@example.com',
            'password'  => Hash::make('StudentBSecret@2026'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        $this->studentUserB->roles()->syncWithoutDetaching([$studentRole->id]);

        $this->studentB = Student::create([
            'user_id'         => $this->studentUserB->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/' . rand(10000, 99999),
            'roll_no'         => 'DCE' . rand(100000, 999999),
            'full_name'       => 'Ravi Shankar',
            'father_name'     => 'Shyam Shankar',
            'dob'             => '2003-08-12',
            'gender'          => 'male',
            'mobile'          => '983344' . rand(1000, 9999),
            'state_id'        => $state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Boring Canal Road',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);
    }

    /**
     * Test 1: Student dashboard displays real database-driven ledger cards, shortcuts, and course info.
     */
    public function test_student_dashboard_displays_real_ledger_metrics(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kavita Kumari');
        $response->assertSee($this->student->roll_no);
        $response->assertSee('Advanced Diploma in Computer Applications');
        $response->assertSee('12 Months');
        $response->assertSee('Drishti Main Campus');

        // Total Fees: 10,000.00; Total Paid: 3,000.00; Total Dues: 7,000.00
        $response->assertSee('10,000.00');
        $response->assertSee('7,000.00');
        $response->assertSee('3,000.00');

        // Navigation shortcuts
        $response->assertSee(route('student.id-card'));
        $response->assertSee(route('student.admission-form'));
        $response->assertSee(route('student.fees.pay'));
    }

    /**
     * Test 2: Student profile view and contact information update.
     */
    public function test_student_profile_view_and_contact_update(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.profile'));
        $response->assertStatus(200);
        $response->assertSee('Mahesh Prasad');
        $response->assertSee($this->student->registration_no);

        // Update contact & personal details
        $updateResp = $this->actingAs($this->studentUser)->post(route('student.profile.update'), [
            'father_name'      => 'Mahesh Prasad Upgraded',
            'mother_name'      => 'Sunita Devi Upgraded',
            'phone'            => '9811223344',
            'date_of_birth'    => '2004-03-25',
            'gender'           => 'female',
            'alternate_mobile' => '9876598765',
            'address'          => 'House 14B, New Rajendra Nagar, Patna',
        ]);

        $updateResp->assertRedirect();
        $this->student->refresh();
        $this->assertEquals('Mahesh Prasad Upgraded', $this->student->father_name);
        $this->assertEquals('Sunita Devi Upgraded', $this->student->mother_name);
        $this->assertEquals('9811223344', $this->student->mobile);
        $this->assertEquals('9811223344', $this->student->phone);
        $this->assertEquals('9876598765', $this->student->alternate_mobile);
        $this->assertEquals('House 14B, New Rajendra Nagar, Patna', $this->student->address);
    }

    /**
     * Test 3: Printable admission form dossier loads actual enrolled student records.
     */
    public function test_student_admission_form_dossier_view(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.admission-form'));
        $response->assertStatus(200);
        $response->assertSee('STUDENT ADMISSION ENROLMENT FORM');
        $response->assertSee('Kavita Kumari');
        $response->assertSee('Mahesh Prasad');
        $response->assertSee('10,000.00');
    }

    /**
     * Test 4: Enrolled course and syllabus module view.
     */
    public function test_student_enrolled_course_and_syllabus_view(): void
    {
        $subject = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_name' => 'Database Management with MySQL'],
            [
                'subject_code' => 'ADCA-102',
                'max_marks'    => 100,
                'pass_marks'   => 40,
            ]
        );

        $response = $this->actingAs($this->studentUser)->get(route('student.course'));
        $response->assertStatus(200);
        $response->assertSee('Advanced Diploma in Computer Applications');
        $response->assertSee('Database Management with MySQL');
    }

    /**
     * Test 5: Digital ID card renders student roll number, centre name, and photo container.
     */
    public function test_student_digital_id_card_view(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.id-card'));
        $response->assertStatus(200);
        $response->assertSee('Student Identity Card');
        $response->assertSee($this->student->roll_no);
        $response->assertSee('Drishti Main Campus');
    }

    /**
     * Test 6: Batch schedule displays active timing and days.
     */
    public function test_student_batch_schedule_view(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.schedule'));
        $response->assertStatus(200);
        $response->assertSee('Morning IT Master Batch');
        $response->assertSee('09:00:00 - 11:00:00');
    }

    /**
     * Test 7: Attendance records and calculation rate.
     */
    public function test_student_attendance_view_and_analytics(): void
    {
        // Add attendance logs: 3 present, 1 absent = 75% rate
        Attendance::create([
            'centre_id'       => $this->centre->id,
            'student_id'      => $this->student->id,
            'batch_id'        => $this->batch->id,
            'attendance_date' => now()->subDays(3)->toDateString(),
            'status'          => 'present',
        ]);
        Attendance::create([
            'centre_id'       => $this->centre->id,
            'student_id'      => $this->student->id,
            'batch_id'        => $this->batch->id,
            'attendance_date' => now()->subDays(2)->toDateString(),
            'status'          => 'present',
        ]);
        Attendance::create([
            'centre_id'       => $this->centre->id,
            'student_id'      => $this->student->id,
            'batch_id'        => $this->batch->id,
            'attendance_date' => now()->subDays(1)->toDateString(),
            'status'          => 'present',
        ]);
        Attendance::create([
            'centre_id'       => $this->centre->id,
            'student_id'      => $this->student->id,
            'batch_id'        => $this->batch->id,
            'attendance_date' => now()->toDateString(),
            'status'          => 'absent',
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.attendance'));
        $response->assertStatus(200);
        $response->assertSee('75%');
        $response->assertSee('Attendance Log History');
        $response->assertSee('Present');
    }

    /**
     * Test 8: Fee payment intake, processing, and ledger auto-reconciliation.
     */
    public function test_student_fee_payment_processing(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('student.fees.pay'));
        $response->assertStatus(200);
        $response->assertSee('7,000.00'); // Total Dues

        // Submit fee payment of 3500.00 via card
        $payResp = $this->actingAs($this->studentUser)->post(route('student.fees.pay.submit'), [
            'amount'       => 3500.00,
            'payment_mode' => 'card',
        ]);

        $payResp->assertRedirect();

        // Check admission total_paid updated from 3000 -> 6500
        $this->admission->refresh();
        $this->assertEquals(6500.00, (float) $this->admission->total_paid);

        // Check installment 2 status transitioned from pending -> paid
        $fee2 = StudentFee::where('admission_id', $this->admission->id)->where('installment_no', 2)->first();
        $this->assertEquals('paid', $fee2->status);

        // Check fee payment record created with card
        $payment = FeePayment::where('student_id', $this->student->id)->where('amount', 3500.00)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals('card', $payment->payment_mode);
        $this->assertStringStartsWith('RCP-', $payment->receipt_no);

        // Verify receipt view renders successfully for card payment
        $receiptResp = $this->actingAs($this->studentUser)->get(route('student.fees.receipt', $payment->id));
        $receiptResp->assertStatus(200);
        $receiptResp->assertSee('card');

        // Submit remaining payment of 1000.00 via net_banking
        $payResp2 = $this->actingAs($this->studentUser)->post(route('student.fees.pay.submit'), [
            'amount'       => 1000.00,
            'payment_mode' => 'net_banking',
        ]);
        $payResp2->assertRedirect();
        $payment2 = FeePayment::where('student_id', $this->student->id)->where('amount', 1000.00)->latest()->first();
        $this->assertNotNull($payment2);
        $this->assertEquals('net_banking', $payment2->payment_mode);
    }

    /**
     * Test 9: Receipt download authorization and IDOR protection.
     */
    public function test_receipt_view_and_idor_protection(): void
    {
        $paymentA = FeePayment::create([
            'student_id'      => $this->student->id,
            'admission_id'    => $this->admission->id,
            'centre_id'       => $this->centre->id,
            'receipt_no'      => 'RCP-2026-TESTA01',
            'payment_date'    => now()->toDateString(),
            'amount'          => 1500.00,
            'payment_mode'    => 'upi',
            'transaction_ref' => 'TXN-A12345678',
        ]);

        // Student A can access own receipt
        $responseA = $this->actingAs($this->studentUser)->get(route('student.fees.receipt', $paymentA->id));
        $responseA->assertStatus(200);
        $responseA->assertSee('RCP-2026-TESTA01');
        $responseA->assertSee('Kavita Kumari');

        // Student B tries to access Student A's receipt -> 403 Forbidden
        $responseB = $this->actingAs($this->studentUserB)->get(route('student.fees.receipt', $paymentA->id));
        $responseB->assertStatus(403);
    }

    /**
     * Test 10: Study materials course restriction and access.
     */
    public function test_study_materials_course_restriction(): void
    {
        // Material for student's course
        $myMaterial = StudyMaterial::create([
            'title'         => 'Python & MySQL Master Guide',
            'slug'          => 'python-mysql-master-guide-' . uniqid(),
            'description'   => 'Complete reference notes for ADCA students.',
            'material_type' => 'pdf',
            'course_id'     => $this->course->id,
            'centre_id'     => $this->centre->id,
            'external_url'  => 'https://example.com/adca-notes.pdf',
            'status'        => true,
        ]);

        // Material for other course
        $otherMaterial = StudyMaterial::create([
            'title'         => 'Foreign Course Secret Manual',
            'slug'          => 'foreign-course-secret-manual-' . uniqid(),
            'description'   => 'Only for DCA students.',
            'material_type' => 'pdf',
            'course_id'     => $this->otherCourse->id,
            'centre_id'     => $this->centre->id,
            'external_url'  => 'https://example.com/other-notes.pdf',
            'status'        => true,
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.study-materials'));
        $response->assertStatus(200);
        $response->assertSee('Python & MySQL Master Guide');
        $response->assertDontSee('Foreign Course Secret Manual');

        // IDOR: Trying to download material of another course directly returns 403
        $downloadOther = $this->actingAs($this->studentUser)->get(route('student.study-materials.download', $otherMaterial->id));
        $downloadOther->assertStatus(403);
    }

    /**
     * Test 11: Examination schedule and admit card hall ticket generation.
     */
    public function test_examination_schedule_and_admit_card(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Mid-Term Practical & Theory Exam 2026',
            'exam_code'    => 'EXAM-ADCA-01',
            'session_name' => '2026',
            'exam_date'    => now()->addDays(14)->toDateString(),
            'status'       => 'scheduled',
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.exams'));
        $response->assertStatus(200);
        $response->assertSee('Mid-Term Practical & Theory Exam 2026');
        $response->assertSee('EXAM-ADCA-01');

        // View Admit Card
        $admitResp = $this->actingAs($this->studentUser)->get(route('student.admit-card', $exam->id));
        $admitResp->assertStatus(200);
        $admitResp->assertSee('EXAMINATION ADMIT CARD / HALL TICKET');
        $admitResp->assertSee($this->student->roll_no);
        $admitResp->assertSee('Kavita Kumari');
    }

    /**
     * Test 12: Examination results and official marksheet statement.
     */
    public function test_examination_results_and_marksheet_view(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Final Certification Exam',
            'exam_code'    => 'EXAM-FINAL-01',
            'session_name' => '2026',
            'exam_date'    => now()->subDays(10)->toDateString(),
            'status'       => 'published',
        ]);

        $result = ExamResult::create([
            'exam_id'              => $exam->id,
            'student_id'           => $this->student->id,
            'centre_id'            => $this->centre->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 88.00,
            'total_max_marks'      => 100.00,
            'percentage'           => 88.00,
            'grade'                => 'A+',
            'result_status'        => 'passed',
            'approved_at'          => now()->subDays(5),
        ]);

        $marksheet = Marksheet::create([
            'exam_result_id' => $result->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'marksheet_no'   => 'MS-2026-990011',
            'issue_date'     => now()->subDays(3)->toDateString(),
            'is_issued'      => true,
        ]);

        // Results listing
        $resList = $this->actingAs($this->studentUser)->get(route('student.results'));
        $resList->assertStatus(200);
        $resList->assertSee('88.0%');
        $resList->assertSee('A+');
        $resList->assertSee('PASSED');

        // Marksheet detailed statement
        $msResp = $this->actingAs($this->studentUser)->get(route('student.marksheet', $result->id));
        $msResp->assertStatus(200);
        $msResp->assertSee('STATEMENT OF MARKS');
        $msResp->assertSee('MS-2026-990011');
        $msResp->assertSee('88.00%');
        $msResp->assertSee('A+');
    }

    /**
     * Test 13: Issued certificate listing with cryptographic verification link.
     */
    public function test_student_certificates_view(): void
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Certificate Qualifying Exam',
            'exam_code'    => 'EXAM-CERT-01',
            'session_name' => '2026',
            'exam_date'    => now()->subDays(20)->toDateString(),
            'status'       => 'published',
        ]);

        $result = ExamResult::create([
            'exam_id'              => $exam->id,
            'student_id'           => $this->student->id,
            'centre_id'            => $this->centre->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 90.00,
            'total_max_marks'      => 100.00,
            'percentage'           => 90.00,
            'grade'                => 'A+',
            'result_status'        => 'passed',
            'approved_at'          => now()->subDays(10),
        ]);

        $cert = Certificate::create([
            'exam_result_id' => $result->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'certificate_no' => 'DCE-CERT-2026-0089',
            'issue_date'     => now()->subDays(2)->toDateString(),
            'security_hash'  => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
            'status'         => 'issued',
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.certificates'));
        $response->assertStatus(200);
        $response->assertSee('DCE-CERT-2026-0089');
        $response->assertSee('ISSUED');
        $response->assertSee('VALID');
        $response->assertSee('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855');
    }

    /**
     * Test 14: Help & support ticket creation, response thread, and IDOR isolation.
     */
    public function test_support_ticket_creation_and_idor_isolation(): void
    {
        // 1. Raise support ticket
        $createResp = $this->actingAs($this->studentUser)->post(route('student.support.store'), [
            'category'    => 'Fees & Payments',
            'priority'    => 'medium',
            'subject'     => 'Query regarding 2nd installment receipt confirmation',
            'description' => 'I made a payment and need the formal stamped receipt for my scholarship application.',
        ]);

        $createResp->assertRedirect();

        $ticket = SupportTicket::where('user_id', $this->studentUser->id)->latest()->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_no);

        // 2. View ticket thread
        $threadResp = $this->actingAs($this->studentUser)->get(route('student.support.show', $ticket->id));
        $threadResp->assertStatus(200);
        $threadResp->assertSee($ticket->ticket_no);
        $threadResp->assertSee('scholarship application');

        // 3. Post reply to ticket
        $replyResp = $this->actingAs($this->studentUser)->post(route('student.support.reply', $ticket->id), [
            'message' => 'Attaching additional clarification regarding scholarship deadline.',
        ]);
        $replyResp->assertRedirect();
        $this->assertDatabaseHas('support_ticket_replies', [
            'ticket_id' => $ticket->id,
            'message'   => 'Attaching additional clarification regarding scholarship deadline.',
        ]);

        // 4. IDOR isolation: Student B cannot view Student A's ticket
        $responseB = $this->actingAs($this->studentUserB)->get(route('student.support.show', $ticket->id));
        $responseB->assertStatus(404);
    }

    /**
     * Test 15: In-app notifications read tracking and mark-all-read.
     */
    public function test_notifications_read_and_mark_all(): void
    {
        $notif1 = SystemNotification::create([
            'user_id' => $this->studentUser->id,
            'title'   => 'Batch Timetable Rescheduled',
            'message' => 'Your Friday class will commence at 10:00 AM instead of 9:00 AM.',
            'type'    => 'info',
        ]);

        $notif2 = SystemNotification::create([
            'user_id' => $this->studentUser->id,
            'title'   => 'Fee Due Reminder',
            'message' => 'Quarter 2 installment is due in 30 days.',
            'type'    => 'fee',
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Batch Timetable Rescheduled');
        $response->assertSee('Fee Due Reminder');
        $response->assertSee('2 Unread');

        // Mark single notification read
        $markResp = $this->actingAs($this->studentUser)->post(route('student.notifications.read', $notif1->id));
        $markResp->assertRedirect();
        $notif1->refresh();
        $this->assertNotNull($notif1->read_at);

        // Mark all read
        $markAllResp = $this->actingAs($this->studentUser)->post(route('student.notifications.mark-all-read'));
        $markAllResp->assertRedirect();
        $notif2->refresh();
        $this->assertNotNull($notif2->read_at);
    }

    /**
     * Test 16: Change password security validation.
     */
    public function test_change_password_validation_and_update(): void
    {
        // Wrong current password fails
        $failResp = $this->actingAs($this->studentUser)->post(route('student.change-password.update'), [
            'current_password'      => 'WrongPassword@123',
            'password'              => 'BrandNewSecret@2026',
            'password_confirmation' => 'BrandNewSecret@2026',
        ]);
        $failResp->assertRedirect();
        $failResp->assertSessionHas('error');

        // Correct current password succeeds
        $successResp = $this->actingAs($this->studentUser)->post(route('student.change-password.update'), [
            'current_password'      => 'StudentSecret@2026',
            'password'              => 'BrandNewSecret@2026',
            'password_confirmation' => 'BrandNewSecret@2026',
        ]);
        $successResp->assertRedirect();
        $successResp->assertSessionHas('success');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('BrandNewSecret@2026', $this->studentUser->password));
    }

    /**
     * Test 17: All student modules render dynamically and safely without active admission/batch (Empty State Resiliency).
     */
    public function test_all_student_modules_render_dynamically_without_active_admission(): void
    {
        $freshUser = User::create([
            'name'      => 'Fresh Registered Student',
            'email'     => 'fresh.student.' . uniqid() . '@example.com',
            'password'  => Hash::make('Password@123'),
            'role'      => 'student',
            'is_active' => true,
        ]);
        $freshUser->roles()->syncWithoutDetaching([Role::where('name', 'student')->first()->id]);

        $freshStudent = Student::create([
            'user_id'         => $freshUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'REG-FRESH-' . rand(1000, 9999),
            'roll_no'         => 'ROL-FRESH-' . rand(1000, 9999),
            'full_name'       => 'Fresh Registered Student',
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'mobile'          => '9988776655',
            'gender'          => 'female',
            'dob'             => '2004-06-15',
            'state_id'        => $this->centre->state_id,
            'district_id'     => $this->centre->district_id,
            'city'            => 'Patna',
            'address'         => 'Boring Road',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        $routesToVerify = [
            route('student.dashboard'),
            route('student.profile'),
            route('student.admission-form'),
            route('student.id-card'),
            route('student.course'),
            route('student.schedule'),
            route('student.attendance'),
            route('student.change-password'),
            route('student.fees'),
            route('student.fees.history'),
            route('student.study-materials'),
            route('student.exams'),
            route('student.results'),
            route('student.certificates'),
            route('student.support'),
            route('student.support.create'),
            route('student.notifications'),
        ];

        foreach ($routesToVerify as $url) {
            $response = $this->actingAs($freshUser)->get($url);
            $redirectTarget = $response->headers->get('Location');
            $this->assertEquals(200, $response->getStatusCode(), "Route {$url} failed with status {$response->getStatusCode()}, redirect: {$redirectTarget}");
        }
    }
}
