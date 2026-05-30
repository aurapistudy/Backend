<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AAC - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .aac-table thead {
            background: var(--color-primary-light);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .aac-table th {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            white-space: nowrap;
            border-bottom: 2px solid var(--color-gray);
        }

        .aac-table td {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--color-gray);
            vertical-align: middle;
        }

        .aac-table th:nth-child(1),
        .aac-table td:nth-child(1) {
            width: 50px;
            text-align: center;
        }

        .aac-table th:nth-child(2),
        .aac-table td:nth-child(2) {
            width: 110px;
        }

        .aac-table th:nth-child(5),
        .aac-table td:nth-child(5) {
            width: 100px;
            text-align: center;
        }

        .aac-table th:nth-child(6),
        .aac-table td:nth-child(6) {
            width: 90px;
            text-align: center;
        }

        .aac-table th:nth-child(7),
        .aac-table td:nth-child(7) {
            width: 90px;
            text-align: center;
        }

        .aac-table th:nth-child(8),
        .aac-table td:nth-child(8) {
            width: 130px;
        }

        .aac-table td:nth-child(3),
        .aac-table td:nth-child(4) {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .aac-table th:last-child,
        .aac-table td:last-child {
            position: sticky;
            right: 0;
            background: var(--color-white);
            z-index: 2;
        }

        .aac-table thead th:last-child {
            background: var(--color-primary-light);
        }
.page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

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

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .table-title {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        /* Add Button */
        .add-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            background: var(--color-accent);
            color: #1F2937;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0; 
            text-decoration: none;
        }
        
        .add-button:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }
        
        .add-button:active {
            transform: translateY(0);
        }
        
        /* Table Container */
        .table-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
            max-width: 100%;
            margin: 0;
        }
        
        .aac-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .aac-table thead {
            background: var(--color-primary-light);
        }
        
        .aac-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-text);
            font-size: 0.9rem;
            border-bottom: 2px solid var(--color-gray);
        }
        
        .aac-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-gray);
            color: var(--color-text);
            font-size: 0.9rem;
        }
        
        .aac-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .aac-table tbody tr:hover {
            background: #F9FAFB;
        }
        
        .aac-table tbody tr:last-child td {
            border-bottom: none;
        }

        .thumbnail {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--color-gray);
            background: #F9FAFB;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
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
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .badge-success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-danger {
            background: #FFEBEE;
            color: #C62828;
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

        .empty-state-icon i {
            width: 48px;
            height: 48px;
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
        
        /* Pagination */
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
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
@media (max-width: 768px) {.table-container { padding: 1rem; margin-top: 0; } .aac-table { font-size: 0.85rem; } .aac-table th, .aac-table td { padding: 0.75rem 0.5rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">AAC</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div class="page-container">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="list-search-panel">
                    <div class="page-top">
                        <div class="page-subtitle">Kelola daftar simbol dan ungkapan AAC agar mudah digunakan.</div>
                        <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="message-circle"></i>
                            <span>{{ ($search ?? '') !== '' ? 'Hasil pencarian' : 'Total AAC' }}: {{ $aac->total() }} item</span>
                        </div>
                        <a href="{{ route('aac.create') }}" class="add-button">
                            <i data-lucide="plus"></i>
                            Tambah AAC
                        </a>
                        </div>
                    </div>

                    @include('components.list-search', [
                        'action' => route('aac.index'),
                        'resetRoute' => route('aac.index'),
                        'value' => $search ?? '',
                        'placeholder' => 'Cari AAC berdasarkan ID, judul, kategori, urutan, atau pembuat...',
                        'note' => 'Gunakan kata kunci seperti ID AAC, judul/ungkapan, kategori, deskripsi, urutan, nama pembuat, atau email pembuat.',
                        'panel' => false
                    ])
                </div>
      
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Daftar AAC</div>
                    </div>
                    @if($aac->count() > 0)
                        <table class="aac-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Ungkapan</th>
                                    <th>Kategori</th>
                                    <th>Gambar</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aac as $index => $item)
                                    <tr>
                                        <td>{{ $aac->firstItem() + $index }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>{{ $item->judul }}</td>
                                        <td>{{ $item->kategori ?? '-' }}</td>
                                        <td>
                                            @if($item->gambar_path)
                                                <img src="{{ Storage::url($item->gambar_path) }}" alt="{{ $item->judul }}" class="thumbnail">
                                            @else
                                                <span style="color: var(--color-text-light);">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->urutan ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $item->status_aktif ? 'badge-success' : 'badge-danger' }}">
                                                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('aac.show', $item->id) }}" class="action-btn view">
                                                    <i data-lucide="eye"></i>
                                                </a>
                                                <a href="{{ route('aac.edit', $item->id) }}" class="action-btn edit">
                                                    <i data-lucide="edit-3"></i>
                                                </a>
                                                <button class="action-btn delete" onclick="handleDeleteAac({{ $item->id }}, '{{ addslashes($item->judul) }}')">
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
                            <div class="empty-state-icon"><i data-lucide="message-circle"></i></div>
                            <h3 style="margin-bottom: 0.5rem;">Belum ada AAC</h3>
                            <p>Mulai dengan menambahkan data AAC baru.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Pagination -->
                @if($aac->hasPages())
                    <div class="pagination">
                        @if($aac->onFirstPage())
                            <button class="pagination-btn" disabled>&lsaquo;</button>
                        @else
                            <a href="{{ $aac->previousPageUrl() }}" class="pagination-btn">&lsaquo;</a>
                        @endif

                        @foreach($aac->getUrlRange(1, $aac->lastPage()) as $page => $url)
                            @if($page == $aac->currentPage())
                                <button class="pagination-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($aac->hasMorePages())
                            <a href="{{ $aac->nextPageUrl() }}" class="pagination-btn">&rsaquo;</a>
                        @else
                            <button class="pagination-btn" disabled>&rsaquo;</button>
                        @endif
                    </div>
                @endif
                </div>
            </div>
        </main>
    </div>
    
    {{-- Include Modal Component --}}
    @include('components.modal')
    
    <script>
        // Handle Delete AAC
        function handleDeleteAac(id, judul) {
            showModal({
                type: 'delete',
                title: 'Hapus AAC',
                message: `Apakah Anda yakin ingin menghapus AAC "${judul}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/aac/${id}`;
                    
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
                    form.submit();
                }
            });
        }
        
        // Handle Logout
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
        
        // Active nav item is handled by sidebar component
    </script>
    <script>
    lucide.createIcons();
    </script>
</body>
</html>
