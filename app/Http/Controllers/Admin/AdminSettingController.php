<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSettingController extends Controller
{
    /**
     * Display the system settings hub.
     */
    public function index()
    {
        $savedSettings = Setting::getAllKeyValues();

        // Default application parameters
        $defaults = [
            // Branding & Institute Identity
            'institute_name'               => 'TECH ERA COMPUTER',
            'institute_tagline'            => 'Building Digital Skills for the Future',
            'affiliation_text'             => '',
            'institute_logo'               => 'images/tech-era-logo.png',
            'institute_favicon'            => null,
            'institute_seal'               => null,

            // Communication & Corporate Location
            'contact_email'                => '',
            'contact_phone'                => '9162384660',
            'helpline_number'              => '9162384660',
            'whatsapp_number'              => '9162384660',
            'corporate_address'            => 'Sasbahana, Sheikhpura, Bihar - 801105',

            // Academic & Operations
            'academic_session'             => '2025-2026',
            'admissions_open'              => '1',
            'exam_registrations_open'      => '1',
            'franchise_applications_open'  => '1',

            // Financial & Document Numbering
            'receipt_prefix'               => 'RCP',
            'certificate_prefix'           => 'DCE/CERT',
            'marksheet_prefix'             => 'DCE/MS',
            'currency_symbol'              => '₹',
            'tax_percentage'               => '0.00',
            'franchise_admission_commission' => '200.00',

            // Integrations & Notification Toggles
            'online_payments_enabled'      => '1',
            'payment_gateway_mode'         => 'sandbox',
            'sms_alerts_enabled'           => '0',
            'whatsapp_alerts_enabled'      => '0',

            // Razorpay Gateway Parameters
            'razorpay_key_id'                  => '',
            'razorpay_key_secret'              => '',
            'razorpay_webhook_secret'          => '',
            'razorpay_wallet_recharge_enabled' => '1',

            // Head Office UPI & Franchise Wallet QR Settings
            'admin_upi_id'                 => '9162384660@upi',
            'admin_upi_name'               => 'TECH ERA COMPUTER',
            'admin_qr_code'                => null,
            'admin_bank_name'              => 'State Bank of India',
            'admin_bank_account_no'        => '38491028472',
            'admin_bank_ifsc'              => 'SBIN0001234',
            'admin_bank_holder'            => 'TECH ERA COMPUTER',
        ];

        $settings = array_merge($defaults, $savedSettings);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system configuration parameters in database.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Branding
            'institute_name'              => ['required', 'string', 'max:191'],
            'institute_tagline'           => ['nullable', 'string', 'max:255'],
            'affiliation_text'            => ['nullable', 'string', 'max:500'],
            'institute_logo'              => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'institute_favicon'           => ['nullable', 'image', 'mimes:jpeg,png,webp,gif,ico', 'max:1024'],
            'institute_seal'              => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],

            // Contact
            'contact_email'               => ['required', 'email', 'max:191'],
            'contact_phone'               => ['required', 'string', 'max:50'],
            'helpline_number'             => ['nullable', 'string', 'max:50'],
            'whatsapp_number'             => ['nullable', 'string', 'max:50'],
            'corporate_address'           => ['required', 'string', 'max:500'],

            // Academic
            'academic_session'            => ['required', 'string', 'max:50'],
            'admissions_open'             => ['nullable', 'boolean'],
            'exam_registrations_open'     => ['nullable', 'boolean'],
            'franchise_applications_open' => ['nullable', 'boolean'],

            // Financial
            'receipt_prefix'              => ['required', 'string', 'max:20'],
            'certificate_prefix'          => ['required', 'string', 'max:30'],
            'marksheet_prefix'            => ['required', 'string', 'max:30'],
            'currency_symbol'             => ['required', 'string', 'max:10'],
            'tax_percentage'              => ['nullable', 'numeric', 'min:0', 'max:100'],
            'franchise_admission_commission' => ['nullable', 'numeric', 'min:0'],

            // Integrations
            'online_payments_enabled'     => ['nullable', 'boolean'],
            'payment_gateway_mode'        => ['required', 'string', 'in:sandbox,live'],
            'sms_alerts_enabled'          => ['nullable', 'boolean'],
            'whatsapp_alerts_enabled'     => ['nullable', 'boolean'],

            // Razorpay Gateway Credentials
            'razorpay_key_id'                  => ['nullable', 'string', 'max:100'],
            'razorpay_key_secret'              => ['nullable', 'string', 'max:100'],
            'razorpay_webhook_secret'          => ['nullable', 'string', 'max:100'],
            'razorpay_wallet_recharge_enabled' => ['nullable', 'boolean'],

            // Head Office UPI & Bank Details
            'admin_upi_id'                => ['nullable', 'string', 'max:100'],
            'admin_upi_name'              => ['nullable', 'string', 'max:191'],
            'admin_qr_code'               => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'admin_bank_name'             => ['nullable', 'string', 'max:100'],
            'admin_bank_account_no'       => ['nullable', 'string', 'max:50'],
            'admin_bank_ifsc'             => ['nullable', 'string', 'max:50'],
            'admin_bank_holder'           => ['nullable', 'string', 'max:191'],
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // Group 1: Branding
            Setting::set('institute_name', $validated['institute_name'], 'branding');
            Setting::set('institute_tagline', $validated['institute_tagline'] ?? '', 'branding');
            Setting::set('affiliation_text', $validated['affiliation_text'] ?? '', 'branding');

            if ($request->hasFile('institute_logo')) {
                $logoPath = FileUploadService::uploadImage($request->file('institute_logo'), 'uploads/settings');
                Setting::set('institute_logo', $logoPath, 'branding');
            }

            if ($request->hasFile('institute_favicon')) {
                $favPath = FileUploadService::uploadImage($request->file('institute_favicon'), 'uploads/settings');
                Setting::set('institute_favicon', $favPath, 'branding');
            }

            if ($request->hasFile('institute_seal')) {
                $sealPath = FileUploadService::uploadImage($request->file('institute_seal'), 'uploads/settings');
                Setting::set('institute_seal', $sealPath, 'branding');
            }

            // Group 2: Contact
            Setting::set('contact_email', $validated['contact_email'], 'contact');
            Setting::set('contact_phone', $validated['contact_phone'], 'contact');
            Setting::set('helpline_number', $validated['helpline_number'] ?? '', 'contact');
            Setting::set('whatsapp_number', $validated['whatsapp_number'] ?? '', 'contact');
            Setting::set('corporate_address', $validated['corporate_address'], 'contact');

            // Group 3: Academic
            Setting::set('academic_session', $validated['academic_session'], 'academic');
            Setting::set('admissions_open', $request->boolean('admissions_open') ? '1' : '0', 'academic');
            Setting::set('exam_registrations_open', $request->boolean('exam_registrations_open') ? '1' : '0', 'academic');
            Setting::set('franchise_applications_open', $request->boolean('franchise_applications_open') ? '1' : '0', 'academic');

            // Group 4: Financial & Numbering
            Setting::set('receipt_prefix', strtoupper(trim($validated['receipt_prefix'])), 'financial');
            Setting::set('certificate_prefix', strtoupper(trim($validated['certificate_prefix'])), 'financial');
            Setting::set('marksheet_prefix', strtoupper(trim($validated['marksheet_prefix'])), 'financial');
            Setting::set('currency_symbol', $validated['currency_symbol'], 'financial');
            Setting::set('tax_percentage', (string) ($validated['tax_percentage'] ?? '0.00'), 'financial');
            Setting::set('franchise_admission_commission', (string) ($validated['franchise_admission_commission'] ?? '200.00'), 'financial');

            // Group 5: Integrations & Razorpay
            Setting::set('online_payments_enabled', $request->boolean('online_payments_enabled') ? '1' : '0', 'integrations');
            Setting::set('payment_gateway_mode', $validated['payment_gateway_mode'], 'integrations');
            Setting::set('sms_alerts_enabled', $request->boolean('sms_alerts_enabled') ? '1' : '0', 'integrations');
            Setting::set('whatsapp_alerts_enabled', $request->boolean('whatsapp_alerts_enabled') ? '1' : '0', 'integrations');
            Setting::set('razorpay_key_id', trim($validated['razorpay_key_id'] ?? ''), 'payment');
            Setting::set('razorpay_key_secret', trim($validated['razorpay_key_secret'] ?? ''), 'payment');
            Setting::set('razorpay_webhook_secret', trim($validated['razorpay_webhook_secret'] ?? ''), 'payment');
            Setting::set('razorpay_wallet_recharge_enabled', $request->boolean('razorpay_wallet_recharge_enabled') ? '1' : '0', 'payment');

            // Group 6: Head Office UPI & Wallet Recharge Settings
            if (isset($validated['admin_upi_id'])) {
                Setting::set('admin_upi_id', trim($validated['admin_upi_id']), 'payment');
            }
            if (isset($validated['admin_upi_name'])) {
                Setting::set('admin_upi_name', trim($validated['admin_upi_name']), 'payment');
            }
            if ($request->hasFile('admin_qr_code')) {
                $qrPath = FileUploadService::uploadImage($request->file('admin_qr_code'), 'uploads/settings');
                Setting::set('admin_qr_code', $qrPath, 'payment');
            }
            if (isset($validated['admin_bank_name'])) {
                Setting::set('admin_bank_name', trim($validated['admin_bank_name']), 'payment');
            }
            if (isset($validated['admin_bank_account_no'])) {
                Setting::set('admin_bank_account_no', trim($validated['admin_bank_account_no']), 'payment');
            }
            if (isset($validated['admin_bank_ifsc'])) {
                Setting::set('admin_bank_ifsc', strtoupper(trim($validated['admin_bank_ifsc'])), 'payment');
            }
            if (isset($validated['admin_bank_holder'])) {
                Setting::set('admin_bank_holder', trim($validated['admin_bank_holder']), 'payment');
            }

            AuditLog::record('settings_updated', null, null, [
                'updated_by' => auth()->id(),
                'ip'         => $request->ip(),
            ]);

            return redirect()->route('admin.settings.index')
                ->with('success', 'System settings and institutional configuration updated successfully.');
        });
    }
}
