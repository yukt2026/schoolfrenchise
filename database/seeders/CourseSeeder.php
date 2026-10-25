<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSubject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Diploma Programs' => 'Comprehensive multi-month diplomas covering software, office automation, web design, and accounting.',
            'Certificate Courses' => 'Short term specialized skill certification programs for employment and government jobs.',
            'Accounting & Financial IT' => 'Industry-standard accounting software, Tally ERP, Tally Prime, and GST compliance.',
            'Vocational & Typing' => 'Speed typing certifications in Hindi and English with shorthand stenography.',
            'Programming & Software' => 'Modern computer programming, Python, web development, and digital technologies.',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $desc) {
            $categoryModels[$name] = CourseCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc, 'status' => true]
            );
        }

        $coursesData = [
            [
                'category' => 'Diploma Programs',
                'course_code' => 'ADCA',
                'course_name' => 'ADVANCE DIPLOMA IN COMPUTER APPLICATION',
                'duration' => '12 Months',
                'eligibility' => '10th / 12th Pass',
                'default_course_fee' => 7500.00,
                'registration_fee' => 500.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 400.00,
                'is_popular' => true,
                'syllabus' => "Semester 1:\n- Computer Fundamentals & OS (Windows)\n- Microsoft Office Suite (Word, Excel, PowerPoint, Access)\n- Internet Concepts, Email, Cyber Security Basics\n- HTML5 & CSS Web Fundamentals\n\nSemester 2:\n- Financial Accounting with Tally Prime & GST\n- Desktop Publishing (Photoshop & CorelDraw Basics)\n- Database Management Basics\n- Project Work, Practical Lab & Viva Voce",
                'subjects' => [
                    ['code' => 'ADCA-101', 'name' => 'Computer Fundamentals & OS', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'ADCA-102', 'name' => 'MS Office Applications', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'ADCA-103', 'name' => 'Internet & Web Designing (HTML)', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'ADCA-104', 'name' => 'Financial Accounting with Tally GST', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Diploma Programs',
                'course_code' => 'DCA',
                'course_name' => 'DIPLOMA IN COMPUTER APPLICATION',
                'duration' => '6 Months',
                'eligibility' => '10th Pass',
                'default_course_fee' => 4500.00,
                'registration_fee' => 500.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 400.00,
                'is_popular' => true,
                'syllabus' => "- Computer Fundamentals & Information Technology\n- Operating System Concepts\n- MS Office (Word, Advanced Excel, PowerPoint)\n- Internet, Digital Services (CSC, Forms, Aadhaar, PAN)\n- Photoshop & Basic Image Editing\n- Tally with Basic GST Accounting\n- Practical Viva & Lab Project",
                'subjects' => [
                    ['code' => 'DCA-101', 'name' => 'IT Tools & Basics of OS', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'DCA-102', 'name' => 'Office Automation & Documentation', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'DCA-103', 'name' => 'Digital Financial Tools & Tally', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Certificate Courses',
                'course_code' => 'BCC',
                'course_name' => 'BASIC COMPUTER COURSE',
                'duration' => '3 Months',
                'eligibility' => '8th / 10th Pass',
                'default_course_fee' => 2500.00,
                'registration_fee' => 300.00,
                'exam_fee' => 200.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "- Knowing Computer & Hardware Components\n- Operating Windows Environment\n- Word Processing using MS Word\n- Spreadsheets with MS Excel\n- Presentations with MS PowerPoint\n- Internet Browsing, Emailing & Cyber Hygiene",
                'subjects' => [
                    ['code' => 'BCC-101', 'name' => 'Fundamentals & OS', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'BCC-102', 'name' => 'Word Processing & Spreadsheets', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Certificate Courses',
                'course_code' => 'CCC',
                'course_name' => 'COURSE ON COMPUTER CONCEPTS',
                'duration' => '3 Months',
                'eligibility' => '10th Pass',
                'default_course_fee' => 2800.00,
                'registration_fee' => 400.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "- Introduction to Computer & GUI Operating System\n- Elements of Word Processing\n- Spreadsheets & Formulae\n- Making Small Presentations\n- Internet, WWW & Web Browsers\n- Communications and Collaboration\n- Digital Financial Services Applications",
                'subjects' => [
                    ['code' => 'CCC-101', 'name' => 'IT Concepts & Digital Literacy', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                    ['code' => 'CCC-102', 'name' => 'Office Applications & Cyber Services', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Vocational & Typing',
                'course_code' => 'CCT-EH',
                'course_name' => 'Certificate in Computer Typing (English: 40WPM & Hindi: 35WPM)',
                'duration' => '6 Months',
                'eligibility' => '10th / 12th Pass',
                'default_course_fee' => 3500.00,
                'registration_fee' => 400.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "- Touch Typing Techniques and Finger Placement\n- English Speed Building Drills (Target 40+ WPM)\n- Hindi KrutiDev & Mangal Font Remington Gail Layouts (Target 35+ WPM)\n- Accuracy Verification, Error Detection and Speed Tests\n- Official Letter & Table Formatting",
                'subjects' => [
                    ['code' => 'TYP-101', 'name' => 'English Speed Typing (40 WPM)', 'theory' => 30, 'practical' => 60, 'viva' => 10],
                    ['code' => 'TYP-102', 'name' => 'Hindi Speed Typing (35 WPM)', 'theory' => 30, 'practical' => 60, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Vocational & Typing',
                'course_code' => 'STENO',
                'course_name' => 'STENOGRAPHY & TYPING (ENGLISH / HINDI)',
                'duration' => '12 Months',
                'eligibility' => '12th Pass / Graduate',
                'default_course_fee' => 8000.00,
                'registration_fee' => 600.00,
                'exam_fee' => 400.00,
                'certificate_fee' => 400.00,
                'is_popular' => false,
                'syllabus' => "- Pitman English Shorthand Theory & Grammalogues\n- Hindi Shorthand (Rishi / Vishisht Pranali)\n- Audio Dictation Speeds (80 WPM & 100 WPM)\n- Shorthand Transcription to Computer\n- Secretarial Practice & Drafting Skills",
                'subjects' => [
                    ['code' => 'STN-101', 'name' => 'Shorthand Theory & Dictation', 'theory' => 50, 'practical' => 40, 'viva' => 10],
                    ['code' => 'STN-102', 'name' => 'Transcription & Speed Typing', 'theory' => 30, 'practical' => 60, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Accounting & Financial IT',
                'course_code' => 'TALLY-GST',
                'course_name' => 'TALLY PRIME WITH GST & INVENTORY',
                'duration' => '3 Months',
                'eligibility' => '10th / 12th / Commerce',
                'default_course_fee' => 4000.00,
                'registration_fee' => 400.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "- Principles of Accounting & Ledger Creation\n- Voucher Entry (Sales, Purchase, Payment, Receipt)\n- Inventory Management & Stock Items\n- Goods & Services Tax (CGST, SGST, IGST)\n- E-Way Bill & E-Invoicing Generation\n- Balance Sheet, Profit & Loss, GST Returns Export",
                'subjects' => [
                    ['code' => 'TAL-101', 'name' => 'Accounting Fundamentals & Tally', 'theory' => 60, 'practical' => 30, 'viva' => 10],
                    ['code' => 'TAL-102', 'name' => 'GST Compliance & Final Accounts', 'theory' => 60, 'practical' => 30, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Diploma Programs',
                'course_code' => 'DTP',
                'course_name' => 'DESKTOP PUBLISHING & GRAPHIC DESIGN (DTP)',
                'duration' => '6 Months',
                'eligibility' => '10th Pass',
                'default_course_fee' => 5000.00,
                'registration_fee' => 500.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 400.00,
                'is_popular' => false,
                'syllabus' => "- Adobe Photoshop: Photo Editing, Color Correction, Layers, Retouching\n- CorelDraw: Vector Graphics, Logo Design, Visiting Cards, Pamphlets, Banners\n- PageMaker / InDesign: Book Layouts, Magazines, Newspapers\n- Print Media Setup & Commercial Printing Principles",
                'subjects' => [
                    ['code' => 'DTP-101', 'name' => 'Photo Retouching & Photoshop', 'theory' => 50, 'practical' => 40, 'viva' => 10],
                    ['code' => 'DTP-102', 'name' => 'Vector Graphics & Page Layouts', 'theory' => 50, 'practical' => 40, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Programming & Software',
                'course_code' => 'PYTHON',
                'course_name' => 'CERTIFICATE IN PYTHON PROGRAMMING',
                'duration' => '3 Months',
                'eligibility' => '12th Pass / Undergraduate',
                'default_course_fee' => 4500.00,
                'registration_fee' => 500.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "- Python Syntax, Variables & Data Types\n- Control Flow, Loops, Conditionals\n- Functions, Modules & Packages\n- Object Oriented Programming (OOP)\n- File Handling & Exception Handling\n- Introduction to Data Handling with Pandas\n- Capstone Project",
                'subjects' => [
                    ['code' => 'PY-101', 'name' => 'Python Programming Basics', 'theory' => 60, 'practical' => 30, 'viva' => 10],
                    ['code' => 'PY-102', 'name' => 'Data Structures & Applications', 'theory' => 60, 'practical' => 30, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Diploma Programs',
                'course_code' => 'PGDCA',
                'course_name' => 'POST GRADUATE DIPLOMA IN COMPUTER APPLICATIONS',
                'duration' => '12 Months',
                'eligibility' => 'Graduate in any stream',
                'default_course_fee' => 9500.00,
                'registration_fee' => 500.00,
                'exam_fee' => 400.00,
                'certificate_fee' => 400.00,
                'is_popular' => true,
                'syllabus' => "Advanced IT Architecture, SQL, Cloud Fundamentals & Industrial Project.",
                'subjects' => [
                    ['code' => 'PGD-101', 'name' => 'Advanced Computing & IT Systems', 'theory' => 70, 'practical' => 20, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Programming & Software',
                'course_code' => 'WEB-DEV',
                'course_name' => 'CERTIFICATE IN WEB DESIGNING & DEVELOPMENT',
                'duration' => '6 Months',
                'eligibility' => '10th / 12th Pass',
                'default_course_fee' => 6500.00,
                'registration_fee' => 500.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "HTML5, Modern CSS3, Bootstrap 5 UI, JavaScript Essentials & Hosting.",
                'subjects' => [
                    ['code' => 'WD-101', 'name' => 'Web Designing & Frontend Tools', 'theory' => 50, 'practical' => 40, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Certificate Courses',
                'course_code' => 'HARDWARE',
                'course_name' => 'CERTIFICATE IN COMPUTER HARDWARE & NETWORKING',
                'duration' => '6 Months',
                'eligibility' => '10th Pass',
                'default_course_fee' => 5500.00,
                'registration_fee' => 400.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "PC Assembling, CPU Diagnostics, LAN Cabling & Network Troubleshooting.",
                'subjects' => [
                    ['code' => 'CHN-101', 'name' => 'Computer Hardware Diagnostics', 'theory' => 50, 'practical' => 40, 'viva' => 10],
                ],
            ],
            [
                'category' => 'Accounting & Financial IT',
                'course_code' => 'DFA',
                'course_name' => 'DIPLOMA IN FINANCIAL ACCOUNTING & ADVANCED EXCEL',
                'duration' => '6 Months',
                'eligibility' => '10th / 12th / Commerce',
                'default_course_fee' => 4800.00,
                'registration_fee' => 400.00,
                'exam_fee' => 300.00,
                'certificate_fee' => 300.00,
                'is_popular' => true,
                'syllabus' => "Tally Prime Voucher Entries, GST Filing, Advanced MS Excel & Balance Sheet.",
                'subjects' => [
                    ['code' => 'DFA-101', 'name' => 'Financial Accounting & Advanced Excel', 'theory' => 60, 'practical' => 30, 'viva' => 10],
                ],
            ],
        ];

        foreach ($coursesData as $c) {
            $cat = $categoryModels[$c['category']];
            $course = Course::firstOrCreate(
                ['course_code' => $c['course_code']],
                [
                    'category_id' => $cat->id,
                    'course_name' => $c['course_name'],
                    'slug' => Str::slug($c['course_name']),
                    'duration' => $c['duration'],
                    'eligibility' => $c['eligibility'],
                    'default_course_fee' => $c['default_course_fee'],
                    'registration_fee' => $c['registration_fee'],
                    'exam_fee' => $c['exam_fee'],
                    'certificate_fee' => $c['certificate_fee'],
                    'syllabus' => $c['syllabus'],
                    'thumbnail' => 'images/courses/' . strtolower(Str::slug($c['course_code'])) . '.svg',
                    'is_popular' => $c['is_popular'],
                    'status' => true,
                ]
            );

            foreach ($c['subjects'] as $idx => $s) {
                CourseSubject::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'subject_code' => $s['code'],
                    ],
                    [
                        'subject_name' => $s['name'],
                        'theory_max' => $s['theory'],
                        'practical_max' => $s['practical'],
                        'viva_max' => $s['viva'],
                        'total_max' => $s['theory'] + $s['practical'] + $s['viva'],
                        'min_pass_marks' => 40,
                        'sort_order' => $idx + 1,
                    ]
                );
            }
        }
    }
}
