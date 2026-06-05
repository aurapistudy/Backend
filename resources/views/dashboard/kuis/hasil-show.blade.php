<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Kuis - Ruma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .card { background: var(--color-white); border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 1rem; }
        .tag { display: inline-block; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.6rem; border-radius: 999px; background: rgba(248, 184, 3, 0.15); color: #B35E00; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.1rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: var(--color-accent); color: #1F2937; }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .btn-outline-green { background: #E9F9EF; color: #166534; border: 2px solid #22C55E; padding: 0.65rem 1rem; border-radius: 10px; gap: 0.4rem; }
        .btn-outline-green:hover { background: #DFF5E7; }
        .badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-approved { background: #DCFCE7; color: #166534; }
        .badge-rejected { background: #FEE2E2; color: #991B1B; }
        .info-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:0.75rem; margin-top:0.75rem; }
        .info-item { background: #F9FAFB; border: 1px solid var(--color-gray); border-radius: 12px; padding: 0.75rem; }
        .info-label { font-size: 0.8rem; color: var(--color-text-light); margin-bottom: 0.25rem; }
        .info-value { font-weight: 700; }
        .koreksi-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem; }
        .koreksi-row textarea { width: 100%; padding: 0.75rem; border-radius: 10px; border: 1px solid var(--color-gray); }
        .form-group { margin-top: 0.5rem; }
        .breadcrumb { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--color-text-light); flex-wrap: wrap; }
        .breadcrumb a { color: #166534; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        @media (max-width: 900px) {.koreksi-row { grid-template-columns: 1fr; }}
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Koreksi Kuis</h1>
            </header>
            <div class="content-area">
                <nav class="breadcrumb" aria-label="Navigasi">
                    <a href="{{ route('kuis.hasil.index') }}">Hasil Kuis</a>
                    @if($hasil->kuis)
                        <span>/</span>
                        <a href="{{ route('kuis.hasil.kuis', $hasil->kuis_id) }}">{{ $hasil->kuis->judul }}</a>
                    @endif
                    @if($hasil->pengguna)
                        <span>/</span>
                        <span>{{ $hasil->pengguna->nama }}</span>
                    @endif
                    <span>/</span>
                    <span>Koreksi</span>
                </nav>

                @if(session('success'))
                    <div class="card">
                        <span class="tag">Sukses</span>
                        <p style="margin-top:0.5rem;">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="card">
                    <span class="tag">Ringkasan</span>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Siswa</div>
                            <div class="info-value">{{ $hasil->pengguna->nama ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kuis</div>
                            <div class="info-value">{{ $hasil->kuis->judul ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Mata Pelajaran</div>
                            <div class="info-value">{{ $hasil->kuis->materi->judul ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Skor</div>
                            <div class="info-value">{{ $hasil->skor }}% ({{ $hasil->total_benar }}/{{ $hasil->total_pertanyaan }})</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('kuis.hasil.update', $hasil->id) }}" method="post">
                    @csrf
                    @foreach($hasil->jawaban as $index => $jawaban)
                        @php
                            $p = $jawaban->pertanyaan;
                        @endphp
                        <div class="card">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem;">
                                <span class="tag">Soal {{ $index + 1 }} - {{ $p?->tipe }}</span>
                                @php
                                    $status = $jawaban->status_koreksi ?? 'pending';
                                @endphp
                                @if($status === 'approved')
                                    <span class="badge badge-approved">Disetujui</span>
                                @elseif($status === 'rejected')
                                    <span class="badge badge-rejected">Ditolak</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            </div>
                            <h3 style="margin-top:0.5rem;">{{ $p?->pertanyaan }}</h3>

                            @if($p && in_array($p->tipe, ['essay','speaking']))
                                <div class="koreksi-row">
                                    <div>
                                        <strong>Jawaban Siswa</strong>
                                        <textarea rows="4" readonly>{{ $jawaban->jawaban_teks }}</textarea>
                                    </div>
                                    <div>
                                        @if($p->tipe === 'essay')
                                            <strong>Keyword</strong>
                                            <textarea rows="4" readonly>{{ $p->keyword }}</textarea>
                                        @else
                                            <strong>Jawaban Target</strong>
                                            <textarea rows="4" readonly>{{ $p->jawaban_teks }}</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Skor Auto (0-100)</label>
                                    <input type="number" min="0" max="100" name="koreksi[{{ $jawaban->id }}][skor_auto]" value="{{ $jawaban->skor_auto ?? 0 }}">
                                </div>
                                <div class="form-group">
                                    <label>Status Koreksi</label>
                                    <select name="koreksi[{{ $jawaban->id }}][status_koreksi]">
                                        <option value="pending" {{ $jawaban->status_koreksi === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $jawaban->status_koreksi === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="rejected" {{ $jawaban->status_koreksi === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                            @else
                                <p>Jawaban: {{ $jawaban->opsi?->label ?? '-' }}</p>
                                <p>Status: {{ $jawaban->benar ? 'Benar' : 'Salah' }}</p>
                            @endif
                        </div>
                    @endforeach

                    <div class="card" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <button class="btn btn-outline-green" type="submit">
                            <i data-lucide="save"></i>
                            Simpan Koreksi
                        </button>
                        @if($hasil->kuis_id)
                            <a class="btn btn-secondary" href="{{ route('kuis.hasil.kuis', $hasil->kuis_id) }}">Kembali ke Daftar Siswa</a>
                        @else
                            <a class="btn btn-secondary" href="{{ route('kuis.hasil.index') }}">Kembali</a>
                        @endif
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
</body>
</html>

