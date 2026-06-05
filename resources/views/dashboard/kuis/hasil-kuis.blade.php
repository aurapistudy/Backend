<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $kuis->judul }} - Ruma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .header-subtitle { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-top: 0.35rem; }
        .card { background: var(--color-white); border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 1rem; }
        .tag { display: inline-block; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.6rem; border-radius: 999px; background: rgba(248, 184, 3, 0.15); color: #B35E00; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid var(--color-gray); font-size: 0.95rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.1rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); gap: 0.35rem; font-size: 0.9rem; padding: 0.55rem 0.9rem; }
        .btn-outline-green { background: #E9F9EF; color: #166534; border: 2px solid #22C55E; padding: 0.4rem 0.7rem; border-radius: 8px; gap: 0.35rem; font-size: 0.85rem; }
        .btn-outline-green:hover { background: #DFF5E7; }
        .badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-approved { background: #DCFCE7; color: #166534; }
        .table-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem; flex-wrap: wrap; gap: 0.75rem; }
        .table-title { display:flex; align-items:center; gap:0.5rem; font-weight:700; color: var(--color-text); }
        .breadcrumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--color-text-light); flex-wrap: wrap; }
        .breadcrumb a { color: #166534; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .kuis-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-top: 0.75rem; }
        .summary-item { background: #F9FAFB; border: 1px solid var(--color-gray); border-radius: 12px; padding: 0.75rem; }
        .summary-label { font-size: 0.8rem; color: var(--color-text-light); }
        .summary-value { font-weight: 700; margin-top: 0.2rem; }
        .filter-bar { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .filter-bar label { font-size: 0.85rem; color: var(--color-text-light); font-weight: 600; }
        .filter-bar select { padding: 0.45rem 0.75rem; border-radius: 8px; border: 1px solid var(--color-gray); font: inherit; }
        .user-cell { display: flex; align-items: center; gap: 0.75rem; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: #E5E7EB; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #374151; flex-shrink: 0; }
        .user-meta { font-size: 0.8rem; color: var(--color-text-light); }
        .skor-cell { font-weight: 700; }
        .skor-high { color: #166534; }
        .skor-mid { color: #B45309; }
        .skor-low { color: #B91C1C; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Hasil Kuis — {{ $kuis->judul }}</h1>
                <p class="header-subtitle">{{ $kuis->materi->judul ?? 'Tanpa mata pelajaran' }}</p>
            </header>
            <div class="content-area">
                <nav class="breadcrumb" aria-label="Navigasi">
                    <a href="{{ route('kuis.hasil.index') }}">Hasil Kuis</a>
                    <span>/</span>
                    <span>{{ $kuis->judul }}</span>
                </nav>

                <div class="card">
                    <span class="tag">Ringkasan Kuis</span>
                    <div class="kuis-summary">
                        <div class="summary-item">
                            <div class="summary-label">Siswa Mengerjakan</div>
                            <div class="summary-value">{{ $ringkasan['siswa_unik'] }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Total Pengerjaan</div>
                            <div class="summary-value">{{ $ringkasan['total_pengerjaan'] }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Rata-rata Skor</div>
                            <div class="summary-value">{{ $ringkasan['rata_skor'] !== null ? round($ringkasan['rata_skor']) . '%' : '-' }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Perlu Koreksi</div>
                            <div class="summary-value">{{ $ringkasan['perlu_koreksi'] }}</div>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <a class="btn btn-secondary" href="{{ route('kuis.hasil.index') }}">
                            <i data-lucide="arrow-left"></i>
                            Kembali ke Daftar Kuis
                        </a>
                    </div>
                </div>

                @include('components.list-search', [
                    'action' => route('kuis.hasil.kuis', $kuis->id),
                    'resetRoute' => route('kuis.hasil.kuis', $kuis->id),
                    'value' => $search ?? '',
                    'placeholder' => 'Cari siswa berdasarkan nama, email, level, atau skor...',
                    'note' => 'Gunakan kata kunci seperti nama siswa, email, level, skor, atau ID hasil.'
                ])

                <div class="card">
                    <div class="table-head">
                        <div class="table-title">
                            <i data-lucide="users"></i>
                            <span>Siswa yang Mengerjakan</span>
                        </div>
                        <form method="GET" action="{{ route('kuis.hasil.kuis', $kuis->id) }}" class="filter-bar">
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            <label for="sort">Urutkan:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="skor_desc" @selected(($sort ?? 'skor_desc') === 'skor_desc')>Nilai tertinggi</option>
                                <option value="skor_asc" @selected(($sort ?? '') === 'skor_asc')>Nilai terendah</option>
                                <option value="terbaru" @selected(($sort ?? '') === 'terbaru')>Terbaru dikerjakan</option>
                                <option value="terlama" @selected(($sort ?? '') === 'terlama')>Terlama dikerjakan</option>
                                <option value="nama" @selected(($sort ?? '') === 'nama')>Nama A–Z</option>
                            </select>
                        </form>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Level</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Skor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasil as $item)
                                @php
                                    $siswa = $item->pengguna;
                                    $initials = strtoupper(substr($siswa?->nama ?? '?', 0, 2));
                                    $status = $item->has_pending ? 'pending' : 'approved';
                                    $skorClass = $item->skor >= 70 ? 'skor-high' : ($item->skor >= 50 ? 'skor-mid' : 'skor-low');
                                @endphp
                                <tr>
                                    <td>{{ $hasil->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">{{ $initials }}</div>
                                            <div>
                                                <div>{{ $siswa?->nama ?? '-' }}</div>
                                                <div class="user-meta">{{ $siswa?->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $siswa?->siswa?->level?->nama ?? '-' }}</td>
                                    <td>{{ $item->selesai_at ? \Carbon\Carbon::parse($item->selesai_at)->format('d M Y, H:i') : '-' }}</td>
                                    <td>
                                        @if($status === 'pending')
                                            <span class="badge badge-pending">Perlu Koreksi</span>
                                        @else
                                            <span class="badge badge-approved">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="skor-cell {{ $skorClass }}">{{ $item->skor }}%</td>
                                    <td>
                                        <a class="btn btn-outline-green" href="{{ route('kuis.hasil.show', $item->id) }}">
                                            <i data-lucide="edit-3"></i>
                                            Koreksi
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Belum ada siswa yang mengerjakan kuis ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:1rem;">{{ $hasil->links() }}</div>
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
