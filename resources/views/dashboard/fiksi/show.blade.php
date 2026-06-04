<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Fiksi - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .top-header-strip {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
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
            color: var(--color-text);
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
            background: var(--color-accent);
            color: #1F2937;
        }
        
        .btn-primary:hover {
            background: #E6A500;
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

        .detail-cover {
            width: 160px;
            height: 220px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid var(--color-gray);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        .detail-cover-placeholder {
            width: 160px;
            height: 220px;
            border-radius: 14px;
            border: 2px dashed var(--color-gray);
            background: #F9FAFB;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-light);
            flex-shrink: 0;
        }

        .detail-cover-placeholder i {
            width: 36px;
            height: 36px;
        }

        .detail-header-main {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
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
                <h1 class="header-title">Detail Fiksi</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('fiksi.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Daftar Fiksi
                    </a>
                </div>

                <div class="detail-container">
                    <!-- Header -->
                    <div class="detail-header">
                        <div class="detail-header-main" style="flex: 1;">
                            @if($fiksi->cover_path)
                                <img src="{{ Storage::url($fiksi->cover_path) }}" alt="Cover {{ $fiksi->judul_buku }}" class="detail-cover">
                            @else
                                <div class="detail-cover-placeholder">
                                    <i data-lucide="book"></i>
                                </div>
                            @endif
                            <div style="flex: 1;">
                                <h2 class="detail-title">{{ $fiksi->judul_buku }}</h2>
                                <div class="detail-meta">
                                    <span class="meta-badge {{ $fiksi->status_aktif ? 'badge-success' : 'badge-danger' }}">
                                        {{ $fiksi->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="info-grid">
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="user"></i></div>
                            <div>
                                <div class="info-label">Dibuat Oleh</div>
                                <div class="info-value">{{ $fiksi->pengguna->nama ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="calendar-plus"></i></div>
                            <div>
                                <div class="info-label">Tanggal Dibuat</div>
                                <div class="info-value">{{ $fiksi->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon"><i data-lucide="refresh-cw"></i></div>
                            <div>
                                <div class="info-label">Terakhir Diupdate</div>
                                <div class="info-value">{{ $fiksi->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- File -->
                    @if($fiksi->file_path)
                        <div class="content-section">
                            <h3 class="section-title">File Fiksi</h3>
                            <div>
                                <p style="margin-bottom: 1rem; color: var(--color-text-light);">
                                    File: <strong>{{ basename($fiksi->file_path) }}</strong>
                                </p>
                                <iframe 
                                    src="{{ Storage::url($fiksi->file_path) }}" 
                                    class="pdf-viewer"
                                    type="application/pdf">
                                    <p style="padding: 2rem; text-align: center; color: var(--color-text-light);">
                                        Browser Anda tidak mendukung preview PDF. 
                                        <a href="{{ Storage::url($fiksi->file_path) }}" target="_blank" style="color: var(--color-primary-dark); text-decoration: underline;">
                                            Klik di sini untuk membuka file
                                        </a>
                                    </p>
                                </iframe>
                                <div style="margin-top: 1rem;">
                                    <a href="{{ Storage::url($fiksi->file_path) }}" target="_blank" class="btn btn-primary">
                                        <i data-lucide="download"></i>
                                        Download File
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('fiksi.edit', $fiksi->id) }}" class="btn btn-primary">
                            <i data-lucide="edit-3"></i>
                            Edit Fiksi
                        </a>
                        <a href="{{ route('fiksi.index') }}" class="btn btn-secondary">
                            <i data-lucide="arrow-left"></i>
                            Kembali
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



