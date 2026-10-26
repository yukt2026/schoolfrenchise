<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card - {{ $student->full_name }} ({{ $student->roll_no }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .id-card-wrapper {
            width: 340px;
            height: 520px;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #cbd5e1;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .id-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: #ffffff;
            padding: 16px 12px 12px 12px;
            text-align: center;
            border-bottom: 3px solid #f59e0b;
        }

        .id-header h5 {
            font-size: 0.95rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .id-header small {
            font-size: 0.65rem;
            color: #fbbf24;
            text-transform: uppercase;
            font-weight: 600;
        }

        .id-photo-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #312e81;
            overflow: hidden;
            margin: -20px auto 10px auto;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
            z-index: 2;
        }

        .id-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-body {
            padding: 0 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .student-name {
            font-size: 1.05rem;
            font-weight: 800;
            text-align: center;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .roll-badge {
            background: #e0e7ff;
            color: #3730a3;
            font-family: monospace;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 3px 10px;
            border-radius: 50px;
            margin: 0 auto 12px auto;
            display: table;
        }

        .id-field {
            display: flex;
            justify-content: space-between;
            font-size: 0.76rem;
            padding: 3px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .id-field .label {
            color: #64748b;
            font-weight: 600;
        }

        .id-field .val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }

        .id-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .centre-watermark {
            font-size: 0.65rem;
            color: #64748b;
            line-height: 1.2;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .line {
            border-top: 1px solid #334155;
            width: 70px;
            margin: 15px auto 2px auto;
        }

        .signature-box small {
            font-size: 0.6rem;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .id-card-wrapper {
                box-shadow: none;
                border: 1px solid #000000;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="no-print mb-4 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-2 shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print ID Card
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 rounded-2">
            <i class="fa-solid fa-xmark me-1"></i>Close Window
        </button>
    </div>

    <div class="id-card-wrapper">
        <!-- Header -->
        <div class="id-header" style="background: linear-gradient(135deg, #0a192f 0%, #0284c7 100%);">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 30px; width: auto; object-fit: contain;">
                <h5 class="mb-0 text-white">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h5>
            </div>
            <small style="color: #fbbf24;">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</small>
        </div>

        <!-- Student Photo -->
        <div class="id-photo-container">
            @if($student->photo_path)
            <img src="{{ $student->photo_url ?? asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" onerror="this.onerror=null;this.src='https://placehold.co/100x120?text=Photo';">
            @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-primary fs-3">
                <i class="fa-solid fa-user"></i>
            </div>
            @endif
        </div>

        <!-- Body Details -->
        <div class="id-body">
            <div class="student-name">{{ $student->full_name }}</div>
            <div class="roll-badge">ROLL: {{ $student->roll_no }}</div>

            <div class="id-field">
                <span class="label">Course:</span>
                <span class="val text-truncate" style="max-width: 170px;">{{ $admission?->course?->course_name ?? 'N/A' }}</span>
            </div>
            <div class="id-field">
                <span class="label">Course Code:</span>
                <span class="val font-monospace">{{ $admission?->course?->course_code ?? 'N/A' }}</span>
            </div>
            <div class="id-field">
                <span class="label">Registration No:</span>
                <span class="val font-monospace">{{ $student->registration_no }}</span>
            </div>
            <div class="id-field">
                <span class="label">Father's Name:</span>
                <span class="val">{{ $student->father_name }}</span>
            </div>
            <div class="id-field">
                <span class="label">Contact Mobile:</span>
                <span class="val">{{ $student->mobile }}</span>
            </div>
            <div class="id-field">
                <span class="label">Date of Birth:</span>
                <span class="val">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : 'N/A' }}</span>
            </div>
            <div class="id-field">
                <span class="label">Study Centre:</span>
                <span class="val text-truncate" style="max-width: 170px;">{{ $centre->centre_name }}</span>
            </div>
            <div class="id-field border-0">
                <span class="label">Centre Code:</span>
                <span class="val font-monospace">{{ $centre->centre_code }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="id-footer">
            <div class="centre-watermark">
                <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong><br>
                {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <small>Auth Signatory</small>
            </div>
        </div>
    </div>
</body>
</html>
