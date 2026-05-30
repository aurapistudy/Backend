<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mata Pelajaran - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        :root {
            --color-accent-dark: #E6A500;
            --color-accent-light: #FFF9E6;
            --color-sidebar: #111827;
            --color-sidebar-dark: #0b1220;
            --color-muted: #6B7280;
        }
.back-links { display: flex; gap: 0.85rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-text); text-decoration: none; font-weight: 600;
            background: var(--color-white); border: 1px solid var(--color-gray); border-radius: 10px; padding: 0.45rem 0.75rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06); transition: all 0.2s ease;
        }
        .back-link:hover { transform: translateY(-1px); box-shadow: 0 6px 12px rgba(0,0,0,0.12); background: var(--color-accent-light); }
        .form-container { background: var(--color-white); border-radius: 16px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        .form-group { margin-bottom: 1.5rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }
        .form-label { display: block; font-weight: 600; color: var(--color-text); margin-bottom: 0.5rem; font-size: 0.95rem; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 0.875rem 1.25rem; border: 2px solid var(--color-gray); border-radius: 12px;
            font-size: 1rem; font-family: 'Inter', sans-serif; transition: all 0.3s ease;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--color-accent); box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.12); }
        .form-textarea { resize: vertical; min-height: 130px; }
        .hint { color: var(--color-muted); font-size: 0.86rem; margin-top: 0.35rem; display: block; line-height: 1.6; }
        .cover-panel {
            margin-top: 0.75rem; padding: 1rem; border-radius: 16px; border: 1px solid rgba(17, 24, 39, 0.08);
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
        }
        .current-cover { margin-top: 0.9rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .current-cover img { width: 150px; aspect-ratio: 3 / 4; object-fit: cover; border-radius: 14px; border: 1px solid var(--color-gray); }
        .note-box {
            margin-bottom: 1.25rem; padding: 1rem 1.1rem; border-radius: 14px; background: #F8FAFC; color: var(--color-muted);
            line-height: 1.65; font-size: 0.92rem;
        }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-error { background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; }
        .error-message { color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem; }
        .required { color: #DC2626; }
        .form-checkbox { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; }
        .form-checkbox input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; }
        .form-actions { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
        .btn {
            flex: 1; padding: 1rem; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem;
            cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-primary { background: var(--color-accent); color: var(--color-sidebar); }
        .btn-primary:hover { background: var(--color-accent-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(248, 184, 3, 0.35); }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .btn-secondary:hover { background: #D1D5DB; }

        @media (max-width: 768px) {.form-container { padding: 1.25rem; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Edit Mata Pelajaran</h1>
            </header>

            <div class="content-area">
                <div class="back-links">
                    <a href="{{ route('materi.show', $materi->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail Mata Pelajaran
                    </a>
                    <a href="{{ route('materi.index') }}" class="back-link">
                        <i data-lucide="book-open"></i>
                        Daftar Mata Pelajaran
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

                <form action="{{ route('materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf
                    @method('PUT')

                    <div class="note-box">
                        Halaman ini hanya untuk mengubah metadata mata pelajaran: judul, deskripsi, level, cover, dan status. Isi materi dikelola dari halaman detail mata pelajaran agar struktur pembahasan tetap rapi.
                    </div>

                    <div class="form-group">
                        <label class="form-label">Judul Mata Pelajaran <span class="required">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}" class="form-input" required>
                        @error('judul')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-textarea">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Level</label>
                        <select name="level_id" class="form-select">
                            <option value="">Pilih Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id', $materi->level_id) == $level->id ? 'selected' : '' }}>
                                    {{ $level->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Semester</label>
                        <div class="form-input" style="background: #F9FAFB; cursor: default;">
                            {{ $materi->semester ? ucfirst($materi->semester) : 'Belum ditetapkan' }}
                        </div>
                        <span class="hint">Semester ditetapkan otomatis saat mata pelajaran dibuat, sesuai periode tahun akademik yang aktif.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cover Mata Pelajaran</label>
                        <div class="cover-panel">
                            <input type="file" name="cover_path" id="cover_path" accept=".jpg,.jpeg,.png,.webp" class="form-input">
                            <span class="hint">Opsional. Kalau tidak diganti, sistem tetap memakai cover yang sekarang.</span>
                            <div id="cover_compress_hint" class="hint" style="display:none;"></div>

                            @if($materi->cover_url)
                                <div class="current-cover">
                                    <img src="{{ $materi->cover_url }}" alt="Cover {{ $materi->judul }}">
                                    <div class="hint" style="margin-top:0;">
                                        Cover saat ini digunakan di daftar mata pelajaran dan halaman detail mata pelajaran.
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('cover_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $materi->status_aktif) ? 'checked' : '' }}>
                            <span>Status Mata Pelajaran Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('materi.show', $materi->id) }}" class="btn btn-secondary">
                            <i data-lucide="x"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

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
    @include('components.modal')
    <script>
        lucide.createIcons();
    </script>
    <script>
        const coverImageInput = document.getElementById('cover_path');
        const coverImageHint = document.getElementById('cover_compress_hint');
        const COVER_MAX_IMAGE_BYTES = 5 * 1024 * 1024;

        function setCoverImageHint(message, isError = false) {
            if (!coverImageHint) return;
            coverImageHint.style.display = message ? 'block' : 'none';
            coverImageHint.textContent = message || '';
            coverImageHint.style.color = isError ? '#DC2626' : 'var(--color-muted)';
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

        async function compressCoverImage(file) {
            if (file.size <= COVER_MAX_IMAGE_BYTES || file.type === 'image/svg+xml') return file;

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
                if (!blob) break;
                if (!bestBlob || blob.size < bestBlob.size) bestBlob = blob;
                if (blob.size <= COVER_MAX_IMAGE_BYTES) {
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

            if (!bestBlob || bestBlob.size > COVER_MAX_IMAGE_BYTES) return null;

            const extension = outputType === 'image/webp' ? 'webp' : 'jpg';
            const baseName = file.name.replace(/\.[^.]+$/, '');
            return new File([bestBlob], `${baseName}.${extension}`, {
                type: outputType,
                lastModified: Date.now(),
            });
        }

        if (coverImageInput) {
            coverImageInput.addEventListener('change', async () => {
                const file = coverImageInput.files && coverImageInput.files[0] ? coverImageInput.files[0] : null;
                setCoverImageHint('');
                if (!file || file.type === 'image/svg+xml' || file.size <= COVER_MAX_IMAGE_BYTES) return;

                setCoverImageHint('Cover melebihi 5MB. Sedang dicoba dikompres otomatis...');
                try {
                    const compressedFile = await compressCoverImage(file);
                    if (!compressedFile) {
                        coverImageInput.value = '';
                        setCoverImageHint('Cover tidak berhasil dikompres sampai 5MB. Coba pilih gambar lain atau kompres manual.', true);
                        return;
                    }
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    coverImageInput.files = dataTransfer.files;
                    setCoverImageHint(`Cover berhasil dikompres dari ${(file.size / 1024 / 1024).toFixed(2)} MB menjadi ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB.`);
                } catch (error) {
                    coverImageInput.value = '';
                    setCoverImageHint('Terjadi kesalahan saat kompres cover otomatis. Coba lagi dengan file lain.', true);
                }
            });
        }
    </script>
</body>
</html>
