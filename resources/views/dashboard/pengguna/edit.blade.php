<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
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
            color: var(--color-brown-dark);
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
            color: var(--color-primary-dark);
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
                <h1 class="header-title">Edit Pengguna</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="{{ route('pengguna.show', $pengguna->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail
                    </a>
                    <a href="{{ route('pengguna.index') }}" class="back-link">
                        <i data-lucide="list"></i>
                        Daftar Pengguna
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

                <form action="{{ route('pengguna.update', $pengguna->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf
                    @method('PUT')
                    
                    <div class="section-title"><i data-lucide="user-cog"></i> Informasi Akun</div>
                    <div class="section-subtitle">Perbarui nama, email, dan peran pengguna.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Nama <span class="required">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $pengguna->nama) }}" class="form-input" required>
                        @error('nama')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $pengguna->email) }}" class="form-input" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" name="kata_sandi" class="form-input" minlength="6">
                        <small class="hint">Kosongkan jika tidak ingin mengubah kata sandi. Minimal 6 karakter.</small>
                        @error('kata_sandi')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="section-title" style="margin-top: 1.5rem;"><i data-lucide="layers"></i> Peran & Profil</div>
                    <div class="section-subtitle">Lengkapi data tambahan sesuai peran.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Peran <span class="required">*</span>
                        </label>
                        <select name="peran" id="peran" class="form-select" required>
                            <option value="">Pilih Peran</option>
                            <option value="siswa" {{ old('peran', $pengguna->peran) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="guru" {{ old('peran', $pengguna->peran) == 'guru' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                            <option value="admin" {{ old('peran', $pengguna->peran) == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('peran')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $pengguna->siswa->nama_sekolah ?? $pengguna->guru->nama_sekolah ?? '') }}" class="form-input">
                        @error('nama_sekolah')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="guru_mapel_fields" style="display: {{ old('peran', $pengguna->peran) == 'guru' ? 'block' : 'none' }}; margin-top: 1rem;">
                        @if($tahunAkademikAktif)
                            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem;">
                                Penugasan diperbarui untuk tahun akademik <strong>{{ $tahunAkademikAktif->nama }}</strong>. Riwayat tahun sebelumnya tetap tersimpan.
                            </div>
                        @else
                            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem;">
                                Belum ada tahun akademik aktif. Atur dulu di menu <strong>Tahun Akademik</strong>.
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label">Mata Pelajaran yang Dikelola <span class="required">*</span></label>
                            <p style="font-size: 0.85rem; color: #6B7280; margin-bottom: 0.5rem;">Daftar ini sama dengan item di menu <strong>Mata Pelajaran</strong>.</p>
                            <div style="display: grid; gap: 0.5rem; max-height: 220px; overflow-y: auto; border: 1px solid #E5E7EB; border-radius: 10px; padding: 0.75rem;">
                                @php
                                    $selectedMateri = old('materi_ids', $assignedMateriIds ?? []);
                                @endphp
                                @forelse($materiList as $materi)
                                    <label class="form-checkbox" style="justify-content: flex-start;">
                                        <input type="checkbox" name="materi_ids[]" value="{{ $materi->id }}"
                                            {{ in_array($materi->id, $selectedMateri) ? 'checked' : '' }}>
                                        <span>{{ $materi->judul }}</span>
                                    </label>
                                @empty
                                    <span style="color: #6B7280; font-size: 0.9rem;">Belum ada mata pelajaran aktif.</span>
                                @endforelse
                            </div>
                            @error('materi_ids')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div id="siswa_fields" style="display: {{ old('peran', $pengguna->peran) == 'siswa' ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label class="form-label">Jenjang</label>
                            <input type="text" name="jenjang" value="{{ old('jenjang', $pengguna->siswa->jenjang ?? '') }}" placeholder="misal: Kelas 4 SD" class="form-input">
                            @error('jenjang')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan', $pengguna->siswa->catatan ?? '') }}</textarea>
                            @error('catatan')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $pengguna->status_aktif) ? 'checked' : '' }}>
                            <span>Status Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('pengguna.show', $pengguna->id) }}" class="btn btn-secondary">
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
        document.getElementById('peran').addEventListener('change', function() {
            const peran = this.value;
            const siswaFields = document.getElementById('siswa_fields');
            const guruMapelFields = document.getElementById('guru_mapel_fields');

            siswaFields.style.display = peran === 'siswa' ? 'block' : 'none';
            guruMapelFields.style.display = peran === 'guru' ? 'block' : 'none';
        });

        // Trigger on page load if value exists
        if (document.getElementById('peran').value) {
            document.getElementById('peran').dispatchEvent(new Event('change'));
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



