<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengguna - Ruma Dashboard</title>
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-gray-light);
            color: var(--color-text);
            overflow-x: hidden;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
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
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
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
        
        .logo-icon {
            width: 26px;
            height: 26px;
            color: var(--color-accent);
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--color-white);
            letter-spacing: 1px;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
            overflow-y: auto;
        }
        
        .nav-item {
            margin: 0.5rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            color: var(--color-white);
            text-decoration: none;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }

        .nav-item.active a {
            background: transparent;
            color: #FFFFFF;
            font-weight: 600;
            border-left: 4px solid var(--color-accent);
        }
        
        .nav-item:not(.active):hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #CBD5E1;
        }
        
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
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .top-header-strip {
            background: var(--color-primary);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .user-info-top {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-brown-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-icon-small {
            width: 24px;
            height: 24px;
            background: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .header-bar {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #FFFFFF;
        }
        
        .content-area {
            flex: 1;
            padding: 2rem;
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
        
        .detail-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            color: var(--color-text-light);
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
            background: var(--color-primary-light);
            color: var(--color-primary-dark);
        }

        .badge-info {
            background: #E3F2FD;
            color: #1976D2;
        }
        
        .badge-success {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        .badge-danger {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .detail-content {
            margin-top: 2rem;
        }
        
        .content-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-brown);
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
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--color-gray);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: #ffffff;;
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: #FFF9E6;
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
            color: var(--color-text-light);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-text);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-circle"><img src="{{ asset('images/image.png') }}" alt="Ruma Logo"></div>
                    <div class="logo-text">Ruma</div>
                </div>
            </div>
            
            @include('components.sidebar')
            
            <div class="logout-btn" onclick="handleLogout()">
                🚪 Keluar
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Detail Pengguna</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('pengguna.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Daftar Pengguna
                    </a>
                </div>

                <div class="detail-container">
                    <!-- Header -->
                    <div class="detail-header">
                        <div style="flex: 1;">
                            @php
                                $initials = strtoupper(substr($pengguna->nama, 0, 2));
                            @endphp
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem;">{{ $initials }}</div>
                                <div>
                                    <h2 class="detail-title">{{ $pengguna->nama }}</h2>
                                    <div class="detail-meta">
                                        <span class="meta-badge {{ $pengguna->status_aktif ? 'badge-success' : 'badge-danger' }}">
                                            {{ $pengguna->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <span class="meta-badge {{ in_array($pengguna->peran, ['guru', 'admin'], true) ? 'badge-primary' : 'badge-info' }}">
                                            @if($pengguna->peran === 'admin')
                                                Administrator
                                            @elseif($pengguna->peran === 'guru')
                                                Guru Mapel
                                            @else
                                                Siswa
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="info-grid">
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="mail"></i></div>
                            <div>
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $pengguna->email }}</div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="badge-check"></i></div>
                            <div>
                                <div class="info-label">Peran</div>
                                <div class="info-value">
                                    @if($pengguna->peran === 'admin')
                                        Administrator
                                    @elseif($pengguna->peran === 'guru')
                                        Guru Mata Pelajaran
                                    @else
                                        Siswa
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="activity"></i></div>
                            <div>
                                <div class="info-label">Status</div>
                                <div class="info-value">{{ $pengguna->status_aktif ? 'Aktif' : 'Nonaktif' }}</div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="calendar-plus"></i></div>
                            <div>
                                <div class="info-label">Tanggal Daftar</div>
                                <div class="info-value">{{ $pengguna->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="refresh-cw"></i></div>
                            <div>
                                <div class="info-label">Terakhir Diupdate</div>
                                <div class="info-value">{{ $pengguna->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Tambahan berdasarkan Peran -->
                    @if($pengguna->peran === 'siswa' && $pengguna->siswa)
                        <div class="content-section">
                            <h3 class="section-title">Informasi Siswa</h3>
                            <div class="info-grid">
                                <div class="info-card">
                                    <div class="info-label">Nama Sekolah</div>
                                    <div class="info-value">{{ $pengguna->siswa->nama_sekolah ?? '-' }}</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-label">Jenjang</div>
                                    <div class="info-value">{{ $pengguna->siswa->jenjang ?? '-' }}</div>
                                </div>
                                @if($pengguna->siswa->catatan)
                                    <div class="info-card" style="grid-column: 1 / -1;">
                                        <div class="info-label">Catatan</div>
                                        <div class="info-value">{{ $pengguna->siswa->catatan }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($pengguna->peran === 'guru')
                        <div class="content-section">
                            <h3 class="section-title">Informasi Guru Mapel</h3>
                            <div class="info-grid">
                                <div class="info-card">
                                    <div class="info-label">Nama Sekolah</div>
                                    <div class="info-value">{{ $pengguna->guru->nama_sekolah ?? '-' }}</div>
                                </div>
                                <div class="info-card" style="grid-column: 1 / -1;">
                                    <div class="info-label">
                                        Mata Pelajaran Tahun {{ $tahunAkademikAktif?->nama ?? 'Aktif' }}
                                    </div>
                                    <div class="info-value">
                                        @if($materiTahunAktif->isNotEmpty())
                                            {{ $materiTahunAktif->pluck('judul')->join(', ') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($riwayatPenugasan->isNotEmpty())
                            <div class="content-section">
                                <h3 class="section-title">Riwayat Penugasan</h3>
                                <div class="info-grid">
                                    @foreach($riwayatPenugasan as $tahunNama => $penugasan)
                                        <div class="info-card" style="grid-column: 1 / -1;">
                                            <div class="info-label">{{ $tahunNama }}</div>
                                            <div class="info-value">
                                                {{ $penugasan->pluck('materi.judul')->filter()->join(', ') ?: '-' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('pengguna.edit', $pengguna->id) }}" class="btn btn-primary">
                            📝 Edit Pengguna
                        </a>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">
                            ← Kembali
                        </a>
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
    </script>
    <script>
    lucide.createIcons();
</script>
</body>
</html>



