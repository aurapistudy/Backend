<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahun Akademik - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --color-primary: #1F2937;
            --color-primary-dark: #111827;
            --color-primary-light: #F9FAFB;
            --color-accent: #F8B803;
            --color-white: #FFFFFF;
            --color-gray-light: #F3F4F6;
            --color-gray: #E5E7EB;
            --color-text: #111827;
            --color-text-light: #6B7280;
            --sidebar-width: 280px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-gray-light);
            color: var(--color-text);
            overflow-x: hidden;
        }

        .dashboard-container { display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container { display: flex; align-items: center; gap: 1rem; }

        .logo-circle {
            width: 50px;
            height: 50px;
            background: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo-text { font-size: 1.5rem; font-weight: 800; color: var(--color-white); letter-spacing: 1px; }

        .logout-btn {
            margin: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: var(--color-white);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .logout-btn:hover { background: rgba(255, 255, 255, 0.3); }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-bar {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-title { font-size: 1.75rem; font-weight: 700; color: #FFFFFF; }
        .content-area { flex: 1; padding: 2rem; }

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

        .form-group { margin-bottom: 1.5rem; }

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

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .form-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
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

        .btn-primary { background: var(--color-primary); color: #ffffff; }
        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.4);
        }

        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .btn-secondary:hover { background: #D1D5DB; }

        .error-message { color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem; }

        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-error { background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; }

        .required { color: #DC2626; }

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

        .hint {
            color: var(--color-text-light);
            font-size: 0.85rem;
            margin-top: 0.35rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-circle"><span>👋</span></div>
                    <div class="logo-text">Ruma</div>
                </div>
            </div>

            @include('components.sidebar')

            <div class="logout-btn" onclick="handleLogout()" style="display:flex; align-items:center; gap:8px; justify-content:center;">
                <i data-lucide="log-out"></i>
                <span>Keluar</span>
            </div>
        </aside>

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Edit Tahun Akademik</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="{{ route('tahun-akademik.show', $tahunAkademik->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail
                    </a>
                    <a href="{{ route('tahun-akademik.index') }}" class="back-link">
                        <i data-lucide="list"></i>
                        Daftar Tahun Akademik
                    </a>
                </div>

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

                <form action="{{ route('tahun-akademik.update', $tahunAkademik->id) }}" method="POST" class="form-container">
                    @csrf
                    @method('PUT')

                    <div class="section-title"><i data-lucide="calendar-range"></i> Informasi Tahun Akademik</div>
                    <div class="section-subtitle">Perbarui nama tahun dan periode berlaku.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Nama Tahun Akademik <span class="required">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $tahunAkademik->nama) }}" class="form-input" required placeholder="2025/2026">
                        <span class="hint">Format: YYYY/YYYY (contoh: 2025/2026).</span>
                        @error('nama')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Tanggal Mulai <span class="required">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tahunAkademik->tanggal_mulai->format('Y-m-d')) }}" class="form-input" required>
                            @error('tanggal_mulai')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Tanggal Selesai <span class="required">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tahunAkademik->tanggal_selesai->format('Y-m-d')) }}" class="form-input" required>
                            @error('tanggal_selesai')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $tahunAkademik->status_aktif) ? 'checked' : '' }}>
                            <span>Jadikan tahun akademik aktif</span>
                        </label>
                        <span class="hint">Hanya satu tahun akademik yang boleh aktif pada satu waktu.</span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('tahun-akademik.show', $tahunAkademik->id) }}" class="btn btn-secondary">
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
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>
