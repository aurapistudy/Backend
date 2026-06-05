<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun {{ $appBranding->title }} - Platform Edukasi Modern">
    <title>Daftar - {{ $appBranding->title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-yellow-light: #FFF9E6;
            --color-yellow-primary: #F8B803;
            --color-yellow-dark: #E6A500;
            --color-brown: #6B4215;
            --color-brown-dark: #4A2D0F;
            --color-gray-light: #F0F0F0;
            --color-gray-placeholder: #9CA3AF;
            --color-orange: #FF6B35;
        }

        html, body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background: linear-gradient(
                180deg,
                var(--color-yellow-light) 0%,
                var(--color-yellow-light) 80%,
                var(--color-yellow-primary) 80%,
                var(--color-yellow-primary) 100%
            );
            display: flex;
            flex-direction: column;
        }

        body::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .login-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            position: relative;
            z-index: 10;
            min-height: calc(100vh - 48px);
            gap: 0.5rem;
        }

        .logo-section,
        .auth-brand {
            text-align: center;
            margin-bottom: 0.6rem;
            animation: fadeInDown 0.6s ease-out;
        }

        .auth-brand-logo {
            width: auto;
            height: 88px;
            max-width: 168px;
            object-fit: contain;
            display: block;
            margin: 0 auto 1rem;
            filter: drop-shadow(0 8px 18px rgba(248, 184, 3, 0.25));
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--color-orange) 0%, var(--color-yellow-primary) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(248, 184, 3, 0.3);
            position: relative;
            overflow: hidden;
        }

        .logo-icon::before {
            content: '????';
            font-size: 3rem;
            z-index: 2;
        }

        .logo-icon svg {
            width: 60px;
            height: 60px;
            position: absolute;
            z-index: 1;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--color-brown-dark);
            margin-bottom: 0.3rem;
        }

        .login-subtitle {
            font-size: 0.95rem;
            color: #8B6B3D;
            margin-bottom: 0.6rem;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 24px;
            padding: 1.5rem 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.6s ease-out 0.2s both;
            position: relative;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--color-brown);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.7rem 1.25rem;
            border: 2px solid var(--color-gray-light);
            border-radius: 12px;
            background: var(--color-gray-light);
            font-size: 1rem;
            color: var(--color-brown);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-yellow-primary);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }

        .form-input::placeholder {
            color: var(--color-gray-placeholder);
        }

        .login-button {
            width: 100%;
            padding: 0.85rem;
            background: var(--color-yellow-primary);
            border: none;
            border-radius: 10px;
            color: var(--color-brown);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(248, 184, 3, 0.4);
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .login-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .login-button:hover {
            background: var(--color-yellow-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(248, 184, 3, 0.5);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button span {
            position: relative;
            z-index: 1;
        }

        .footer-strip {
            background: var(--color-yellow-primary);
            width: 100%;
            padding: 0.6rem 2rem;
            display: flex;
            align-items: center;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .footer-icon {
            width: 24px;
            height: 24px;
            opacity: 0.3;
            color: white;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(248, 184, 3, 0.1);
            z-index: 0;
        }

        .decoration-circle-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            right: -50px;
        }

        .decoration-circle-2 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: -30px;
        }

        @media (max-width: 640px) {
            .login-card {
                background: #FFFFFF;
                border-radius: 20px;
                padding: 1.2rem;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.14);
            }

            .logo-icon {
                width: 70px;
                height: 70px;
            }

            .login-title {
                font-size: 1.6rem;
            }
        }

        @media (max-height: 700px) {
            body {
                overflow-y: auto;
            }

            .login-container {
                min-height: auto;
                padding: 1rem 1rem 3.5rem;
            }
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
            color: var(--color-brown);
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--color-yellow-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--color-yellow-dark);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="decoration-circle decoration-circle-1"></div>
    <div class="decoration-circle decoration-circle-2"></div>

        <div class="login-container">
        <div class="logo-section">
            <x-auth-brand :heading="'Daftar Akun ' . $appBranding->title" subtitle="Buat akun siswa untuk mulai belajar" />
        </div>

        <div class="login-card">
            @if ($errors->any())
                <div style="background: #fee; border: 2px solid #fcc; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; color: #c33;">
                    <strong>Registrasi Gagal!</strong>
                    <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="registerForm" method="POST" action="{{ route('register', [], false) }}">
                @csrf

                <div class="form-group">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-input @error('nama') error-input @enderror"
                        placeholder="Masukan nama lengkap"
                        value="{{ old('nama') }}"
                        required
                        autofocus
                    >
                    @error('nama')
                        <span style="color: #c33; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input @error('email') error-input @enderror"
                        placeholder="Masukan email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <span style="color: #c33; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kata_sandi" class="form-label">Kata Sandi</label>
                    <input
                        type="password"
                        id="kata_sandi"
                        name="kata_sandi"
                        class="form-input @error('kata_sandi') error-input @enderror"
                        placeholder="Masukan kata sandi"
                        required
                        autocomplete="new-password"
                    >
                    @error('kata_sandi')
                        <span style="color: #c33; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kata_sandi_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <input
                        type="password"
                        id="kata_sandi_confirmation"
                        name="kata_sandi_confirmation"
                        class="form-input"
                        placeholder="Ulangi kata sandi"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="login-button">
                    <span>Daftar</span>
                </button>

                <div class="register-link">
                    Sudah punya akun? <a href="{{ route('login', [], false) }}">Masuk</a>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-strip">
        <svg class="footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 12L5 10M5 10L12 3L19 10M5 10V20C5 20.5523 5.44772 21 6 21H9M19 10L21 12M19 10V20C19 20.5523 18.5523 21 18 21H15M9 21C9.55228 21 10 20.5523 10 20V16C10 15.4477 10.4477 15 11 15H13C13.5523 15 14 15.4477 14 16V20C14 20.5523 14.4477 15 15 21M9 21H15"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"/>
        </svg>
    </div>

    <style>
        .error-input {
            border-color: #f33 !important;
            background: #fff5f5 !important;
        }
    </style>

    <script>
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        document.getElementById('registerForm').addEventListener('submit', function() {
            const button = this.querySelector('.login-button');
            const buttonText = button.querySelector('span');
            buttonText.textContent = 'Memproses...';
            button.disabled = true;
        });
    </script>
</body>
</html>
