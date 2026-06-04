<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tahun Akademik - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .page-subtitle {
            color: var(--color-text-light);
            margin-bottom: 1.5rem;
        }

        .active-card {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border: 1px solid #BFDBFE;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .active-card-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }

        .active-card-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1E3A8A;
            margin-bottom: 0.75rem;
        }

        .materi-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .materi-tag {
            background: var(--color-white);
            border: 1px solid #93C5FD;
            color: #1D4ED8;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .history-section {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .history-item {
            border: 1px solid var(--color-gray);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .history-item:last-child { margin-bottom: 0; }

        .history-item.is-active {
            border-color: #93C5FD;
            background: #F8FAFC;
        }

        .history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .history-year {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .badge-active { background: #DCFCE7; color: #166534; }
        .badge-archive { background: #F3F4F6; color: #4B5563; }

        .history-meta {
            font-size: 0.85rem;
            color: var(--color-text-light);
            margin-bottom: 0.75rem;
        }

        .materi-list {
            list-style: none;
            padding: 0;
        }

        .materi-list li {
            padding: 0.45rem 0;
            border-bottom: 1px solid var(--color-gray-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .materi-list li:last-child { border-bottom: none; }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--color-text-light);
        }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Riwayat Tahun Akademik</h1>
            </header>

            <div class="content-area">
                <p class="page-subtitle">
                    Daftar mata pelajaran yang pernah Anda ampu per tahun akademik.
                </p>

                @if($tahunAkademikAktif)
                    <div class="active-card">
                        <div class="active-card-label">Tahun Akademik Aktif</div>
                        <div class="active-card-title">{{ $tahunAkademikAktif->nama }}</div>
                        <div class="materi-tags">
                            @forelse($materiTahunAktif as $materi)
                                <span class="materi-tag">{{ $materi->judul }}</span>
                            @empty
                                <span style="color: #1E40AF;">Belum ada mata pelajaran ditugaskan tahun ini.</span>
                            @endforelse
                        </div>
                    </div>
                @endif

                <div class="history-section">
                    <h2 class="section-title">
                        <i data-lucide="history"></i>
                        Riwayat Penugasan
                    </h2>

                    @if($riwayatPenugasan->isNotEmpty())
                        @foreach($riwayatPenugasan as $tahunNama => $penugasan)
                            @php
                                $tahunRow = $penugasan->first()?->tahunAkademik;
                                $isActive = (bool) ($tahunRow?->status_aktif);
                            @endphp
                            <div class="history-item {{ $isActive ? 'is-active' : '' }}">
                                <div class="history-header">
                                    <div class="history-year">{{ $tahunNama }}</div>
                                    <span class="badge {{ $isActive ? 'badge-active' : 'badge-archive' }}">
                                        {{ $isActive ? 'Tahun Aktif' : 'Arsip' }}
                                    </span>
                                </div>
                                @if($tahunRow && ($tahunRow->tanggal_mulai || $tahunRow->tanggal_selesai))
                                    <div class="history-meta">
                                        {{ $tahunRow->tanggal_mulai?->format('d M Y') ?? '—' }}
                                        –
                                        {{ $tahunRow->tanggal_selesai?->format('d M Y') ?? '—' }}
                                    </div>
                                @endif
                                <ul class="materi-list">
                                    @foreach($penugasan as $row)
                                        <li>
                                            <i data-lucide="book-open" style="width:16px;height:16px;color:var(--color-text-light);"></i>
                                            {{ $row->materi?->judul ?? 'Materi tidak ditemukan' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i data-lucide="inbox" style="width:40px;height:40px;margin-bottom:0.75rem;opacity:0.5;"></i>
                            <p>Belum ada riwayat penugasan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @include('components.modal')

    <script>
        lucide.createIcons();

        function handleLogout() {
            showModal({
                type: 'logout',
                title: 'Konfirmasi Logout',
                message: 'Apakah Anda yakin ingin keluar dari akun Anda?',
                confirmText: 'Ya, Keluar',
                cancelText: 'Batal',
                onConfirm: () => {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
