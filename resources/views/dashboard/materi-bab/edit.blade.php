<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Materi - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .back-link-clean {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-primary);
            text-decoration: none;
            transition: 0.2s ease;
            margin-bottom: 1rem;
        }

        .back-link-clean:hover {
            color: var(--color-accent);
            transform: translateX(-3px);
        }

        .form-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
            max-width: 1080px;
        }

        .hero-panel {
            display: grid;
            grid-template-columns: 1.4fr 0.8fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-card,
        .info-card {
            border-radius: 18px;
            padding: 1.25rem 1.35rem;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
            border: 1px solid rgba(248, 184, 3, 0.18);
        }

        .hero-title {
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--color-text);
        }

        .hero-subtitle {
            color: var(--color-text-light);
            line-height: 1.7;
            font-size: 0.96rem;
        }

        .hero-book-name {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-top: 0.9rem;
            padding: 0.7rem 0.9rem;
            border-radius: 12px;
            background: #FFFFFF;
            border: 1px solid rgba(17, 24, 39, 0.08);
            font-weight: 700;
        }

        .mini-label {
            font-size: 0.82rem;
            color: var(--color-text-light);
            margin-bottom: 0.35rem;
        }

        .mini-value {
            font-size: 0.96rem;
            font-weight: 700;
            color: var(--color-text);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem 1.25rem;
        }

        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--color-gray);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }

        .form-textarea { min-height: 180px; resize: vertical; }

        .section-title {
            font-size: 1.12rem;
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

        .required { color: #DC2626; }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            font-weight: 600;
        }

        .form-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .hint {
            color: var(--color-text-light);
            font-size: 0.85rem;
            margin-top: 0.35rem;
            display: block;
        }

        .note-box {
            padding: 1rem 1.1rem;
            border-radius: 14px;
            background: #F8FAFC;
            color: var(--color-text-light);
            font-size: 0.9rem;
            line-height: 1.65;
            margin-bottom: 1.25rem;
        }

        .alert {
            padding: 1rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
        }

        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .btn-row {
            display: flex;
            gap: 0.85rem;
            flex-wrap: wrap;
            margin-top: 1.75rem;
        }

        .btn {
            padding: 0.95rem 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-accent);
            color: #1F2937;
        }

        .btn-primary:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.4);
        }

        .btn-secondary {
            background: var(--color-gray);
            color: var(--color-text);
        }

        .btn-secondary:hover { background: #D1D5DB; }

        .pdf-selection-panel { margin-top: 1rem; padding: 1rem; border: 1px solid #E5E7EB; border-radius: 14px; background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%); }
        .pdf-selection-toolbar, .pdf-selection-actions { display:flex; gap:.75rem; flex-wrap:wrap; justify-content:space-between; align-items:center; }
        .pdf-selection-range { display:grid; grid-template-columns:repeat(2,minmax(0,180px)); gap:.9rem; margin-bottom:1rem; }
        .pdf-action-btn { border:1px solid rgba(31,41,55,.12); background:#fff; color:#111827; border-radius:999px; padding:.55rem .9rem; font-size:.85rem; font-weight:600; cursor:pointer; }
        .pdf-pages-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:1rem; }
        .pdf-page-card { border:1px solid rgba(17,24,39,.08); border-radius:16px; background:#fff; overflow:hidden; box-shadow:0 6px 18px rgba(17,24,39,.06); }
        .pdf-page-card.selected { border-color: rgba(248,184,3,.95); background: linear-gradient(180deg, #FFF5CC 0%, #FFF0B3 100%); }
        .pdf-page-preview { position:relative; aspect-ratio:3/4; background:#F8FAFC; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .pdf-page-preview canvas { width:100%; height:100%; object-fit:contain; display:block; }
        .pdf-page-check { position:absolute; top:.6rem; right:.6rem; width:24px; height:24px; accent-color:#F8B803; }
        .pdf-page-meta { padding:.85rem .9rem 1rem; }
        .pdf-page-title { font-size:.9rem; font-weight:700; }
        .pdf-page-subtitle, .pdf-selection-empty, .pdf-selection-loading, .current-file { color:#6B7280; font-size:.9rem; }
        .current-file { margin-top:.5rem; }

        @media (max-width: 960px) {.hero-panel { grid-template-columns: 1fr; }}

        @media (max-width: 768px) {.form-container { padding: 1.25rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Edit Materi</h1>
            </header>

            <div class="content-area">
                <a href="{{ route('materi.show', $materi->id) }}" class="back-link-clean">
                    <i data-lucide="arrow-left"></i>
                    Kembali ke detail mata pelajaran
                </a>

                <div class="form-container">
                    <div class="hero-panel">
                        <div class="hero-card">
                            <div class="hero-title">Perbarui Materi</div>
                            <div class="hero-subtitle">
                                Edit isi, urutan, atau tipe konten materi tanpa memecah struktur mata pelajaran utama. Perubahan ini akan langsung menjaga alur belajar siswa tetap runtut.
                            </div>
                            <div class="hero-book-name">
                                <i data-lucide="book-open"></i>
                                <span>{{ $materi->judul }} - Materi {{ $bab->urutan }}</span>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="mini-label">Judul Materi Saat Ini</div>
                            <div class="mini-value">{{ $bab->judul_bab }}</div>
                            <div class="mini-label" style="margin-top:0.85rem;">Relasi Kuis</div>
                            <div class="hero-subtitle" style="font-size:0.9rem;">
                                Setelah bagian ini diperbarui, kuis yang terhubung tetap bisa dikelola dari halaman detail mata pelajaran melalui tombol cepat per materi.
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul style="margin:0; padding-left:1.2rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="section-title"><i data-lucide="pencil-line"></i> Form Edit Materi</div>
                    <div class="section-subtitle">Perbarui data materi dengan jelas agar mata pelajaran tetap konsisten dan profesional di sisi admin maupun siswa.</div>

                    <div class="note-box">
                        Materi ini tetap menjadi bagian dari satu mata pelajaran utama. Gunakan halaman ini untuk memperbarui konten tanpa membuat mata pelajaran baru, sehingga struktur isi tetap efisien dan mudah dikelola.
                    </div>

                    <form method="POST" action="{{ route('materi.bab.update', [$materi->id, $bab->id]) }}" enctype="multipart/form-data" id="babForm">
                        @csrf
                        @method('PUT')
                        @include('dashboard.materi-bab._form')

                        <div class="btn-row">
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save"></i>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-secondary">
                                <i data-lucide="arrow-left"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    @include('dashboard.materi-bab.partials.form-script', ['bab' => $bab])
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
    @include('components.modal')
</body>
</html>
