<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan API - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .form-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .section-subtitle {
            color: var(--color-text-light);
            font-size: 0.92rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .form-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--color-gray);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }
        .form-help {
            font-size: 0.85rem;
            color: var(--color-text-light);
            margin-top: 0.35rem;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: var(--color-primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.4);
        }
        .btn-secondary {
            background: var(--color-gray);
            color: var(--color-text);
        }
        .btn-secondary:hover {
            background: #D1D5DB;
        }
        .error-message {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-text);
            text-decoration: none;
            font-weight: 600;
            background: var(--color-white);
            border: 1px solid var(--color-gray);
            border-radius: 10px;
            padding: 0.45rem 0.75rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
        }
        .back-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.12);
            background: var(--color-primary-light);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Pengaturan API</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('landing.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Landing
                    </a>
                </div>

                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <strong>Terjadi kesalahan:</strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST" class="form-container">
                    @csrf
                    @method('PUT')

                    <div class="section-title"><i data-lucide="key-round"></i> Kunci API Generate Gambar</div>
                    <div class="section-subtitle">Kunci API ini dipakai untuk menghasilkan gambar otomatis pada konten landing. Kosongkan field jika tidak ingin mengubah nilai yang sudah tersimpan.</div>

                    <div class="form-group">
                        <label class="form-label">Gemini API Key</label>
                        <input type="password" name="gemini_api_key" class="form-input" placeholder="{{ $settings['gemini_api_key'] ? '•••••••••••••••• (sudah tersimpan)' : 'Masukkan Gemini API Key' }}" autocomplete="off">
                        @error('gemini_api_key')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gemini Image Model</label>
                        <input type="text" name="gemini_image_model" value="{{ old('gemini_image_model', $settings['gemini_image_model']) }}" class="form-input" placeholder="contoh: gemini-2.5-flash-image">
                        @error('gemini_image_model')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hugging Face API Token</label>
                        <input type="password" name="hf_api_token" class="form-input" placeholder="{{ $settings['hf_api_token'] ? '•••••••••••••••• (sudah tersimpan)' : 'Masukkan HF API Token' }}" autocomplete="off">
                        @error('hf_api_token')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hugging Face Image Model</label>
                        <input type="text" name="hf_image_model" value="{{ old('hf_image_model', $settings['hf_image_model']) }}" class="form-input" placeholder="contoh: black-forest-labs/FLUX.1-schnell">
                        <div class="form-help">Model ini dipakai sebagai alternatif jika Gemini tidak tersedia.</div>
                        @error('hf_image_model')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Pengaturan
                        </button>
                        <a href="{{ route('landing.index') }}" class="btn btn-secondary">
                            <i data-lucide="x"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    @include('components.modal')
    <script>
        function handleLogout() {
            showModal({
                type: 'logout',
                title: 'Konfirmasi Logout',
                message: 'Apakah Anda yakin ingin keluar dari akun Anda?',
                icon: 'log-out',
                confirmText: 'Ya, Keluar',
                isDanger: false,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout", [], false) }}';
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        lucide.createIcons();
    </script>
</body>
</html>