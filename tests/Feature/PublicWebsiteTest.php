<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enquiry;
use App\Models\FranchiseApplication;
use App\Models\OnlineTransaction;
use App\Models\State;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    public function test_all_public_pages_render_successfully(): void
    {
        $routes = [
            '/',
            '/about-us',
            '/company',
            '/chairman',
            '/mission',
            '/corporate-members',
            '/facilities',
            '/gallery',
            '/contact-us',
            '/admission-enquiry',
            '/check-admission',
            '/check-certificate-marksheet',
            '/online-payment',
            '/frenchise',
            '/search_centre',
            '/frenchise-status',
            '/student',
            '/login',
            '/privacy-policy',
            '/terms-conditions',
            '/refund-policy',
            '/courses',
            '/sitemap.xml',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }

    public function test_course_details_page_renders_with_database_record(): void
    {
        $course = Course::first();
        $this->assertNotNull($course, 'A course should exist in database.');

        $response = $this->get('/course/' . $course->slug);
        $response->assertStatus(200);
        $response->assertSee($course->course_name);
    }

    public function test_certificate_verification_searches_actual_records(): void
    {
        // Search by known seeded certificate student
        $response = $this->post('/check-certificate-marksheet', [
            'registration' => 'DCE/2025/101/0042',
            'father' => 'RAMESH SHARMA',
        ]);

        $response->assertStatus(200);
        $response->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');
        $response->assertSee('RAHUL SHARMA');
        $response->assertSee('DCE2501042');
    }

    public function test_admission_verification_checks_student_enrollment(): void
    {
        $response = $this->post('/check-admission', [
            'search_key' => 'DCE2501042',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Verified Student Admission Record');
        $response->assertSee('RAHUL SHARMA');
    }

    public function test_direct_qr_certificate_verification(): void
    {
        $response = $this->get('/verify/cert/DCE-CERT-2025-1042');
        $response->assertStatus(200);
        $response->assertSee('RAHUL SHARMA');
        $response->assertSee('OFFICIALLY VERIFIED CERTIFICATE RECORD');
    }

    public function test_demo_enquiry_form_validates_and_persists_to_mysql(): void
    {
        $state = State::first();
        $district = $state->districts()->first();

        $payload = [
            'name' => 'Kunal Kashyap',
            'mobile' => '9123456780',
            'state_id' => $state->id,
            'district_id' => $district->id,
            'subject' => 'Live Demo for Centre in Patna',
        ];

        $response = $this->post('/demo-request', $payload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Kunal Kashyap',
            'mobile' => '9123456780',
            'type' => 'demo_request',
        ]);
    }

    public function test_franchise_application_validates_and_generates_tracking_number(): void
    {
        $state = State::first();
        $district = $state->districts()->first();

        $payload = [
            'applicant_name' => 'Deepak Verma',
            'centre_name' => 'Drishti Apex Skill Center',
            'mobile' => '9876500112',
            'email' => 'deepak@example.com',
            'state_id' => $state->id,
            'district_id' => $district->id,
            'city' => 'Muzaffarpur',
            'address' => 'Station Road Near Tower',
            'qualification' => 'MCA Computer Science',
            'computers_count' => 10,
            'carpet_area_sqft' => 500,
            'remarks' => 'Ready to launch next month',
        ];

        $response = $this->post('/frenchise/submit', $payload);
        $response->assertRedirect(route('franchise.status'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('franchise_applications', [
            'applicant_name' => 'Deepak Verma',
            'mobile' => '9876500112',
        ]);
    }

    public function test_online_fee_payment_processing_creates_verified_transaction_and_receipt(): void
    {
        $payload = [
            'payer_name' => 'Vijay Saxena',
            'payer_mobile' => '9988776655',
            'payer_email' => 'vijay@example.com',
            'student_roll_no' => 'DCE2501042',
            'purpose' => 'Course Tuition Fee',
            'amount' => 1500.00,
            'payment_method' => 'UPI',
        ];

        $response = $this->post('/online-payment/process', $payload);
        $response->assertSessionHas('success');

        $transaction = OnlineTransaction::where('payer_mobile', '9988776655')->latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('success', $transaction->status);

        $response->assertRedirect(route('payment.receipt', $transaction->transaction_id));

        $receiptResponse = $this->get(route('payment.receipt', $transaction->transaction_id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($transaction->transaction_id);
        $receiptResponse->assertSee('₹1,500.00');
    }

    public function test_api_districts_returns_json(): void
    {
        $state = State::first();
        $response = $this->get('/api/districts/' . $state->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    public function test_header_contains_student_and_franchise_login_for_desktop_and_mobile(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Student Login');
        $response->assertSee('Franchise Login');
        $response->assertSee(route('student.login'));
        $response->assertSee(route('login'));
    }

    public function test_homepage_renders_hero_banner_slider_with_images_and_quick_service_cards(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('dceHeroSlider');
        $response->assertSee('banner1.jpg');
        $response->assertSee('banner2.jpg');
        $response->assertSee('banner3.jpg');
        $response->assertSee('banner4.jpg');
        $response->assertSee('Long Term Courses');
        $response->assertSee('Short Term Courses');
        $response->assertSee('Registration');
        $response->assertSee('Admit Card');
        $response->assertSee('Student Verify');
        $response->assertSee('Marksheet');
        $response->assertSee('Certificate');
    }

    public function test_homepage_renders_twelve_courses_with_images_and_two_column_mobile_layout(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Academic Programs');
        $response->assertSee('Why Study With Us');
        $response->assertSee('dce-course-grid-card');
        $response->assertSee('dce-course-img');

        $content = $response->getContent();
        $this->assertEquals(12, substr_count($content, 'dce-course-grid-card'));
    }

    public function test_homepage_and_gallery_page_render_student_and_certificate_gallery_items(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('homeGallerySection');
        $response->assertSee('Students & Certificate Awards', false);
        $response->assertSee('home-gallery-item');
        $response->assertSee('homeGalleryLightboxModal');
        $response->assertSee('uploads/gallery/gallery_cert_1.jpg');

        $galleryPageResponse = $this->get('/gallery');
        $galleryPageResponse->assertStatus(200);
        $galleryPageResponse->assertSee('galleryContainer');
        $galleryPageResponse->assertSee('mainGalleryLightboxModal');
        $galleryPageResponse->assertSee('uploads/gallery/gallery_cert_1.jpg');
    }
}
