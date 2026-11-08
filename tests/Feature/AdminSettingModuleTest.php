<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'superadmin@drishticomputer.com'],
            [
                'name'      => 'Super Administrator',
                'password'  => bcrypt('SuperSecretAdmin@2026'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
    }

    protected function tearDown(): void
    {
        Setting::set('payment_gateway_mode', 'sandbox');
        parent::tearDown();
    }

    public function test_guest_cannot_access_settings()
    {
        $response = $this->get(route('admin.settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_settings_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('System & Institutional Settings', false);
        $response->assertSee('Institute Identity & Branding', false);
        $response->assertSee('Corporate Office & Communication Helplines', false);
        $response->assertSee('Financial & Serial Numbering Formats', false);
    }

    public function test_admin_can_update_settings()
    {
        $payload = [
            'institute_name'              => 'Drishti Computer Academy India',
            'institute_tagline'           => 'ISO 9001:2015 Premier Skill Development',
            'affiliation_text'            => 'Approved by Council for Technical Education',
            'contact_email'               => 'corporate@drishticomputer.com',
            'contact_phone'               => '+91 99999 11111',
            'helpline_number'             => '1800-999-8888',
            'whatsapp_number'             => '+91 99999 11111',
            'corporate_address'           => 'Plot 55, Tech Zone, Greater Noida, UP - 201306',
            'academic_session'            => '2026-2027',
            'admissions_open'             => 1,
            'exam_registrations_open'     => 1,
            'franchise_applications_open' => 1,
            'receipt_prefix'              => 'TEST-RCP',
            'certificate_prefix'          => 'TEST-CERT',
            'marksheet_prefix'            => 'TEST-MS',
            'currency_symbol'             => '₹',
            'tax_percentage'              => '18.00',
            'payment_gateway_mode'        => 'live',
            'online_payments_enabled'     => 1,
            'whatsapp_alerts_enabled'     => 1,
            'sms_alerts_enabled'          => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('Drishti Computer Academy India', Setting::get('institute_name'));
        $this->assertEquals('corporate@drishticomputer.com', Setting::get('contact_email'));
        $this->assertEquals('TEST-RCP', Setting::get('receipt_prefix'));
        $this->assertEquals('TEST-CERT', Setting::get('certificate_prefix'));
        $this->assertEquals('2026-2027', Setting::get('academic_session'));
        $this->assertEquals('live', Setting::get('payment_gateway_mode'));
    }

    public function test_admin_can_upload_branding_files()
    {
        Storage::fake('public');

        $logoFile = UploadedFile::fake()->image('test_logo.png', 200, 60);
        $sealFile = UploadedFile::fake()->image('test_seal.png', 100, 100);

        $payload = [
            'institute_name'          => 'Drishti Computer Education',
            'contact_email'           => 'info@drishticomputer.com',
            'contact_phone'           => '+91 98765 43210',
            'corporate_address'       => 'Knowledge Park III, Greater Noida, UP',
            'academic_session'        => '2025-2026',
            'receipt_prefix'          => 'RCP',
            'certificate_prefix'      => 'DCE/CERT',
            'marksheet_prefix'        => 'DCE/MS',
            'currency_symbol'         => '₹',
            'payment_gateway_mode'    => 'sandbox',
            'institute_logo'          => $logoFile,
            'institute_seal'          => $sealFile,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $logoSetting = Setting::get('institute_logo');
        $this->assertNotNull($logoSetting);
        Storage::disk('public')->assertExists($logoSetting);

        $sealSetting = Setting::get('institute_seal');
        $this->assertNotNull($sealSetting);
        Storage::disk('public')->assertExists($sealSetting);
    }

    public function test_validation_requires_essential_fields()
    {
        $payload = [
            'institute_name'    => '', // required
            'contact_email'     => 'not-an-email', // invalid email
            'contact_phone'     => '', // required
            'corporate_address' => '', // required
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertSessionHasErrors([
            'institute_name',
            'contact_email',
            'contact_phone',
            'corporate_address',
        ]);
    }

    public function test_admin_can_update_razorpay_credentials_in_settings()
    {
        $payload = [
            'institute_name'                   => 'Tech Era Computer Institute',
            'contact_email'                    => 'info@techeracomputer.com',
            'contact_phone'                    => '+91 91623 84660',
            'corporate_address'                => 'Main Road, Patna, Bihar',
            'academic_session'                 => '2026-2027',
            'receipt_prefix'                   => 'RCP',
            'certificate_prefix'               => 'TEC/CERT',
            'marksheet_prefix'                 => 'TEC/MS',
            'currency_symbol'                  => '₹',
            'payment_gateway_mode'             => 'sandbox',
            'razorpay_key_id'                  => 'rzp_test_CustomKey123',
            'razorpay_key_secret'              => 'Secr3tToken987654321',
            'razorpay_webhook_secret'          => 'WhSecr3t12345',
            'razorpay_wallet_recharge_enabled' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('rzp_test_CustomKey123', Setting::get('razorpay_key_id'));
        $this->assertEquals('Secr3tToken987654321', Setting::get('razorpay_key_secret'));
        $this->assertEquals('WhSecr3t12345', Setting::get('razorpay_webhook_secret'));
        $this->assertEquals('1', Setting::get('razorpay_wallet_recharge_enabled'));
    }
}
