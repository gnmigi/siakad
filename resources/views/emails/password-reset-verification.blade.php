<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .verification-code {
            background: #f8f9fa;
            border: 2px dashed #28a745;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            border-radius: 8px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        .warning ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .warning li {
            color: #856404;
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .security-info {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Password</h1>
            <p class="subtitle">Kode Verifikasi untuk Reset Password Anda</p>
        </div>

        <p>Assalamu'alaikum {{ $name ?? 'Pengguna' }},</p>

        <p>Kami menerima permintaan untuk reset password akun Anda di <strong>{{ config('app.name') }}</strong>. Untuk melanjutkan proses reset password, silakan masukkan kode verifikasi berikut:</p>

        <div class="verification-code">
            <div class="code">{{ $verificationCode }}</div>
            <p style="margin: 10px 0 0 0; color: #666;">Kode Verifikasi Reset Password</p>
        </div>

        <div class="security-info">
            <strong>🛡️ Informasi Keamanan:</strong>
            <ul>
                <li>Kode ini hanya berlaku untuk <strong>{{ $expiryMinutes }} menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapa pun</li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                <li>Untuk keamanan, segera ganti password setelah berhasil reset</li>
            </ul>
        </div>

        <div class="warning">
            <div class="warning-title">⚠️ Penting:</div>
            <ul>
                <li>Kode ini akan kadaluarsa dalam <strong>{{ $expiryMinutes }} menit</strong></li>
                <li>Maksimal <strong>3 kali percobaan</strong> untuk memasukkan kode</li>
                <li>Jika Anda tidak melakukan permintaan ini, segera hubungi administrator</li>
            </ul>
        </div>

        <p>Setelah memasukkan kode verifikasi, Anda akan diminta untuk membuat password baru. Pastikan password baru Anda:</p>
        <ul>
            <li>Minimal 6 karakter</li>
            <li>Kombinasi huruf dan angka</li>
            <li>Mudah diingat tapi sulit ditebak</li>
        </ul>

        <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami.</p>

        <p>Barakallahu fiikum,<br>
        <strong>Tim {{ config('app.name') }}</strong></p>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
            <p style="font-size: 12px; color: #999;">
                Dikirim pada {{ now()->format('d F Y, H:i') }} WIB
            </p>
        </div>
    </div>
</body>
</html>
