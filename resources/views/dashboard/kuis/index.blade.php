<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kuis - Ruma</title>
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

        .summary-card i {
            width: 18px;
            height: 18px;
        }

        .add-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--color-accent);
            color: #1F2937;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
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

        .table-title {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .kuis-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .kuis-table thead {
            background: var(--color-primary-light);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .kuis-table th {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text);
            white-space: nowrap;
            text-align: left;
            border-bottom: 2px solid var(--color-gray);
        }

        .kuis-table th:nth-child(1),
        .kuis-table td:nth-child(1) {
            width: 50px;
            text-align: center;
        }

        .kuis-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid var(--color-gray);
            color: var(--color-text);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .kuis-table td:nth-child(1) {
            text-align: center;
            color: var(--color-text-light);
            font-weight: 500;
        }

        .kuis-table tbody tr {
            transition: all 0.2s ease;
        }

        .kuis-table tbody tr:hover {
            background: #F9FAFB;
        }

        .kuis-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge.aktif {
            background: #E8F5E9;
            color: #388E3C;
        }

        .status-badge.nonaktif {
            background: #FFEBEE;
            color: #D32F2F;
        }

        .action-buttons {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            background: #F9FAFB;
            text-decoration: none;
        }

        .action-btn.view {
            background: #E3F2FD;
            color: #1976D2;
        }

        .action-btn.edit {
            background: #FFF9E6;
            color: var(--color-accent);
        }

        .action-btn.delete {
            background: #FFEBEE;
            color: #D32F2F;
        }

        .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--color-text-light);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .pagination-wrap {
            margin-top: 1.5rem;
        }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Manajemen Kuis</h1>
            </header>
            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="page-subtitle">Buat dan kelola kuis untuk mata pelajaran dan materi yang terhubung.</div>
                <div class="list-search-panel">
                    <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="clipboard-list"></i>
                            <span>{{ ($search ?? '') !== '' ? 'Hasil pencarian' : 'Total kuis' }}: {{ $kuis->total() }} item</span>
                        </div>
                        <a href="{{ route('kuis.create') }}" class="add-button">
                            <i data-lucide="plus"></i>
                            <span>Tambah Kuis</span>
                        </a>
                    </div>

                    @include('components.list-search', [
                        'action' => route('kuis.index'),
                        'resetRoute' => route('kuis.index'),
                        'value' => $search ?? '',
                        'placeholder' => 'Cari kuis berdasarkan ID, judul, deskripsi, atau mata pelajaran...',
                        'note' => 'Gunakan kata kunci seperti ID kuis, judul kuis, deskripsi, atau judul mata pelajaran yang terhubung.',
                        'panel' => false
                    ])
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Daftar Kuis</div>
                    </div>
                    @if($kuis->count() > 0)
                        <table class="kuis-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Judul</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Pertanyaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kuis as $index => $item)
                                    <tr>
                                        <td>{{ $kuis->firstItem() + $index }}</td>
                                        <td><strong>{{ $item->judul }}</strong></td>
                                        <td>{{ $item->materi->judul ?? '-' }}</td>
                                        <td>{{ $item->pertanyaan_count }}</td>
                                        <td>
                                            <span class="status-badge {{ $item->status_aktif ? 'aktif' : 'nonaktif' }}">
                                                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('kuis.show', $item->id) }}" class="action-btn view" title="Lihat">
                                                <i data-lucide="eye"></i>
                                                </a>
                                                <a href="{{ route('kuis.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                                    <i data-lucide="edit-3"></i>
                                                </a>
                                                <button type="button" class="action-btn delete" title="Hapus"
                                                    onclick="handleDeleteKuis({{ $item->id }}, '{{ addslashes($item->judul) }}')">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i data-lucide="clipboard-list"></i></div>
                            <h3 style="margin-bottom: 0.5rem;">Belum ada kuis</h3>
                            <p>Mulai dengan menambahkan kuis baru.</p>
                        </div>
                    @endif
                    <div class="pagination-wrap">
                        {{ $kuis->links() }}
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

        function handleDeleteKuis(id, judul) {
            showModal({
                type: 'delete',
                title: 'Hapus Kuis',
                message: `Apakah Anda yakin ingin menghapus kuis "${judul}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('dashboard/kuis') }}/${id}`;

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
                        showInfoToast('Menghapus...', 'Sedang menghapus kuis...');
                    }

                    form.submit();
                }
            });
        }

        lucide.createIcons();
    </script>
</body>
</html>

