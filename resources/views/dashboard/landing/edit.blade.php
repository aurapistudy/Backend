<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konten Landing - Ruma Dashboard</title>
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
        .form-help {
            font-size: 0.85rem;
            color: var(--color-text-light);
            margin-top: 0.35rem;
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
            background: var(--color-primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
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
        .image-preview {
            margin-top: 0.75rem;
        }
        .image-preview img {
            max-width: 200px;
            border-radius: 12px;
            border: 1px solid var(--color-gray);
        }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Edit Konten Landing</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('landing.index') }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali
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

                <form action="{{ route('landing.update', $landingItem->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf
                    @method('PUT')

                    <div class="section-title"><i data-lucide="layout-dashboard"></i> Perbarui Konten</div>
                    <div class="section-subtitle">Sesuaikan konten landing dengan form yang lebih ringkas.</div>

                    <div class="form-group">
                        <label class="form-label">Bagian Landing <span class="required">*</span></label>
                        <select name="section" class="form-select" required>
                            <option value="">Pilih Bagian</option>
                            @foreach($sections as $value => $label)
                                <option value="{{ $value }}" {{ old('section', $landingItem->section) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div style="margin-top: 0.5rem; color: var(--color-text-light); font-size: 0.9rem;">
                            Jika memilih <strong>Branding Aplikasi</strong>, logo pada item ini akan dipakai untuk landing page, login, dan sidebar dashboard.
                        </div>
                        @error('section')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Judul <span class="required">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $landingItem->title) }}" class="form-input" required>
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gambar</label>
                        <input type="file" name="image" id="image" class="form-input" accept=".jpg,.jpeg,.png,.webp,.svg">
                        <div class="form-help">Format: jpg, jpeg, png, webp. Maks 5MB.</div>
                        <div id="image_compress_hint" class="form-help" style="display:none;"></div>
                        @if($landingItem->image_path)
                            <div class="image-preview">
                                <img src="{{ Storage::url($landingItem->image_path) }}" alt="Preview">
                            </div>
                        @endif
                        @error('image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $landingItem->is_active) ? 'checked' : '' }}>
                            <span>Status Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('landing.index') }}" class="btn btn-secondary">
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

        lucide.createIcons();
    </script>
    <script>
        const landingImageInput = document.getElementById('image');
        const landingImageHint = document.getElementById('image_compress_hint');
        const LANDING_MAX_IMAGE_BYTES = 5 * 1024 * 1024;

        function setLandingImageHint(message, isError = false) {
            if (!landingImageHint) {
                return;
            }

            landingImageHint.style.display = message ? 'block' : 'none';
            landingImageHint.textContent = message || '';
            landingImageHint.style.color = isError ? '#DC2626' : 'var(--color-text-light)';
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

        async function compressLandingImage(file) {
            if (file.size <= LANDING_MAX_IMAGE_BYTES || file.type === 'image/svg+xml') {
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

                if (blob.size <= LANDING_MAX_IMAGE_BYTES) {
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

            if (!bestBlob || bestBlob.size > LANDING_MAX_IMAGE_BYTES) {
                return null;
            }

            const extension = outputType === 'image/webp' ? 'webp' : 'jpg';
            const baseName = file.name.replace(/\.[^.]+$/, '');

            return new File([bestBlob], `${baseName}.${extension}`, {
                type: outputType,
                lastModified: Date.now(),
            });
        }

        if (landingImageInput) {
            landingImageInput.addEventListener('change', async () => {
                const file = landingImageInput.files && landingImageInput.files[0] ? landingImageInput.files[0] : null;
                setLandingImageHint('');

                if (!file || file.type === 'image/svg+xml' || file.size <= LANDING_MAX_IMAGE_BYTES) {
                    return;
                }

                setLandingImageHint('Gambar melebihi 5MB. Sedang dicoba dikompres otomatis...');

                try {
                    const compressedFile = await compressLandingImage(file);

                    if (!compressedFile) {
                        landingImageInput.value = '';
                        setLandingImageHint('Gambar tidak berhasil dikompres sampai 5MB. Coba pilih gambar lain atau kompres manual.', true);
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    landingImageInput.files = dataTransfer.files;

                    setLandingImageHint(`Gambar berhasil dikompres dari ${(file.size / 1024 / 1024).toFixed(2)} MB menjadi ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB.`);
                } catch (error) {
                    landingImageInput.value = '';
                    setLandingImageHint('Terjadi kesalahan saat kompres gambar otomatis. Coba lagi dengan file lain.', true);
                }
            });
        }
    </script>
</body>
</html>


