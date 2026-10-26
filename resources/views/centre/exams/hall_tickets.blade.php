<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards - {{ $exam->exam_title }} (Centre: {{ $centre->centre_code }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .hall-ticket {
            max-width: 800px;
            margin: 0 auto 30px auto;
            background: #ffffff;
            border: 2px solid #1e1b4b;
            padding: 25px;
            page-break-after: always;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .ticket-header {
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .ticket-header h4 {
            color: #1e1b4b;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .ticket-header small {
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .photo-box {
            width: 100px;
            height: 120px;
            border: 1px solid #64748b;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .details-table td, .details-table th {
            padding: 5px 8px;
            font-size: 0.85rem;
        }

        .details-table th {
            width: 25%;
            color: #475569;
        }

        .subjects-table td, .subjects-table th {
            padding: 6px 8px;
            font-size: 0.82rem;
            border: 1px solid #cbd5e1;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .hall-ticket {
                box-shadow: none;
                border: 2px solid #000000;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-2 shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print All Hall Tickets
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 rounded-2 ms-2">
            <i class="fa-solid fa-xmark me-1"></i>Close
        </button>
    </div>

    @forelse($students as $student)
    <div class="hall-ticket">
        <div class="ticket-header">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 38px; width: auto; object-fit: contain;">
                <h4 class="mb-0">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h4>
            </div>
            <small>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }} &bull; ADMIT CARD / HALL TICKET</small>
            <div class="fw-bold mt-1 text-primary">{{ $exam->exam_title }} ({{ $exam->session_name }})</div>
        </div>

        <div class="row align-items-center mb-3">
            <div class="col-9">
                <table class="table table-borderless table-sm details-table mb-0">
                    <tr>
                        <th>Candidate Name:</th>
                        <td class="fw-bold fs-6">{{ $student->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Father's Name:</th>
                        <td class="fw-semibold">{{ $student->father_name }}</td>
                    </tr>
                    <tr>
                        <th>Roll Number:</th>
                        <td class="fw-bold font-monospace text-primary fs-6">{{ $student->roll_no }}</td>
                    </tr>
                    <tr>
                        <th>Registration No:</th>
                        <td class="font-monospace">{{ $student->registration_no }}</td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td class="fw-bold">{{ $exam->course?->course_name }} ({{ $exam->course?->course_code }})</td>
                    </tr>
                    <tr>
                        <th>Exam Date:</th>
                        <td class="fw-bold">{{ $exam->exam_date ? $exam->exam_date->format('d F, Y') : 'Announced' }}</td>
                    </tr>
                    <tr>
                        <th>Exam Centre:</th>
                        <td>{{ $centre->centre_name }} (CODE: {{ $centre->centre_code }})</td>
                    </tr>
                </table>
            </div>
            <div class="col-3 d-flex justify-content-end">
                <div class="photo-box text-center">
                    @if($student->photo_path)
                    <img src="{{ $student->photo_url ?? asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" onerror="this.onerror=null;this.src='https://placehold.co/100x120?text=Photo';">
                    @else
                    <span class="small text-muted">Photo</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subjects Schedule -->
        <h6 class="fw-bold text-dark small mb-2 text-uppercase">Subjects for Examination:</h6>
        <table class="table subjects-table mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">Code</th>
                    <th>Subject Title</th>
                    <th class="text-center" style="width: 100px;">Theory Max</th>
                    <th class="text-center" style="width: 100px;">Practical Max</th>
                    <th class="text-center" style="width: 100px;">Viva Max</th>
                    <th class="text-center" style="width: 100px;">Total Max</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exam->course?->subjects ?? [] as $sub)
                <tr>
                    <td class="font-monospace text-muted">{{ $sub->subject_code }}</td>
                    <td class="fw-semibold">{{ $sub->subject_name }}</td>
                    <td class="text-center">{{ $sub->theory_max }}</td>
                    <td class="text-center">{{ $sub->practical_max }}</td>
                    <td class="text-center">{{ $sub->viva_max }}</td>
                    <td class="text-center fw-bold">{{ $sub->total_max }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-2 text-muted">Core Examination Modules</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="row pt-4 text-center">
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Candidate's Signature</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Centre Superintendent</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark mx-auto" style="width: 80%;"></div>
                <div class="small fw-bold mt-1">Controller of Examinations</div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <h5>No eligible candidates found enrolled for this examination in your study centre.</h5>
    </div>
    @endforelse
</body>
</html>
