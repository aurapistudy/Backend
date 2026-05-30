<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Pelajaran - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        /* Top Header Strip */
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
            color: #FFFFFF;
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
.page-subtitle {
            color: var(--color-text-light);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
/* Add Button */
        .add-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--color-accent);
            color: #111827;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.35);
        }
        
        .add-button:hover {
            background: var(--color-primary-dark);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }
        
        .add-button:active {
            transform: translateY(0);
        }

        .materi-controls {
            background: var(--color-white);
            border: 1px solid rgba(17, 24, 39, 0.06);
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .page-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
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
        
        .materi-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .materi-table thead {
            background: var(--color-primary-light);
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .materi-table th {
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text);
            white-space: nowrap;
            text-align: left;
            border-bottom: 2px solid var(--color-gray);
        }
        
        .materi-table th:nth-child(1) {
            width: 50px;
            text-align: center;
        }
        
        .materi-table th:nth-child(2) {
            width: 110px;
        }

        .materi-table th:nth-child(3) {
            width: 34%;
        }

        .materi-table th:nth-child(4) {
            width: 90px;
        }

        .materi-table th:nth-child(5) {
            width: 120px;
            text-align: center;
        }

        .materi-table th:nth-child(6) {
            width: 110px;
        }

        .materi-table th:nth-child(7) {
            width: 120px;
            text-align: center;
        }
        
        .materi-table td {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--color-gray);
            color: var(--color-text);
        }
        
        .materi-table td:nth-child(1) {
            text-align: center;
            color: var(--color-text-light);
            font-weight: 500;
        }
        
        .materi-table tbody tr {
            transition: all 0.2s ease;
        }

        .materi-title-cell {
            max-width: 0;
        }

        .materi-title-text {
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        .materi-table th:last-child,
        .materi-table td:last-child {
            position: sticky;
            right: 0;
            background: var(--color-white);
            z-index: 2;
        }
        
        .materi-table thead th:last-child {
            background: var(--color-primary-light);
        }
        
        .materi-table tbody tr:hover td:last-child {
            background: var(--color-primary-light);
        }


        
        .materi-table tbody tr:hover {
            background: var(--color-primary-light);
        }
        
        .materi-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
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
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #F3F4F6;
            color: var(--color-text);
            flex-shrink: 0;
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

        .badge-neutral {
            background: #EEF2FF;
            color: #3730A3;
        }

        .badge-success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .badge-danger {
            background: #FFEBEE;
            color: #C62828;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
            background: var(--color-primary-light);
            border-color: var(--color-primary);
        }
        
        .pagination-btn.active {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
@media (max-width: 1024px) {.materi-table th, .materi-table td { padding: 0.75rem 0.5rem; font-size: 0.8rem; } .action-btn { width: 32px; height: 32px; font-size: 0.9rem; } .materi-table th:nth-child(3) { width: 30%; }}
        
        @media (max-width: 768px) {.table-container { padding: 1rem; border-radius: 12px; } .materi-table { font-size: 0.75rem; } .materi-table th, .materi-table td { padding: 0.6rem 0.4rem; } .materi-table th:nth-child(1) { width: 40px; } .materi-table th:nth-child(2) { width: 90px; } .materi-table th:nth-child(7) { width: 100px; } .action-btn { width: 28px; height: 28px; font-size: 0.8rem; } .action-buttons { gap: 0.3rem; } .search-form { grid-template-columns: 1fr; } .search-input-wrap, .search-button, .reset-search { width: 100%; }}
        
        /* Empty State */
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
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Mata Pelajaran</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="page-subtitle">Kelola mata pelajaran sebagai bahan ajar utama. Setiap mata pelajaran bisa dipecah lagi menjadi beberapa materi.</div>

                <div class="materi-controls">
                    <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="layers"></i>
                            <span>{{ $search ? 'Hasil pencarian' : 'Total mata pelajaran' }}: {{ $materi->total() }} item</span>
                        </div>
                        <a href="{{ route('materi.create') }}" class="add-button" style="text-decoration: none; display: inline-flex;">
                            <span><i data-lucide="plus"></i></span>
                            <span>Tambah Mata Pelajaran</span>
                        </a>
                    </div>

                    <form action="{{ route('materi.index') }}" method="GET" class="search-form" role="search">
                        <div class="search-input-wrap">
                            <i data-lucide="search" class="search-icon"></i>
                            <input
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                class="search-input"
                                placeholder="Cari mata pelajaran berdasarkan ID, judul, level, atau pembuat..."
                                aria-label="Cari mata pelajaran"
                            >
                        </div>
                        <button type="submit" class="search-button">
                            <i data-lucide="search"></i>
                            <span>Cari</span>
                        </button>
                        @if($search)
                            <a href="{{ route('materi.index') }}" class="reset-search">
                                <i data-lucide="x"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                        <div class="search-note">Gunakan kata kunci seperti ID mata pelajaran, judul, level, nama pembuat, atau email pembuat.</div>
                    </form>
                </div>
                
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">
                            @if($search)
                                Hasil untuk "{{ $search }}"
                            @else
                                Daftar Mata Pelajaran
                            @endif
                        </div>
                    </div>
                    @if($materi->count() > 0)
                        <table class="materi-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Level</th>
                                    <th>Semester</th>
                                    <th>Jumlah Materi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materi as $index => $item)
                                    <tr>
                                        <td>{{ $materi->firstItem() + $index }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td class="materi-title-cell">
                                            <span class="materi-title-text" title="{{ $item->judul }}">{{ $item->judul }}</span>
                                        </td>
                                        <td>{{ $item->level?->nama ?? '-' }}</td>
                                        <td>{{ $item->semester ? ucfirst($item->semester) : '-' }}</td>
                                        <td style="text-align:center;">
                                            <span class="badge badge-neutral">
                                                {{ $item->bab_count }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->status_aktif ? 'badge-success' : 'badge-danger' }}">
                                                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                               <a href="{{ route('materi.show', $item->id) }}" class="action-btn view" title="Lihat">
                                            <i data-lucide="eye"></i>
                                        </a>
                                    <a href="{{ route('materi.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                        <i data-lucide="edit-3"></i>
                                    </a>
                                    <button type="button" class="action-btn delete" title="Hapus"
                                        onclick="handleDeleteMateri({{ $item->id }}, '{{ addslashes($item->judul) }}')">
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
                        <div class="empty-state-icon">
                            <i data-lucide="book-open"></i>
                        </div>
                            @if($search)
                                <h3 style="margin-bottom: 0.5rem;">Mata pelajaran tidak ditemukan</h3>
                                <p>Tidak ada mata pelajaran yang cocok dengan kata kunci "{{ $search }}".</p>
                            @else
                                <h3 style="margin-bottom: 0.5rem;">Belum ada mata pelajaran</h3>
                                <p>Mulai dengan menambahkan mata pelajaran baru.</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Pagination -->
                @if($materi->hasPages())
                    <div class="pagination">
                        @if($materi->onFirstPage())
                            <button class="pagination-btn" disabled>&lsaquo;</button>
                        @else
                            <a href="{{ $materi->previousPageUrl() }}" class="pagination-btn">&lsaquo;</a>
                        @endif

                        @foreach($materi->getUrlRange(1, $materi->lastPage()) as $page => $url)
                            @if($page == $materi->currentPage())
                                <button class="pagination-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($materi->hasMorePages())
                            <a href="{{ $materi->nextPageUrl() }}" class="pagination-btn">&rsaquo;</a>
                        @else
                            <button class="pagination-btn" disabled>&rsaquo;</button>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    {{-- Include Modal Component --}}
    @include('components.modal')
    
    <script>
        // Handle Delete Materi
        function handleDeleteMateri(id, judul) {
            showModal({
                type: 'delete',
                title: 'Hapus Mata Pelajaran',
                message: `Apakah Anda yakin ingin menghapus mata pelajaran "${judul}"? Tindakan ini tidak dapat dibatalkan dan semua data terkait akan terhapus.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    // Create form for delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/materi/${id}`;
                    
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
                        showInfoToast('Menghapus...', 'Sedang menghapus mata pelajaran...');
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



