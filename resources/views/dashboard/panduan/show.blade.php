<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Panduan - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .detail-card {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .detail-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .detail-meta {
            color: var(--color-text-light);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--color-gray);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--color-text);
        }

        .detail-value {
            color: var(--color-text);
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

        .action-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.9rem 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--color-gray);
            color: var(--color-text);
        }

        .btn-secondary:hover {
            background: #D1D5DB;
        }

        .btn-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .btn-danger:hover {
            background: #FECACA;
        }

        @media (max-width: 768px) {.detail-row { grid-template-columns: 1fr; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Detail Panduan</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('panduan.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Daftar
                    </a>
                </div>

                <div class="detail-card">
                    <div class="detail-title">{{ $data->judul }}</div>
                    <div class="detail-meta">Terakhir diperbarui {{ $data->updated_at->format('d M Y, H:i') }}</div>

                    <div class="detail-row">
                        <div class="detail-label">Deskripsi</div>
                        <div class="detail-value">{{ $data->deskripsi ?? '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tag</div>
                        <div class="detail-value">{{ $data->tag ?? '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Urutan</div>
                        <div class="detail-value">{{ $data->urutan ?? '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Dibuat</div>
                        <div class="detail-value">{{ $data->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <div class="action-group">
                    <a href="{{ route('panduan.edit', $data->id) }}" class="btn btn-primary">
                        <i data-lucide="edit-3"></i>
                        Edit
                    </a>
                    <button class="btn btn-danger" onclick="handleDeletePanduan({{ $data->id }}, '{{ $data->judul }}')">
                        <i data-lucide="trash-2"></i>
                        Hapus
                    </button>
                    <a href="{{ route('panduan.index') }}" class="btn btn-secondary">
                        <i data-lucide="list"></i>
                        Daftar Panduan
                    </a>
                </div>
            </div>
        </main>
    </div>

    @include('components.modal')
    <script>
        function handleDeletePanduan(id, judul) {
            showModal({
                type: 'delete',
                title: 'Hapus Panduan',
                message: `Apakah Anda yakin ingin menghapus panduan "${judul}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'trash-2',
                confirmText: 'Ya, Hapus',
                isDanger: true,
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/panduan/${id}`;

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
