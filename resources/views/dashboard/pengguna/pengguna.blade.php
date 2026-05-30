<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengguna - Ruma Dashboard</title>
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
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
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
        
        /* Table Container */
        .table-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }
        
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
        
        /* Column widths */
        .pengguna-table th:nth-child(1),
        .pengguna-table td:nth-child(1) {
            width: 50px;
            text-align: center;
        }
        
        .pengguna-table th:nth-child(7),
        .pengguna-table td:nth-child(7) {
            width: 100px;
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
        
        .pengguna-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .pengguna-table tbody tr:hover {
            background: #F9FAFB;
        }
        
        .pengguna-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #1F2937;
            font-size: 0.9rem;
        }
        
        /* Badge */
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .role-badge.admin {
            background: #FFEBEE;
            color: #D32F2F;
        }
        
        .role-badge.guru {
            background: #E3F2FD;
            color: #1976D2;
        }
        
        .role-badge.siswa {
            background: #E8F5E9;
            color: #388E3C;
        }
        
        /* Status Badge */
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
@media (max-width: 768px) {.table-container { padding: 1rem; } .pengguna-table { font-size: 0.85rem; } .pengguna-table th, .pengguna-table td { padding: 0.75rem 0.5rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Manajemen Pengguna</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="page-subtitle">Kelola akun pengguna agar rapi dan terpantau.</div>
                <div class="list-search-panel">
                    <div class="page-toolbar">
                        <div class="summary-card">
                            <i data-lucide="users"></i>
                            <span>{{ ($search ?? '') !== '' ? 'Hasil pencarian' : 'Total pengguna' }}: {{ $pengguna->total() }} akun</span>
                        </div>
                        <a href="{{ route('pengguna.create') }}" class="add-button" style="text-decoration: none; display: inline-flex;">
                            <i data-lucide="plus"></i>
                            <span>Tambah Pengguna</span>
                        </a>
                    </div>

                    @include('components.list-search', [
                        'action' => route('pengguna.index'),
                        'resetRoute' => route('pengguna.index'),
                        'value' => $search ?? '',
                        'placeholder' => 'Cari pengguna berdasarkan ID, nama, email, atau peran...',
                        'note' => 'Gunakan kata kunci seperti ID pengguna, nama, email, atau peran pengguna.',
                        'panel' => false
                    ])
                </div>
                
                <!-- Table Container -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">Daftar Pengguna</div>
                    </div>
                    @if($pengguna->count() > 0)
                        <table class="pengguna-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengguna as $index => $item)
                                    @php
                                        $initials = strtoupper(substr($item->nama, 0, 2));
                                    @endphp
                                    <tr>
                                        <td>{{ $pengguna->firstItem() + $index }}</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div class="user-avatar">{{ $initials }}</div>
                                                <span>{{ $item->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            <span class="role-badge {{ $item->peran }}">
                                                @if($item->peran === 'admin')
                                                    Administrator
                                                @elseif($item->peran === 'guru')
                                                    Guru Mapel
                                                @else
                                                    Siswa
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $item->status_aktif ? 'aktif' : 'nonaktif' }}">
                                                {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>
                                             <div class="action-buttons">
                                               <a href="{{ route('pengguna.show', $item->id) }}" class="action-btn view" title="Lihat">
                                            <i data-lucide="eye"></i>
                                        </a>
                                    <a href="{{ route('pengguna.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                        <i data-lucide="edit-3"></i>
                                    </a>
                                    <button type="button" class="action-btn delete" title="Hapus"
                                        onclick="handleDeletePengguna({{ $item->id }}, '{{ addslashes($item->nama) }}')">
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
                            <div class="empty-state-icon">👥</div>
                            <h3 style="margin-bottom: 0.5rem;">Belum ada pengguna</h3>
                            <p>Mulai dengan menambahkan pengguna baru.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Pagination -->
                @if($pengguna->hasPages())
                    <div class="pagination">
                        @if($pengguna->onFirstPage())
                            <button class="pagination-btn" disabled>‹</button>
                        @else
                            <a href="{{ $pengguna->previousPageUrl() }}" class="pagination-btn">‹</a>
                        @endif

                        @foreach($pengguna->getUrlRange(1, $pengguna->lastPage()) as $page => $url)
                            @if($page == $pengguna->currentPage())
                                <button class="pagination-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($pengguna->hasMorePages())
                            <a href="{{ $pengguna->nextPageUrl() }}" class="pagination-btn">›</a>
                        @else
                            <button class="pagination-btn" disabled>›</button>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    {{-- Include Modal Component --}}
    @include('components.modal')
    
    <script>
        // Handle Delete Pengguna
        function handleDeletePengguna(id, nama) {
            showModal({
                type: 'delete',
                title: 'Hapus Pengguna',
                message: `Apakah Anda yakin ingin menghapus pengguna "${nama}"? Tindakan ini tidak dapat dibatalkan dan semua data terkait akan terhapus.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    // Create form for delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/pengguna/${id}`;
                    
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
                        showInfoToast('Menghapus...', 'Sedang menghapus pengguna...');
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


