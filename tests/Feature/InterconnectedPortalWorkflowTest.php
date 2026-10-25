<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\District;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\FeePayment;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InterconnectedPortalWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $centreUser;
    protected Centre $centre;
    protected Course $course;
    protected CourseSubject $subject;
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
            ['centre_code' => 'DCE-FLOW-01'],
            [
                'centre_name' => 'Flow Test Centre',
                'director_name' => 'Er. Flow Manager',
                'contact_person' => 'Er. Flow Manager',
                'mobile' => '9162380000',
                'email' => 'flow_centre@drishti.local',
                'address' => 'Main Road, Patna',
                'city' => 'Patna',
                'pincode' => '800001',
                'affiliation_date' => now()->toDateString(),
                'valid_upto' => now()->addYear()->toDateString(),
                'status' => 'active',
                'state_id' => $this->state->id,
                'district_id' => $this->district->id,
            ]
        );

        $this->centreUser = User::firstOrCreate(
            ['email' => 'flow_director@drishti.local'],
            [
                'name' => 'Er. Flow Manager',
                'password' => bcrypt('Centre@12345'),
                'role' => 'centre',
                'centre_id' => $this->centre->id,
                'is_active' => true,
            ]
        );
        $this->centreUser->roles()->syncWithoutDetaching([$centreRole->id]);

        // Wallet
        CentreWallet::firstOrCreate(
            ['centre_id' => $this->centre->id],
            ['balance' => 5000.00]
        );

        $this->course = Course::firstOrCreate(
            ['course_code' => 'ADCA-FLOW-26'],
            [
                'course_name' => 'Advance Diploma in Computer Flow',
                'slug' => 'advance-diploma-computer-flow',
                'duration' => '12 Months',
                'eligibility' => '10+2',
                'status' => true,
            ]
        );

        $this->subject = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_code' => 'ADCA-S1'],
            [
                'subject_name' => 'Computer Fundamentals & Office Automation',
                'theory_max' => 70,
                'practical_max' => 20,
                'viva_max' => 10,
                'pass_marks' => 40,
                'total_max' => 100,
            ]
        );
    }

    /**
     * Test full interconnected pipeline across Admin, Centre, and Student panels.
     */
    public function test_complete_interconnected_multi_portal_workflow(): void
    {
        // -------------------------------------------------------------
        // STEP 1: Centre Portal: Register Student & Enroll in Course
        // -------------------------------------------------------------
        $studentUser = User::create([
            'name' => 'Rohit Sharma Flow',
            'email' => 'rohit.flow.' . uniqid() . '@example.com',
            'password' => Hash::make('Student@12345'),
            'role' => 'student',
            'is_active' => true,
        ]);
        $studentRole = Role::where('name', 'student')->first();
        $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'centre_id' => $this->centre->id,
            'roll_no' => 'ROLL-FLOW-' . rand(1000, 9999),
            'registration_no' => 'REG-FLOW-' . rand(1000, 9999),
            'full_name' => 'Rohit Sharma Flow',
            'father_name' => 'Sanjay Sharma',
            'mother_name' => 'Geeta Sharma',
            'gender' => 'male',
            'dob' => '2004-06-15',
            'mobile' => '98765' . rand(10000, 99999),
            'email' => $studentUser->email,
            'address' => 'Kankarbagh, Patna',
            'city' => 'Patna',
            'state_id' => $this->state->id,
            'district_id' => $this->district->id,
            'pincode' => '800020',
            'status' => 'enrolled',
        ]);

        $batch = Batch::create([
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'batch_name' => 'Flow Master Batch 2026',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'days' => 'Mon-Sat',
            'status' => 'active',
        ]);

        $admission = Admission::create([
            'student_id' => $student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 9000.00,
            'discount_amount' => 500.00,
            'net_fee' => 8500.00,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);

        // -------------------------------------------------------------
        // STEP 2: Centre marks daily attendance for this student
        // -------------------------------------------------------------
        Attendance::create([
            'batch_id' => $batch->id,
            'student_id' => $student->id,
            'centre_id' => $this->centre->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'present',
            'remarks' => 'Lab session completed',
        ]);

        // -------------------------------------------------------------
        // STEP 3: Centre collects Fee payment
        // -------------------------------------------------------------
        $payment = FeePayment::create([
            'centre_id' => $this->centre->id,
            'student_id' => $student->id,
            'admission_id' => $admission->id,
            'receipt_no' => 'RCP-FLOW-001',
            'amount' => 4000.00,
            'payment_date' => now()->toDateString(),
            'payment_mode' => 'cash',
            'transaction_ref' => 'CASH-COLLECTED',
            'remarks' => 'First installment',
        ]);
        $admission->update(['total_paid' => 4000.00]);

        // -------------------------------------------------------------
        // STEP 4: Admin Portal: verify dynamic reflection
        // -------------------------------------------------------------
        // Admin sees student in Students Roster
        $resAdminStudents = $this->actingAs($this->superAdmin)
            ->get(route('admin.students.index', ['search' => 'Rohit Sharma Flow']));
        $resAdminStudents->assertStatus(200);
        $resAdminStudents->assertSee('Rohit Sharma Flow');
        $resAdminStudents->assertSee($student->roll_no);

        // Admin sees payment in Fee ledger
        $resAdminFees = $this->actingAs($this->superAdmin)
            ->get(route('admin.fees.index', ['search' => 'RCP-FLOW-001']));
        $resAdminFees->assertStatus(200);
        $resAdminFees->assertSee('RCP-FLOW-001');

        // -------------------------------------------------------------
        // STEP 5: Student Portal: Login and inspect live data
        // -------------------------------------------------------------
        $resStudentDash = $this->actingAs($studentUser)->get(route('student.dashboard'));
        $resStudentDash->assertStatus(200);
        $resStudentDash->assertSee('Rohit Sharma Flow');
        $resStudentDash->assertSee('Advance Diploma in Computer Flow');
        $resStudentDash->assertSee('4,000'); // Paid amount

        // Student fees history
        $resStudentFees = $this->actingAs($studentUser)->get(route('student.fees.history'));
        $resStudentFees->assertStatus(200);
        $resStudentFees->assertSee('RCP-FLOW-001');
        $resStudentFees->assertSee('4,000.00');

        // Student attendance
        $resStudentAtt = $this->actingAs($studentUser)->get(route('student.attendance'));
        $resStudentAtt->assertStatus(200);
        $resStudentAtt->assertSee('Lab session completed');
        $resStudentAtt->assertSee('Present');

        // -------------------------------------------------------------
        // STEP 6: Examination, Marks Entry & Result Publication
        // -------------------------------------------------------------
        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Interconnected Central Board Exam 2026',
            'exam_code' => 'EXM-FLOW-01',
            'session_name' => 'Annual 2026',
            'exam_date' => now()->toDateString(),
            'status' => 'published', // Board published
        ]);

        $examResult = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'centre_id' => $this->centre->id,
            'total_max_marks' => 100.00,
            'total_marks_obtained' => 88.00,
            'percentage' => 88.00,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        ExamResultSubject::create([
            'exam_result_id' => $examResult->id,
            'course_subject_id' => $this->subject->id,
            'theory_obtained' => 61.00,
            'practical_obtained' => 18.00,
            'viva_obtained' => 9.00,
            'total_obtained' => 88.00,
            'max_marks' => 100.00,
            'status' => 'pass',
        ]);

        // Student can view Results & Official Statement of Marks
        $resStudentResults = $this->actingAs($studentUser)->get(route('student.results'));
        $resStudentResults->assertStatus(200);
        $resStudentResults->assertSee('Interconnected Central Board Exam 2026');
        $resStudentResults->assertSee('88.0%');

        $resStudentMarksheet = $this->actingAs($studentUser)->get(route('student.marksheet', $examResult->id));
        $resStudentMarksheet->assertStatus(200);
        $resStudentMarksheet->assertSee('STATEMENT OF MARKS');
        $resStudentMarksheet->assertSee('Rohit Sharma Flow');
        $resStudentMarksheet->assertSee('88.00');

        // -------------------------------------------------------------
        // STEP 7: Certificate Issuance & Multi-Portal Reflection
        // -------------------------------------------------------------
        $cert = Certificate::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'centre_id' => $this->centre->id,
            'exam_result_id' => $examResult->id,
            'certificate_no' => 'CERT-FLOW-9999',
            'issue_date' => now()->toDateString(),
            'status' => 'issued',
        ]);

        // Student sees Certificate
        $resStudentCert = $this->actingAs($studentUser)->get(route('student.certificates'));
        $resStudentCert->assertStatus(200);
        $resStudentCert->assertSee('CERT-FLOW-9999');

        // Centre sees Certificate in Centre Downloads
        $resCentreCert = $this->actingAs($this->centreUser)->get(route('centre.downloads.student-certificates'));
        $resCentreCert->assertStatus(200);
        $resCentreCert->assertSee('CERT-FLOW-9999');
        $resCentreCert->assertSee('Rohit Sharma Flow');

        // -------------------------------------------------------------
        // STEP 8: Support Ticket Loop (Student -> Admin/Centre -> Student)
        // -------------------------------------------------------------
        $ticket = SupportTicket::create([
            'ticket_no' => 'TCK-FLOW-001',
            'user_id' => $studentUser->id,
            'centre_id' => $this->centre->id,
            'category' => 'academic',
            'subject' => 'Request for Certificate verification letter',
            'description' => 'Please verify my certificate for job application.',
            'priority' => 'high',
            'status' => 'open',
        ]);

        // Centre sees ticket
        $resCentreTickets = $this->actingAs($this->centreUser)->get(route('centre.support.index'));
        $resCentreTickets->assertStatus(200);

        // Admin replies to ticket
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->superAdmin->id,
            'message' => 'Your certificate has been officially authenticated and confirmed.',
            'is_staff_reply' => true,
        ]);
        $ticket->update(['status' => 'resolved']);

        // Student views resolved ticket with reply
        $resStudentTicket = $this->actingAs($studentUser)->get(route('student.support.show', $ticket->id));
        $resStudentTicket->assertStatus(200);
        $resStudentTicket->assertSee('Your certificate has been officially authenticated');
        $resStudentTicket->assertSee('resolved');

        // -------------------------------------------------------------
        // STEP 9: Centre Wallet Top-up -> Admin Approval -> Balance Update
        // -------------------------------------------------------------
        $tx = CentreWalletTransaction::create([
            'centre_id' => $this->centre->id,
            'type' => 'credit',
            'amount' => 2500.00,
            'balance_after' => 5000.00,
            'reference_no' => 'NEFT-FLOW-987654',
            'description' => 'Recharge request via NEFT (Ref: NEFT-FLOW-987654)',
            'status' => 'pending',
        ]);

        // Admin approves top-up
        $resApprove = $this->actingAs($this->superAdmin)
            ->post(route('admin.wallet.transactions.approve', $tx->id));
        $resApprove->assertSessionHas('success');

        $this->centre->wallet->refresh();
        $this->assertEquals(7500.00, (float) $this->centre->wallet->balance);

        // Re-fetch fresh user to avoid in-memory cached model relations
        $freshCentreUser = User::find($this->centreUser->id);

        // Centre Portal header displays updated wallet balance
        $resCentreDash = $this->actingAs($freshCentreUser)->get(route('centre.dashboard'));
        $resCentreDash->assertStatus(200);
        $resCentreDash->assertSee('7,500');
    }

    /**
     * Test responsive layout classes across all 3 portals.
     */
    public function test_responsive_layout_structure_and_components(): void
    {
        // 1. Admin Portal Layout: contains turquoise topbar, table-responsive, pagination classes
        $resAdmin = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('dce-admin-topbar');

        // 2. Centre Portal Layout: contains centre-header, mobile bottom bar, and wallet pill
        $resCentre = $this->actingAs($this->centreUser)->get(route('centre.dashboard'));
        $resCentre->assertStatus(200);
        $resCentre->assertSee('centre-header');
        $resCentre->assertSee('wallet-pill');
        $resCentre->assertSee('centre-mobile-bottom-bar');

        // 3. Student Portal Layout: contains student-header and bottom navigation
        $studentUser = User::firstOrCreate(
            ['email' => 'student.layout@example.com'],
            ['name' => 'Layout Student', 'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true]
        );
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'centre_id' => $this->centre->id,
                'roll_no' => 'ROLL-LAY-01',
                'registration_no' => 'REG-LAY-01',
                'full_name' => 'Layout Student',
                'father_name' => 'Father',
                'mother_name' => 'Mother',
                'gender' => 'male',
                'dob' => '2004-01-01',
                'mobile' => '9112233999',
                'address' => 'Patna',
                'city' => 'Patna',
                'state_id' => $this->state->id,
                'district_id' => $this->district->id,
                'pincode' => '800001',
                'status' => 'enrolled',
            ]
        );

        $resStudent = $this->actingAs($studentUser)->get(route('student.dashboard'));
        $resStudent->assertStatus(200);
        $resStudent->assertSee('student-header');
        $resStudent->assertSee('mobile-bottom-nav');
    }
}
