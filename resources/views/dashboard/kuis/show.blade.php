<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kuis - Ruma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .page { max-width: 1000px; margin: 0 auto; padding: 0 1rem; }
        .card { background: var(--color-white); border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 1rem; }
        .title { font-size: 1.5rem; font-weight: 700; }
        .desc { color: var(--color-text-light); margin-top: 0.35rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.1rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: var(--color-accent); color: #1F2937; }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .tag { display: inline-block; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.6rem; border-radius: 999px; background: rgba(248, 184, 3, 0.15); color: #B35E00; }
        ul { padding-left: 1.2rem; }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Detail Kuis</h1>
            </header>
            <div class="content-area">
            <div class="page">
                <div class="card">
                    <div class="tag">Detail Kuis</div>
                    <div class="title" style="margin-top:0.5rem;">{{ $kuis->judul }}</div>
                    <div class="desc">{{ $kuis->deskripsi ?? 'Tanpa deskripsi.' }}</div>
                    <div class="desc" style="margin-top:0.5rem;">Mata Pelajaran: {{ $kuis->materi->judul ?? '-' }}</div>
                    <div class="desc">Materi: {{ $kuis->materiBab ? 'Materi ' . $kuis->materiBab->urutan . ' - ' . $kuis->materiBab->judul_bab : '-' }}</div>
                    <div class="desc">Status: {{ $kuis->status_aktif ? 'Aktif' : 'Nonaktif' }}</div>
                    <div class="actions" style="margin-top:1rem;">
                        <a href="{{ route('kuis.edit', $kuis->id) }}" class="btn btn-secondary">Edit</a>
                        <a href="{{ route('kuis.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                @foreach($kuis->pertanyaan as $index => $pertanyaan)
                    <div class="card">
                        <div class="tag">Pertanyaan {{ $index + 1 }}</div>
                        <div class="title" style="font-size:1.1rem; margin-top:0.5rem;">{{ $pertanyaan->pertanyaan }}</div>
                        <ul style="margin-top:0.75rem;">
                            @foreach($pertanyaan->opsi as $opsi)
                                <li class="desc">
                                    <strong>{{ $opsi->label }}.</strong> {{ $opsi->teks }}
                                    @if($opsi->benar)
                                        <span style="color:#16A34A; font-weight:600;">(Benar)</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
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
        lucide.createIcons();
    </script>
</body>
</html>

