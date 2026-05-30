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
    @include('components.dashboard-shell-styles')
    <style>
        .page-subtitle {
            color: var(--color-text-light);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .page-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .summary-card {
            background: #FFF9E6;
            border: 1px solid rgba(248, 184, 3, 0.35);
            border-radius: 14px;
            padding: 0.75rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: #7A4A00;
            font-weight: 600;
        }

        .summary-card i { width: 18px; height: 18px; }

        .info-card-aktif {
            background: #E8F5E9;
            border: 1px solid rgba(56, 142, 60, 0.35);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #1B5E20;
            margin-bottom: 1.25rem;
        }

        .info-card-aktif i { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }

        .info-card-aktif strong { display: block; margin-bottom: 0.25rem; }

        .info-card-aktif span { font-size: 0.9rem; opacity: 0.9; }

        .add-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--color-accent);
            color: #1F2937;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.3);
            text-decoration: none;
        }

        .add-button:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }

        .table-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .table-title { font-weight: 700; font-size: 1.1rem; }

        .pengguna-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .pengguna-table thead {
            background: var(--color-primary-light);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .pengguna-table th {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text);
            white-space: nowrap;
            text-align: left;
            border-bottom: 2px solid var(--color-gray);
        }

        .pengguna-table th:nth-child(1),
        .pengguna-table td:nth-child(1) {
            width: 50px;
            text-align: center;
        }

        .pengguna-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid var(--color-gray);
            color: var(--color-text);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .pengguna-table td:nth-child(1) {
            text-align: center;
            color: var(--color-text-light);
            font-weight: 500;
        }

        .pengguna-table tbody tr:hover { background: #F9FAFB; }
        .pengguna-table tbody tr:last-child td { border-bottom: none; }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge.aktif { background: #E8F5E9; color: #388E3C; }
        .status-badge.nonaktif { background: #FFEBEE; color: #D32F2F; }

        .action-buttons { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

        .action-btn {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background: #F9FAFB;
            text-decoration: none;
        }

        .action-btn.view { background: #E3F2FD; color: #1976D2; }
        .action-btn.edit { background: #FFF9E6; color: var(--color-accent); }
        .action-btn.delete { background: #FFEBEE; color: #D32F2F; }
        .action-btn.activate { background: #E8F5E9; color: #388E3C; }
        .action-btn.penugasan { background: #EFF6FF; color: #1D4ED8; }
        .action-btn:hover { transform: scale(1.1); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); }

        .activate-form { display: inline; margin: 0; padding: 0; }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--color-text-light);
        }

        .empty-state-icon { margin-bottom: 1rem; opacity: 0.5; }
        .empty-state-icon i { width: 48px; height: 48px; }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 8px;
            background: var(--color-white);
            color: var(--color-text);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: 1px solid var(--color-gray);
            text-decoration: none;
        }

        .pagination-btn:hover:not(.active):not(:disabled) {
            background: #F9FAFB;
            border-color: #1F2937;
        }

        .pagination-btn.active {
            background: var(--color-accent);
            color: #1F2937;
            border-color: var(--color-accent);
        }

        .pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        @media (max-width: 768px) {.pengguna-table { font-size: 0.85rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Manajemen Tahun Akademik</h1>
            </header>

            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('error') }}
                    </div>
                @endif

                @if($tahunAktif)
                    <div class="info-card-aktif">
                        <i data-lucide="calendar-check"></i>
                        <div>
                            <strong>Tahun akademik aktif: {{ $tahunAktif->periodeLabel() }}</strong>
                            <span>
                                Periode {{ $tahunAktif->tanggal_mulai->format('d M Y') }} – {{ $tahunAktif->tanggal_selesai->format('d M Y') }}. Materi baru otomatis masuk ke semester ini.
                            </span>
                        </div>
                    </div>
                @endif

                <div class="page-subtitle">Kelola tahun akademik dan periode penugasan guru.</div>

                <div class="list-search-panel">
                    <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="calendar-range"></i>
                            <span>{{ ($search ?? '') !== '' ? 'Hasil pencarian' : 'Total tahun akademik' }}: {{ $tahunAkademik->total() }} item</span>
                        </div>
                        <a href="{{ route('tahun-akademik.create') }}" class="add-button">
                            <i data-lucide="plus"></i>
                            <span>Tambah Tahun Akademik</span>
                        </a>
                        @if($tahunAktif)
                            <a href="{{ route('tahun-akademik.penugasan', $tahunAktif->id) }}" class="add-button" style="background:#EFF6FF;color:#1D4ED8;box-shadow:0 4px 12px rgba(59,130,246,0.15);">
                                <i data-lucide="users"></i>
                                <span>Penugasan Guru (Aktif)</span>
                            </a>
                        @endif
                    </div>

                    @include('components.list-search', [
                        'action' => route('tahun-akademik.index'),
                        'resetRoute' => route('tahun-akademik.index'),
                        'value' => $search ?? '',
                        'placeholder' => 'Cari tahun akademik berdasarkan nama...',
                        'note' => 'Gunakan format nama seperti 2025/2026.',
                        'panel' => false
                    ])
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Daftar Tahun Akademik</div>
                    </div>

                    @if($tahunAkademik->count() > 0)
                        <table class="pengguna-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Semester</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Jumlah Penugasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tahunAkademik as $index => $item)
                                    <tr>
                                        <td>{{ $tahunAkademik->firstItem() + $index }}</td>
                                        <td><strong>{{ $item->nama }}</strong></td>
                                        <td>{{ $item->semesterLabel() }}</td>
                                        <td>
                                            {{ $item->tanggal_mulai->format('d M Y') }} – {{ $item->tanggal_selesai->format('d M Y') }}
                                        </td>
                                        <td>
                                            @if($item->status_aktif)
                                                <span class="status-badge aktif">Aktif</span>
                                            @else
                                                <span class="status-badge nonaktif">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->penugasan_guru_count }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('tahun-akademik.penugasan', $item->id) }}" class="action-btn penugasan" title="Penugasan Guru">
                                                    <i data-lucide="users"></i>
                                                </a>
                                                <a href="{{ route('tahun-akademik.show', $item->id) }}" class="action-btn view" title="Lihat">
                                                    <i data-lucide="eye"></i>
                                                </a>
                                                <a href="{{ route('tahun-akademik.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                                    <i data-lucide="edit-3"></i>
                                                </a>
                                                <button type="button" class="action-btn delete" title="Hapus"
                                                    onclick="handleDelete({{ $item->id }}, '{{ addslashes($item->nama) }}')">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                                @if(!$item->status_aktif)
                                                    <form action="{{ route('tahun-akademik.activate', $item->id) }}" method="POST" class="activate-form">
                                                        @csrf
                                                        <button type="submit" class="action-btn activate" title="Aktifkan">
                                                            <i data-lucide="check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i data-lucide="calendar-off"></i></div>
                            <h3 style="margin-bottom: 0.5rem;">Belum ada tahun akademik</h3>
                            <p>Mulai dengan menambahkan tahun akademik baru.</p>
                        </div>
                    @endif
                </div>

                @if($tahunAkademik->hasPages())
                    <div class="pagination">
                        @if($tahunAkademik->onFirstPage())
                            <button class="pagination-btn" disabled>‹</button>
                        @else
                            <a href="{{ $tahunAkademik->previousPageUrl() }}" class="pagination-btn">‹</a>
                        @endif

                        @foreach($tahunAkademik->getUrlRange(1, $tahunAkademik->lastPage()) as $page => $url)
                            @if($page == $tahunAkademik->currentPage())
                                <button class="pagination-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($tahunAkademik->hasMorePages())
                            <a href="{{ $tahunAkademik->nextPageUrl() }}" class="pagination-btn">›</a>
                        @else
                            <button class="pagination-btn" disabled>›</button>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>

    @include('components.modal')

    <script>
        function handleDelete(id, nama) {
            showModal({
                type: 'delete',
                title: 'Hapus Tahun Akademik',
                message: `Apakah Anda yakin ingin menghapus tahun akademik "${nama}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/tahun-akademik/${id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);

                    if (typeof showInfoToast !== 'undefined') {
                        showInfoToast('Menghapus...', 'Sedang menghapus tahun akademik...');
                    }

                    form.submit();
                }
            });
        }

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
