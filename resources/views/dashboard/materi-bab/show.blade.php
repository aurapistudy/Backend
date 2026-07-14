<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Materi - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        :root { --muted: #6B7280; --soft: #F8FAFC; --accent-soft: #FFF7D6; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-text);
            text-decoration: none;
            font-weight: 700;
            background: #FFFFFF;
            border: 1px solid var(--color-gray);
            border-radius: 10px;
            padding: 0.55rem 0.8rem;
            box-shadow: 0 2px 8px rgba(17,24,39,0.08);
        }
        .detail-shell {
            margin-top: 1rem;
            background: #FFFFFF;
            border-radius: 18px;
            border: 1px solid rgba(17,24,39,0.08);
            box-shadow: 0 14px 34px rgba(17,24,39,0.08);
            overflow: hidden;
        }
        .detail-hero {
            padding: 1.35rem 1.5rem;
            background: linear-gradient(180deg, #FFFCF4 0%, #FFFFFF 100%);
            border-bottom: 1px solid rgba(17,24,39,0.08);
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .detail-title { font-size: 1.55rem; font-weight: 800; color: var(--color-text); margin: 0.35rem 0; }
        .detail-subtitle { color: var(--muted); line-height: 1.6; max-width: 760px; }
        .badge-row { display: flex; gap: 0.55rem; flex-wrap: wrap; margin-top: 0.8rem; }
        .badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.7rem; border-radius: 999px; background: var(--soft); border: 1px solid rgba(17,24,39,0.06); font-weight: 800; font-size: 0.82rem; color: var(--color-text); }
        .detail-actions { display: flex; gap: 0.65rem; flex-wrap: wrap; align-items: flex-start; }
        .btn { padding: 0.78rem 1rem; border: none; border-radius: 12px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; }
        .btn-primary { background: var(--color-accent); color: #111827; }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .detail-body { padding: 1.5rem; display: grid; gap: 1.2rem; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.9rem; }
        .info-card { background: var(--soft); border: 1px solid rgba(17,24,39,0.06); border-radius: 14px; padding: 0.9rem 1rem; }
        .info-label { color: var(--muted); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; }
        .info-value { margin-top: 0.35rem; color: var(--color-text); font-weight: 800; line-height: 1.45; word-break: break-word; }
        .section { border: 1px solid rgba(17,24,39,0.08); border-radius: 16px; padding: 1.1rem; background: #FFFFFF; }
        .section-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1.05rem; font-weight: 800; margin-bottom: 0.8rem; }
        .text-content { background: #FAFAFA; border-radius: 14px; padding: 1rem; line-height: 1.8; white-space: pre-wrap; color: var(--color-text); }
        .file-panel { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; background: var(--accent-soft); border: 1px solid rgba(248,184,3,0.28); border-radius: 14px; padding: 1rem; }
        .file-name { font-weight: 800; color: #111827; word-break: break-word; }
        .file-meta { color: var(--muted); margin-top: 0.25rem; font-size: 0.9rem; }
        .summary-list { display: grid; gap: 0.65rem; }
        .summary-point { background: var(--soft); border-radius: 12px; padding: 0.75rem 0.9rem; line-height: 1.6; }
        .poster-img { width: 100%; max-width: 620px; border-radius: 16px; display: block; box-shadow: 0 12px 28px rgba(17,24,39,0.12); }
        @media (max-width: 720px) { .detail-hero, .detail-body { padding: 1rem; } .detail-title { font-size: 1.25rem; } }
    </style>
</head>
<body>
    @php($isSuperAdmin = auth()->user()?->isSuperAdmin())
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Detail Materi</h1>
            </header>

            <div class="content-area">
                <a href="{{ route('materi.show', $materi->id) }}" class="back-link">
                    <i data-lucide="arrow-left"></i>
                    Kembali ke Detail Mata Pelajaran
                </a>

                <div class="detail-shell">
                    <div class="detail-hero">
                        <div>
                            <div class="badge"><i data-lucide="book-open"></i> {{ $materi->judul }}</div>
                            <h2 class="detail-title">Materi {{ $bab->urutan }}. {{ $bab->judul_bab }}</h2>
                            <div class="detail-subtitle">
                                Detail isi materi, file, range halaman, rangkuman, dan relasi kuis untuk bagian ini.
                            </div>
                            <div class="badge-row">
                                <span class="badge">{{ ucfirst($bab->tipe_konten) }}</span>
                                <span class="badge">{{ $bab->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                <span class="badge">{{ $bab->kuis_count }} kuis</span>
                            </div>
                        </div>
                        <div class="detail-actions">
                            @unless($isSuperAdmin)
                                <a href="{{ route('materi.bab.edit', [$materi->id, $bab->id]) }}" class="btn btn-primary">
                                    <i data-lucide="edit-3"></i>
                                    Edit
                                </a>
                            @endunless
                            <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-secondary">
                                <i data-lucide="list"></i>
                                Daftar Materi
                            </a>
                        </div>
                    </div>

                    <div class="detail-body">
                        <div class="info-grid">
                            <div class="info-card">
                                <div class="info-label">Urutan</div>
                                <div class="info-value">Materi {{ $bab->urutan }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Tipe Konten</div>
                                <div class="info-value">{{ ucfirst($bab->tipe_konten) }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Pilihan Halaman PDF</div>
                                <div class="info-value">{{ $bab->pdf_page_selection ?: '-' }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Terakhir Update</div>
                                <div class="info-value">{{ $bab->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="section">
                            <div class="section-title"><i data-lucide="file-text"></i> Isi Materi</div>
                            @if($bab->tipe_konten === 'teks')
                                <div class="text-content">{{ $bab->konten_teks ?: 'Belum ada konten teks.' }}</div>
                            @elseif($bab->file_path)
                                <div class="file-panel">
                                    <div>
                                        <div class="file-name">{{ basename($bab->file_path) }}</div>
                                        <div class="file-meta">
                                            Format: {{ strtoupper(pathinfo($bab->file_path, PATHINFO_EXTENSION)) }}
                                            @if($bab->pdf_source_path)
                                                | Sumber PDF tersimpan
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ $bab->file_url }}" target="_blank" class="btn btn-primary">
                                        <i data-lucide="external-link"></i>
                                        Buka File
                                    </a>
                                </div>
                            @else
                                <div class="text-content">File materi belum tersedia.</div>
                            @endif
                        </div>

                        @if($bab->summary_visual_url || $bab->summary_short || ($bab->summary_key_points ?? []))
                            <div class="section">
                                <div class="section-title"><i data-lucide="sparkles"></i> Rangkuman</div>
                                @if($bab->summary_visual_url)
                                    <img src="{{ $bab->summary_visual_url }}" alt="Poster rangkuman {{ $bab->judul_bab }}" class="poster-img">
                                @endif
                                @if($bab->summary_title)
                                    <h3 style="margin:1rem 0 0.4rem;">{{ $bab->summary_title }}</h3>
                                @endif
                                @if($bab->summary_short)
                                    <div class="text-content">{{ $bab->summary_short }}</div>
                                @endif
                                @if($bab->summary_key_points)
                                    <div class="summary-list" style="margin-top:0.8rem;">
                                        @foreach($bab->summary_key_points as $point)
                                            <div class="summary-point">{{ $point }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
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
