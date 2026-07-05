<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
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
            max-width: 480px;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(107, 66, 21, 0.12);
            padding: 2rem;
        }

        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.9rem;
        }

        p {
            margin: 0 0 1.5rem;
            color: var(--text-soft);
            line-height: 1.6;
        }

        .alert {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 2px solid transparent;
            background: var(--input-bg);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            color: var(--text-main);
            margin-bottom: 1rem;
        }

        input:focus {
            outline: none;
            border-color: var(--bg-accent);
            background: #fff;
        }

        button {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 0.95rem 1.15rem;
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            background: var(--bg-accent);
            color: var(--text-main);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Reset Kata Sandi</h1>
        <p>Masukkan kata sandi baru untuk akun <strong>{{ $email }}</strong>.</p>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="alert" style="background: #eefbf0; border-color: #b7e0bf; color: #246b34;">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update', [], false) }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="code" value="{{ $code ?? '' }}">

            <label for="kata_sandi">Kata Sandi Baru</label>
            <input
                type="password"
                id="kata_sandi"
                name="kata_sandi"
                placeholder="Masukkan kata sandi baru"
                required
                autocomplete="new-password"
            >

            <label for="kata_sandi_confirmation">Konfirmasi Kata Sandi</label>
            <input
                type="password"
                id="kata_sandi_confirmation"
                name="kata_sandi_confirmation"
                placeholder="Ulangi kata sandi baru"
                required
                autocomplete="new-password"
            >

            <button type="submit">Simpan Kata Sandi Baru</button>
        </form>
    </div>
</body>
</html>
