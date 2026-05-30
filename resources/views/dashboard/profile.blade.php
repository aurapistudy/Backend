<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        /* Profile Card */
        .profile-card {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--color-gray);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-accent) 0%, #E6A500 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #1F2937;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(248, 184, 3, 0.3);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(248, 184, 3, 0.4);
        }
        
        .profile-avatar-edit {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            background: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            color: #1F2937;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }
        
        .profile-email {
            font-size: 1.1rem;
            color: var(--color-text-light);
            margin-bottom: 0.75rem;
        }
        
        .profile-role {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #FFF9E6;
            color: var(--color-accent);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .edit-profile-btn {
            padding: 0.7rem 1.2rem;
            background: var(--color-accent);
            color: #1F2937;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .edit-profile-btn:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }
        
        /* Profile Details */
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .detail-card {
            background: #F9FAFB;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--color-text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .detail-value {
            font-size: 1.1rem;
            color: var(--color-text);
            font-weight: 500;
        }
        
        /* Form Section */
        .form-section {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .form-section-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--color-gray);
            border-radius: 12px;
            background: var(--color-white);
            font-size: 1rem;
            color: var(--color-text);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }
        
        .form-input:disabled {
            background: var(--color-gray-light);
            cursor: not-allowed;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-primary {
            padding: 0.75rem 2rem;
            background: var(--color-accent);
            color: #1F2937;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(248, 184, 3, 0.4);
        }
        
        .btn-secondary {
            padding: 0.75rem 2rem;
            background: var(--color-gray);
            color: var(--color-text);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: #D1D5DB;
        }
        
        .edit-mode {
            display: none;
        }
        
        .edit-mode.active {
            display: block;
        }
        
        .view-mode {
            display: block;
        }
        
        .view-mode.hidden {
            display: none;
        }
@media (max-width: 768px) {.profile-header { flex-direction: column; text-align: center; } .profile-details { grid-template-columns: 1fr; } .form-actions { flex-direction: column; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Profile</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar" onclick="document.getElementById('fotoInput').click()">
                            @if($user->foto_profil && file_exists(public_path($user->foto_profil)))
                                <img src="{{ asset($user->foto_profil) }}" alt="Foto Profil" id="avatarImage" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                <span id="avatarInitials" style="display: none;">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                            @else
                                <span id="avatarInitials">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                                <img src="" alt="Foto Profil" id="avatarImage" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @endif
                            <div class="profile-avatar-edit">
                                <i data-lucide="camera"></i>
                            </div>
                        </div>
                        <input type="file" id="fotoInput" name="foto_profil" accept="image/*" style="display: none;" onchange="handleFotoUpload(event)">
                        <div class="profile-info">
                            <h2 class="profile-name" id="profileName">{{ $user->nama }}</h2>
                            <p class="profile-email" id="profileEmail">{{ $user->email }}</p>
                            <span class="profile-role" id="profileRole">{{ ucfirst($user->peran) }}</span>
                        </div>
                        <button class="edit-profile-btn" id="editBtn" onclick="toggleEditMode()">
                            <i data-lucide="pencil"></i>
                            <span class="edit-label">Edit Profile</span>
                        </button>
                    </div>
                    
                    <!-- View Mode -->
                    <div class="view-mode" id="viewMode">
                        <div class="profile-details">
                            <div class="detail-card">
                                <div class="detail-label">Nama Lengkap</div>
                                <div class="detail-value" id="viewFullName">{{ $user->nama }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Email</div>
                                <div class="detail-value" id="viewEmail">{{ $user->email }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Role</div>
                                <div class="detail-value" id="viewRole">{{ ucfirst($user->peran) }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Tanggal Bergabung</div>
                                <div class="detail-value" id="viewJoinDate">{{ $user->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Status</div>
                                <div class="detail-value" id="viewStatus">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: {{ $user->status_aktif ? '#E8F5E9' : '#FFEBEE' }}; color: {{ $user->status_aktif ? '#388E3C' : '#D32F2F' }};">
                                        {{ $user->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            @if($user->siswa)
                            <div class="detail-card">
                                <div class="detail-label">Nama Sekolah</div>
                                <div class="detail-value">{{ $user->siswa->nama_sekolah ?? '-' }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Jenjang</div>
                                <div class="detail-value">{{ $user->siswa->jenjang ?? '-' }}</div>
                            </div>
                            @endif
                            @if($user->guru)
                            <div class="detail-card">
                                <div class="detail-label">Nama Sekolah</div>
                                <div class="detail-value">{{ $user->guru->nama_sekolah ?? '-' }}</div>
                            </div>
                            @endif
                            @if($user->peran === 'guru')
                            <div class="detail-card" style="grid-column: 1 / -1;">
                                <div class="detail-label">
                                    Mapel Tahun {{ $tahunAkademikAktif?->nama ?? 'Aktif' }}
                                </div>
                                <div class="detail-value">
                                    @if($materiTahunAktif->isNotEmpty())
                                        {{ $materiTahunAktif->pluck('judul')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($user->peran === 'guru' && $riwayatPenugasan->isNotEmpty())
                        <div class="form-section" style="margin-top: 1.5rem;">
                            <h3 class="form-section-title"><i data-lucide="history"></i> Riwayat Penugasan</h3>
                            <div class="details-grid">
                                @foreach($riwayatPenugasan as $tahunNama => $penugasan)
                                    <div class="detail-card" style="grid-column: 1 / -1;">
                                        <div class="detail-label">{{ $tahunNama }}</div>
                                        <div class="detail-value">
                                            {{ $penugasan->pluck('materi.judul')->filter()->join(', ') ?: '-' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Edit Mode -->
                    <div class="edit-mode" id="editMode">
                        <div class="form-section">
                            <h3 class="form-section-title"><i data-lucide="user-cog"></i> Edit Informasi Profile</h3>
                            
                            <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label class="form-label" for="editFullName">Nama Lengkap</label>
                                    <input 
                                        type="text" 
                                        id="editFullName" 
                                        name="nama"
                                        class="form-input" 
                                        value="{{ old('nama', $user->nama) }}"
                                        required
                                    >
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="editEmail">Email</label>
                                    <input 
                                        type="email" 
                                        id="editEmail" 
                                        name="email"
                                        class="form-input" 
                                        value="{{ old('email', $user->email) }}"
                                        required
                                    >
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn-secondary" onclick="cancelEdit()">
                                        <i data-lucide="x"></i>
                                        Batal
                                    </button>
                                    <button type="submit" class="btn-primary">
                                        <i data-lucide="save"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="form-section" style="margin-top: 2rem;">
                            <h3 class="form-section-title"><i data-lucide="key-round"></i> Ubah Kata Sandi</h3>
                            
                            <form action="{{ route('profile.password.update') }}" method="POST" id="passwordForm">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label class="form-label" for="currentPassword">Kata Sandi Saat Ini</label>
                                    <input 
                                        type="password" 
                                        id="currentPassword" 
                                        name="kata_sandi_lama"
                                        class="form-input" 
                                        placeholder="Masukkan kata sandi saat ini"
                                        required
                                    >
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="newPassword">Kata Sandi Baru</label>
                                    <input 
                                        type="password" 
                                        id="newPassword" 
                                        name="kata_sandi_baru"
                                        class="form-input" 
                                        placeholder="Masukkan kata sandi baru"
                                        required
                                    >
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="confirmPassword">Konfirmasi Kata Sandi Baru</label>
                                    <input 
                                        type="password" 
                                        id="confirmPassword" 
                                        name="kata_sandi_konfirmasi"
                                        class="form-input" 
                                        placeholder="Konfirmasi kata sandi baru"
                                        required
                                    >
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">
                                        <i data-lucide="shield-check"></i>
                                        Ubah Kata Sandi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    {{-- Include Modal Component --}}
    @include('components.modal')
    
    <script>
        // Toggle Edit Mode
        function toggleEditMode() {
            const viewMode = document.getElementById('viewMode');
            const editMode = document.getElementById('editMode');
            const editBtn = document.getElementById('editBtn');
            
            viewMode.classList.toggle('hidden');
            editMode.classList.toggle('active');
            
            const label = editBtn.querySelector('.edit-label');
            if (editMode.classList.contains('active')) {
                label.textContent = 'Batal Edit';
            } else {
                label.textContent = 'Edit Profile';
            }
        }
        
        // Cancel Edit
        function cancelEdit() {
            toggleEditMode();
            // Reset form values to original
            document.getElementById('editFullName').value = '{{ $user->nama }}';
            document.getElementById('editEmail').value = '{{ $user->email }}';
        }
        
        // Handle Avatar Click
        function handleAvatarClick() {
            showInfoToast('Fitur Upload', 'Untuk mengubah foto profil, gunakan tombol Edit Profile dan upload melalui form edit.');
        }
        
        // Handle Foto Upload - disabled for now, use profile edit form instead
        function handleFotoUpload(event) {
            // Prevent upload, use edit form instead
            event.preventDefault();
            showInfoToast('Info', 'Silakan gunakan tombol Edit Profile untuk mengubah foto.');
        }
        
        // Handle Update Profile - Form will submit normally to backend
        // No need for custom handler, form submits to route('profile.update')
        
        // Handle Change Password - Form will submit normally to backend
        // No need for custom handler, form submits to route('profile.password.update')
        
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





