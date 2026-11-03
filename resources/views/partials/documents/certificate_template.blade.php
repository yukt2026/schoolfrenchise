@php
    $cert = $certificate ?? ($result->certificate ?? null);
    $res = $result ?? ($cert?->result ?? ($cert?->examResult ?? null));
    $ms = $marksheet ?? ($res?->marksheet ?? ($cert?->marksheet ?? null));
    $stu = $student ?? ($cert?->student ?? ($res?->student ?? null));
    $crs = $course ?? ($cert?->course ?? ($res?->course ?? ($stu?->course ?? null)));
    $cnt = $centre ?? ($cert?->centre ?? ($res?->centre ?? ($stu?->centre ?? null)));

    // Fallback student admission date
    $adm = $stu?->admissions?->first();
    $admDate = $adm?->admission_date 
        ? $adm->admission_date->format('d-m-Y') 
        : ($stu?->created_at ? $stu->created_at->format('d-m-Y') : date('d-m-Y'));

    $issueDate = $cert?->issue_date 
        ? $cert->issue_date->format('d-m-Y') 
        : ($ms?->issue_date ? $ms->issue_date->format('d-m-Y') : date('d-m-Y'));

    $dobFormatted = $stu?->dob ? $stu->dob->format('d-m-Y') : '18-10-2006';

    // Unique Verification URL and Real Vector SVG QR Code
    $qrUrl = $cert ? $cert->verification_url : ($ms ? $ms->verification_url : route('verify.certificate'));
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
@endphp

<div class="cert-outer-sheet">
    <!-- MASTER ORNATE FRAME -->
    <div class="cert-gold-frame">
        <div class="cert-inner-frame">
            
            <!-- CORNER ORNAMENTS -->
            <div class="cert-corner corner-tl"></div>
            <div class="cert-corner corner-tr"></div>
            <div class="cert-corner corner-bl"></div>
            <div class="cert-corner corner-br"></div>

            <!-- FAINT WATERMARK LOGO -->
            <div class="cert-watermark-bg">
                <img src="{{ $masterSeal }}" alt="Seal Watermark" class="cert-watermark-img">
            </div>

            <div class="cert-main-content">
                
                <!-- 1. HEADER SECTION -->
                <div class="cert-header-grid">
                    <!-- Left: Franchise Logo -->
                    <div class="cert-logo-box">
                        <img src="{{ $franchiseLogo }}" 
                             alt="{{ $franchiseName }}" 
                             class="cert-franchise-logo" 
                             onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                    </div>

                    <!-- Center: Franchise Name & Accreditations -->
                    <div class="cert-brand-box">
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                            <h1 class="cert-title-franchise">{{ $franchiseName }}</h1>
                            <span class="cert-tagline-script">Learn focus &amp; Grow</span>
                        </div>

                        <div class="cert-legal-text">
                            <div class="fw-bold">Run under: {{ $parentFoundation }}</div>
                            <div>Reg. by- Ministry of Corporate Affairs (Govt. of India) sub-section (2) of section 7 and sub-section (1) of section 8 of the Companies Act, 2013 (18 of 2013) and rule 18 of the Companies (Incorporation) Rules, 2014]</div>
                            <div class="cert-cin-line">
                                <strong>CIN-U85300BR2022NPL055886</strong> &nbsp;&bull;&nbsp; 
                                Regd. NITI Aayog Unique ID: <strong>BR/2022/0304927</strong> &nbsp;&bull;&nbsp; 
                                <span class="text-danger fw-bold">(AN ISO 9001:2015 Certified)</span>
                            </div>
                        </div>

                        <!-- Fixed Non-Editable Main Admin Authorization Banner -->
                        <div class="cert-auth-badge">
                            <i class="fa-solid fa-shield-halved me-1 text-warning"></i>
                            AUTHORIZED BY: TECH ERA COMPUTER
                        </div>
                    </div>

                    <!-- Right: Student Passport Photograph -->
                    <div class="cert-photo-box">
                        <div class="cert-student-photo-frame">
                            @if(!empty($stu?->photo_url))
                                <img src="{{ $stu->photo_url }}" alt="{{ $stu->full_name }}" class="cert-student-photo-img" onerror="this.onerror=null;this.src='https://placehold.co/110x140?text=Photo';">
                            @elseif(!empty($stu?->photo_path) && file_exists(public_path('uploads/' . $stu->photo_path)))
                                <img src="{{ asset('uploads/' . $stu->photo_path) }}" alt="{{ $stu->full_name }}" class="cert-student-photo-img">
                            @else
                                <div class="cert-photo-placeholder">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. SERIALS & TITLE ROW -->
                <div class="cert-meta-bar">
                    <div class="cert-serial-col">
                        <div class="cert-meta-item">
                            <span class="cert-meta-lbl">Certificate No.</span>
                            <span class="cert-meta-sep">:</span>
                            <strong class="cert-meta-val text-danger">{{ $cert->certificate_no ?? '2608248430002' }}</strong>
                        </div>
                        <div class="cert-meta-item">
                            <span class="cert-meta-lbl">Regd. No.</span>
                            <span class="cert-meta-sep">:</span>
                            <strong class="cert-meta-val">{{ $stu->registration_no ?? 'DCE/24/8430002' }}</strong>
                        </div>
                        <div class="cert-meta-item">
                            <span class="cert-meta-lbl">Sl. No.</span>
                            <span class="cert-meta-sep">:</span>
                            <strong class="cert-meta-val">{{ $cert->serial_no ?? '25081345' }}</strong>
                        </div>
                        <div class="cert-meta-item">
                            <span class="cert-meta-lbl">Roll No.</span>
                            <span class="cert-meta-sep">:</span>
                            <strong class="cert-meta-val">{{ $stu->roll_no ?? '0800002' }}</strong>
                        </div>
                    </div>

                    <!-- Center CERTIFICATE Ribbon Badge -->
                    <div class="cert-ribbon-col">
                        <div class="cert-badge-ribbon">
                            CERTIFICATE
                        </div>
                        <div class="visually-hidden">Certificate of Proficiency</div>
                    </div>

                    <!-- Spacer for symmetry with serial column -->
                    <div class="cert-serial-spacer"></div>
                </div>

                <!-- 3. MAIN STATEMENT OF HONOUR -->
                <div class="cert-body-statement">
                    <div class="cert-statement-row">
                        <span class="cert-statement-lead">We are much pleased to honour Mr./Ms./Mrs :</span>
                        <span class="cert-fill-student">{{ strtoupper($stu->full_name ?? 'CANDIDATE NAME') }}</span>
                    </div>

                    <div class="cert-statement-row">
                        <span class="cert-statement-lead">Son/Daughter/Wife Mr. :</span>
                        <span class="cert-fill-father">{{ strtoupper($stu->father_name ?? 'GUARDIAN NAME') }}</span>
                    </div>

                    <div class="cert-statement-row">
                        <span class="cert-statement-lead">Has successfully completed a course of :</span>
                        <span class="cert-fill-course">{{ strtoupper($crs->course_name ?? 'ADVANCE DIPLOMA IN COMPUTER APPLICATION') }}</span>
                    </div>

                    <div class="cert-grid-row">
                        <div class="cert-grid-item">
                            <span class="cert-grid-lbl">Course Duration :</span>
                            <strong class="cert-grid-val">{{ $crs->duration ?? '12 Months' }}</strong>
                        </div>
                        <div class="cert-grid-item text-center">
                            <span class="cert-grid-lbl">Grade Awarded :</span>
                            <strong class="cert-grid-val text-primary">{{ $res->grade ?? 'A' }}</strong>
                        </div>
                        <div class="cert-grid-item text-end">
                            <span class="cert-grid-lbl">D.O.B.:</span>
                            <strong class="cert-grid-val">{{ $dobFormatted }}</strong>
                        </div>
                    </div>

                    <!-- Dynamic Franchise Centre Location (Prominent Crimson/Red uppercase) -->
                    <div class="cert-center-row">
                        <span class="cert-center-lbl">Center :</span>
                        <span class="cert-center-val">{{ strtoupper($franchiseName) }}, {{ strtoupper($franchiseAddress) }}</span>
                    </div>

                    <div class="cert-dates-row">
                        <div>
                            <span class="cert-date-lbl">Date of Admission:</span>
                            <strong class="cert-date-val">{{ $admDate }}</strong>
                        </div>
                        <div>
                            <span class="cert-date-lbl">Date of Issue:</span>
                            <strong class="cert-date-val">{{ $issueDate }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 4. SECURITY, SIGNATURES & ACCREDITATION FOOTER -->
                <div class="cert-footer-section">
                    
                    <!-- Assessment Grading Scale Banner -->
                    <div class="cert-grading-strip">
                        Assessment Grading - A-Excellent (75% Above), B-Good (75%&gt;50%), C-Satisfactory (50%&gt;30%), D%&lt;below)
                    </div>

                    <div class="cert-bottom-action-grid">
                        
                        <!-- Left: Real Dynamic Vector QR Code -->
                        <div class="cert-qr-cell">
                            <div class="cert-qr-box">
                                {!! $qrSvg !!}
                            </div>
                            <div class="cert-qr-caption">Scan to Verify</div>
                        </div>

                        <!-- Middle-Left: Official Verified By Circular Seal -->
                        <div class="cert-verified-cell">
                            <div class="cert-seal-wrap">
                                <img src="{{ $masterSeal }}" alt="Tech Era Official Seal" class="cert-verified-seal-img">
                                <div class="cert-verified-handwrite">Verified</div>
                            </div>
                            <div class="cert-sign-label">Verified By :</div>
                        </div>

                        <!-- Center: 5 Government / International Accreditation Badges -->
                        <div class="cert-accreditation-cell">
                            <div class="cert-accred-strip">
                                <!-- MCA Govt of India -->
                                <div class="accred-badge accred-mca" title="Ministry of Corporate Affairs">
                                    <div class="accred-icon"><i class="fa-solid fa-landmark"></i></div>
                                    <div class="accred-txt">
                                        <span class="accred-org">MINISTRY OF</span>
                                        <span class="accred-sub">CORPORATE AFFAIRS</span>
                                        <span class="accred-gov">GOVERNMENT OF INDIA</span>
                                    </div>
                                </div>

                                <!-- ISO 9001:2015 -->
                                <div class="accred-badge accred-iso" title="ISO 9001:2015 Certified">
                                    <div class="iso-seal">
                                        <span class="iso-top">CERTIFIED</span>
                                        <span class="iso-mid">ISO</span>
                                        <span class="iso-spec">9001:2015</span>
                                        <span class="iso-bot">COMPANY</span>
                                    </div>
                                </div>

                                <!-- MSME -->
                                <div class="accred-badge accred-msme" title="MSME Govt of India">
                                    <div class="msme-logo-wrap">
                                        <i class="fa-solid fa-gears fs-5"></i>
                                        <span class="msme-title">MSME</span>
                                    </div>
                                </div>

                                <!-- NITI Aayog -->
                                <div class="accred-badge accred-niti" title="NITI Aayog Registered">
                                    <div class="niti-emblem"><i class="fa-solid fa-building-columns"></i></div>
                                    <div class="niti-txt">NITI Aayog</div>
                                </div>

                                <!-- #startupindia -->
                                <div class="accred-badge accred-startup" title="#startupindia">
                                    <span class="startup-hash">#</span><span class="startup-bold">startup</span><span class="startup-flag">india</span>
                                </div>
                            </div>

                            <div class="cert-corporate-address">
                                Regional Office : {{ $regOffice }}
                            </div>
                            <div class="cert-contact-strip">
                                <span><i class="fa-solid fa-globe me-1"></i> {{ $websiteUrl }}</span>
                                <span><i class="fa-solid fa-envelope me-1"></i> {{ $officialEmail }}</span>
                                <span><i class="fa-solid fa-phone me-1"></i> {{ $helplinePhone }}</span>
                            </div>
                        </div>

                        <!-- Right: Authorized Director Signature -->
                        <div class="cert-signature-cell">
                            <div class="cert-sig-box">
                                <img src="{{ $masterSignature }}" alt="Deepak Kumar Signature" class="cert-sig-img">
                            </div>
                            <div class="cert-sign-rule"></div>
                            <div class="cert-sign-label">Authorised Signature</div>
                            <div class="cert-sign-org">TECH ERA COMPUTER</div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* FONT IMPORTS */
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;0,800;0,900;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700;800&display=swap');

/* CERTIFICATE A4 LANDSCAPE FRAME (297mm x 210mm) */
.cert-outer-sheet {
    width: 297mm;
    height: 210mm;
    margin: 0 auto;
    padding: 6mm;
    background: #ffffff;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.16);
    box-sizing: border-box;
    position: relative;
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: #0f172a;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

.cert-gold-frame {
    width: 100%;
    height: 100%;
    border: 3.5px solid #a16207;
    padding: 2.5px;
    box-sizing: border-box;
    position: relative;
    background: #ffffff;
}

.cert-inner-frame {
    width: 100%;
    height: 100%;
    border: 1.5px solid #b45309;
    padding: 10px 18px 8px 18px;
    box-sizing: border-box;
    position: relative;
    background: radial-gradient(circle at center, #ffffff 60%, #fdfbf7 100%);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* CORNER GEOMETRIC ORNAMENTS */
.cert-corner {
    position: absolute;
    width: 26px;
    height: 26px;
    border: 3.5px solid #78350f;
    z-index: 3;
    pointer-events: none;
}
.cert-corner::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    border: 2px solid #ca8a04;
}
.corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
.corner-tl::after { top: 2px; left: 2px; border-right: none; border-bottom: none; }
.corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
.corner-tr::after { top: 2px; right: 2px; border-left: none; border-bottom: none; }
.corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
.corner-bl::after { bottom: 2px; left: 2px; border-right: none; border-top: none; }
.corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }
.corner-br::after { bottom: 2px; right: 2px; border-left: none; border-top: none; }

/* WATERMARK BACKGROUND */
.cert-watermark-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 320px;
    opacity: 0.045;
    pointer-events: none;
    z-index: 0;
}
.cert-watermark-img {
    width: 100%;
    height: auto;
    filter: grayscale(20%);
}

.cert-main-content {
    position: relative;
    z-index: 1;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* 1. HEADER */
.cert-header-grid {
    display: grid;
    grid-template-columns: 85px 1fr 100px;
    align-items: center;
    gap: 12px;
}

.cert-logo-box {
    text-align: left;
}
.cert-franchise-logo {
    max-height: 72px;
    max-width: 85px;
    object-fit: contain;
}

.cert-brand-box {
    text-align: center;
}
.cert-title-franchise {
    font-family: 'Cinzel', 'Playfair Display', serif;
    font-size: 1.55rem;
    font-weight: 900;
    color: #b91c1c;
    letter-spacing: 0.5px;
    margin: 0;
    text-transform: uppercase;
    line-height: 1.1;
}
.cert-tagline-script {
    font-family: 'Great Vibes', cursive;
    font-size: 1.35rem;
    color: #1d4ed8;
    line-height: 1;
}
.cert-legal-text {
    font-size: 0.58rem;
    color: #334155;
    line-height: 1.25;
    margin-top: 2px;
}
.cert-cin-line {
    font-size: 0.59rem;
    color: #0f172a;
    margin-top: 1px;
}

/* AUTH BADGE */
.cert-auth-badge {
    display: inline-flex;
    align-items: center;
    background: #0f172a;
    color: #f8fafc;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 0.62rem;
    letter-spacing: 1.2px;
    padding: 2px 14px;
    border-radius: 4px;
    margin-top: 2px;
    text-transform: uppercase;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}

/* STUDENT PHOTO FRAME */
.cert-photo-box {
    text-align: right;
}
.cert-student-photo-frame {
    display: inline-block;
    width: 88px;
    height: 110px;
    border: 2px solid #334155;
    padding: 2px;
    background: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.12);
}
.cert-student-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.cert-photo-placeholder {
    width: 100%;
    height: 100%;
    background: #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
}

/* 2. SERIALS & TITLE ROW */
.cert-meta-bar {
    display: grid;
    grid-template-columns: 220px 1fr 220px;
    align-items: center;
    margin-top: 4px;
    margin-bottom: 2px;
}
.cert-serial-col {
    font-size: 0.68rem;
    line-height: 1.35;
}
.cert-meta-item {
    display: flex;
    align-items: center;
}
.cert-meta-lbl {
    width: 92px;
    font-weight: 700;
    color: #0f172a;
}
.cert-meta-sep {
    width: 10px;
    font-weight: 700;
}
.cert-meta-val {
    font-family: 'Montserrat', monospace;
    font-weight: 700;
    color: #0f172a;
}
.cert-ribbon-col {
    text-align: center;
}
.cert-badge-ribbon {
    display: inline-block;
    background: linear-gradient(135deg, #0b1f3a 0%, #173868 100%);
    color: #ffffff;
    font-family: 'Cinzel', serif;
    font-style: italic;
    font-size: 1.25rem;
    font-weight: 900;
    letter-spacing: 4px;
    padding: 3px 36px;
    border-radius: 6px;
    border: 2px solid #eab308;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    text-transform: uppercase;
}
.cert-serial-spacer {
    /* empty spacer for balanced center alignment */
}

/* 3. MAIN STATEMENT */
.cert-body-statement {
    padding: 2px 4px;
    text-align: left;
}
.cert-statement-row {
    font-size: 0.82rem;
    margin-bottom: 3px;
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 6px;
}
.cert-statement-lead {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-weight: 700;
    color: #0f172a;
    font-size: 0.88rem;
}
.cert-fill-student, .cert-fill-father, .cert-fill-course {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 0.95rem;
    color: #0f172a;
    letter-spacing: 0.5px;
    border-bottom: 1px dotted #94a3b8;
    padding-bottom: 1px;
}
.cert-fill-student {
    color: #0f172a;
    font-size: 1.05rem;
}
.cert-fill-course {
    color: #0369a1;
}

.cert-grid-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    font-size: 0.78rem;
    margin-top: 4px;
    margin-bottom: 3px;
    padding: 3px 0;
}
.cert-grid-lbl {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-weight: 700;
    color: #0f172a;
    margin-right: 4px;
}
.cert-grid-val {
    font-weight: 800;
    color: #0f172a;
}

.cert-center-row {
    font-size: 0.8rem;
    margin: 3px 0;
}
.cert-center-lbl {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-weight: 800;
    color: #0f172a;
    margin-right: 4px;
}
.cert-center-val {
    color: #dc2626;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.cert-dates-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.76rem;
    margin-top: 3px;
    margin-bottom: 4px;
}
.cert-date-lbl {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-weight: 700;
    color: #0f172a;
    margin-right: 4px;
}
.cert-date-val {
    font-weight: 700;
    color: #0f172a;
}

/* 4. FOOTER & SECURITY */
.cert-grading-strip {
    background: #b91c1c;
    color: #ffffff;
    font-size: 0.62rem;
    font-weight: 700;
    text-align: center;
    padding: 2.5px 8px;
    border-radius: 3px;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
}

.cert-bottom-action-grid {
    display: grid;
    grid-template-columns: 88px 100px 1fr 130px;
    align-items: end;
    gap: 8px;
}

/* QR */
.cert-qr-cell {
    text-align: center;
}
.cert-qr-box {
    display: inline-block;
    padding: 3px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    line-height: 0;
}
.cert-qr-box svg {
    width: 68px;
    height: 68px;
}
.cert-qr-caption {
    font-size: 0.58rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 2px;
}

/* Verified Seal */
.cert-verified-cell {
    text-align: center;
}
.cert-seal-wrap {
    position: relative;
    display: inline-block;
    width: 64px;
    height: 64px;
}
.cert-verified-seal-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.cert-verified-handwrite {
    position: absolute;
    top: 42%;
    left: 45%;
    transform: translate(-50%, -50%) rotate(-12deg);
    font-family: 'Great Vibes', cursive;
    color: #1e3a8a;
    font-size: 1.15rem;
    font-weight: 700;
    pointer-events: none;
}
.cert-sign-label {
    font-size: 0.68rem;
    font-weight: 800;
    color: #0f172a;
    margin-top: 2px;
}
.cert-sign-org {
    font-size: 0.55rem;
    font-weight: 700;
    color: #64748b;
}

/* Accreditation Logos Strip */
.cert-accreditation-cell {
    text-align: center;
}
.cert-accred-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: nowrap;
    margin-bottom: 2px;
}

/* Individual Accreditation Badges */
.accred-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.52rem;
    line-height: 1.1;
    color: #0f172a;
}
.accred-mca {
    border: 1px solid #94a3b8;
    padding: 2px 4px;
    border-radius: 3px;
    background: #f8fafc;
}
.accred-mca .accred-icon {
    font-size: 0.85rem;
    color: #0369a1;
}
.accred-mca .accred-txt {
    text-align: left;
}
.accred-mca .accred-org { font-weight: 800; display: block; font-size: 0.52rem; }
.accred-mca .accred-sub { font-weight: 700; display: block; font-size: 0.48rem; color: #b91c1c; }
.accred-mca .accred-gov { font-size: 0.44rem; color: #475569; display: block; }

.iso-seal {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1.5px solid #0369a1;
    background: #f0f9ff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.38rem;
    line-height: 1;
    font-weight: 800;
    color: #0369a1;
}
.iso-seal .iso-mid { font-size: 0.6rem; color: #0284c7; }

.msme-logo-wrap {
    display: flex;
    align-items: center;
    gap: 2px;
    border: 1px solid #94a3b8;
    padding: 2px 5px;
    border-radius: 3px;
    background: #f8fafc;
    color: #4338ca;
    font-weight: 900;
    font-size: 0.65rem;
}

.accred-niti {
    display: flex;
    align-items: center;
    gap: 3px;
    border: 1px solid #94a3b8;
    padding: 2px 5px;
    border-radius: 3px;
    background: #f8fafc;
    font-weight: 800;
    color: #0f172a;
    font-size: 0.58rem;
}

.accred-startup {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 0.62rem;
    border: 1px solid #cbd5e1;
    padding: 2px 6px;
    border-radius: 3px;
    background: #ffffff;
}
.startup-hash { color: #f97316; font-weight: 900; }
.startup-bold { color: #0f172a; }
.startup-flag { color: #16a34a; }

.cert-corporate-address {
    font-size: 0.54rem;
    color: #334155;
    margin-top: 2px;
}
.cert-contact-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 0.52rem;
    color: #475569;
    font-weight: 600;
}

/* Authorized Director Signature */
.cert-signature-cell {
    text-align: center;
}
.cert-sig-box {
    height: 40px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.cert-sig-img {
    max-height: 38px;
    max-width: 115px;
    object-fit: contain;
}
.cert-sign-rule {
    border-top: 1.5px solid #334155;
    width: 100%;
    margin-top: 2px;
    margin-bottom: 2px;
}

/* PRINT MEDIA QUERIES */
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: none !important;
    }
    .cert-outer-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        width: 297mm !important;
        height: 210mm !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }
}
</style>
