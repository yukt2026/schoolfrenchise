@extends('layouts.admin')

@section('title', 'System Settings - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0" style="max-width: 1050px;">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">System & Institutional Settings</h2>
            <p class="text-muted small mb-0">Configure branding, contact helplines, academic sessions, receipt numbering formats, and system integrations.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="settingsForm" class="btn btn-primary shadow-sm px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm" enctype="multipart/form-data">
        @csrf

        <!-- SECTION 1: Institute Branding & Visual Identity -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-award me-2 text-primary"></i> 1. Institute Identity & Branding
                </h5>
                <p class="text-muted small mb-0">Configure legal registered name, accreditation slogans, logos, and official stamps.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="institute_name" class="form-label small fw-bold">Registered Institute Name <span class="text-danger">*</span></label>
                        <input type="text" name="institute_name" id="institute_name" class="form-control @error('institute_name') is-invalid @enderror" value="{{ old('institute_name', $settings['institute_name']) }}" required>
                        @error('institute_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="institute_tagline" class="form-label small fw-bold">Accreditation Tagline / Subtitle</label>
                        <input type="text" name="institute_tagline" id="institute_tagline" class="form-control @error('institute_tagline') is-invalid @enderror" value="{{ old('institute_tagline', $settings['institute_tagline']) }}">
                        @error('institute_tagline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="affiliation_text" class="form-label small fw-bold">Statutory & Legal Affiliation Statement</label>
                        <textarea name="affiliation_text" id="affiliation_text" rows="2" class="form-control @error('affiliation_text') is-invalid @enderror">{{ old('affiliation_text', $settings['affiliation_text']) }}</textarea>
                        <div class="form-text small">Appears on official marksheets, printed certificates, and computerized fee receipts.</div>
                        @error('affiliation_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Uploads -->
                    <div class="col-md-4">
                        <label for="institute_logo" class="form-label small fw-bold">Institute Header Logo</label>
                        <input type="file" name="institute_logo" id="institute_logo" class="form-control form-control-sm @error('institute_logo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text small">PNG/JPG/WebP, max 2MB.</div>
                        @error('institute_logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(!empty($settings['institute_logo']))
                            <div class="mt-2 p-2 border rounded bg-light text-center">
                                <img src="{{ asset('storage/' . $settings['institute_logo']) }}" alt="Current Logo" class="img-fluid" style="max-height: 48px;">
                                <div class="small text-muted mt-1 font-monospace" style="font-size: 0.7rem;">Active Logo</div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="institute_favicon" class="form-label small fw-bold">Browser Favicon</label>
                        <input type="file" name="institute_favicon" id="institute_favicon" class="form-control form-control-sm @error('institute_favicon') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text small">Square image, max 1MB.</div>
                        @error('institute_favicon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(!empty($settings['institute_favicon']))
                            <div class="mt-2 p-2 border rounded bg-light text-center">
                                <img src="{{ asset('storage/' . $settings['institute_favicon']) }}" alt="Current Favicon" style="max-height: 32px;">
                                <div class="small text-muted mt-1 font-monospace" style="font-size: 0.7rem;">Active Favicon</div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="institute_seal" class="form-label small fw-bold">Official Seal / Stamp</label>
                        <input type="file" name="institute_seal" id="institute_seal" class="form-control form-control-sm @error('institute_seal') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text small">Transparent PNG for certificates.</div>
                        @error('institute_seal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(!empty($settings['institute_seal']))
                            <div class="mt-2 p-2 border rounded bg-light text-center">
                                <img src="{{ asset('storage/' . $settings['institute_seal']) }}" alt="Current Seal" class="img-fluid" style="max-height: 48px;">
                                <div class="small text-muted mt-1 font-monospace" style="font-size: 0.7rem;">Active Stamp</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Corporate Office & Communication Channels -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-address-book me-2 text-primary"></i> 2. Corporate Office & Communication Helplines
                </h5>
                <p class="text-muted small mb-0">Official communications channels, student helpline, and registered office address.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label small fw-bold">Official Administration Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email" id="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', $settings['contact_email']) }}" required>
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label small fw-bold">Primary Office Phone <span class="text-danger">*</span></label>
                        <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', $settings['contact_phone']) }}" required>
                        @error('contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="helpline_number" class="form-label small fw-bold">Toll-Free / Student Support Helpline</label>
                        <input type="text" name="helpline_number" id="helpline_number" class="form-control @error('helpline_number') is-invalid @enderror" value="{{ old('helpline_number', $settings['helpline_number']) }}">
                        @error('helpline_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="whatsapp_number" class="form-label small fw-bold">Official WhatsApp Broadcast Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}">
                        @error('whatsapp_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="corporate_address" class="form-label small fw-bold">Corporate Headquarters Postal Address <span class="text-danger">*</span></label>
                        <textarea name="corporate_address" id="corporate_address" rows="2" class="form-control @error('corporate_address') is-invalid @enderror" required>{{ old('corporate_address', $settings['corporate_address']) }}</textarea>
                        @error('corporate_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Academic Operations & Portal Controls -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-graduation-cap me-2 text-primary"></i> 3. Academic Sessions & Operational Toggles
                </h5>
                <p class="text-muted small mb-0">Active academic year and admissions lifecycle controls.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="academic_session" class="form-label small fw-bold">Current Academic Session <span class="text-danger">*</span></label>
                        <input type="text" name="academic_session" id="academic_session" class="form-control @error('academic_session') is-invalid @enderror" value="{{ old('academic_session', $settings['academic_session']) }}" placeholder="e.g. 2025-2026" required>
                        <div class="form-text small">Used for new admissions, batch allocation, and certificate generation.</div>
                        @error('academic_session')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="admissions_open" name="admissions_open" value="1" {{ old('admissions_open', $settings['admissions_open']) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark" for="admissions_open">
                                    New Student Admissions Open
                                </label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="exam_registrations_open" name="exam_registrations_open" value="1" {{ old('exam_registrations_open', $settings['exam_registrations_open']) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark" for="exam_registrations_open">
                                    Examination Hall Ticket Registration Open
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="franchise_applications_open" name="franchise_applications_open" value="1" {{ old('franchise_applications_open', $settings['franchise_applications_open']) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark" for="franchise_applications_open">
                                    Accept Public Franchise Applications
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Financial & Document Numbering Parameters -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-receipt me-2 text-primary"></i> 4. Financial & Serial Numbering Formats
                </h5>
                <p class="text-muted small mb-0">Receipt prefixes, certificate serial keys, and taxation parameters.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="receipt_prefix" class="form-label small fw-bold">Fee Receipt Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="receipt_prefix" id="receipt_prefix" class="form-control font-monospace @error('receipt_prefix') is-invalid @enderror" value="{{ old('receipt_prefix', $settings['receipt_prefix']) }}" required>
                        <div class="form-text small">e.g. RCP generates <code>RCP-2026-0001</code></div>
                        @error('receipt_prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="certificate_prefix" class="form-label small fw-bold">Certificate Serial Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="certificate_prefix" id="certificate_prefix" class="form-control font-monospace @error('certificate_prefix') is-invalid @enderror" value="{{ old('certificate_prefix', $settings['certificate_prefix']) }}" required>
                        <div class="form-text small">e.g. DCE/CERT generates <code>DCE/CERT/2026/01</code></div>
                        @error('certificate_prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="marksheet_prefix" class="form-label small fw-bold">Marksheet Serial Prefix <span class="text-danger">*</span></label>
                        <input type="text" name="marksheet_prefix" id="marksheet_prefix" class="form-control font-monospace @error('marksheet_prefix') is-invalid @enderror" value="{{ old('marksheet_prefix', $settings['marksheet_prefix']) }}" required>
                        <div class="form-text small">e.g. DCE/MS generates <code>DCE/MS/2026/01</code></div>
                        @error('marksheet_prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="currency_symbol" class="form-label small fw-bold">Currency Symbol <span class="text-danger">*</span></label>
                        <input type="text" name="currency_symbol" id="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required>
                        @error('currency_symbol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tax_percentage" class="form-label small fw-bold">Applicable GST / Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_percentage" id="tax_percentage" class="form-control @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage', $settings['tax_percentage']) }}">
                        <div class="form-text small">Set 0.00 for tax-exempt education vocational courses.</div>
                        @error('tax_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="franchise_admission_commission" class="form-label small fw-bold">Franchise Admission Fee / Deduction per Student (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">₹</span>
                            <input type="number" step="1" min="0" name="franchise_admission_commission" id="franchise_admission_commission" class="form-control @error('franchise_admission_commission') is-invalid @enderror" value="{{ old('franchise_admission_commission', $settings['franchise_admission_commission'] ?? '200.00') }}" placeholder="200.00">
                        </div>
                        <div class="form-text small">Amount automatically debited from Franchise Wallet upon eligible student admission (Default: ₹200).</div>
                        @error('franchise_admission_commission')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Integrations & Gateways -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-network-wired me-2 text-primary"></i> 5. Integrations & Payment Gateway Environment
                </h5>
                <p class="text-muted small mb-0">Payment gateway testing mode and external messaging dispatches.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="payment_gateway_mode" class="form-label small fw-bold">Payment Gateway Mode <span class="text-danger">*</span></label>
                        <select name="payment_gateway_mode" id="payment_gateway_mode" class="form-select @error('payment_gateway_mode') is-invalid @enderror" required>
                            <option value="sandbox" {{ old('payment_gateway_mode', $settings['payment_gateway_mode']) === 'sandbox' ? 'selected' : '' }}>Sandbox / Test Mode (Simulated Checkout)</option>
                            <option value="live" {{ old('payment_gateway_mode', $settings['payment_gateway_mode']) === 'live' ? 'selected' : '' }}>Live Production Gateway</option>
                        </select>
                        @error('payment_gateway_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center pt-md-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="online_payments_enabled" name="online_payments_enabled" value="1" {{ old('online_payments_enabled', $settings['online_payments_enabled']) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small" for="online_payments_enabled">
                                Enable Online Public Fee Checkout
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="whatsapp_alerts_enabled" name="whatsapp_alerts_enabled" value="1" {{ old('whatsapp_alerts_enabled', $settings['whatsapp_alerts_enabled']) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark" for="whatsapp_alerts_enabled">
                                    <i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp Instant Notifications
                                </label>
                                <div class="small text-muted mt-1">Dispatches automated payment receipts and admit cards.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="sms_alerts_enabled" name="sms_alerts_enabled" value="1" {{ old('sms_alerts_enabled', $settings['sms_alerts_enabled']) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark" for="sms_alerts_enabled">
                                    <i class="fa-solid fa-comment-sms text-primary me-1"></i> SMS DLT Gateway Notifications
                                </label>
                                <div class="small text-muted mt-1">Dispatches transactional OTPs and admission confirmations.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 6: Head Office UPI & Franchise Wallet Recharge QR -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-qrcode me-2 text-success"></i> 6. Head Office UPI & Wallet Recharge Settings
                        </h5>
                        <p class="text-muted small mb-0">Configure the Head Office UPI ID, Payee Name, and official bank accounts. When franchises recharge their wallet, the system automatically generates an exact-amount UPI QR code with these details.</p>
                    </div>
                    <span class="badge bg-success-subtle text-success px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-shield-check me-1"></i> Admin Supervised
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="admin_upi_id" class="form-label small fw-bold">Head Office Official UPI ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-at text-success"></i></span>
                            <input type="text" name="admin_upi_id" id="admin_upi_id" class="form-control font-monospace fw-bold @error('admin_upi_id') is-invalid @enderror" value="{{ old('admin_upi_id', $settings['admin_upi_id'] ?? '9162384660@upi') }}" placeholder="e.g. 9162384660@upi or techeracomputer@okaxis">
                        </div>
                        <div class="form-text small">This UPI ID is embedded into the dynamic QR code generated for franchises to scan and pay.</div>
                        @error('admin_upi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="admin_upi_name" class="form-label small fw-bold">Payee / Account Display Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-building text-primary"></i></span>
                            <input type="text" name="admin_upi_name" id="admin_upi_name" class="form-control fw-semibold @error('admin_upi_name') is-invalid @enderror" value="{{ old('admin_upi_name', $settings['admin_upi_name'] ?? 'TECH ERA COMPUTER') }}" placeholder="e.g. TECH ERA COMPUTER">
                        </div>
                        <div class="form-text small">Displayed on PhonePe, GPay, Paytm as the verified receiver name.</div>
                        @error('admin_upi_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="admin_qr_code" class="form-label small fw-bold">Official Standee / Merchant QR Image <small class="text-muted">(Optional)</small></label>
                        <input type="file" name="admin_qr_code" id="admin_qr_code" class="form-control form-control-sm @error('admin_qr_code') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text small">Upload your physical shop/office QR code image (PNG/JPG/WebP, max 2MB) if you wish to display it alongside the dynamic QR.</div>
                        @error('admin_qr_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(!empty($settings['admin_qr_code']))
                            <div class="mt-2 p-2 border rounded bg-light d-flex align-items-center gap-3">
                                <img src="{{ asset('storage/' . $settings['admin_qr_code']) }}" alt="Uploaded QR Code" class="img-thumbnail" style="max-height: 80px;">
                                <div class="small">
                                    <span class="badge bg-success">Uploaded Merchant QR</span>
                                    <div class="text-muted mt-1 font-monospace" style="font-size: 0.72rem;">{{ $settings['admin_qr_code'] }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="admin_bank_holder" class="form-label small fw-bold">Bank Account Holder Name</label>
                        <input type="text" name="admin_bank_holder" id="admin_bank_holder" class="form-control @error('admin_bank_holder') is-invalid @enderror" value="{{ old('admin_bank_holder', $settings['admin_bank_holder'] ?? 'TECH ERA COMPUTER') }}" placeholder="TECH ERA COMPUTER">
                        @error('admin_bank_holder')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="admin_bank_name" class="form-label small fw-bold">Bank Name</label>
                        <input type="text" name="admin_bank_name" id="admin_bank_name" class="form-control @error('admin_bank_name') is-invalid @enderror" value="{{ old('admin_bank_name', $settings['admin_bank_name'] ?? 'State Bank of India') }}" placeholder="e.g. State Bank of India">
                        @error('admin_bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="admin_bank_account_no" class="form-label small fw-bold">Bank Account Number</label>
                        <input type="text" name="admin_bank_account_no" id="admin_bank_account_no" class="form-control font-monospace @error('admin_bank_account_no') is-invalid @enderror" value="{{ old('admin_bank_account_no', $settings['admin_bank_account_no'] ?? '38491028472') }}" placeholder="e.g. 38491028472">
                        @error('admin_bank_account_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="admin_bank_ifsc" class="form-label small fw-bold">Bank IFSC Code</label>
                        <input type="text" name="admin_bank_ifsc" id="admin_bank_ifsc" class="form-control font-monospace text-uppercase @error('admin_bank_ifsc') is-invalid @enderror" value="{{ old('admin_bank_ifsc', $settings['admin_bank_ifsc'] ?? 'SBIN0001234') }}" placeholder="e.g. SBIN0001234">
                        @error('admin_bank_ifsc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Submit Footer -->
        <div class="d-flex justify-content-end gap-2 pt-3 pb-5">
            <button type="submit" class="btn btn-primary px-5 py-2 shadow">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
