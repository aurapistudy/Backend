<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode Reset</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-soft: #fff8df;
            --bg-accent: #f8b803;
            --text-main: #5f3b16;
            --text-soft: #8a6a3f;
            --card-bg: #ffffff;
            --input-bg: #f5f1e7;
            --danger-bg: #fff1f1;
            --danger-border: #f3b6b6;
            --danger-text: #a22626;
            --success-bg: #eefbf0;
            --success-border: #b7e0bf;
            --success-text: #246b34;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, var(--bg-soft) 0%, #fff2b8 100%);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(107, 66, 21, 0.12);
            padding: 2rem;
        }

        h1 { margin: 0 0 0.5rem; font-size: 1.9rem; }
        p { margin: 0 0 1.5rem; color: var(--text-soft); line-height: 1.6; }
        .alert { border-radius: 14px; padding: 0.9rem 1rem; margin-bottom: 1rem; font-size: 0.95rem; }
        .alert-error { background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger-text); }
        .alert-success { background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success-text); }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input {
            width: 100%;
            border: 2px solid transparent;
            background: var(--input-bg);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            color: var(--text-main);
            margin-bottom: 1rem;
            letter-spacing: 0.2em;
        }
        input:focus { outline: none; border-color: var(--bg-accent); background: #fff; }
        .actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        button, .back-link {
            border: none;
            border-radius: 14px;
            padding: 0.9rem 1.15rem;
            font-size: 0.98rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        button { background: var(--bg-accent); color: var(--text-main); }
        .back-link { background: #f4ead4; color: var(--text-main); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Verifikasi Kode Reset</h1>
        <p>Masukkan kode 6 digit yang dikirim ke email Anda untuk melanjutkan reset kata sandi.</p>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.verify.post', [], false) }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" placeholder="Masukkan email akun" required autocomplete="email">

            <label for="code">Kode Verifikasi</label>
            <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="123456" required inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code">

            <div class="actions">
                <button type="submit">Verifikasi Kode</button>
                <a href="{{ route('login', [], false) }}" class="back-link">Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>
