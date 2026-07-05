<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset Kata Sandi</title>
</head>
<body style="font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #333;">
  <div style="max-width:600px;margin:0 auto;padding:20px;">
    <div style="text-align:center;margin-bottom:18px;">
      <h2 style="margin:0;color:#6B4215">Reset Kata Sandi</h2>
    </div>

    <p>Halo {{ $pengguna->nama }},</p>

    <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda. Gunakan kode verifikasi di bawah ini untuk melanjutkan reset kata sandi. Kode ini berlaku selama {{ config('auth.passwords.users.expire', 60) }} menit.</p>

    <p style="text-align:center;margin:24px 0;">
      <span style="display:inline-block;padding:14px 20px;background:#F8B803;color:#fff;border-radius:8px;font-size:1.3rem;font-weight:700;letter-spacing:0.2em">{{ $code }}</span>
    </p>

    <p>Masukkan kode tersebut di halaman verifikasi reset kata sandi di aplikasi atau situs kami.</p>

    <hr style="border:none;border-top:1px solid #eee;margin:20px 0" />
    <p style="font-size:0.85rem;color:#777">Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>

    <p style="font-size:0.85rem;color:#777">Salam,<br>{{ config('app.name') }}</p>
  </div>
</body>
</html>