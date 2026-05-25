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
        .btn-outline-green { background: #E9F9EF; color: #166534; border: 2px solid #22C55E; padding: 0.4rem 0.7rem; border-radius: 8px; gap: 0.35rem; font-size: 0.85rem; }
        .btn-outline-green:hover { background: #DFF5E7; }
        .badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-muted { background: #F3F4F6; color: #6B7280; }
        .table-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem; }
        .table-title { display:flex; align-items:center; gap:0.5rem; font-weight:700; color: var(--color-text); }
        .user-cell { display: flex; align-items: center; gap: 0.75rem; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: #E5E7EB; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #374151; flex-shrink: 0; }
        .user-meta { font-size: 0.8rem; color: var(--color-text-light); }
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
                <h1 class="header-title">Hasil Kuis</h1>
                <p class="header-subtitle">Pilih siswa untuk melihat dan mengoreksi hasil kuis mereka.</p>
            </header>
            <div class="content-area">
                @include('components.list-search', [
                    'action' => route('kuis.hasil.index'),
                    'resetRoute' => route('kuis.hasil.index'),
                    'value' => $search ?? '',
                    'placeholder' => 'Cari siswa berdasarkan nama, email, atau level...',
                    'note' => 'Gunakan kata kunci seperti nama siswa, email, atau nama level.'
                ])

                <div class="card">
                    <div class="table-head">
                        <div class="table-title">
                            <i data-lucide="users"></i>
                            <span>Daftar Siswa</span>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Level</th>
                                <th>Total Pengerjaan</th>
                                <th>Kuis Unik</th>
                                <th>Perlu Koreksi</th>
                                <th>Terakhir Mengerjakan</th>
                                <th>Rata-rata Skor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $item)
                                @php
                                    $initials = strtoupper(substr($item->nama, 0, 2));
                                @endphp
                                <tr>
                                    <td>{{ $siswa->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">{{ $initials }}</div>
                                            <div>
                                                <div>{{ $item->nama }}</div>
                                                <div class="user-meta">{{ $item->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->siswa?->level?->nama ?? '-' }}</td>
                                    <td>{{ $item->total_hasil }}</td>
                                    <td>{{ $item->kuis_unik_count }}</td>
                                    <td>
                                        @if($item->perlu_koreksi_count > 0)
                                            <span class="badge badge-pending">{{ $item->perlu_koreksi_count }} hasil</span>
                                        @else
                                            <span class="badge badge-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->terakhir_selesai ? \Carbon\Carbon::parse($item->terakhir_selesai)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td>{{ $item->rata_skor !== null ? round($item->rata_skor) . '%' : '-' }}</td>
                                    <td>
                                        <a class="btn btn-outline-green" href="{{ route('kuis.hasil.siswa', $item->id) }}">
                                            <i data-lucide="list"></i>
                                            Lihat Hasil
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Belum ada siswa dengan hasil kuis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:1rem;">{{ $siswa->links() }}</div>
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
