@php
    $adm = $admission ?? ($student?->admissions?->first() ?? null);
    $stu = $student ?? ($adm?->student ?? null);
    $crs = $course ?? ($adm?->course ?? ($stu?->course ?? null));
    $cnt = $centre ?? ($adm?->centre ?? ($stu?->centre ?? null));
    $btc = $batch ?? ($adm?->batch ?? null);

    // Fallback student admission date
    $admDate = $adm?->admission_date 
        ? $adm->admission_date->format('d-m-Y') 
        : ($stu?->created_at ? $stu->created_at->format('d-m-Y') : date('d-m-Y'));

    $dobFormatted = $stu?->dob ? $stu->dob->format('d-m-Y') : 'N/A';

    // Unique Verification URL & Real Vector SVG QR Code
    $qrUrl = $stu ? $stu->verification_url : route('verify.admission');
    $qrSvg = \App\Services\QrCodeService::generateSvg($qrUrl, 120);

    // Dynamic Franchise Branding
    $franchiseName = $cnt?->centre_name ?? 'TECH ERA COMPUTER INSTITUTE';
    $franchiseAddress = $cnt?->address ?? (($cnt?->city ?? 'Sasbahana, Sheikhpura') . ', ' . ($cnt?->state?->name ?? 'Bihar'));
    $franchiseLogo = $cnt?->centre_logo_url ?? asset('images/tech-era-logo.png');
    $masterSeal = asset('images/tech-era-seal.png');
    $masterSignature = asset('images/deepak-kumar-signature.png');
    $parentFoundation = $instituteSettings['parent_foundation'] ?? 'AROGYA JEEVAN SOCIAL HEALTHCARE FOUNDATION';
    $regOffice = $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105';
    $helplinePhone = $instituteSettings['contact_phone'] ?? '9162384660';
    $officialEmail = $instituteSettings['official_email'] ?? 'info@techeracomputer.in';
    $websiteUrl = $instituteSettings['website'] ?? 'www.techeracomputer.in';

    // Fee Ledger Calculations
    $courseFee = (float) ($adm?->course_fee ?: ($crs?->default_course_fee ?: 0));
    $regFee = (float) ($adm?->registration_fee ?: ($crs?->registration_fee ?: 0));
    $discount = (float) ($adm?->discount_amount ?: 0);
    $netFee = (float) ($adm?->net_fee ?: max(0, ($courseFee + $regFee - $discount)));
    $totalPaid = (float) ($adm?->total_paid ?: ($adm?->paid_amount ?: 0));
    $balanceDue = max(0, $netFee - $totalPaid);
@endphp

<div class="adm-outer-sheet">
    <!-- MASTER ORNATE FRAME -->
    <div class="adm-gold-frame">
        <div class="adm-inner-frame">

            <!-- CORNER GEOMETRIC ORNAMENTS -->
            <div class="adm-corner adm-corner-tl"></div>
            <div class="adm-corner adm-corner-tr"></div>
            <div class="adm-corner adm-corner-bl"></div>
            <div class="adm-corner adm-corner-br"></div>

            <!-- WATERMARK EMBLEM -->
            <div class="adm-watermark-bg">
                <img src="{{ $masterSeal }}" alt="Seal Watermark" class="adm-watermark-img">
            </div>

            <div class="adm-main-content">

                <!-- 1. HEADER SECTION (Brand, Logo & Accreditations) -->
                <div class="adm-header-grid">
                    <!-- Left: Franchise Logo -->
                    <div class="adm-logo-box">
                        <img src="{{ $franchiseLogo }}" 
                             alt="{{ $franchiseName }}" 
                             class="adm-franchise-logo" 
                             onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                    </div>

                    <!-- Center: Franchise Name & Legal Info -->
                    <div class="adm-brand-box">
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                            <h1 class="adm-title-franchise">{{ $franchiseName }}</h1>
                            <span class="adm-tagline-script">Learn focus &amp; Grow</span>
                        </div>

                        <div class="adm-legal-text">
                            <div class="fw-bold">Run under: {{ $parentFoundation }}</div>
                            <div>Reg. by- Ministry of Corporate Affairs (Govt. of India) sub-section (2) of section 7 and sub-section (1) of section 8 of the Companies Act, 2013 (18 of 2013) and rule 18 of the Companies (Incorporation) Rules, 2014]</div>
                            <div class="adm-cin-line">
                                <strong>CIN-U85300BR2022NPL055886</strong> &nbsp;&bull;&nbsp; 
                                Regd. NITI Aayog Unique ID: <strong>BR/2022/0304927</strong> &nbsp;&bull;&nbsp; 
                                <span class="text-danger fw-bold">(AN ISO 9001:2015 Certified)</span>
                            </div>
                        </div>

                        <!-- Fixed Non-Editable Main Admin Authorization Banner -->
                        <div class="adm-auth-badge">
                            <i class="fa-solid fa-shield-halved me-1 text-warning"></i>
                            AUTHORIZED BY: TECH ERA COMPUTER
                        </div>
                    </div>

                    <!-- Right: Empty spacer for alignment symmetry -->
                    <div class="adm-header-spacer"></div>
                </div>

                <!-- 2. TITLE BANNER -->
                <div class="adm-title-banner-wrap">
                    <div class="adm-title-badge">
                        OFFICIAL STUDENT ADMISSION DOSSIER &amp; ENROLMENT FORM
                    </div>
                </div>

                <!-- 3. TOP META STRIP & STUDENT PHOTO -->
                <div class="adm-top-meta-grid">
                    <div class="adm-meta-fields">
                        <div class="adm-meta-pill-box">
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Registration No.:</span>
                                <strong class="adm-val font-monospace text-dark">{{ $stu?->registration_no ?? 'DCE/2026/0001' }}</strong>
                            </div>
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Roll Number:</span>
                                <strong class="adm-val font-monospace text-primary">{{ $stu?->roll_no ?? 'Under Process' }}</strong>
                            </div>
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Admission Date:</span>
                                <strong class="adm-val">{{ $admDate }}</strong>
                            </div>
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Academic Session:</span>
                                <strong class="adm-val">{{ $stu?->academic_session ?? date('Y') . '-' . (date('Y') + 1) }}</strong>
                            </div>
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Centre Code:</span>
                                <strong class="adm-val font-monospace">{{ $cnt?->centre_code ?? 'MAIN-101' }}</strong>
                            </div>
                            <div class="adm-meta-item">
                                <span class="adm-lbl">Enrolment Status:</span>
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-0.5 border border-success-subtle">
                                    <i class="fa-solid fa-circle-check me-1"></i>VERIFIED &amp; APPROVED
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Student Passport Photo -->
                    <div class="adm-photo-col">
                        <div class="adm-photo-frame">
                            @if(!empty($stu?->photo_url))
                                <img src="{{ $stu->photo_url }}" alt="{{ $stu->full_name }}" class="adm-student-photo" onerror="this.onerror=null;this.src='https://placehold.co/110x140?text=Passport+Photo';">
                            @elseif(!empty($stu?->photo_path) && file_exists(public_path('uploads/' . $stu->photo_path)))
                                <img src="{{ asset('uploads/' . $stu->photo_path) }}" alt="{{ $stu->full_name }}" class="adm-student-photo">
                            @else
                                <div class="adm-photo-placeholder">
                                    <i class="fa-solid fa-user-graduate"></i>
                                    <span>Affix Photo</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. SECTION 1: CANDIDATE PERSONAL PARTICULARS -->
                <div class="adm-section-box">
                    <div class="adm-section-title">
                        <i class="fa-solid fa-id-card me-1 text-primary"></i> 1. Candidate Personal Particulars
                    </div>
                    <table class="adm-data-table">
                        <tr>
                            <td class="adm-td-lbl">Full Name of Student</td>
                            <td class="adm-td-val fw-bold text-uppercase text-dark">{{ $stu?->full_name ?? 'CANDIDATE NAME' }}</td>
                            <td class="adm-td-lbl">Father's / Guardian's Name</td>
                            <td class="adm-td-val fw-bold text-uppercase">{{ $stu?->father_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Mother's Name</td>
                            <td class="adm-td-val text-uppercase">{{ $stu?->mother_name ?? 'N/A' }}</td>
                            <td class="adm-td-lbl">Date of Birth (D.O.B.)</td>
                            <td class="adm-td-val font-monospace">{{ $dobFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Gender</td>
                            <td class="adm-td-val text-capitalize">{{ $stu?->gender ?? 'Male' }}</td>
                            <td class="adm-td-lbl">Highest Qualification</td>
                            <td class="adm-td-val">{{ $stu?->highest_qualification ?? 'Secondary / Higher Secondary' }}</td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Mobile Number</td>
                            <td class="adm-td-val font-monospace">{{ $stu?->mobile ?? 'N/A' }}</td>
                            <td class="adm-td-lbl">Email Address</td>
                            <td class="adm-td-val font-monospace text-primary">{{ $stu?->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Complete Address</td>
                            <td colspan="3" class="adm-td-val">
                                {{ $stu?->address ?? 'N/A' }}, 
                                {{ $stu?->city ?? '' }}, 
                                {{ $stu?->district?->name ?? '' }}, 
                                {{ $stu?->state?->name ?? '' }} - 
                                <strong class="font-monospace">{{ $stu?->pincode ?? '' }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 5. SECTION 2: ACADEMIC & COURSE ENROLMENT DETAILS -->
                <div class="adm-section-box">
                    <div class="adm-section-title">
                        <i class="fa-solid fa-graduation-cap me-1 text-primary"></i> 2. Academic &amp; Course Enrolment Particulars
                    </div>
                    <table class="adm-data-table">
                        <tr>
                            <td class="adm-td-lbl">Enrolled Course Name</td>
                            <td colspan="3" class="adm-td-val fw-bold text-primary text-uppercase">
                                {{ $crs?->course_name ?? 'ADVANCE DIPLOMA IN COMPUTER APPLICATION' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Course Code</td>
                            <td class="adm-td-val font-monospace fw-bold">{{ $crs?->course_code ?? 'ADCA' }}</td>
                            <td class="adm-td-lbl">Prescribed Duration</td>
                            <td class="adm-td-val fw-bold">{{ $crs?->duration ?? '12 Months' }}</td>
                        </tr>
                        <tr>
                            <td class="adm-td-lbl">Allocated Batch / Schedule</td>
                            <td class="adm-td-val">
                                @if($btc)
                                    <strong>{{ $btc->batch_name }}</strong> ({{ $btc->start_time }} - {{ $btc->end_time }})
                                @else
                                    <span class="text-muted">General Morning / Evening Regular Session</span>
                                @endif
                            </td>
                            <td class="adm-td-lbl">Authorised Study Centre</td>
                            <td class="adm-td-val fw-bold text-danger text-uppercase">
                                {{ $franchiseName }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 6. SECTION 3: FEE SCHEDULE & FINANCIAL LEDGER -->
                <div class="adm-section-box">
                    <div class="adm-section-title">
                        <i class="fa-solid fa-receipt me-1 text-primary"></i> 3. Official Fee Schedule &amp; Financial Ledger
                    </div>
                    <table class="adm-fee-table">
                        <thead>
                            <tr>
                                <th>Course Fee</th>
                                <th>Registration Fee</th>
                                <th>Scholarship / Discount</th>
                                <th class="text-primary">Net Total Payable</th>
                                <th class="text-success">Paid Amount</th>
                                <th class="text-danger">Balance Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-monospace">₹{{ number_format($courseFee, 2) }}</td>
                                <td class="font-monospace">₹{{ number_format($regFee, 2) }}</td>
                                <td class="font-monospace text-muted">₹{{ number_format($discount, 2) }}</td>
                                <td class="font-monospace fw-bold text-primary">₹{{ number_format($netFee, 2) }}</td>
                                <td class="font-monospace fw-bold text-success">₹{{ number_format($totalPaid, 2) }}</td>
                                <td class="font-monospace fw-bold text-danger">₹{{ number_format($balanceDue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 7. SECTION 4: STUDENT UNDERTAKING & DECLARATION -->
                <div class="adm-declaration-box">
                    <div class="adm-dec-heading">Applicant Undertaking &amp; Declaration:</div>
                    <p class="adm-dec-text">
                        I hereby declare that the particulars furnished above are true and complete to the best of my knowledge and belief. I agree to abide by the rules, regulations, attendance norms, and code of conduct of <strong>{{ $franchiseName }}</strong> and central directives authorized by <strong>TECH ERA COMPUTER</strong>. I understand that any false information may lead to the cancellation of my admission.
                    </p>
                </div>

                <!-- 8. SECURITY, ACCREDITATIONS & SIGNATURES FOOTER -->
                <div class="adm-footer-section">

                    <div class="adm-sign-grid">
                        
                        <!-- Left: Real Dynamic Vector QR Code -->
                        <div class="adm-qr-cell">
                            <div class="adm-qr-box">
                                {!! $qrSvg !!}
                            </div>
                            <div class="adm-qr-caption">Scan to Verify</div>
                        </div>

                        <!-- Signatures: Student, Centre, and Head Office -->
                        <div class="adm-sig-item">
                            <div class="adm-sig-line"></div>
                            <div class="adm-sig-lbl">Candidate Signature</div>
                            <div class="adm-sig-sub">{{ $stu?->full_name ?? 'Student' }}</div>
                        </div>

                        <div class="adm-sig-item">
                            <div class="adm-seal-wrap">
                                <img src="{{ $masterSeal }}" alt="Tech Era Official Seal" class="adm-verified-seal-img">
                            </div>
                            <div class="adm-sig-line"></div>
                            <div class="adm-sig-lbl">Centre Director Seal &amp; Sign</div>
                            <div class="adm-sig-sub">{{ $franchiseName }}</div>
                        </div>

                        <div class="adm-sig-item">
                            <div class="adm-sig-box">
                                <img src="{{ $masterSignature }}" alt="Deepak Kumar Signature" class="adm-sig-img">
                            </div>
                            <div class="adm-sig-line"></div>
                            <div class="adm-sig-lbl">Head Office Authorisation</div>
                            <div class="adm-sig-sub">TECH ERA COMPUTER</div>
                        </div>

                    </div>

                    <!-- 5 Government / International Accreditation Badges -->
                    <div class="adm-accred-strip">
                        <div class="adm-accred-badge accred-mca" title="Ministry of Corporate Affairs">
                            <div class="accred-icon"><i class="fa-solid fa-landmark"></i></div>
                            <div class="accred-txt">
                                <span class="accred-org">MINISTRY OF</span>
                                <span class="accred-sub">CORPORATE AFFAIRS</span>
                                <span class="accred-gov">GOVERNMENT OF INDIA</span>
                            </div>
                        </div>

                        <div class="adm-accred-badge accred-iso" title="ISO 9001:2015 Certified">
                            <div class="iso-seal">
                                <span class="iso-top">CERTIFIED</span>
                                <span class="iso-mid">ISO</span>
                                <span class="iso-spec">9001:2015</span>
                                <span class="iso-bot">COMPANY</span>
                            </div>
                        </div>

                        <div class="adm-accred-badge accred-msme" title="MSME Govt of India">
                            <div class="msme-logo-wrap">
                                <i class="fa-solid fa-gears fs-5"></i>
                                <span class="msme-title">MSME</span>
                            </div>
                        </div>

                        <div class="adm-accred-badge accred-niti" title="NITI Aayog Registered">
                            <div class="niti-emblem"><i class="fa-solid fa-building-columns"></i></div>
                            <div class="niti-txt">NITI Aayog</div>
                        </div>

                        <div class="adm-accred-badge accred-startup" title="#startupindia">
                            <span class="startup-hash">#</span><span class="startup-bold">startup</span><span class="startup-flag">india</span>
                        </div>
                    </div>

                    <div class="adm-corporate-address">
                        Regional Office : {{ $regOffice }}
                    </div>
                    <div class="adm-contact-strip">
                        <span><i class="fa-solid fa-globe me-1"></i> {{ $websiteUrl }}</span>
                        <span><i class="fa-solid fa-envelope me-1"></i> {{ $officialEmail }}</span>
                        <span><i class="fa-solid fa-phone me-1"></i> {{ $helplinePhone }}</span>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* FONT IMPORTS */
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;0,800;0,900;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700;800&display=swap');

/* ADMISSION FORM A4 PORTRAIT SHEET (210mm x 297mm) */
.adm-outer-sheet {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto;
    padding: 5mm;
    background: #ffffff;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.16);
    box-sizing: border-box;
    position: relative;
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: #0f172a;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

.adm-gold-frame {
    width: 100%;
    min-height: 100%;
    border: 3.5px solid #a16207;
    padding: 2.5px;
    box-sizing: border-box;
    position: relative;
    background: #ffffff;
}

.adm-inner-frame {
    width: 100%;
    min-height: 100%;
    border: 1.5px solid #b45309;
    padding: 10px 16px 8px 16px;
    box-sizing: border-box;
    position: relative;
    background: radial-gradient(circle at center, #ffffff 65%, #fdfbf7 100%);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* CORNER GEOMETRIC ORNAMENTS */
.adm-corner {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 3px solid #78350f;
    z-index: 3;
    pointer-events: none;
}
.adm-corner::after {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    border: 2px solid #ca8a04;
}
.adm-corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
.adm-corner-tl::after { top: 2px; left: 2px; border-right: none; border-bottom: none; }
.adm-corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
.adm-corner-tr::after { top: 2px; right: 2px; border-left: none; border-bottom: none; }
.adm-corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
.adm-corner-bl::after { bottom: 2px; left: 2px; border-right: none; border-top: none; }
.adm-corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }
.adm-corner-br::after { bottom: 2px; right: 2px; border-left: none; border-top: none; }

/* WATERMARK BACKGROUND */
.adm-watermark-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    opacity: 0.04;
    pointer-events: none;
    z-index: 0;
}
.adm-watermark-img {
    width: 100%;
    height: auto;
    filter: grayscale(20%);
}

.adm-main-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* 1. HEADER */
.adm-header-grid {
    display: grid;
    grid-template-columns: 80px 1fr 10px;
    align-items: center;
    gap: 10px;
}
.adm-franchise-logo {
    max-height: 65px;
    max-width: 80px;
    object-fit: contain;
}
.adm-brand-box {
    text-align: center;
}
.adm-title-franchise {
    font-family: 'Cinzel', 'Playfair Display', serif;
    font-size: 1.4rem;
    font-weight: 900;
    color: #b91c1c;
    letter-spacing: 0.5px;
    margin: 0;
    text-transform: uppercase;
    line-height: 1.1;
}
.adm-tagline-script {
    font-family: 'Great Vibes', cursive;
    font-size: 1.25rem;
    color: #1d4ed8;
    line-height: 1;
}
.adm-legal-text {
    font-size: 0.53rem;
    color: #334155;
    line-height: 1.2;
    margin-top: 1px;
}
.adm-cin-line {
    font-size: 0.54rem;
    color: #0f172a;
    margin-top: 1px;
}
.adm-auth-badge {
    display: inline-flex;
    align-items: center;
    background: #0f172a;
    color: #f8fafc;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 0.58rem;
    letter-spacing: 1.1px;
    padding: 2px 12px;
    border-radius: 4px;
    margin-top: 2px;
    text-transform: uppercase;
}

/* 2. TITLE BANNER */
.adm-title-banner-wrap {
    text-align: center;
    margin: 4px 0 6px 0;
}
.adm-title-badge {
    display: inline-block;
    background: linear-gradient(135deg, #0b1f3a 0%, #173868 100%);
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.88rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    padding: 3px 24px;
    border-radius: 6px;
    border: 1.5px solid #eab308;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

/* 3. TOP META & PHOTO */
.adm-top-meta-grid {
    display: grid;
    grid-template-columns: 1fr 90px;
    gap: 12px;
    align-items: center;
    margin-bottom: 6px;
}
.adm-meta-pill-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 12px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.68rem;
}
.adm-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}
.adm-lbl {
    font-weight: 700;
    color: #475569;
}
.adm-val {
    color: #0f172a;
}
.adm-photo-col {
    text-align: right;
}
.adm-photo-frame {
    display: inline-block;
    width: 82px;
    height: 100px;
    border: 1.5px solid #334155;
    padding: 2px;
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}
.adm-student-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.adm-photo-placeholder {
    width: 100%;
    height: 100%;
    background: #e2e8f0;
    color: #475569;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}
.adm-photo-placeholder span {
    font-size: 0.52rem;
    font-weight: 700;
    margin-top: 2px;
}

/* 4 & 5. SECTIONS */
.adm-section-box {
    margin-bottom: 6px;
}
.adm-section-title {
    font-size: 0.72rem;
    font-weight: 800;
    color: #1e3a8a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1.5px solid #1e3a8a;
    padding-bottom: 2px;
    margin-bottom: 3px;
}
.adm-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.66rem;
    background: #ffffff;
}
.adm-data-table td {
    border: 1px solid #cbd5e1;
    padding: 3px 6px;
    vertical-align: middle;
}
.adm-td-lbl {
    width: 22%;
    background: #f1f5f9;
    font-weight: 700;
    color: #334155;
}
.adm-td-val {
    width: 28%;
    color: #0f172a;
}

/* 6. FEE TABLE */
.adm-fee-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.66rem;
    text-align: center;
    background: #ffffff;
}
.adm-fee-table th {
    background: #1e3a8a;
    color: #ffffff;
    border: 1px solid #1e3a8a;
    padding: 3.5px 6px;
    font-weight: 700;
}
.adm-fee-table td {
    border: 1px solid #cbd5e1;
    padding: 3.5px 6px;
}

/* 7. DECLARATION */
.adm-declaration-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #0284c7;
    border-radius: 4px;
    padding: 4px 8px;
    margin-top: 4px;
    margin-bottom: 4px;
}
.adm-dec-heading {
    font-size: 0.62rem;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    margin-bottom: 1px;
}
.adm-dec-text {
    font-size: 0.56rem;
    line-height: 1.25;
    color: #475569;
    margin: 0;
}

/* 8. FOOTER & SIGNATURES */
.adm-footer-section {
    margin-top: 4px;
}
.adm-sign-grid {
    display: grid;
    grid-template-columns: 80px 1fr 1fr 1fr;
    align-items: end;
    gap: 12px;
    text-align: center;
    margin-bottom: 4px;
}
.adm-qr-cell {
    text-align: center;
}
.adm-qr-box {
    display: inline-block;
    padding: 2.5px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    line-height: 0;
}
.adm-qr-box svg {
    width: 58px;
    height: 58px;
}
.adm-qr-caption {
    font-size: 0.52rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1px;
}

.adm-sig-item {
    text-align: center;
}
.adm-seal-wrap {
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.adm-verified-seal-img {
    max-height: 36px;
    max-width: 50px;
    object-fit: contain;
}
.adm-sig-box {
    height: 38px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.adm-sig-img {
    max-height: 34px;
    max-width: 100px;
    object-fit: contain;
}
.adm-sig-line {
    border-top: 1.5px solid #334155;
    width: 100%;
    margin-top: 2px;
    margin-bottom: 2px;
}
.adm-sig-lbl {
    font-size: 0.62rem;
    font-weight: 800;
    color: #0f172a;
}
.adm-sig-sub {
    font-size: 0.52rem;
    color: #64748b;
    font-weight: 600;
}

/* Accreditation Logos Strip */
.adm-accred-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: nowrap;
    margin-bottom: 2px;
}
.adm-accred-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.5rem;
    line-height: 1.1;
    color: #0f172a;
}
.adm-corporate-address {
    text-align: center;
    font-size: 0.52rem;
    color: #334155;
    margin-top: 1px;
}
.adm-contact-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.5rem;
    color: #475569;
    font-weight: 600;
}

/* PRINT MEDIA QUERIES */
@media print {
    @page {
        size: A4 portrait;
        margin: 0;
    }
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: none !important;
    }
    .adm-outer-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        width: 210mm !important;
        min-height: 297mm !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }
}
</style>
