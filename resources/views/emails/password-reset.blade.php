<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #0b1329 0%, #1e293b 100%);
            color: #ffffff;
            padding: 30px 25px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .email-header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .email-body {
            padding: 35px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
        }
        .message-text {
            font-size: 15px;
            color: #475569;
            margin-bottom: 25px;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-reset {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            padding: 14px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .expiry-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            color: #92400e;
            margin-bottom: 25px;
        }
        .fallback-link {
            font-size: 12px;
            color: #64748b;
            word-break: break-all;
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }
        .email-footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h1>
            <p>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">Hello {{ $user->name }},</div>

            <div class="message-text">
                We received a request to reset your password for your account on the <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> portal.
            </div>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">Reset Your Password</a>
            </div>

            <div class="expiry-note">
                <strong>Important:</strong> This password reset link is valid for <strong>60 minutes</strong>. If you did not request a password reset, please ignore this email or contact support. Your password will not change until you access the link above and create a new one.
            </div>

            <div class="fallback-link">
                <strong>Button not working?</strong> Copy and paste this URL into your browser:<br>
                <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong></p>
            <p>{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</p>
            <p>Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }} | Email: {{ $instituteSettings['contact_email'] ?? 'techera.computer@gmail.com' }}</p>
            <p style="margin-top: 10px; color: #94a3b8; font-size: 11px;">This is an automated system notification. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
