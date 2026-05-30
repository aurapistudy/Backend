<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mata Pelajaran - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        :root {
            --color-accent-dark: #E6A500;
            --color-accent-light: #FFF9E6;
            --color-sidebar: #111827;
            --color-sidebar-dark: #0b1220;
            --color-muted: #6B7280;
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
            background: var(--color-accent-light);
        }

        .detail-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.35rem 2rem 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--color-gray);
        }

        .detail-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }

        .detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-muted);
            font-size: 0.9rem;
        }

        .meta-item i {
            width: 16px;
            height: 16px;
        }

        .meta-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-primary {
            background: var(--color-accent-light);
            color: var(--color-accent-dark);
        }

        .badge-success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-danger {
            background: #FFEBEE;
            color: #C62828;
        }

        .content-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        .section-content {
            color: var(--color-text);
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .pdf-viewer {
            width: 100%;
            height: 80vh;
            border: 2px solid var(--color-gray);
            border-radius: 12px;
            margin-top: 1rem;
        }

        .text-content {
            background: var(--color-gray-light);
            padding: 1.5rem;
            border-radius: 12px;
            line-height: 1.8;
            white-space: pre-wrap;
            font-size: 1rem;
            color: var(--color-text);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--color-gray);
        }

        .btn {
            padding: 0.75rem 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-accent);
            color: var(--color-sidebar);
        }

        .btn-primary:hover {
            background: var(--color-accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.35);
        }

        .btn-secondary {
            background: var(--color-gray);
            color: var(--color-text);
        }

        .btn-secondary:hover {
            background: #D1D5DB;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--color-accent-light);
            padding: 1rem 1.25rem;
            border-radius: 14px;
            border: 1px solid rgba(248, 184, 3, 0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #FFF2C7;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--color-accent-dark);
        }

        .info-icon i {
            width: 18px;
            height: 18px;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--color-muted);
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-text);
        }

        .flash-alert {
            padding: 1rem 1.1rem;
            border-radius: 14px;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .flash-alert.success {
            background: #ECFDF3;
            color: #166534;
            border-color: #A7F3D0;
        }

        .flash-alert.error {
            background: #FEF2F2;
            color: #991B1B;
            border-color: #FECACA;
        }

        .chapter-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--color-gray);
        }

        .chapter-library {
            display: grid;
            grid-template-columns: minmax(260px, 0.42fr) minmax(0, 1fr);
            gap: clamp(0.85rem, 1.8vw, 1.35rem);
            align-items: start;
            margin-top: 1rem;
            width: 100%;
            max-width: min(1180px, 100%);
            margin-inline: auto;
        }

        .chapter-side {
            position: sticky;
            top: 1.25rem;
            border-radius: 22px;
            background: linear-gradient(180deg, #FFFDF6 0%, #FFFFFF 100%);
            border: 1px solid rgba(248, 184, 3, 0.18);
            padding: 1.2rem;
        }

        .chapter-cover {
            width: 100%;
            aspect-ratio: 3 / 4;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(160deg, #FFF2C7 0%, #F8FAFC 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 26px rgba(17, 24, 39, 0.12);
        }

        .chapter-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .chapter-cover-fallback {
            padding: 1.3rem;
            text-align: center;
        }

        .chapter-cover-fallback strong {
            font-size: 1.45rem;
            line-height: 1.2;
            color: var(--color-text);
            display: block;
        }

        .chapter-cover-fallback span {
            display: block;
            margin-top: 0.7rem;
            color: var(--color-muted);
            font-size: 0.88rem;
        }

        .chapter-side-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--color-text);
            margin-top: 0.9rem;
            line-height: 1.3;
        }

        .chapter-side-copy {
            margin-top: 0.5rem;
            color: var(--color-muted);
            line-height: 1.58;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 8;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .chapter-side-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem;
            margin-top: 0.95rem;
        }

        .chapter-stat {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            background: #FFFFFF;
            padding: 0.75rem 0.8rem;
            min-height: 84px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .chapter-stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--color-muted);
        }

        .chapter-stat-value {
            margin-top: 0.3rem;
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--color-text);
            line-height: 1.35;
            word-break: break-word;
        }

        .chapter-side-actions {
            display: grid;
            gap: 0.6rem;
            margin-top: 0.95rem;
        }

        .chapter-side-actions .btn {
            width: 100%;
            justify-content: flex-start;
            padding: 0.88rem 1rem;
            border-radius: 14px;
            font-size: 0.95rem;
        }

        .chapter-main {
            border-radius: 22px;
            background: #FFFFFF;
            border: 1px solid rgba(17, 24, 39, 0.08);
            box-shadow: 0 14px 30px rgba(17, 24, 39, 0.08);
            overflow: hidden;
        }

        .chapter-main-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid rgba(17, 24, 39, 0.08);
            background: linear-gradient(180deg, #FFFFFF 0%, #FFFCF4 100%);
        }

        .chapter-main-title {
            font-size: 1.14rem;
            font-weight: 800;
            color: var(--color-text);
        }

        .chapter-main-subtitle {
            margin-top: 0.25rem;
            color: var(--color-muted);
            font-size: 0.9rem;
        }

        .chapter-list {
            display: grid;
        }

        .chapter-row {
            border-top: 1px solid rgba(17, 24, 39, 0.08);
            padding: 1.05rem 1.25rem 1.15rem;
        }

        .chapter-row:first-child {
            border-top: none;
        }

        .chapter-row-head {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }

        .chapter-copy {
            min-width: 0;
        }

        .chapter-handle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: #F8FAFC;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--color-muted);
        }

        .chapter-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--color-text);
            line-height: 1.35;
        }

        .chapter-meta {
            margin-top: 0.4rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            color: var(--color-muted);
            font-size: 0.88rem;
        }

        .chapter-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.65rem;
            border-radius: 999px;
            background: #F8FAFC;
            border: 1px solid rgba(17, 24, 39, 0.06);
            color: var(--color-text);
            font-weight: 700;
        }

        .chapter-actions {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
            justify-content: flex-start;
            grid-column: 2 / -1;
            margin-top: 0.9rem;
        }

        .chapter-actions form {
            margin: 0;
        }

        .chapter-actions .btn,
        .chapter-actions button {
            flex: 0 0 auto;
            white-space: nowrap;
            padding: 0.78rem 1rem;
            font-size: 0.92rem;
        }

        .chapter-outline {
            margin-top: 0.85rem;
            color: var(--color-text);
            line-height: 1.7;
            background: #FAFAFA;
            border-radius: 14px;
            padding: 0.9rem 1rem;
        }

        .chapter-note {
            margin-top: 0.75rem;
            padding: 0.78rem 0.92rem;
            border-radius: 12px;
            background: #F8FAFC;
            color: var(--color-muted);
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .summary-shell {
            margin-top: 1rem;
            border: 1px solid rgba(17, 24, 39, 0.06);
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(248, 184, 3, 0.22), transparent 34%),
                linear-gradient(135deg, #FFF7D6 0%, #FFFFFF 55%, #F8FAFC 100%);
            padding: 1.1rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .summary-poster-frame {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .summary-poster-img {
            width: 100%;
            max-width: min(560px, 100%);
            height: auto;
            display: block;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }

        .summary-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .summary-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.34rem 0.7rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.7);
            color: #8A6500;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .summary-title {
            margin-top: 0.55rem;
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
        }

        .summary-generated {
            margin-top: 0.35rem;
            font-size: 0.82rem;
            color: var(--color-muted);
        }

        .summary-hero {
            border-radius: 20px;
            background: #111827;
            color: #FFFFFF;
            padding: 1rem 1.05rem;
            margin-bottom: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .summary-hero::after {
            content: '';
            position: absolute;
            inset: auto -20px -30px auto;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(248, 184, 3, 0.22);
        }

        .summary-hero-label {
            position: relative;
            z-index: 1;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 0.45rem;
        }

        .summary-hero-text {
            position: relative;
            z-index: 1;
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 700;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0.9rem;
        }

        .summary-panel {
            display: grid;
            gap: 0.9rem;
        }

        .summary-card {
            border-radius: 18px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: rgba(255, 255, 255, 0.88);
            padding: 1rem;
            backdrop-filter: blur(10px);
        }

        .summary-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #6B7280;
            margin-bottom: 0.7rem;
        }

        .summary-points {
            display: grid;
            gap: 0.72rem;
        }

        .summary-point {
            display: grid;
            grid-template-columns: 38px 1fr;
            gap: 0.7rem;
            align-items: center;
            padding: 0.72rem 0.78rem;
            border-radius: 16px;
            background: #FFFFFF;
            border: 1px solid rgba(17, 24, 39, 0.06);
        }

        .summary-point-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #F8B803 0%, #F59E0B 100%);
            color: #111827;
            font-weight: 800;
            font-size: 0.95rem;
        }

        .summary-point-text,
        .summary-tip-text,
        .summary-example-text {
            color: #111827;
            line-height: 1.6;
            font-weight: 600;
        }

        .keyword-wrap {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .keyword-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #FEF3C7 0%, #FFFFFF 100%);
            color: #8A6500;
            border: 1px solid rgba(248, 184, 3, 0.3);
            font-size: 0.82rem;
            font-weight: 800;
        }

        .summary-tip-card {
            background: linear-gradient(135deg, #DBEAFE 0%, #FFFFFF 100%);
        }

        .summary-example-badge {
            margin-top: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.78rem;
            border-radius: 999px;
            background: rgba(17, 24, 39, 0.06);
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
        }

        @media (max-width: 1080px) {.chapter-library { grid-template-columns: 1fr; max-width: none; margin-inline: 0; } .chapter-side { position: static; } .summary-poster-img { max-width: 100%; }}

        @media (max-width: 860px) {.summary-grid { grid-template-columns: 1fr; } .chapter-row-head { grid-template-columns: 1fr; } .chapter-actions { justify-content: flex-start; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Detail Mata Pelajaran</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1rem;">
                    <a href="{{ route('materi.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Daftar Mata Pelajaran
                    </a>
                </div>

                <div class="detail-container">
                    @if(session('success'))
                        <div class="flash-alert success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="flash-alert error">{{ session('error') }}</div>
                    @endif

                    <div class="chapter-section">
                        <div class="chapter-library">
                            <aside class="chapter-side">
                                <div class="chapter-cover">
                                    @if($materi->cover_url)
                                        <img src="{{ $materi->cover_url }}" alt="Cover {{ $materi->judul }}">
                                    @else
                                        <div class="chapter-cover-fallback">
                                            <strong>{{ \Illuminate\Support\Str::limit($materi->judul, 42) }}</strong>
                                            <span>Struktur mata pelajaran per materi</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="chapter-side-title">{{ $materi->judul }}</div>
                                <div class="chapter-side-copy">
                                    {{ \Illuminate\Support\Str::limit($materi->deskripsi ?: 'Kelola materi agar mata pelajaran utama tetap rapi dan kuis per bagian tetap mudah diatur.', 420) }}
                                </div>
                                <div class="chapter-side-stats">
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Total Materi</div>
                                        <div class="chapter-stat-value">{{ $materi->bab->count() }}</div>
                                    </div>
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Status</div>
                                        <div class="chapter-stat-value">{{ $materi->status_aktif ? 'Aktif' : 'Nonaktif' }}</div>
                                    </div>
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Level</div>
                                        <div class="chapter-stat-value">{{ $materi->level->nama ?? '-' }}</div>
                                    </div>
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Semester</div>
                                        <div class="chapter-stat-value">{{ $materi->semester ? ucfirst($materi->semester) : '-' }}</div>
                                    </div>
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Dibuat Oleh</div>
                                        <div class="chapter-stat-value">{{ $materi->pengguna->nama ?? '-' }}</div>
                                    </div>
                                    <div class="chapter-stat">
                                        <div class="chapter-stat-label">Terakhir Update</div>
                                        <div class="chapter-stat-value">{{ $materi->updated_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                                <div class="chapter-side-actions">
                                    <a href="{{ route('materi.bab.create', $materi->id) }}" class="btn btn-primary">
                                        <i data-lucide="plus-circle"></i>
                                        Tambah Materi
                                    </a>
                                    <a href="{{ route('materi.edit', $materi->id) }}" class="btn btn-secondary">
                                        <i data-lucide="edit-3"></i>
                                        Edit Mata Pelajaran
                                    </a>
                                </div>
                            </aside>

                            <div class="chapter-main">
                                <div class="chapter-main-top">
                                    <div>
                                        <div class="chapter-main-title">Daftar Materi</div>
                                        <div class="chapter-main-subtitle">Kelola rincian materi langsung dari sini agar setiap topik tetap runtut dan mudah dihubungkan ke kuis.</div>
                                    </div>
                                    <a href="{{ route('materi.bab.create', $materi->id) }}" class="btn btn-primary">
                                        <i data-lucide="plus-circle"></i>
                                        Tambah Materi
                                    </a>
                                </div>

                                @if($materi->bab->count() > 0)
                                    <div class="chapter-list">
                                        @foreach($materi->bab as $bab)
                                            <div class="chapter-row">
                                                <div class="chapter-row-head">
                                                    <div class="chapter-handle">
                                                        <i data-lucide="grip"></i>
                                                    </div>
                                                    <div class="chapter-copy">
                                                        <div class="chapter-title">Materi {{ $bab->urutan }}. {{ $bab->judul_bab }}</div>
                                                        <div class="chapter-meta">
                                                            <span class="chapter-badge">{{ ucfirst($bab->tipe_konten) }}</span>
                                                            <span class="chapter-badge">{{ $bab->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                                            <span class="chapter-badge">{{ $bab->kuis_count }} kuis</span>
                                                            <span>{{ $bab->updated_at->format('d M Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="chapter-actions">
                                                        <form method="POST" action="{{ route('materi.bab.generate-summary', [$materi->id, $bab->id]) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-secondary" style="background:#FFF7D6; color:#8A6500;">
                                                                <i data-lucide="sparkles"></i>
                                                                {{ $bab->summary_generated_at ? 'Perbarui Rangkuman' : 'Generate Rangkuman' }}
                                                            </button>
                                                        </form>
                                                        @if($bab->kuis->isNotEmpty())
                                                            <a href="{{ route('kuis.edit', $bab->kuis->first()->id) }}" class="btn btn-primary">
                                                                <i data-lucide="clipboard-check"></i>
                                                                Kelola Kuis
                                                            </a>
                                                        @else
                                                            <a href="{{ route('kuis.create', ['materi_id' => $materi->id, 'materi_bab_id' => $bab->id]) }}" class="btn btn-primary">
                                                                <i data-lucide="clipboard-plus"></i>
                                                                Buat Kuis
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('materi.bab.edit', [$materi->id, $bab->id]) }}" class="btn btn-secondary">
                                                            <i data-lucide="edit-3"></i>
                                                            Edit
                                                        </a>
                                                        <form method="POST" action="{{ route('materi.bab.destroy', [$materi->id, $bab->id]) }}" onsubmit="return confirm('Hapus materi ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-secondary" style="background:#FEE2E2; color:#991B1B;">
                                                                <i data-lucide="trash-2"></i>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                @if($bab->tipe_konten === 'teks' && $bab->konten_teks)
                                                    <div class="chapter-outline">
                                                        {{ \Illuminate\Support\Str::limit($bab->konten_teks, 240) }}
                                                    </div>
                                                @elseif($bab->file_path)
                                                    <div class="chapter-outline" style="color:var(--color-muted);">
                                                        File materi tersimpan: {{ basename($bab->file_path) }}
                                                    </div>
                                                @endif

                                                <div class="chapter-note">
                                                    Letakkan kuis dan rangkuman di akhir materi agar alur belajar tetap runtut dan setiap topik punya penutup yang jelas.
                                                </div>

                                                @if($bab->summary_visual_url || $bab->summary_short || ($bab->summary_key_points ?? []))
                                                    <div class="summary-shell">
                                                        <div class="summary-head">
                                                            <div>
                                                                <div class="summary-kicker">
                                                                    <i data-lucide="image"></i>
                                                                    Poster rangkuman
                                                                </div>
                                                                <div class="summary-title">{{ $bab->summary_title ?: 'Rangkuman Materi' }}</div>
                                                                <div class="summary-generated">
                                                                    Rangkuman akhir materi dalam bentuk gambar
                                                                    @if($bab->summary_generated_at)
                                                                        | diperbarui {{ $bab->summary_generated_at->format('d M Y H:i') }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($bab->summary_visual_url)
                                                            <div class="summary-poster-frame">
                                                            <img
                                                                class="summary-poster-img"
                                                                src="{{ $bab->summary_visual_url }}"
                                                                alt="Poster rangkuman {{ $bab->judul_bab }}"
                                                            >
                                                            </div>
                                                        @else
                                                            <div class="chapter-note" style="margin-top:0;">
                                                                Poster gambar untuk rangkuman ini belum tersedia. Klik "Perbarui Rangkuman" untuk membuat poster rangkuman terbaru.
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="padding:1.2rem 1.25rem; color:var(--color-muted);">
                                        Mata pelajaran ini belum punya materi. Tambahkan materi pertama agar struktur isi mulai terbentuk dengan rapi.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('materi.edit', $materi->id) }}" class="btn btn-primary">
                            <i data-lucide="edit-3"></i>
                            Edit Mata Pelajaran
                        </a>
                        <a href="{{ route('materi.index') }}" class="btn btn-secondary">
                            <i data-lucide="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
