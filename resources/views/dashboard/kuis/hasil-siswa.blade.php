<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $pengguna->nama }} - Ruma</title>
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
        body { font-family: 'Inter', sans-serif; background: var(--color-gray-light); color: var(--color-text); }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); background: linear-gradient(180deg, #1F2937 0%, #111827 100%); position: fixed; height: 100vh; left: 0; top: 0; z-index: 1000; display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15); }
        .sidebar-header { padding: 2rem 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .logo-container { display: flex; align-items: center; gap: 1rem; }
        .logo-circle { width: 50px; height: 50px; background: var(--color-white); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); color: var(--color-accent); font-weight: 800; }
        .logo-text { font-size: 1.5rem; font-weight: 800; color: var(--color-white); letter-spacing: 1px; }
        .sidebar-nav { flex: 1; padding: 1.5rem 0; overflow-y: auto; }
        .nav-item { margin: 0.5rem 1rem; border-radius: 12px; transition: all 0.3s ease; }
        .nav-item a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; color: var(--color-white); text-decoration: none; font-weight: 500; border-radius: 12px; transition: all 0.3s ease; }
        .nav-item.active { background: rgba(255, 255, 255, 0.08); }
        .nav-item.active a { background: transparent; color: #FFFFFF; font-weight: 600; border-left: 4px solid var(--color-accent); }
        .nav-item:not(.active):hover { background: rgba(255, 255, 255, 0.15); }
        .nav-icon { width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #CBD5E1; }
        .nav-item.active .nav-icon { color: var(--color-accent); }
        .logout-btn { margin: 1rem; padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 12px; color: var(--color-white); font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-align: center; display: flex; align-items: center; gap: 0.5rem; justify-content: center; }
        .logout-btn:hover { background: rgba(255, 255, 255, 0.3); }
        .main-content { flex: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .header-bar { background: linear-gradient(135deg, #1F2937 0%, #111827 100%); padding: 1.5rem 2rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15); }
        .header-title { font-size: 1.6rem; font-weight: 700; color: #FFFFFF; }
        .header-subtitle { color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-top: 0.35rem; }
        .content-area { flex: 1; padding: 2rem; }
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
        .siswa-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-top: 0.75rem; }
        .summary-item { background: #F9FAFB; border: 1px solid var(--color-gray); border-radius: 12px; padding: 0.75rem; }
        .summary-label { font-size: 0.8rem; color: var(--color-text-light); }
        .summary-value { font-weight: 700; margin-top: 0.2rem; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-circle"><img src="{{ asset('images/image.png') }}" alt="Ruma Logo"></div>
                    <div class="logo-text">Ruma</div>
                </div>
            </div>
            @include('components.sidebar')
            <div class="logout-btn" onclick="handleLogout()">
                <i data-lucide="log-out"></i>
                <span>Keluar</span>
            </div>
        </aside>

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Hasil Kuis — {{ $pengguna->nama }}</h1>
                <p class="header-subtitle">{{ $pengguna->email }}@if($pengguna->siswa?->level) · Level {{ $pengguna->siswa->level->nama }}@endif</p>
            </header>
            <div class="content-area">
                <nav class="breadcrumb" aria-label="Navigasi">
                    <a href="{{ route('kuis.hasil.index') }}">Hasil Kuis</a>
                    <span>/</span>
                    <span>{{ $pengguna->nama }}</span>
                </nav>

                <div class="card">
                    <span class="tag">Ringkasan Siswa</span>
                    <div class="siswa-summary">
                        <div class="summary-item">
                            <div class="summary-label">Total Pengerjaan</div>
                            <div class="summary-value">{{ $ringkasan['total_hasil'] }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Kuis Berbeda</div>
                            <div class="summary-value">{{ $ringkasan['kuis_unik'] }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Level</div>
                            <div class="summary-value">{{ $pengguna->siswa?->level?->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <a class="btn btn-secondary" href="{{ route('kuis.hasil.index') }}">
                            <i data-lucide="arrow-left"></i>
                            Kembali ke Daftar Siswa
                        </a>
                    </div>
                </div>

                @include('components.list-search', [
                    'action' => route('kuis.hasil.siswa', $pengguna->id),
                    'resetRoute' => route('kuis.hasil.siswa', $pengguna->id),
                    'value' => $search ?? '',
                    'placeholder' => 'Cari hasil kuis berdasarkan ID, judul kuis, mata pelajaran, atau skor...',
                    'note' => 'Gunakan kata kunci seperti ID hasil, judul kuis, judul mata pelajaran, skor, total benar, atau total pertanyaan.'
                ])

                <div class="card">
                    <div class="table-head">
                        <div class="table-title">
                            <i data-lucide="clipboard-check"></i>
                            <span>Daftar Hasil Kuis</span>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Kuis</th>
                                <th>Mata Pelajaran</th>
                                <th>Status</th>
                                <th>Skor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasil as $item)
                                @php
                                    $status = $item->has_pending ? 'pending' : 'approved';
                                @endphp
                                <tr>
                                    <td>{{ $hasil->firstItem() + $loop->index }}</td>
                                    <td>{{ $item->selesai_at ? \Carbon\Carbon::parse($item->selesai_at)->format('d M Y, H:i') : '-' }}</td>
                                    <td>{{ $item->kuis->judul ?? '-' }}</td>
                                    <td>{{ $item->kuis->materi->judul ?? '-' }}</td>
                                    <td>
                                        @if($status === 'pending')
                                            <span class="badge badge-pending">Perlu Koreksi</span>
                                        @else
                                            <span class="badge badge-approved">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->skor }}%</td>
                                    <td>
                                        <a class="btn btn-outline-green" href="{{ route('kuis.hasil.show', $item->id) }}">
                                            <i data-lucide="edit-3"></i>
                                            Koreksi
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Siswa ini belum memiliki hasil kuis.</td>
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
