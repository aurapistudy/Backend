<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .section-card {
            background: var(--color-white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            text-align: left;
            padding: 0.85rem 0.75rem;
            border-bottom: 1px solid var(--color-gray);
            vertical-align: top;
        }
        .table th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-text-light);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 0.9rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            gap: 0.4rem;
        }
        .btn-danger {
            background: #DC2626;
            color: #fff;
        }
        .btn-danger:hover {
            background: #B91C1C;
        }
        .btn-export {
            background: #E9F9EF;
            color: #166534;
            border: 2px solid #22C55E;
            box-shadow: 0 6px 14px rgba(34, 197, 94, 0.18);
        }
        .btn-export:hover {
            background: #DFF5E7;
        }
        .action-btn {
            border-radius: 8px;
            padding: 0.45rem 0.75rem;
            font-size: 0.85rem;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(248, 184, 3, 0.15);
            color: #B35E00;
        }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Ulasan</h1>
            </header>

            <div class="content-area">
                @if(session('success'))
                    <div class="section-card" style="margin-bottom:1rem; border-left:4px solid #16A34A;">
                        <span class="badge">Sukses</span>
                        <p style="margin-top:0.5rem; color:var(--color-text-light);">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="list-search-panel">
                    <div style="display:flex; justify-content:flex-start; margin-bottom:0.85rem;">
                        <a href="{{ route('ulasan.export', [], false) }}" class="btn btn-export">
                            <i data-lucide="download"></i>
                            Export CSV
                        </a>
                    </div>

                    @include('components.list-search', [
                        'action' => route('ulasan.index'),
                        'resetRoute' => route('ulasan.index'),
                        'value' => $search ?? '',
                        'placeholder' => 'Cari ulasan berdasarkan ID, nama, email, rating, atau isi ulasan...',
                        'note' => 'Gunakan kata kunci seperti ID ulasan, nama pengirim, email, rating, atau isi ulasan.',
                        'panel' => false
                    ])
                </div>

                <div class="section-card">
                    @if($ulasan->count() === 0)
                        @if(($search ?? '') !== '')
                            <p style="color:var(--color-text-light);">Tidak ada ulasan yang cocok dengan kata kunci "{{ $search }}".</p>
                        @else
                            <p style="color:var(--color-text-light);">Belum ada ulasan masuk.</p>
                        @endif
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Rating</th>
                                    <th>Ulasan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ulasan as $item)
                                    <tr>
                                        <td>{{ $ulasan->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->rating ?? '-' }}</td>
                                        <td style="max-width:520px;">{{ $item->isi }}</td>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <button class="btn btn-danger action-btn" type="button" onclick="handleDeleteUlasan({{ $item->id }}, '{{ addslashes($item->nama) }}')">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div style="margin-top:1rem;">
                            {{ $ulasan->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <form id="deleteUlasanForm" method="post" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>

    @include('components.modal')

    <script>
        function handleDeleteUlasan(id, nama) {
            const form = document.getElementById('deleteUlasanForm');
            showModal({
                type: 'delete',
                title: 'Hapus Ulasan',
                message: `Apakah Anda yakin ingin menghapus ulasan dari "${nama}"?`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    form.action = `/dashboard/ulasan/${id}`;
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
                    showInfoToast('Logout', 'Sedang keluar...');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 1000);
                }
            });
        }
    </script>
</body>
</html>

