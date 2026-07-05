<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fiksi - Ruma Dashboard</title>
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
.form-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .section-subtitle {
            color: var(--color-text-light);
            font-size: 0.92rem;
            margin-bottom: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--color-gray);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }
        
        .form-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .error-message {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
        }
        
        .required {
            color: #DC2626;
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

        .hint {
            color: var(--color-text-light);
            font-size: 0.85rem;
            margin-top: 0.35rem;
            display: block;
        }
        
        .current-file {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: var(--color-gray-light);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--color-text);
        }
        
        .current-file a {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
        }
        
        .current-file a:hover {
            text-decoration: underline;
        }

        .cover-preview {
            width: 140px;
            height: 190px;
            border-radius: 12px;
            border: 2px solid var(--color-gray);
            background: #F9FAFB;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-top: 0.75rem;
        }

        .cover-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cover-preview-placeholder {
            text-align: center;
            color: var(--color-text-light);
            font-size: 0.82rem;
            padding: 0.75rem;
        }

        .cover-preview-placeholder i {
            width: 28px;
            height: 28px;
            margin-bottom: 0.35rem;
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
                <h1 class="header-title">Edit Fiksi</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="{{ route('fiksi.show', $fiksi->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail
                    </a>
                    <a href="{{ route('fiksi.index') }}" class="back-link">
                        <i data-lucide="list"></i>
                        Daftar Fiksi
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-error">
                        <strong>Terjadi kesalahan:</strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('fiksi.update', $fiksi->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf
                    @method('PUT')
                    
                    <div class="section-title"><i data-lucide="book-open"></i> Informasi Fiksi</div>
                    <div class="section-subtitle">Perbarui judul, cover, dan file cerita fiksi.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Judul Buku <span class="required">*</span>
                        </label>
                        <input type="text" name="judul_buku" value="{{ old('judul_buku', $fiksi->judul_buku) }}" class="form-input" required>
                        @error('judul_buku')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                    <label class="form-label">Cover Buku</label>
                    <div class="cover-preview">
                        @if($fiksi->cover_path)
                            <img src="{{ Storage::url($fiksi->cover_path) }}" alt="Cover {{ $fiksi->judul_buku }}">
                        @else
                            <div class="cover-preview-placeholder">
                                <i data-lucide="image"></i>
                                <div>Belum ada cover</div>
                            </div>
                        @endif
                    </div>
                    <span class="hint">Cover dibuat otomatis dari halaman pertama file PDF saat fiksi ditambahkan.</span>
                </div>

                    <div class="form-group">
                        <label class="form-label">File Cerita</label>
                        <input type="file" name="file_path" accept=".pdf,.doc,.docx" class="form-input">
                        <span class="hint">PDF, DOC, atau DOCX. Maksimal 10 MB. Kosongkan jika tidak ingin mengganti file.</span>
                        @if($fiksi->file_path)
                            <div class="current-file">
                                File saat ini: <a href="{{ Storage::url($fiksi->file_path) }}" target="_blank">{{ basename($fiksi->file_path) }}</a>
                            </div>
                        @endif
                        @error('file_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $fiksi->status_aktif) ? 'checked' : '' }}>
                            <span>Status Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('fiksi.show', $fiksi->id) }}" class="btn btn-secondary">
                            <i data-lucide="x"></i>
                            Batal
                        </a>
                    </div>
                </form>
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



