<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $paymentReceipt->receipt_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .receipt-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #1e1b4b;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .receipt-header h3 {
            color: #1e1b4b;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .receipt-header small {
            color: #475569;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .receipt-title-badge {
            background: #1e1b4b;
            color: #ffffff;
            font-weight: bold;
            font-size: 0.9rem;
            padding: 4px 16px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 8px;
        }

        .receipt-table td, .receipt-table th {
            padding: 6px 10px;
            font-size: 0.85rem;
            border: 1px solid #cbd5e1;
        }

        .receipt-table th {
            background-color: #f8fafc;
            width: 25%;
            color: #334155;
        }

        .amount-highlight {
            background: #ecfdf5;
            border: 2px dashed #10b981;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin: 15px 0;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none;
                border: 2px solid #000000;
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-2 shadow-sm">
            <i class="fa-solid fa-print me-1"></i>Print Receipt
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3 rounded-2 ms-2">
            <i class="fa-solid fa-xmark me-1"></i>Close Window
        </button>
    </div>

    <div class="receipt-card">
        <!-- Header -->
        <div class="receipt-header">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 48px; width: auto; object-fit: contain;">
                <h3 class="mb-0">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h3>
            </div>
            <small>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }} &bull; {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</small>
            <div><span class="receipt-title-badge">OFFICIAL FEE PAYMENT RECEIPT</span></div>
        </div>

        <!-- Receipt Meta Grid -->
        <div class="row align-items-center mb-3">
            <div class="col-6">
                <div class="small text-muted">Receipt Number:</div>
                <div class="fw-bold font-monospace text-primary fs-5">{{ $paymentReceipt->receipt_no }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="small text-muted">Receipt Date:</div>
                <div class="fw-bold">{{ $paymentReceipt->receipt_date ? $paymentReceipt->receipt_date->format('d F, Y') : now()->format('d F, Y') }}</div>
            </div>
        </div>

        <!-- Student & Centre Details -->
        <table class="table receipt-table mb-3">
            <tr>
                <th>Candidate Full Name:</th>
                <td class="fw-bold">{{ $paymentReceipt->student?->full_name }}</td>
                <th>Roll Number:</th>
                <td class="fw-bold font-monospace text-primary">{{ $paymentReceipt->student?->roll_no }}</td>
            </tr>
            <tr>
                <th>Father's Name:</th>
                <td>{{ $paymentReceipt->student?->father_name }}</td>
                <th>Registration No:</th>
                <td class="font-monospace">{{ $paymentReceipt->student?->registration_no }}</td>
            </tr>
            <tr>
                <th>Enrolled Course:</th>
                <td colspan="3" class="fw-bold">
                    {{ $paymentReceipt->admission?->course?->course_name }} ({{ $paymentReceipt->admission?->course?->course_code }})
                </td>
            </tr>
            <tr>
                <th>Issuing Study Centre:</th>
                <td colspan="3">
                    <strong>{{ $paymentReceipt->centre?->centre_name }}</strong> (CODE: {{ $paymentReceipt->centre?->centre_code }})
                    &bull; {{ $paymentReceipt->centre?->city }}
                </td>
            </tr>
            <tr>
                <th>Payment Mode:</th>
                <td class="fw-bold text-uppercase">{{ $paymentReceipt->payment_mode }}</td>
                <th>Transaction Ref:</th>
                <td class="font-monospace">{{ $paymentReceipt->payment?->transaction_ref ?? 'Counter Cash' }}</td>
            </tr>
            @if($paymentReceipt->notes)
            <tr>
                <th>Notes / Remarks:</th>
                <td colspan="3" class="text-muted">{{ $paymentReceipt->notes }}</td>
            </tr>
            @endif
        </table>

        <!-- Amount Box -->
        <div class="amount-highlight">
            <div class="text-muted small text-uppercase fw-semibold">Amount Received</div>
            <div class="fs-2 fw-bold text-success my-1">₹{{ number_format($paymentReceipt->amount, 2) }}</div>
            <div class="small text-muted fst-italic">Computerized verified receipt. Payment recorded in central database ledger.</div>
        </div>

        <!-- Admission Balance Summary -->
        @if($paymentReceipt->admission)
        @php
            $netFee = (float) $paymentReceipt->admission->net_fee;
            $totalPaid = (float) $paymentReceipt->admission->total_paid;
            $due = max(0, $netFee - $totalPaid);
        @endphp
        <table class="table table-sm receipt-table mb-4">
            <tr class="text-center">
                <th>Total Course Net Fee</th>
                <th>Total Paid to Date</th>
                <th>Remaining Outstanding Due</th>
            </tr>
            <tr class="text-center fw-bold">
                <td>₹{{ number_format($netFee, 2) }}</td>
                <td class="text-success">₹{{ number_format($totalPaid, 2) }}</td>
                <td class="{{ $due > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($due, 2) }}</td>
            </tr>
        </table>
        @endif

        <!-- Signatures & Verification -->
        <div class="row pt-4 text-center">
            <div class="col-6 text-start">
                <div class="small text-muted font-monospace" style="font-size: 0.68rem; word-break: break-all;">
                    QR Hash: {{ $paymentReceipt->qr_token }}
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="border-top border-dark d-inline-block pt-1" style="width: 180px;">
                    <div class="small fw-bold">Authorized Signatory & Seal</div>
                    <div class="small text-muted">{{ $paymentReceipt->centre?->centre_name }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
