<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiksi - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .fiksi-table thead {
            background: var(--color-primary-light);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .fiksi-table th {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            white-space: nowrap;
            border-bottom: 2px solid var(--color-gray);
        }

        .fiksi-table td {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--color-gray);
            vertical-align: middle;
        }

        /* Lebar kolom penting */
        /* No */
.fiksi-table th:nth-child(1),
.fiksi-table td:nth-child(1) {
    width: 50px;
    text-align: center;
}

/* Tanggal */
.fiksi-table th:nth-child(2),
.fiksi-table td:nth-child(2) {
    width: 110px;
}

/* Tahun Terbit */
.fiksi-table th:nth-child(6),
.fiksi-table td:nth-child(6) {
    width: 110px;
}

/* Status */
.fiksi-table th:nth-child(8),
.fiksi-table td:nth-child(8) {
    width: 90px;
    text-align: center;
}

/* Aksi */
.fiksi-table th:nth-child(9),
.fiksi-table td:nth-child(9) {
    width: 130px;
}


        /* Text panjang dipotong */
        .fiksi-table td:nth-child(3),
        .fiksi-table td:nth-child(4) {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* File name dipotong */
        .fiksi-table td:nth-child(7) a {
            display: inline-block;
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fiksi-table th:last-child,
        .fiksi-table td:last-child {
            position: sticky;
            right: 0;
            background: var(--color-white);
            z-index: 2;
        }

        .fiksi-table thead th:last-child {
            background: var(--color-primary-light);
        }
.page-container {
            width: 100%;
            max-width: none;
            margin: 0 auto;
            min-width: 0;
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
            margin-bottom: 0.85rem;
        }

        .page-top {
            padding: 0;
        }

        .fiksi-controls {
            background: var(--color-white);
            border: 1px solid rgba(17, 24, 39, 0.06);
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
            padding: 0.75rem 1.5rem;
            background: var(--color-accent);
            color: #1F2937;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.3);
        }
        
        .add-button:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }
        
        .add-button:active {
            transform: translateY(0);
        }

        .search-form {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) auto auto;
            align-items: center;
            gap: 0.65rem;
            width: 100%;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            padding: 0.75rem;
        }

        .search-input-wrap {
            position: relative;
            min-width: 0;
        }

        .search-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--color-text-light);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 46px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            padding: 0 0.9rem 0 2.65rem;
            font: inherit;
            color: var(--color-text);
            background: var(--color-white);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .search-input:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(248, 184, 3, 0.18);
        }

        .search-button,
        .reset-search {
            height: 46px;
            border-radius: 10px;
            padding: 0 1.15rem;
            border: 1px solid transparent;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .search-button {
            background: var(--color-primary);
            color: var(--color-white);
            box-shadow: 0 4px 10px rgba(17, 24, 39, 0.12);
        }

        .search-button:hover {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        .reset-search {
            background: var(--color-white);
            border-color: var(--color-gray);
            color: var(--color-text);
        }

        .reset-search:hover {
            background: var(--color-primary-light);
        }

        .search-note {
            grid-column: 1 / -1;
            color: var(--color-text-light);
            font-size: 0.82rem;
            line-height: 1.45;
            padding-top: 0.2rem;
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
        
        .fiksi-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .fiksi-table thead {
            background: var(--color-primary-light);
        }
        
        .fiksi-table th {
            padding: 0.9rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-text);
            font-size: 0.9rem;
            border-bottom: 2px solid var(--color-gray);
        }
        
        .fiksi-table td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid var(--color-gray);
            color: var(--color-text);
            font-size: 0.9rem;
        }

        .fiksi-table th:nth-child(1),
        .fiksi-table td:nth-child(1) { width: 48px; text-align: center; }

        .fiksi-table th:nth-child(2),
        .fiksi-table td:nth-child(2) { width: 90px; }

        .fiksi-table th:nth-child(3),
        .fiksi-table td:nth-child(3) { width: 20%; }

        .fiksi-table th:nth-child(4),
        .fiksi-table td:nth-child(4) { width: 12%; }

        .fiksi-table th:nth-child(5),
        .fiksi-table td:nth-child(5) { width: 13%; }

        .fiksi-table th:nth-child(6),
        .fiksi-table td:nth-child(6) { width: 96px; }

        .fiksi-table th:nth-child(7),
        .fiksi-table td:nth-child(7) { width: 18%; }

        .fiksi-table th:nth-child(8),
        .fiksi-table td:nth-child(8) { width: 98px; text-align: center; }

        .fiksi-table th:nth-child(9),
        .fiksi-table td:nth-child(9) { width: 128px; }

        .fiksi-table td:nth-child(3),
        .fiksi-table td:nth-child(4),
        .fiksi-table td:nth-child(5),
        .fiksi-table td:nth-child(7) {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .fiksi-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .fiksi-table tbody tr:hover {
            background: #F9FAFB;
        }
        
        .fiksi-table tbody tr:last-child td {
            border-bottom: none;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem; 
        }

        
        /* File Link */
        .file-link {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .file-link:hover {
            color: var(--color-primary-dark);
            text-decoration: underline;
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
@media (max-width: 768px) {.table-container { padding: 1rem; margin-top: 0; } .search-form { grid-template-columns: 1fr; } .search-input-wrap, .search-button, .reset-search { width: 100%; } .fiksi-table { font-size: 0.85rem; } .fiksi-table th, .fiksi-table td { padding: 0.75rem 0.5rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">

            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Koleksi Fiksi</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div class="page-container">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="page-subtitle">Kelola koleksi fiksi agar rapi dan mudah ditemukan.</div>

                <div class="fiksi-controls">
                    <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="book"></i>
                            <span>{{ $search ? 'Hasil pencarian' : 'Total fiksi' }}: {{ $fiksi->total() }} item</span>
                        </div>
                        <a href="{{ route('fiksi.create') }}" class="add-button" style="text-decoration:none;">
                            <i data-lucide="plus"></i>
                            Tambah Fiksi
                        </a>
                    </div>

                    <form action="{{ route('fiksi.index') }}" method="GET" class="search-form" role="search">
                        <div class="search-input-wrap">
                            <i data-lucide="search" class="search-icon"></i>
                            <input
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                class="search-input"
                                placeholder="Cari fiksi berdasarkan ID, judul, penulis, kategori, tahun, atau pembuat..."
                                aria-label="Cari fiksi"
                            >
                        </div>
                        <button type="submit" class="search-button">
                            <i data-lucide="search"></i>
                            <span>Cari</span>
                        </button>
                        @if($search)
                            <a href="{{ route('fiksi.index') }}" class="reset-search">
                                <i data-lucide="x"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                        <div class="search-note">Gunakan kata kunci seperti ID fiksi, judul buku, penulis, kategori, tahun terbit, nama pembuat, atau email pembuat.</div>
                    </form>
                </div>

                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">
                            @if($search)
                                Hasil untuk "{{ $search }}"
                            @else
                                Daftar Fiksi
                            @endif
                        </div>
                    </div>
                    @if($fiksi->count() > 0)
                        <table class="fiksi-table materi-style">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>Tahun Terbit</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fiksi as $index => $item)
                                    <tr>
                                        <td>{{ $fiksi->firstItem() + $index }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>{{ $item->judul_buku }}</td>
                                        <td>{{ $item->penulis }}</td>
                                        <td>{{ $item->kategori ?? '-' }}</td>
                                        <td>{{ $item->tahun_terbit ?? '-' }}</td>
                                        <td>
                                            @if($item->file_path)
                                                <a href="{{ Storage::url($item->file_path) }}" class="file-link" target="_blank">
                                                    {{ basename($item->file_path) }}
                                                </a>
                                            @else
                                                <span style="color: var(--color-text-light);">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->status_aktif ? 'badge-success' : 'badge-danger' }}">
                                                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                               <a href="{{ route('fiksi.show', $item->id) }}" class="action-btn view">
                                            <i data-lucide="eye"></i>
                                                </a>

                                                <a href="{{ route('fiksi.edit', $item->id) }}" class="action-btn edit">
                                                    <i data-lucide="edit-3"></i>
                                                </a>

                                                <button class="action-btn delete" onclick="handleDeleteFiksi({{ $item->id }}, '{{ addslashes($item->judul_buku) }}')">
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
                            <div class="empty-state-icon"><i data-lucide="book-open"></i></div>
                            @if($search)
                                <h3 style="margin-bottom: 0.5rem;">Fiksi tidak ditemukan</h3>
                                <p>Tidak ada fiksi yang cocok dengan kata kunci "{{ $search }}".</p>
                            @else
                                <h3 style="margin-bottom: 0.5rem;">Belum ada fiksi</h3>
                                <p>Mulai dengan menambahkan fiksi baru.</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Pagination -->
                @if($fiksi->hasPages())
                    <div class="pagination">
                        @if($fiksi->onFirstPage())
                            <button class="pagination-btn" disabled>&lsaquo;</button>
                        @else
                            <a href="{{ $fiksi->previousPageUrl() }}" class="pagination-btn">&lsaquo;</a>
                        @endif

                        @foreach($fiksi->getUrlRange(1, $fiksi->lastPage()) as $page => $url)
                            @if($page == $fiksi->currentPage())
                                <button class="pagination-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($fiksi->hasMorePages())
                            <a href="{{ $fiksi->nextPageUrl() }}" class="pagination-btn">&rsaquo;</a>
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
        // Handle Delete Fiksi
        function handleDeleteFiksi(id, judul) {
            showModal({
                type: 'delete',
                title: 'Hapus Fiksi',
                message: `Apakah Anda yakin ingin menghapus fiksi "${judul}"? Tindakan ini tidak dapat dibatalkan dan semua data terkait akan terhapus.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    // Create form for delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/fiksi/${id}`;
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add method spoofing
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    
                    // Show loading toast
                    if (typeof showInfoToast !== 'undefined') {
                        showInfoToast('Menghapus...', 'Sedang menghapus fiksi...');
                    }
                    
                    // Submit form
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


