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
use App\Models\Download;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\PaymentReceipt;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CentrePortalModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $centreUserA;
    protected Centre $centreA;
    protected User $centreUserB;
    protected Centre $centreB;
    protected User $superAdmin;
    protected Course $course;
    protected State $state;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();

        $centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);

        $this->state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);
        $this->district = District::firstOrCreate(
            ['name' => 'Patna'],
            ['state_id' => $this->state->id, 'status' => true]
        );

        // Centre A
        $this->centreUserA = User::create([
            'name' => 'Centre Director A',
            'email' => 'director_a_' . uniqid() . '@drishti.com',
            'mobile' => '98765' . rand(10000, 99999),
            'password' => Hash::make('password123'),
            'role' => 'centre',
            'is_active' => true,
        ]);
        $this->centreUserA->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->centreA = Centre::create([
            'user_id' => $this->centreUserA->id,
            'centre_code' => 'DCE-CT-' . strtoupper(substr(uniqid(), -4)) . rand(100, 999),
            'centre_name' => 'Drishti Study Centre Alpha',
            'director_name' => 'Rajesh Sharma',
            'email' => $this->centreUserA->email,
            'mobile' => $this->centreUserA->mobile,
            'address' => 'Boring Road Commercial Complex',
            'city' => 'Patna',
            'pincode' => '800001',
            'lab_capacity' => 30,
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'status' => 'active',
        ]);
        $this->centreUserA->update(['centre_id' => $this->centreA->id]);

        CentreWallet::create([
            'centre_id' => $this->centreA->id,
            'balance' => 5000.00,
            'total_credited' => 5000.00,
            'total_debited' => 0.00,
            'status' => 'active',
        ]);

        // Centre B (For IDOR tests)
        $this->centreUserB = User::create([
            'name' => 'Centre Director B',
            'email' => 'director_b_' . uniqid() . '@drishti.com',
            'mobile' => '98766' . rand(10000, 99999),
            'password' => Hash::make('password123'),
            'role' => 'centre',
            'is_active' => true,
        ]);
        $this->centreUserB->roles()->syncWithoutDetaching([$centreRole->id]);

        $this->centreB = Centre::create([
            'user_id' => $this->centreUserB->id,
            'centre_code' => 'DCE-CT-' . strtoupper(substr(uniqid(), -4)) . rand(100, 999),
            'centre_name' => 'Drishti Study Centre Beta',
            'director_name' => 'Anita Verma',
            'email' => $this->centreUserB->email,
            'mobile' => $this->centreUserB->mobile,
            'address' => 'Kankarbagh Main Road',
            'city' => 'Patna',
            'pincode' => '800020',
            'lab_capacity' => 20,
            'affiliation_date' => now()->toDateString(),
            'valid_upto' => now()->addYear()->toDateString(),
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'status' => 'active',
        ]);
        $this->centreUserB->update(['centre_id' => $this->centreB->id]);

        // Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin_test_' . uniqid() . '@drishti.com',
            'mobile' => '98767' . rand(10000, 99999),
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        // Course
        $this->course = Course::create([
            'course_name' => 'Diploma in Financial Accounting',
            'course_code' => 'DFA-' . rand(100, 999),
            'slug' => 'dfa-' . uniqid(),
            'duration' => '6 Months',
            'duration_months' => 6,
            'eligibility' => '10th Pass',
            'default_course_fee' => 7500.00,
            'status' => true,
        ]);
    }

    public function test_centre_director_can_access_dashboard_with_scoped_stats(): void
    {
        $response = $this->actingAs($this->centreUserA)->get(route('centre.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($this->centreA->centre_name);
        $response->assertSee($this->centreA->centre_code);
        $response->assertSee('5,000.00'); // Wallet balance
        $response->assertSee('Admitted Students');
        $response->assertSee('Active Batches');
    }

    public function test_centre_director_can_list_only_their_centre_students(): void
    {
        // Student in Centre A
        $studentA = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/001',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Student of Centre Alpha',
            'father_name' => 'Father Alpha',
            'dob' => '2004-01-15',
            'gender' => 'male',
            'mobile' => '9876543210',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Boring Road',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        // Student in Centre B
        $studentB = Student::create([
            'centre_id' => $this->centreB->id,
            'registration_no' => 'DCE/2026/' . $this->centreB->centre_code . '/002',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Secret Student Beta',
            'father_name' => 'Father Beta',
            'dob' => '2003-05-20',
            'gender' => 'female',
            'mobile' => '9876543211',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Kankarbagh',
            'pincode' => '800020',
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($this->centreUserA)->get(route('centre.students.index'));

        $response->assertStatus(200);
        $response->assertSee('Student of Centre Alpha');
        $response->assertDontSee('Secret Student Beta');
    }

    public function test_centre_director_can_register_new_candidate_directly(): void
    {
        $mobile = '98' . rand(10000000, 99999999);

        $payload = [
            'full_name' => 'Manish Kumar',
            'father_name' => 'Suresh Prasad',
            'dob' => '2004-08-12',
            'gender' => 'male',
            'mobile' => $mobile,
            'email' => 'manish_' . uniqid() . '@example.com',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Ashok Nagar Road No 2',
            'pincode' => '800020',
            'highest_qualification' => '12th Pass',
            'course_id' => $this->course->id,
            'course_fee' => 7500.00,
            'discount_amount' => 500.00,
        ];

        $response = $this->actingAs($this->centreUserA)->post(route('centre.students.store'), $payload);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'centre_id' => $this->centreA->id,
            'full_name' => 'Manish Kumar',
            'mobile' => $mobile,
        ]);

        $this->assertDatabaseHas('users', [
            'mobile' => $mobile,
            'role' => 'student',
        ]);

        $this->assertDatabaseHas('admissions', [
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'course_fee' => 7500.00,
            'discount_amount' => 500.00,
            'net_fee' => 7000.00,
        ]);
    }

    public function test_centre_director_can_view_student_dossier_id_card_and_admission_form(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/123',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Pooja Kumari',
            'father_name' => 'Mahesh Singh',
            'dob' => '2005-02-14',
            'gender' => 'female',
            'mobile' => '9876543299',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Rajendra Nagar',
            'pincode' => '800016',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);

        // Profile Dossier
        $resDossier = $this->actingAs($this->centreUserA)->get(route('centre.students.show', $student->id));
        $resDossier->assertStatus(200);
        $resDossier->assertSee('Pooja Kumari');
        $resDossier->assertSee($student->roll_no);

        // ID Card
        $resId = $this->actingAs($this->centreUserA)->get(route('centre.students.id-card', $student->id));
        $resId->assertStatus(200);
        $resId->assertSee('Pooja Kumari');
        $resId->assertSee($student->roll_no);

        // Admission Form
        $resForm = $this->actingAs($this->centreUserA)->get(route('centre.students.admission-form', $student->id));
        $resForm->assertStatus(200);
        $resForm->assertSee('OFFICIAL STUDENT ADMISSION DOSSIER');
        $resForm->assertSee('Pooja Kumari');
    }

    public function test_cross_centre_student_access_is_blocked_by_idor_protection(): void
    {
        $studentOfB = Student::create([
            'centre_id' => $this->centreB->id,
            'registration_no' => 'DCE/2026/' . $this->centreB->centre_code . '/888',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Victim Student Of Centre B',
            'father_name' => 'Father B',
            'dob' => '2004-03-10',
            'gender' => 'male',
            'mobile' => '9876543288',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Secret Lane',
            'pincode' => '800020',
            'status' => 'enrolled',
        ]);

        // Centre A director attempts to inspect Centre B's student
        $res = $this->actingAs($this->centreUserA)->get(route('centre.students.show', $studentOfB->id));
        $res->assertStatus(403);

        $resId = $this->actingAs($this->centreUserA)->get(route('centre.students.id-card', $studentOfB->id));
        $resId->assertStatus(403);
    }

    public function test_centre_director_can_create_batch_and_mark_daily_attendance(): void
    {
        // 1. Create Batch
        $batchPayload = [
            'course_id' => $this->course->id,
            'batch_name' => 'Morning Tally Batch Alpha',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'days' => 'Mon-Sat',
            'status' => 'active',
        ];

        $resBatch = $this->actingAs($this->centreUserA)->post(route('centre.batches.store'), $batchPayload);
        $resBatch->assertSessionHas('success');

        $batch = Batch::where('centre_id', $this->centreA->id)->where('batch_name', 'Morning Tally Batch Alpha')->first();
        $this->assertNotNull($batch);

        // 2. Enroll student in batch
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/991',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Rohan Varma',
            'father_name' => 'Ramesh Varma',
            'dob' => '2004-10-10',
            'gender' => 'male',
            'mobile' => '9876543201',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Boring Canal Road',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);

        // 3. Mark Attendance
        $today = now()->toDateString();
        $attPayload = [
            'attendance_date' => $today,
            'attendance' => [
                $student->id => 'present',
            ],
            'remarks' => [
                $student->id => 'Regular lab practical completed',
            ],
        ];

        $resAtt = $this->actingAs($this->centreUserA)->post(route('centre.batches.attendance.mark', $batch->id), $attPayload);
        $resAtt->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'batch_id' => $batch->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'attendance_date' => $today,
            'status' => 'present',
        ]);
    }

    public function test_centre_director_can_view_exams_and_submit_marks(): void
    {
        $subject = CourseSubject::create([
            'course_id' => $this->course->id,
            'subject_code' => 'SUB-' . rand(100, 999),
            'subject_name' => 'Financial Accounting Principles',
            'theory_max' => 70,
            'practical_max' => 20,
            'viva_max' => 10,
            'pass_marks' => 40,
            'total_max' => 100,
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Annual Diploma Examination 2026',
            'exam_code' => 'EXM-DCE-' . rand(1000, 9999),
            'session_name' => 'Session 2026-A',
            'exam_date' => now()->addDays(5)->toDateString(),
            'status' => 'scheduled',
        ]);

        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/777',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Kiran Mehta',
            'father_name' => 'Sunil Mehta',
            'dob' => '2004-06-18',
            'gender' => 'female',
            'mobile' => '9876543202',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Bailey Road',
            'pincode' => '800014',
            'status' => 'enrolled',
        ]);

        Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);

        $marksPayload = [
            'student_id' => $student->id,
            'subjects' => [
                [
                    'id' => $subject->id,
                    'theory_obtained' => 60,
                    'practical_obtained' => 18,
                    'viva_obtained' => 9,
                ],
            ],
        ];

        $response = $this->actingAs($this->centreUserA)->post(route('centre.exams.marks.store', $exam->id), $marksPayload);
        $response->assertSessionHas('success');
        $response->assertRedirect(route('centre.exams.results'));

        $this->assertDatabaseHas('exam_results', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'total_marks_obtained' => 87.00,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);
    }

    public function test_centre_director_can_collect_offline_fee_and_view_receipt(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/555',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Aman Verma',
            'father_name' => 'Pramod Verma',
            'dob' => '2005-04-12',
            'gender' => 'male',
            'mobile' => '9876543203',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Gola Road',
            'pincode' => '801503',
            'status' => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 500.00,
            'net_fee' => 7000.00,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);

        $feePayload = [
            'admission_id' => $admission->id,
            'amount' => 3000.00,
            'payment_mode' => 'cash',
            'transaction_ref' => 'CTR-CASH-001',
            'notes' => 'First installment collected at centre counter',
        ];

        $response = $this->actingAs($this->centreUserA)->post(route('centre.fees.store'), $feePayload);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('fee_payments', [
            'admission_id' => $admission->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'amount' => 3000.00,
            'payment_mode' => 'cash',
        ]);

        $admission->refresh();
        $this->assertEquals(3000.00, (float) $admission->total_paid);

        $receipt = PaymentReceipt::where('admission_id', $admission->id)->first();
        $this->assertNotNull($receipt);

        $receiptView = $this->actingAs($this->centreUserA)->get(route('centre.fees.receipt', $receipt->id));
        $receiptView->assertStatus(200);
        $receiptView->assertSee($receipt->receipt_no);
        $receiptView->assertSee('Aman Verma');
        $receiptView->assertSee('3,000.00');
    }

    public function test_centre_cannot_collect_fee_exceeding_outstanding_due(): void
    {
        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/556',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Overpay Tester',
            'father_name' => 'Father',
            'dob' => '2005-01-01',
            'gender' => 'male',
            'mobile' => '9876543204',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Danapur',
            'pincode' => '801503',
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
            'total_paid' => 4000.00, // Remaining due is only 1000
            'status' => 'active',
        ]);

        $feePayload = [
            'admission_id' => $admission->id,
            'amount' => 1500.00, // Exceeds 1000
            'payment_mode' => 'cash',
        ];

        $response = $this->actingAs($this->centreUserA)->post(route('centre.fees.store'), $feePayload);

        $response->assertSessionHas('error');
    }

    public function test_centre_director_can_view_wallet_and_submit_topup_request(): void
    {
        $topupPayload = [
            'amount' => 10000.00,
            'payment_mode' => 'upi',
            'reference_no' => 'UPI-987123654012',
            'notes' => 'Wallet recharge for 15 student kits',
        ];

        $response = $this->actingAs($this->centreUserA)->post(route('centre.wallet.topup.store'), $topupPayload);

        $response->assertSessionHas('success');
        $response->assertRedirect(route('centre.wallet.index'));

        $this->assertDatabaseHas('centre_wallet_transactions', [
            'centre_id' => $this->centreA->id,
            'amount' => 10000.00,
            'reference_no' => 'UPI-987123654012',
            'type' => 'credit',
            'status' => 'pending',
        ]);
    }

    public function test_centre_director_can_view_affiliation_certificate(): void
    {
        $response = $this->actingAs($this->centreUserA)->get(route('centre.downloads.certificate'));

        $response->assertStatus(200);
        $response->assertSee('CERTIFICATE OF AUTHORIZED STUDY CENTRE');
        $response->assertSee($this->centreA->centre_name);
        $response->assertSee($this->centreA->centre_code);
        $response->assertSee($this->centreA->director_name);
    }

    public function test_centre_director_can_open_support_ticket_and_post_reply(): void
    {
        $ticketPayload = [
            'category' => 'wallet',
            'priority' => 'high',
            'subject' => 'Please expedite verification of top-up UTR-987123654012',
            'description' => 'Payment made via SBI UPI. Screenshot attached in transaction history.',
        ];

        $resTicket = $this->actingAs($this->centreUserA)->post(route('centre.support.store'), $ticketPayload);
        $resTicket->assertSessionHas('success');
        $resTicket->assertRedirect();

        $ticket = SupportTicket::where('centre_id', $this->centreA->id)->latest()->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);

        // Post Reply
        $replyPayload = [
            'message' => 'Thank you, we received the central reference acknowledging receipt.',
        ];

        $resReply = $this->actingAs($this->centreUserA)->post(route('centre.support.reply', $ticket->id), $replyPayload);
        $resReply->assertSessionHas('success');

        $this->assertDatabaseHas('support_ticket_replies', [
            'ticket_id' => $ticket->id,
            'message' => 'Thank you, we received the central reference acknowledging receipt.',
            'is_staff_reply' => false,
        ]);
    }

    public function test_cross_centre_ticket_and_receipt_access_blocked_by_idor(): void
    {
        // Ticket of Centre B
        $ticketOfB = SupportTicket::create([
            'ticket_no' => 'TCK-2026-BBBBBB',
            'user_id' => $this->centreUserB->id,
            'centre_id' => $this->centreB->id,
            'category' => 'wallet',
            'priority' => 'medium',
            'subject' => 'Confidential Financial Dispute of Centre B',
            'description' => 'Private information of centre B',
            'status' => 'open',
        ]);

        $resTicket = $this->actingAs($this->centreUserA)->get(route('centre.support.show', $ticketOfB->id));
        $resTicket->assertStatus(403);

        // Receipt of Centre B
        $feePaymentOfB = FeePayment::create([
            'receipt_no' => 'RCP-2026-' . $this->centreB->id . '-9999',
            'student_id' => 1,
            'admission_id' => 1,
            'centre_id' => $this->centreB->id,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $receiptOfB = PaymentReceipt::create([
            'fee_payment_id' => $feePaymentOfB->id,
            'receipt_no' => 'RCP-2026-' . $this->centreB->id . '-9999',
            'student_id' => 1,
            'admission_id' => 1,
            'centre_id' => $this->centreB->id,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'receipt_date' => now()->toDateString(),
            'qr_token' => 'secret_token_b',
        ]);

        $resReceipt = $this->actingAs($this->centreUserA)->get(route('centre.fees.receipt', $receiptOfB->id));
        $resReceipt->assertStatus(403);
    }

    public function test_centre_director_can_update_profile_and_change_password(): void
    {
        // 1. Update Profile
        $profilePayload = [
            'director_name' => 'Rajesh K. Sharma Updated',
            'mobile' => '9876543290',
            'alternate_mobile' => '9876543291',
            'email' => 'updated_director_' . uniqid() . '@drishti.com',
            'city' => 'Patna Central',
            'address' => 'Updated Commercial Complex 2nd Floor',
            'pincode' => '800001',
            'lab_capacity' => 45,
        ];

        $resProfile = $this->actingAs($this->centreUserA)->post(route('centre.profile.update'), $profilePayload);
        $resProfile->assertSessionHas('success');

        $this->centreA->refresh();
        $this->assertEquals('Rajesh K. Sharma Updated', $this->centreA->director_name);
        $this->assertEquals(45, $this->centreA->lab_capacity);

        // 2. Change Password
        $pwdPayload = [
            'current_password' => 'password123',
            'password' => 'newSecretPass2026',
            'password_confirmation' => 'newSecretPass2026',
        ];

        $resPwd = $this->actingAs($this->centreUserA)->post(route('centre.profile.change-password'), $pwdPayload);
        $resPwd->assertSessionHas('success');

        $this->centreUserA->refresh();
        $this->assertTrue(Hash::check('newSecretPass2026', $this->centreUserA->password));
    }

    public function test_super_admin_can_access_centre_portal(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('centre.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('AUTHORIZED STUDY CENTRE PORTAL');
    }

    public function test_all_centre_pages_and_reports_render_successfully(): void
    {
        // Create sample batch
        $batch = Batch::create([
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'batch_name' => 'Report Testing Batch',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'days' => 'Mon-Sat',
            'status' => 'active',
        ]);

        $student = Student::create([
            'centre_id' => $this->centreA->id,
            'registration_no' => 'DCE/2026/' . $this->centreA->centre_code . '/333',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Testing Candidate All',
            'father_name' => 'Father Testing',
            'dob' => '2004-05-10',
            'gender' => 'male',
            'mobile' => '9876543209',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'city' => 'Patna',
            'address' => 'Station Road',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'total_paid' => 3000.00,
            'status' => 'active',
        ]);

        $payment = FeePayment::create([
            'student_id' => $student->id,
            'admission_id' => $admission->id,
            'centre_id' => $this->centreA->id,
            'receipt_no' => 'RCP-2026-TEST-' . rand(1000, 9999),
            'payment_date' => now()->toDateString(),
            'amount' => 3000.00,
            'payment_mode' => 'cash',
        ]);

        $receipt = PaymentReceipt::create([
            'fee_payment_id' => $payment->id,
            'receipt_no' => $payment->receipt_no,
            'student_id' => $student->id,
            'admission_id' => $admission->id,
            'centre_id' => $this->centreA->id,
            'amount' => 3000.00,
            'payment_mode' => 'cash',
            'receipt_date' => now()->toDateString(),
            'qr_token' => 'qr_test_all',
        ]);

        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Test All Exam 2026',
            'exam_code' => 'EXM-TEST-' . rand(1000, 9999),
            'session_name' => 'Session 2026 Test',
            'exam_date' => now()->addDays(10)->toDateString(),
            'status' => 'scheduled',
        ]);

        $examResult = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'total_marks_obtained' => 85.0,
            'total_max_marks' => 100.0,
            'percentage' => 85.0,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        $certificate = Certificate::create([
            'exam_result_id' => $examResult->id,
            'student_id' => $student->id,
            'centre_id' => $this->centreA->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'CERT-2026-TEST-' . rand(1000, 9999),
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        $ticket = SupportTicket::create([
            'ticket_no' => 'TCK-2026-ALLTEST',
            'user_id' => $this->centreUserA->id,
            'centre_id' => $this->centreA->id,
            'category' => 'general',
            'priority' => 'low',
            'subject' => 'General Inquiry All Test',
            'description' => 'Test description',
            'status' => 'open',
        ]);

        $routesToTest = [
            route('centre.dashboard'),
            route('centre.students.index'),
            route('centre.students.create'),
            route('centre.students.show', $student->id),
            route('centre.students.id-card', $student->id),
            route('centre.students.admission-form', $student->id),
            route('centre.batches.index'),
            route('centre.batches.create'),
            route('centre.batches.edit', $batch->id),
            route('centre.batches.attendance', $batch->id),
            route('centre.batches.attendance.report'),
            route('centre.batches.attendance.report', ['batch_id' => $batch->id, 'month' => date('Y-m')]),
            route('centre.exams.index'),
            route('centre.exams.hall-tickets.index'),
            route('centre.exams.hall-tickets.index', ['exam_id' => $exam->id]),
            route('centre.exams.hall-tickets', $exam->id),
            route('centre.exams.hall-tickets.single', [$exam->id, $student->id]),
            route('centre.exams.marks.index'),
            route('centre.exams.marks.index', ['exam_id' => $exam->id]),
            route('centre.exams.marks', $exam->id),
            route('centre.exams.marks', ['exam' => $exam->id, 'student_id' => $student->id]),
            route('centre.exams.results'),
            route('centre.exams.results.marksheet', $examResult->id),
            route('centre.fees.index'),
            route('centre.fees.create'),
            route('centre.fees.create', ['admission_id' => $admission->id]),
            route('centre.fees.receipt', $receipt->id),
            route('centre.fees.dues'),
            route('centre.wallet.index'),
            route('centre.wallet.topup'),
            route('centre.downloads.index'),
            route('centre.downloads.student-certificates'),
            route('centre.downloads.student-certificates.show', $certificate->id),
            route('centre.downloads.certificate'),
            route('centre.support.index'),
            route('centre.support.create'),
            route('centre.support.show', $ticket->id),
            route('centre.profile.show'),
        ];

        foreach ($routesToTest as $uri) {
            $res = $this->actingAs($this->centreUserA)->get($uri);
            $this->assertEquals(200, $res->status(), "Failed asserting that {$uri} returns 200 OK.");
        }
    }
}
