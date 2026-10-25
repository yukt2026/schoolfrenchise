<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre Affiliation Certificate - {{ $centre->centre_name }} ({{ $centre->centre_code }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Georgia', 'Times New Roman', serif;
            padding: 30px;
        }

        .certificate-wrapper {
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            border: 10px double #b45309;
            padding: 40px 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            background-image: radial-gradient(circle at center, #ffffff 60%, #fffbeb 100%);
        }

        .cert-header {
            text-align: center;
            border-bottom: 2px solid #b45309;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .cert-header h1 {
            color: #1e1b4b;
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            margin: 0;
            text-transform: uppercase;
        }

        .cert-header p {
            color: #475569;
            font-size: 0.95rem;
            margin: 4px 0 0 0;
            font-style: italic;
        }

        .cert-title {
            text-align: center;
            margin: 25px 0 20px 0;
        }

        .cert-title span {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: #fbbf24;
            font-size: 1.25rem;
            font-weight: bold;
            padding: 8px 30px;
            border-radius: 50px;
            border: 2px solid #b45309;
            letter-spacing: 1px;
            display: inline-block;
        }

        .cert-body {
            text-align: center;
            line-height: 1.9;
            font-size: 1.1rem;
            color: #1e293b;
            margin: 20px 0 30px 0;
        }

        .centre-title-highlight {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1e1b4b;
            display: block;
            margin: 10px 0;
            text-transform: uppercase;
            text-decoration: underline;
            text-decoration-color: #f59e0b;
        }

        .meta-strip {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 20px;
            display: flex;
            justify-content: space-around;
            font-size: 0.95rem;
            font-weight: bold;
            color: #78350f;
            margin-bottom: 35px;
        }

        .seal-box {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px double #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #b45309;
            font-weight: bold;
            font-size: 0.75rem;
            text-transform: uppercase;
            text-align: center;
            background: #fffbeb;
        }

        .signature-line {
            border-top: 1px solid #334155;
            width: 70%;
            margin: 0 auto 5px auto;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .certificate-wrapper {
                box-shadow: none;
                max-width: 100%;
                border: 8px double #000000;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-2 shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print Certificate
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 rounded-2 ms-2">
            <i class="fa-solid fa-xmark me-1"></i>Close Window
        </button>
    </div>

    <div class="certificate-wrapper">
        <!-- Certificate Header -->
        <div class="cert-header">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 60px; width: auto; object-fit: contain;">
                <div>
                    <h1>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h1>
                    <p>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
                </div>
            </div>
            <div class="small fw-bold text-uppercase mt-1" style="color: #92400e; letter-spacing: 0.5px;">
                {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} &bull; Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
            </div>
        </div>

        <!-- Title Banner -->
        <div class="cert-title">
            <span>CERTIFICATE OF AUTHORIZED STUDY CENTRE</span>
        </div>

        <!-- Body Statement -->
        <div class="cert-body">
            This is to certify that Central Directorate of Examinations & Academic Accreditation has approved the affiliation of:
            <span class="centre-title-highlight">{{ $centre->centre_name }}</span>
            Under the Directorship of <strong>{{ $centre->director_name }}</strong>, located at <strong>{{ $centre->address }}, City: {{ $centre->city }}, District: {{ $centre->district?->name }}, {{ $centre->state?->name }} &ndash; {{ $centre->pincode }}</strong>, as an Authorized Study Centre (ASC) to conduct approved Computer Software, Hardware, Accounting & Vocational Diploma courses.
        </div>

        <!-- Meta Details Box -->
        <div class="meta-strip">
            <div>CENTRE CODE: <span class="font-monospace text-dark">{{ $centre->centre_code }}</span></div>
            <div>AFFILIATION DATE: <span class="text-dark">{{ $centre->affiliation_date ? $centre->affiliation_date->format('d M, Y') : now()->format('d M, Y') }}</span></div>
            <div>VALID UPTO: <span class="text-dark">{{ $centre->valid_upto ? $centre->valid_upto->format('d M, Y') : 'Life / Annual Review' }}</span></div>
            <div>STATUS: <span class="text-success text-uppercase">AUTHORIZED ACTIVE</span></div>
        </div>

        <!-- Signatures & Official Embossed Seal -->
        <div class="row align-items-center text-center pt-3">
            <div class="col-4">
                <div class="signature-line"></div>
                <div class="fw-bold small text-dark">Director of Affiliations</div>
                <div class="small text-muted" style="font-size: 0.75rem;">Central Accreditation Cell</div>
            </div>
            <div class="col-4">
                <div class="seal-box">
                    OFFICIAL<br>COUNCIL<br>SEAL
                </div>
            </div>
            <div class="col-4">
                <div class="signature-line"></div>
                <div class="fw-bold small text-dark">Chairman & Managing Director</div>
                <div class="small text-muted" style="font-size: 0.75rem;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
