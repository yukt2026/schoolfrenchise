<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Certificate;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Marksheet;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentAndCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $centre = Centre::where('centre_code', 'DCE-1001')->first() ?? Centre::first();
        $courseAdca = Course::where('course_code', 'ADCA')->first() ?? Course::first();
        $courseDca = Course::where('course_code', 'DCA')->first() ?? Course::first();

        // 1. Create a Super Admin account
        User::firstOrCreate(
            ['email' => 'admin@drishticomputer.com'],
            [
                'name' => 'TECH ERA Head Office Board Admin',
                'mobile' => '9942434239',
                'password' => Hash::make('Admin@12345'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. Verified Student 1 - Rahul Sharma (ADCA - Grade A+)
        $user1 = User::firstOrCreate(
            ['email' => 'rahul.sharma@example.com'],
            [
                'name' => 'Rahul Sharma',
                'mobile' => '9876543210',
                'password' => Hash::make('Student@123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $student1 = Student::firstOrCreate(
            ['registration_no' => 'DCE/2025/101/0042'],
            [
                'user_id' => $user1->id,
                'centre_id' => $centre->id,
                'roll_no' => 'DCE2501042',
                'full_name' => 'RAHUL SHARMA',
                'father_name' => 'RAMESH SHARMA',
                'mother_name' => 'SUNITA SHARMA',
                'dob' => '2002-08-15',
                'gender' => 'male',
                'mobile' => '9876543210',
                'email' => 'rahul.sharma@example.com',
                'state_id' => $centre->state_id,
                'district_id' => $centre->district_id,
                'city' => 'Noida',
                'address' => 'House No. 45, Sector 22, Noida',
                'pincode' => '201301',
                'highest_qualification' => '12th (Senior Secondary)',
                'status' => 'completed',
            ]
        );

        $admission1 = Admission::firstOrCreate(
            [
                'student_id' => $student1->id,
                'course_id' => $courseAdca->id,
            ],
            [
                'centre_id' => $centre->id,
                'admission_date' => '2024-04-01',
                'course_fee' => 7500.00,
                'discount_amount' => 500.00,
                'net_fee' => 7000.00,
                'total_paid' => 7000.00,
                'status' => 'completed',
                'remarks' => 'Course completed successfully with distinction',
            ]
        );

        $exam1 = Exam::firstOrCreate(
            ['exam_code' => 'EXAM-2025-MAR-ADCA'],
            [
                'course_id' => $courseAdca->id,
                'exam_title' => 'Annual Board Examination March 2025',
                'session_name' => '2024-2025',
                'exam_date' => '2025-03-20',
                'status' => 'published',
            ]
        );

        $result1 = ExamResult::firstOrCreate(
            [
                'student_id' => $student1->id,
                'course_id' => $courseAdca->id,
            ],
            [
                'exam_id' => $exam1->id,
                'centre_id' => $centre->id,
                'total_marks_obtained' => 368.00,
                'total_max_marks' => 400.00,
                'percentage' => 92.00,
                'grade' => 'A+',
                'result_status' => 'passed',
                'approved_at' => '2025-03-28 12:00:00',
            ]
        );

        foreach ($courseAdca->subjects as $subj) {
            ExamResultSubject::firstOrCreate(
                [
                    'exam_result_id' => $result1->id,
                    'course_subject_id' => $subj->id,
                ],
                [
                    'theory_obtained' => 64.00,
                    'practical_obtained' => 19.00,
                    'viva_obtained' => 9.00,
                    'total_obtained' => 92.00,
                    'max_marks' => 100.00,
                    'grade' => 'A+',
                    'is_passed' => true,
                ]
            );
        }

        $marksheet1 = Marksheet::firstOrCreate(
            ['marksheet_no' => 'DCE/MS/2025/1042'],
            [
                'exam_result_id' => $result1->id,
                'student_id' => $student1->id,
                'centre_id' => $centre->id,
                'course_id' => $courseAdca->id,
                'issue_date' => '2025-04-05',
                'security_hash' => hash('sha256', 'DCE/MS/2025/1042-RAHUL SHARMA'),
                'is_issued' => true,
            ]
        );

        Certificate::firstOrCreate(
            ['certificate_no' => 'DCE/CERT/2025/1042'],
            [
                'exam_result_id' => $result1->id,
                'student_id' => $student1->id,
                'centre_id' => $centre->id,
                'course_id' => $courseAdca->id,
                'issue_date' => '2025-04-10',
                'security_hash' => hash('sha256', 'DCE/CERT/2025/1042-RAHUL SHARMA'),
                'qr_code_url' => url('/verify/cert/DCE-CERT-2025-1042'),
                'status' => 'issued',
            ]
        );

        // 3. Verified Student 2 - Priya Kumari (DCA - Grade A)
        $user2 = User::firstOrCreate(
            ['email' => 'priya.singh@example.com'],
            [
                'name' => 'Priya Kumari',
                'mobile' => '9812345678',
                'password' => Hash::make('Student@123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $student2 = Student::firstOrCreate(
            ['registration_no' => 'DCE/2025/101/0088'],
            [
                'user_id' => $user2->id,
                'centre_id' => $centre->id,
                'roll_no' => 'DCE2501088',
                'full_name' => 'PRIYA KUMARI',
                'father_name' => 'SURESH PRASAD',
                'mother_name' => 'GEETA DEVI',
                'dob' => '2003-11-20',
                'gender' => 'female',
                'mobile' => '9812345678',
                'email' => 'priya.singh@example.com',
                'state_id' => $centre->state_id,
                'district_id' => $centre->district_id,
                'city' => 'Noida',
                'address' => 'B-12, Sector 15, Noida',
                'pincode' => '201301',
                'highest_qualification' => 'Graduation B.Com',
                'status' => 'completed',
            ]
        );

        $admission2 = Admission::firstOrCreate(
            [
                'student_id' => $student2->id,
                'course_id' => $courseDca->id,
            ],
            [
                'centre_id' => $centre->id,
                'admission_date' => '2024-09-01',
                'course_fee' => 4500.00,
                'discount_amount' => 0.00,
                'net_fee' => 4500.00,
                'total_paid' => 4500.00,
                'status' => 'completed',
                'remarks' => 'Completed 6 months DCA course',
            ]
        );

        $result2 = ExamResult::firstOrCreate(
            [
                'student_id' => $student2->id,
                'course_id' => $courseDca->id,
            ],
            [
                'exam_id' => $exam1->id,
                'centre_id' => $centre->id,
                'total_marks_obtained' => 246.00,
                'total_max_marks' => 300.00,
                'percentage' => 82.00,
                'grade' => 'A',
                'result_status' => 'passed',
                'approved_at' => '2025-03-28 12:00:00',
            ]
        );

        Marksheet::firstOrCreate(
            ['marksheet_no' => 'DCE/MS/2025/1088'],
            [
                'exam_result_id' => $result2->id,
                'student_id' => $student2->id,
                'centre_id' => $centre->id,
                'course_id' => $courseDca->id,
                'issue_date' => '2025-04-05',
                'security_hash' => hash('sha256', 'DCE/MS/2025/1088-PRIYA KUMARI'),
                'is_issued' => true,
            ]
        );

        Certificate::firstOrCreate(
            ['certificate_no' => 'DCE/CERT/2025/1088'],
            [
                'exam_result_id' => $result2->id,
                'student_id' => $student2->id,
                'centre_id' => $centre->id,
                'course_id' => $courseDca->id,
                'issue_date' => '2025-04-10',
                'security_hash' => hash('sha256', 'DCE/CERT/2025/1088-PRIYA KUMARI'),
                'qr_code_url' => url('/verify/cert/DCE-CERT-2025-1088'),
                'status' => 'issued',
            ]
        );

        // 4. Official TECH ERA COMPUTER Student - Rahul Kumar (ADCA - Roll TEC2601001)
        $userTech = User::updateOrCreate(
            ['email' => 'student@techeracomputer.in'],
            [
                'name' => 'Rahul Kumar',
                'mobile' => '9162384661',
                'password' => Hash::make('Student@123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $studentTech = Student::updateOrCreate(
            ['registration_no' => 'TEC/2026/001'],
            [
                'user_id' => $userTech->id,
                'centre_id' => $centre->id,
                'roll_no' => 'TEC2601001',
                'full_name' => 'RAHUL KUMAR',
                'father_name' => 'RAJESH KUMAR',
                'mother_name' => 'SUNITA DEVI',
                'dob' => '2004-05-12',
                'gender' => 'male',
                'mobile' => '9876543211',
                'email' => 'student@techeracomputer.in',
                'state_id' => $centre->state_id ?? 1,
                'district_id' => $centre->district_id ?? 1,
                'city' => 'Sheikhpura',
                'address' => 'Sasbahana, Sheikhpura, Bihar - 801105',
                'pincode' => '801105',
                'highest_qualification' => '12th Pass (Science)',
                'status' => 'enrolled',
            ]
        );

        $admissionTech = Admission::firstOrCreate(
            [
                'student_id' => $studentTech->id,
                'course_id' => $courseAdca->id,
            ],
            [
                'centre_id' => $centre->id,
                'admission_date' => '2026-01-10',
                'course_fee' => 7500.00,
                'discount_amount' => 500.00,
                'net_fee' => 7000.00,
                'total_paid' => 4000.00,
                'status' => 'active',
                'remarks' => 'Enrolled in ADCA - TECH ERA COMPUTER Main Campus',
            ]
        );

        // 5. Official TECH ERA COMPUTER Student 2 - Priya Kumari (DCA - Roll TEC2601002)
        $userTech2 = User::updateOrCreate(
            ['email' => 'priya@techeracomputer.in'],
            [
                'name' => 'Priya Kumari',
                'mobile' => '9812345679',
                'password' => Hash::make('Student@123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $studentTech2 = Student::updateOrCreate(
            ['registration_no' => 'TEC/2026/002'],
            [
                'user_id' => $userTech2->id,
                'centre_id' => $centre->id,
                'roll_no' => 'TEC2601002',
                'full_name' => 'PRIYA KUMARI',
                'father_name' => 'SURESH PRASAD',
                'mother_name' => 'GEETA DEVI',
                'dob' => '2004-11-20',
                'gender' => 'female',
                'mobile' => '9812345679',
                'email' => 'priya@techeracomputer.in',
                'state_id' => $centre->state_id ?? 1,
                'district_id' => $centre->district_id ?? 1,
                'city' => 'Sheikhpura',
                'address' => 'Sasbahana, Sheikhpura, Bihar - 801105',
                'pincode' => '801105',
                'highest_qualification' => 'Graduation (BCA)',
                'status' => 'enrolled',
            ]
        );

        Admission::firstOrCreate(
            [
                'student_id' => $studentTech2->id,
                'course_id' => $courseDca->id,
            ],
            [
                'centre_id' => $centre->id,
                'admission_date' => '2026-02-01',
                'course_fee' => 4500.00,
                'discount_amount' => 0.00,
                'net_fee' => 4500.00,
                'total_paid' => 4500.00,
                'status' => 'active',
                'remarks' => 'Enrolled in DCA - Full fee paid',
            ]
        );
    }
}
