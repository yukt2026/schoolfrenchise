<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Certificate - {{ $certificate->certificate_no }} - {{ $certificate->student->full_name ?? 'Candidate' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #0f172a;
            margin: 0;
            padding: 20px 0;
        }

        .no-print-bar {
            max-width: 297mm;
            margin: 0 auto 16px auto;
            padding: 10px 18px;
            background: #1e293b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Franchise Action Control Bar -->
@php
    $studentName = $certificate->student->full_name ?? 'Student';
    $rollNo = $certificate->student->roll_no ?? 'N/A';
    $certNo = $certificate->certificate_no;
    $courseName = $certificate->course->course_name ?? 'Course';
    $issueDate = $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d M, Y') : date('d M, Y');
    $instituteName = $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER';
    $verifyUrl = $certificate->verification_url ?? route('verify.cert.direct', ['cert_no' => $certificate->certificate_no]);
    $studentMobile = preg_replace('/[^0-9]/', '', $certificate->student->mobile ?? '');
    if (strlen($studentMobile) === 10) { $studentMobile = '91' . $studentMobile; }

    $waCertMsg = "🎓 *Official Course Completion Certificate Issued!*\n\n"
        . "🏛️ *Institution:* {$instituteName}\n"
        . "👤 *Student Name:* {$studentName}\n"
        . "🆔 *Roll No:* {$rollNo}\n"
        . "🏷️ *Certificate No:* {$certNo}\n"
        . "📚 *Course:* {$courseName}\n"
        . "📅 *Issue Date:* {$issueDate}\n\n"
        . "✅ *Verify Certificate Online (Live QR Link):*\n{$verifyUrl}\n\n"
        . "Congratulations on successfully completing your course! 🎉";
@endphp
<div class="no-print-bar">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('centre.downloads.student-certificates') }}" class="btn btn-outline-light btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
        <span class="badge bg-danger px-3 py-2 font-monospace fs-6">CERT: {{ $certificate->certificate_no }}</span>
        <span class="text-white small fw-bold">{{ $centre->centre_name ?? 'Franchise Centre' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ $certificate->verification_url }}" target="_blank" class="btn btn-outline-info btn-sm fw-bold">
            <i class="fa-solid fa-qrcode me-1"></i> Verify QR Link
        </a>
        <a href="https://api.whatsapp.com/send?{{ !empty($studentMobile) ? 'phone=' . $studentMobile . '&' : '' }}text={{ urlencode($waCertMsg) }}" target="_blank" class="btn btn-success px-3 fw-bold shadow-sm" style="background-color: #16a34a; border-color: #16a34a;" title="Send Certificate to Student via WhatsApp">
            <i class="fa-brands fa-whatsapp me-1 fs-5 align-middle"></i> Share on WhatsApp
        </a>
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #2563eb; border-color: #2563eb;">
            <i class="fa-solid fa-print me-1"></i> Print / Download PDF
        </button>
        <button type="button" onclick="closeWindow('{{ route('centre.downloads.student-certificates') }}')" class="btn btn-outline-light px-3 btn-sm">
            <i class="fa-solid fa-xmark me-1"></i> Close Window
        </button>
    </div>
</div>

<!-- EMBEDDED CERTIFICATE TEMPLATE -->
@include('partials.documents.certificate_template', [
    'certificate' => $certificate,
    'centre'      => $centre,
    'student'     => $certificate->student,
    'course'      => $certificate->course,
    'result'      => $certificate->examResult ?? $certificate->result,
])

<script>
function closeWindow(fallbackUrl) {
    try {
        window.close();
    } catch (e) {}

    try {
        window.open('', '_self', '');
        window.close();
    } catch (e) {}

    setTimeout(function() {
        if (!window.closed) {
            if (window.history.length > 1) {
                window.history.back();
            } else if (fallbackUrl) {
                window.location.href = fallbackUrl;
            }
        }
    }, 150);
}
</script>
</body>
</html>
