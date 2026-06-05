<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - Ruma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .header-subtitle { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-top: 0.35rem; }
        .card { background: var(--color-white); border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid var(--color-gray); font-size: 0.95rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.1rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-outline-green { background: #E9F9EF; color: #166534; border: 2px solid #22C55E; padding: 0.4rem 0.7rem; border-radius: 8px; gap: 0.35rem; font-size: 0.85rem; }
        .btn-outline-green:hover { background: #DFF5E7; }
        .badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-muted { background: #F3F4F6; color: #6B7280; }
        .table-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem; flex-wrap: wrap; gap: 0.75rem; }
        .table-title { display:flex; align-items:center; gap:0.5rem; font-weight:700; color: var(--color-text); }
        .filter-bar { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .filter-bar label { font-size: 0.85rem; color: var(--color-text-light); font-weight: 600; }
        .filter-bar select { padding: 0.45rem 0.75rem; border-radius: 8px; border: 1px solid var(--color-gray); font: inherit; }
        .kuis-meta { font-size: 0.8rem; color: var(--color-text-light); margin-top: 0.15rem; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Hasil Kuis</h1>
                <p class="header-subtitle">Pilih kuis untuk melihat siswa yang mengerjakan dan mengoreksi jawaban.</p>
            </header>
            <div class="content-area">
                @include('components.list-search', [
                    'action' => route('kuis.hasil.index'),
                    'resetRoute' => route('kuis.hasil.index'),
                    'value' => $search ?? '',
                    'placeholder' => 'Cari kuis berdasarkan ID, judul, atau mata pelajaran...',
                    'note' => 'Gunakan kata kunci seperti ID kuis, judul kuis, atau judul mata pelajaran.'
                ])

                <div class="card">
                    <div class="table-head">
                        <div class="table-title">
                            <i data-lucide="clipboard-list"></i>
                            <span>Daftar Kuis</span>
                        </div>
                        <form method="GET" action="{{ route('kuis.hasil.index') }}" class="filter-bar">
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            <label for="sort">Urutkan:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="terakhir" @selected(($sort ?? 'terakhir') === 'terakhir')>Terakhir dikerjakan</option>
                                <option value="skor_tinggi" @selected(($sort ?? '') === 'skor_tinggi')>Rata-rata skor tertinggi</option>
                                <option value="skor_rendah" @selected(($sort ?? '') === 'skor_rendah')>Rata-rata skor terendah</option>
                                <option value="peserta" @selected(($sort ?? '') === 'peserta')>Peserta terbanyak</option>
                                <option value="judul" @selected(($sort ?? '') === 'judul')>Judul A–Z</option>
                            </select>
                        </form>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kuis</th>
                                <th>Mata Pelajaran</th>
                                <th>Siswa</th>
                                <th>Total Pengerjaan</th>
                                <th>Perlu Koreksi</th>
                                <th>Rata-rata Skor</th>
                                <th>Terakhir Dikerjakan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kuisList as $item)
                                <tr>
                                    <td>{{ $kuisList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div>{{ $item->judul }}</div>
                                        <div class="kuis-meta">ID #{{ $item->id }}</div>
                                    </td>
                                    <td>{{ $item->materi->judul ?? '-' }}</td>
                                    <td>{{ $item->siswa_unik_count }}</td>
                                    <td>{{ $item->total_pengerjaan }}</td>
                                    <td>
                                        @if($item->perlu_koreksi_count > 0)
                                            <span class="badge badge-pending">{{ $item->perlu_koreksi_count }} hasil</span>
                                        @else
                                            <span class="badge badge-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->rata_skor !== null ? round($item->rata_skor) . '%' : '-' }}</td>
                                    <td>
                                        {{ $item->terakhir_selesai ? \Carbon\Carbon::parse($item->terakhir_selesai)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-green" href="{{ route('kuis.hasil.kuis', $item->id) }}">
                                            <i data-lucide="users"></i>
                                            Lihat Siswa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Belum ada kuis yang memiliki hasil pengerjaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:1rem;">{{ $kuisList->links() }}</div>
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
