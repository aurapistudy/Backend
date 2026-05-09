<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Level - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --color-primary: #1F2937;
            --color-primary-dark: #111827;
            --color-primary-light: #F9FAFB;
            --color-accent: #F8B803;
            --color-white: #FFFFFF;
            --color-gray-light: #F3F4F6;
            --color-gray: #E5E7EB;
            --color-text: #111827;
            --color-text-light: #6B7280;
            --sidebar-width: 280px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-gray-light);
            color: var(--color-text);
            overflow-x: hidden;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-circle {
            width: 50px;
            height: 50px;
            background: var(--color-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .logo-icon {
            width: 26px;
            height: 26px;
            color: var(--color-accent);
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--color-white);
            letter-spacing: 1px;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
            overflow-y: auto;
        }
        
        .nav-item {
            margin: 0.5rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            color: var(--color-white);
            text-decoration: none;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }

        .nav-item.active a {
            background: transparent;
            color: #FFFFFF;
            font-weight: 600;
            border-left: 4px solid var(--color-accent);
        }
        
        .nav-item:not(.active):hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #CBD5E1;
        }
        
        .logout-btn {
            margin: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: var(--color-white);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
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
        
        .header-bar {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #FFFFFF;
        }
        
        .content-area {
            flex: 1;
            padding: 2rem;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-circle">
                        <span class="logo-icon">👋</span>
                    </div>
                    <div class="logo-text">Ruma</div>
                </div>
            </div>
            
            @include('components.sidebar')
            
           <div class="logout-btn" onclick="handleLogout()" style="display:flex; align-items:center; gap:8px; justify-content:center;">
    <i data-lucide="log-out"></i>
    <span>Keluar</span>
</div>

        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
           
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Tambah Level Baru</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('level.index') }}" class="back-link">
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

                <form action="{{ route('level.store') }}" method="POST" class="form-container">
                    @csrf
                    <div class="section-title"><i data-lucide="layers"></i> Informasi Level</div>
                    <div class="section-subtitle">Masukkan nama level dan deskripsi singkat.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Nama Level <span class="required">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" required placeholder="misal: Kelas 4 SD, Kelas 1 SMP">
                        <span class="hint">Gunakan format yang mudah dikenali.</span>
                        @error('nama')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-textarea" placeholder="Deskripsi level (opsional)">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', true) ? 'checked' : '' }}>
                            <span>Status Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Level
                        </button>
                        <a href="{{ route('level.index') }}" class="btn btn-secondary">
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

