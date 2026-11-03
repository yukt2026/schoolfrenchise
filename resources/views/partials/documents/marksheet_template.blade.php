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

    $issueDate = $ms?->issue_date 
        ? $ms->issue_date->format('d-m-Y') 
        : ($cert?->issue_date ? $cert->issue_date->format('d-m-Y') : date('d-m-Y'));

    // Unique Verification URL and Real Vector SVG QR Code
    $qrUrl = $ms ? $ms->verification_url : ($cert ? $cert->verification_url : route('verify.certificate'));
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

    // Subject Breakdown Collection
    $subjectsList = [];
    if ($res && $res->subjectResults && $res->subjectResults->isNotEmpty()) {
        foreach ($res->subjectResults as $idx => $sr) {
            $subjectsList[] = [
                'sl'       => $idx + 1,
                'name'     => $sr->subject->subject_name ?? ('Module ' . ($idx + 1)),
                'max'      => (int) ($sr->max_marks ?: 100),
                'obtained' => (float) $sr->total_obtained,
            ];
        }
    } elseif ($crs && $crs->subjects && $crs->subjects->isNotEmpty()) {
        foreach ($crs->subjects as $idx => $cs) {
            $subjectsList[] = [
                'sl'       => $idx + 1,
                'name'     => $cs->subject_name,
                'max'      => (int) ($cs->total_max ?: 100),
                'obtained' => (float) round((($cs->total_max ?: 100) * ($res->percentage ?? 75)) / 100),
            ];
        }
    } else {
        // Standard Course Modules Fallback for preview
        $defaultModules = [
            'Computer Fundamentals & Basic Hardware Concepts' => 50,
            'MS Office Tools Windows File & Folder Management' => 50,
            'Internet, Email & HTML-Based Web Designing' => 50,
            'Tally ERP with GST & Inventory Management' => 50,
            'Manual Accounting, Payroll & Basic Taxation' => 50,
            'Graphic- Photoshop, CorelDRAW & PageMaker' => 50,
            'Database Management MySQL, MS Access & PHP' => 50,
            'Software Installation, Antivirus & System Utilities' => 50,
            'Final Practical Exam, Viva & Internal Assessment' => 100,
        ];
        $i = 1;
        $pct = $res->percentage ?? 77.4;
        foreach ($defaultModules as $modName => $modMax) {
            $subjectsList[] = [
                'sl'       => $i++,
                'name'     => $modName,
                'max'      => $modMax,
                'obtained' => round(($modMax * $pct) / 100),
            ];
        }
    }

    $calcTotalMax = 0;
    $calcTotalObt = 0;
    foreach ($subjectsList as $s) {
        $calcTotalMax += $s['max'];
        $calcTotalObt += $s['obtained'];
    }

    $displayTotalMax = $res?->total_max_marks ?: $calcTotalMax;
    $displayTotalObt = $res?->total_marks_obtained ?: $calcTotalObt;
    $displayPct = $res?->percentage ?: ($displayTotalMax > 0 ? round(($displayTotalObt / $displayTotalMax) * 100, 1) : 0);
    $displayGrade = $res?->grade ?: 'A';
@endphp

<div class="ms-outer-sheet">
    <!-- MASTER ORNATE FRAME -->
    <div class="ms-gold-frame">
        <div class="ms-inner-frame">

            <!-- CORNER ORNAMENTS -->
            <div class="ms-corner ms-corner-tl"></div>
            <div class="ms-corner ms-corner-tr"></div>
            <div class="ms-corner ms-corner-bl"></div>
            <div class="ms-corner ms-corner-br"></div>

            <!-- WATERMARK EMBLEM -->
            <div class="ms-watermark-bg">
                <img src="{{ $masterSeal }}" alt="Seal Watermark" class="ms-watermark-img">
            </div>

            <div class="ms-main-content">

                <!-- 1. HEADER SECTION -->
                <div class="ms-header-grid">
                    <!-- Left: Franchise Logo -->
                    <div class="ms-logo-box">
                        <img src="{{ $franchiseLogo }}" 
                             alt="{{ $franchiseName }}" 
                             class="ms-franchise-logo" 
                             onerror="this.onerror=null;this.src='{{ asset('images/tech-era-logo.png') }}';">
                    </div>

                    <!-- Center: Franchise Name & Accreditations -->
                    <div class="ms-brand-box">
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                            <h1 class="ms-title-franchise">{{ $franchiseName }}</h1>
                            <span class="ms-tagline-script">Learn focus &amp; Grow</span>
                        </div>

                        <div class="ms-legal-text">
                            <div class="fw-bold">Run under: {{ $parentFoundation }}</div>
                            <div>Reg. by- Ministry of Corporate Affairs (Govt. of India) sub-section (2) of section 7 and sub-section (1) of section 8 of the Companies Act, 2013 (18 of 2013) and rule 18 of the Companies (Incorporation) Rules, 2014]</div>
                            <div class="ms-cin-line">
                                <strong>CIN-U85300BR2022NPL055886</strong> &nbsp;&bull;&nbsp; 
                                Regd. NITI Aayog Unique ID: <strong>BR/2022/0304927</strong> &nbsp;&bull;&nbsp; 
                                <span class="text-danger fw-bold">(AN ISO 9001:2015 Certified)</span>
                            </div>
                        </div>

                        <!-- Fixed Non-Editable Main Admin Authorization Banner -->
                        <div class="ms-auth-badge">
                            <i class="fa-solid fa-shield-halved me-1 text-warning"></i>
                            AUTHORIZED BY: TECH ERA COMPUTER
                        </div>
                    </div>

                    <!-- Right: Empty spacer for alignment balance -->
                    <div class="ms-header-spacer"></div>
                </div>

                <!-- 2. STATEMENT OF MARKS BANNER -->
                <div class="ms-title-banner-wrap">
                    <div class="ms-title-badge">
                        STATEMENT OF MARKS
                    </div>
                </div>

                <!-- 3. STUDENT METADATA & PHOTO GRID -->
                <div class="ms-particulars-grid">
                    <div class="ms-details-col">
                        <div class="ms-meta-row">
                            <span class="ms-lbl">MS. No.</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-danger">{{ $ms->marksheet_no ?? ($ms->serial_no ?? '25081345') }}</strong>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">Roll No.</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-primary">{{ $stu->roll_no ?? '0800002' }}</strong>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">Registration No.</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val">{{ $stu->registration_no ?? 'DCE/24/8430002' }}</strong>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">Name of Student</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-uppercase">{{ $stu->full_name ?? 'CANDIDATE NAME' }}</strong>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">Father's Name</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-uppercase">{{ $stu->father_name ?? 'GUARDIAN NAME' }}</strong>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">On successful completion of</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-primary text-uppercase">{{ $crs->course_name ?? 'ADVANCE DIPLOMA IN COMPUTER APPLICATION' }}</strong>
                        </div>
                        <div class="ms-meta-row d-flex justify-content-between flex-wrap">
                            <div>
                                <span class="ms-lbl">Course Duration</span>
                                <span class="ms-sep">:</span>
                                <strong class="ms-val">{{ $crs->duration ?? '12 Months' }}</strong>
                            </div>
                            <div class="pe-3">
                                <span class="ms-sub-lbl">Date of admission:</span>
                                <strong class="ms-val">{{ $admDate }}</strong>
                            </div>
                        </div>
                        <div class="ms-meta-row">
                            <span class="ms-lbl">Center</span>
                            <span class="ms-sep">:</span>
                            <strong class="ms-val text-danger text-uppercase">{{ $franchiseName }}, {{ $franchiseAddress }}</strong>
                        </div>
                    </div>

                    <!-- Student Passport Photo -->
                    <div class="ms-photo-col">
                        <div class="ms-photo-frame">
                            @if(!empty($stu?->photo_url))
                                <img src="{{ $stu->photo_url }}" alt="{{ $stu->full_name }}" class="ms-student-photo" onerror="this.onerror=null;this.src='https://placehold.co/110x140?text=Photo';">
                            @elseif(!empty($stu?->photo_path) && file_exists(public_path('uploads/' . $stu->photo_path)))
                                <img src="{{ asset('uploads/' . $stu->photo_path) }}" alt="{{ $stu->full_name }}" class="ms-student-photo">
                            @else
                                <div class="ms-photo-placeholder">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. SUBJECTS & MARKS EVALUATION TABLE -->
                <div class="ms-table-container">
                    <table class="ms-marks-table">
                        <thead>
                            <tr>
                                <th class="ms-th-sl">Sl. No.</th>
                                <th class="ms-th-sub">Subject / Modules</th>
                                <th class="ms-th-max">Max. Marks</th>
                                <th class="ms-th-obt">Obtain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectsList as $subj)
                                <tr>
                                    <td class="text-center font-monospace">{{ $subj['sl'] }}</td>
                                    <td class="text-start fw-semibold">{{ $subj['name'] }}</td>
                                    <td class="text-center font-monospace">{{ $subj['max'] }}</td>
                                    <td class="text-center font-monospace fw-bold">{{ $subj['obtained'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="ms-tr-total">
                                <td colspan="2" class="text-end fw-bold text-uppercase pe-3">Total Marks</td>
                                <td class="text-center font-monospace fw-bold">{{ $displayTotalMax }}</td>
                                <td class="text-center font-monospace fw-extrabold text-primary">{{ $displayTotalObt }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Percentage & Grade Banner Row -->
                    <div class="ms-grade-summary-box">
                        <div class="ms-summary-item border-end">
                            <span class="ms-summary-lbl">Percentage:</span>
                            <span class="ms-summary-val font-monospace">{{ number_format($displayPct, 2) }}%</span>
                        </div>
                        <div class="ms-summary-item">
                            <span class="ms-summary-lbl">Grade:</span>
                            <span class="ms-summary-val font-monospace text-primary">{{ $displayGrade }}</span>
                        </div>
                    </div>
                </div>

                <!-- 5. SECURITY, SIGNATURES & ACCREDITATION FOOTER -->
                <div class="ms-footer-section">

                    <div class="ms-bottom-action-grid">
                        
                        <!-- Left: Real Dynamic Vector QR Code -->
                        <div class="ms-qr-cell">
                            <div class="ms-qr-box">
                                {!! $qrSvg !!}
                            </div>
                            <div class="ms-qr-caption">Scan to Verify</div>
                            <div class="ms-issue-date-box">
                                Issue Date<br>
                                <strong>{{ $issueDate }}</strong>
                            </div>
                        </div>

                        <!-- Center: Official Verified By Stamp & Flourish -->
                        <div class="ms-verified-cell">
                            <div class="ms-seal-wrap">
                                <img src="{{ $masterSeal }}" alt="Tech Era Official Seal" class="ms-verified-seal-img">
                                <div class="ms-verified-handwrite">Verified</div>
                            </div>
                            <div class="ms-sign-label">Verified By :</div>
                        </div>

                        <!-- Right: Authorized Director Signature -->
                        <div class="ms-signature-cell">
                            <div class="ms-sig-box">
                                <img src="{{ $masterSignature }}" alt="Deepak Kumar Signature" class="ms-sig-img">
                            </div>
                            <div class="ms-sign-rule"></div>
                            <div class="ms-sign-label">Authorised Signature</div>
                            <div class="ms-sign-org">TECH ERA COMPUTER</div>
                        </div>

                    </div>

                    <!-- Assessment Grading Scale Banner -->
                    <div class="ms-grading-strip">
                        Assessment Grading-A-Excellent (75% Above), B-Good (75%&gt;50%) C-Satisfactory (50%&gt;30&), D%&gt;below)
                    </div>

                    <!-- 5 Government / International Accreditation Badges -->
                    <div class="ms-accred-strip">
                        <div class="ms-accred-badge accred-mca" title="Ministry of Corporate Affairs">
                            <div class="accred-icon"><i class="fa-solid fa-landmark"></i></div>
                            <div class="accred-txt">
                                <span class="accred-org">MINISTRY OF</span>
                                <span class="accred-sub">CORPORATE AFFAIRS</span>
                                <span class="accred-gov">GOVERNMENT OF INDIA</span>
                            </div>
                        </div>

                        <div class="ms-accred-badge accred-iso" title="ISO 9001:2015 Certified">
                            <div class="iso-seal">
                                <span class="iso-top">CERTIFIED</span>
                                <span class="iso-mid">ISO</span>
                                <span class="iso-spec">9001:2015</span>
                                <span class="iso-bot">COMPANY</span>
                            </div>
                        </div>

                        <div class="ms-accred-badge accred-msme" title="MSME Govt of India">
                            <div class="msme-logo-wrap">
                                <i class="fa-solid fa-gears fs-5"></i>
                                <span class="msme-title">MSME</span>
                            </div>
                        </div>

                        <div class="ms-accred-badge accred-niti" title="NITI Aayog Registered">
                            <div class="niti-emblem"><i class="fa-solid fa-building-columns"></i></div>
                            <div class="niti-txt">NITI Aayog</div>
                        </div>

                        <div class="ms-accred-badge accred-startup" title="#startupindia">
                            <span class="startup-hash">#</span><span class="startup-bold">startup</span><span class="startup-flag">india</span>
                        </div>
                    </div>

                    <div class="ms-corporate-address">
                        Regional Office : {{ $regOffice }}
                    </div>
                    <div class="ms-contact-strip">
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

/* MARKSHEET A4 PORTRAIT SHEET (210mm x 297mm) */
.ms-outer-sheet {
    width: 210mm;
    height: 297mm;
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

.ms-gold-frame {
    width: 100%;
    height: 100%;
    border: 3.5px solid #a16207;
    padding: 2.5px;
    box-sizing: border-box;
    position: relative;
    background: #ffffff;
}

.ms-inner-frame {
    width: 100%;
    height: 100%;
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
.ms-corner {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 3px solid #78350f;
    z-index: 3;
    pointer-events: none;
}
.ms-corner::after {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    border: 2px solid #ca8a04;
}
.ms-corner-tl { top: -2px; left: -2px; border-right: none; border-bottom: none; }
.ms-corner-tl::after { top: 2px; left: 2px; border-right: none; border-bottom: none; }
.ms-corner-tr { top: -2px; right: -2px; border-left: none; border-bottom: none; }
.ms-corner-tr::after { top: 2px; right: 2px; border-left: none; border-bottom: none; }
.ms-corner-bl { bottom: -2px; left: -2px; border-right: none; border-top: none; }
.ms-corner-bl::after { bottom: 2px; left: 2px; border-right: none; border-top: none; }
.ms-corner-br { bottom: -2px; right: -2px; border-left: none; border-top: none; }
.ms-corner-br::after { bottom: 2px; right: 2px; border-left: none; border-top: none; }

/* WATERMARK BACKGROUND */
.ms-watermark-bg {
    position: absolute;
    top: 54%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 280px;
    opacity: 0.04;
    pointer-events: none;
    z-index: 0;
}
.ms-watermark-img {
    width: 100%;
    height: auto;
    filter: grayscale(20%);
}

.ms-main-content {
    position: relative;
    z-index: 1;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* 1. HEADER */
.ms-header-grid {
    display: grid;
    grid-template-columns: 80px 1fr 10px;
    align-items: center;
    gap: 10px;
}
.ms-franchise-logo {
    max-height: 68px;
    max-width: 80px;
    object-fit: contain;
}
.ms-brand-box {
    text-align: center;
}
.ms-title-franchise {
    font-family: 'Cinzel', 'Playfair Display', serif;
    font-size: 1.45rem;
    font-weight: 900;
    color: #b91c1c;
    letter-spacing: 0.5px;
    margin: 0;
    text-transform: uppercase;
    line-height: 1.1;
}
.ms-tagline-script {
    font-family: 'Great Vibes', cursive;
    font-size: 1.25rem;
    color: #1d4ed8;
    line-height: 1;
}
.ms-legal-text {
    font-size: 0.54rem;
    color: #334155;
    line-height: 1.2;
    margin-top: 1px;
}
.ms-cin-line {
    font-size: 0.55rem;
    color: #0f172a;
    margin-top: 1px;
}
.ms-auth-badge {
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
.ms-title-banner-wrap {
    text-align: center;
    margin: 4px 0;
}
.ms-title-badge {
    display: inline-block;
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    letter-spacing: 2px;
    padding: 3px 30px;
    border-radius: 6px;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

/* 3. METADATA & PHOTO */
.ms-particulars-grid {
    display: grid;
    grid-template-columns: 1fr 90px;
    gap: 12px;
    align-items: start;
    margin-bottom: 4px;
}
.ms-meta-row {
    font-size: 0.68rem;
    line-height: 1.35;
    margin-bottom: 1.5px;
}
.ms-lbl {
    font-weight: 700;
    color: #0f172a;
    display: inline-block;
    width: 140px;
}
.ms-sub-lbl {
    font-weight: 700;
    color: #0f172a;
    margin-right: 4px;
}
.ms-sep {
    font-weight: 700;
    margin-right: 4px;
}
.ms-val {
    color: #0f172a;
}
.ms-photo-col {
    text-align: right;
}
.ms-photo-frame {
    display: inline-block;
    width: 82px;
    height: 102px;
    border: 1.5px solid #334155;
    padding: 2px;
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}
.ms-student-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.ms-photo-placeholder {
    width: 100%;
    height: 100%;
    background: #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

/* 4. MARKS TABLE */
.ms-table-container {
    margin: 3px 0;
}
.ms-marks-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.68rem;
    background: #ffffff;
}
.ms-marks-table th, .ms-marks-table td {
    border: 1px solid #1e3a8a;
    padding: 3.5px 6px;
}
.ms-marks-table thead {
    background: #1e3a8a;
    color: #ffffff;
}
.ms-th-sl { width: 50px; text-align: center; }
.ms-th-sub { text-align: left; }
.ms-th-max { width: 85px; text-align: center; }
.ms-th-obt { width: 85px; text-align: center; }
.ms-tr-total {
    background: #f1f5f9;
}

.ms-grade-summary-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1.5px solid #1e3a8a;
    border-top: none;
    background: #ffffff;
    text-align: center;
    padding: 4px 0;
}
.ms-summary-item {
    font-size: 0.8rem;
}
.ms-summary-lbl {
    font-weight: 700;
    color: #0f172a;
    margin-right: 4px;
}
.ms-summary-val {
    font-weight: 800;
}

/* 5. FOOTER & SECURITY */
.ms-footer-section {
    margin-top: 4px;
}
.ms-bottom-action-grid {
    display: grid;
    grid-template-columns: 90px 1fr 130px;
    align-items: end;
    gap: 8px;
    margin-bottom: 4px;
}
.ms-qr-cell {
    text-align: center;
}
.ms-qr-box {
    display: inline-block;
    padding: 2.5px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    line-height: 0;
}
.ms-qr-box svg {
    width: 64px;
    height: 64px;
}
.ms-qr-caption {
    font-size: 0.55rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1px;
}
.ms-issue-date-box {
    font-size: 0.58rem;
    color: #0f172a;
    line-height: 1.15;
    margin-top: 2px;
}

.ms-verified-cell {
    text-align: center;
}
.ms-seal-wrap {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 60px;
}
.ms-verified-seal-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.ms-verified-handwrite {
    position: absolute;
    top: 42%;
    left: 45%;
    transform: translate(-50%, -50%) rotate(-12deg);
    font-family: 'Great Vibes', cursive;
    color: #1e3a8a;
    font-size: 1.1rem;
    font-weight: 700;
    pointer-events: none;
}
.ms-sign-label {
    font-size: 0.65rem;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1px;
}
.ms-sign-org {
    font-size: 0.52rem;
    font-weight: 700;
    color: #64748b;
}

.ms-signature-cell {
    text-align: center;
}
.ms-sig-box {
    height: 38px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.ms-sig-img {
    max-height: 36px;
    max-width: 110px;
    object-fit: contain;
}
.ms-sign-rule {
    border-top: 1.5px solid #334155;
    width: 100%;
    margin-top: 2px;
    margin-bottom: 1px;
}

.ms-grading-strip {
    background: #b91c1c;
    color: #ffffff;
    font-size: 0.56rem;
    font-weight: 700;
    text-align: center;
    padding: 2.5px 6px;
    border-radius: 3px;
    letter-spacing: 0.2px;
    margin-bottom: 3px;
}

.ms-accred-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: nowrap;
    margin-bottom: 2px;
}
.ms-accred-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.5rem;
    line-height: 1.1;
    color: #0f172a;
}
.ms-corporate-address {
    text-align: center;
    font-size: 0.52rem;
    color: #334155;
    margin-top: 1px;
}
.ms-contact-strip {
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
    .ms-outer-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        width: 210mm !important;
        height: 297mm !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        page-break-before: avoid !important;
    }
}
</style>
