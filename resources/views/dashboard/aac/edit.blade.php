<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit AAC - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
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
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Edit AAC</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="{{ route('aac.show', $aac->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail
                    </a>
                    <a href="{{ route('aac.index') }}" class="back-link">
                        <i data-lucide="list"></i>
                        Daftar AAC
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

                <form action="{{ route('aac.update', $aac->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf
                    @method('PUT')
                    
                    <div class="section-title"><i data-lucide="message-circle"></i> Informasi Utama</div>
                    <div class="section-subtitle">Perbarui ungkapan/kata AAC.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Ungkapan / Kata <span class="required">*</span>
                        </label>
                        <input type="text" name="judul" value="{{ old('judul', $aac->judul) }}" class="form-input" required>
                        @error('judul')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="kategori" value="{{ old('kategori', $aac->kategori) }}" placeholder="misal: Kebutuhan, Emosi" class="form-input">
                            @error('kategori')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="urutan" value="{{ old('urutan', $aac->urutan) }}" min="1" class="form-input">
                            @error('urutan')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="section-title" style="margin-top: 1.5rem;"><i data-lucide="image"></i> Media AAC</div>
                    <div class="section-subtitle">Unggah gambar baru jika ingin mengganti yang lama.</div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-textarea">{{ old('deskripsi', $aac->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gambar (JPG/PNG/WEBP/SVG)</label>
                        <input type="file" name="gambar_path" id="gambar_path" accept=".jpg,.jpeg,.png,.webp,.svg" class="form-input">
                        <small class="hint">Maksimal 5MB. Kosongkan jika tidak ingin mengubah gambar. JPG/PNG/WEBP yang lebih besar akan dicoba dikompres otomatis sebelum diupload.</small>
                        <div id="gambar_compress_hint" class="hint" style="display:none;"></div>
                        @if($aac->gambar_path)
                            <div class="current-file">
                                Gambar saat ini: <a href="{{ Storage::url($aac->gambar_path) }}" target="_blank">{{ basename($aac->gambar_path) }}</a>
                            </div>
                        @endif
                        @error('gambar_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="section-title" style="margin-top: 1.5rem;"><i data-lucide="settings"></i> Pengaturan</div>
                    <div class="section-subtitle">Atur status AAC.</div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $aac->status_aktif) ? 'checked' : '' }}>
                            <span>Status Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('aac.show', $aac->id) }}" class="btn btn-secondary">
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
    <script>
        const aacImageInput = document.getElementById('gambar_path');
        const aacImageHint = document.getElementById('gambar_compress_hint');
        const AAC_MAX_IMAGE_BYTES = 5 * 1024 * 1024;

        function setAacImageHint(message, isError = false) {
            if (!aacImageHint) {
                return;
            }

            aacImageHint.style.display = message ? 'block' : 'none';
            aacImageHint.textContent = message || '';
            aacImageHint.style.color = isError ? '#DC2626' : 'var(--color-text-light)';
        }

        async function fileToImageBitmap(file) {
            const imageUrl = URL.createObjectURL(file);

            try {
                const image = new Image();
                image.decoding = 'async';
                image.src = imageUrl;
                await image.decode();
                return image;
            } finally {
                URL.revokeObjectURL(imageUrl);
            }
        }

        async function compressAacImage(file) {
            if (file.size <= AAC_MAX_IMAGE_BYTES || file.type === 'image/svg+xml') {
                return file;
            }

            const image = await fileToImageBitmap(file);
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            let width = image.naturalWidth || image.width;
            let height = image.naturalHeight || image.height;
            let quality = 0.9;
            let bestBlob = null;
            const outputType = file.type === 'image/png' ? 'image/webp' : (file.type === 'image/webp' ? 'image/webp' : 'image/jpeg');

            for (let attempt = 0; attempt < 10; attempt++) {
                canvas.width = Math.max(1, Math.round(width));
                canvas.height = Math.max(1, Math.round(height));
                context.clearRect(0, 0, canvas.width, canvas.height);
                context.drawImage(image, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise((resolve) => canvas.toBlob(resolve, outputType, quality));
                if (!blob) {
                    break;
                }

                if (!bestBlob || blob.size < bestBlob.size) {
                    bestBlob = blob;
                }

                if (blob.size <= AAC_MAX_IMAGE_BYTES) {
                    bestBlob = blob;
                    break;
                }

                if (quality > 0.45) {
                    quality -= 0.1;
                } else {
                    width *= 0.85;
                    height *= 0.85;
                }
            }

            if (!bestBlob || bestBlob.size > AAC_MAX_IMAGE_BYTES) {
                return null;
            }

            const extension = outputType === 'image/webp' ? 'webp' : 'jpg';
            const baseName = file.name.replace(/\.[^.]+$/, '');

            return new File([bestBlob], `${baseName}.${extension}`, {
                type: outputType,
                lastModified: Date.now(),
            });
        }

        if (aacImageInput) {
            aacImageInput.addEventListener('change', async () => {
                const file = aacImageInput.files && aacImageInput.files[0] ? aacImageInput.files[0] : null;
                setAacImageHint('');

                if (!file) {
                    return;
                }

                if (file.type === 'image/svg+xml') {
                    return;
                }

                if (file.size <= AAC_MAX_IMAGE_BYTES) {
                    return;
                }

                setAacImageHint('Gambar melebihi 5MB. Sedang dicoba dikompres otomatis...');

                try {
                    const compressedFile = await compressAacImage(file);

                    if (!compressedFile) {
                        aacImageInput.value = '';
                        setAacImageHint('Gambar tidak berhasil dikompres sampai 5MB. Coba pilih gambar lain atau kompres manual.', true);
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    aacImageInput.files = dataTransfer.files;

                    setAacImageHint(`Gambar berhasil dikompres dari ${(file.size / 1024 / 1024).toFixed(2)} MB menjadi ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB.`);
                } catch (error) {
                    aacImageInput.value = '';
                    setAacImageHint('Terjadi kesalahan saat kompres gambar otomatis. Coba lagi dengan file lain.', true);
                }
            });
        }
    </script>

</body>
</html>
