<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penugasan Guru - {{ $tahunAkademik->periodeLabel() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
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

        .period-banner {
            background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 100%);
            border: 1px solid #BFDBFE;
            border-radius: 16px;
            padding: 1.1rem 1.35rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            color: #1E3A8A;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        .period-banner-main {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            flex: 1;
            min-width: 240px;
        }

        .period-banner i { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }
        .period-banner strong { display: block; margin-bottom: 0.25rem; font-size: 1.02rem; }
        .period-banner span { font-size: 0.88rem; opacity: 0.92; line-height: 1.45; }

        .stats-row {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(191,219,254,0.9);
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .penugasan-shell {
            background: var(--color-white);
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .penugasan-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 480px;
        }

        .guru-sidebar {
            border-right: 1px solid var(--color-gray);
            background: #FAFBFC;
            display: flex;
            flex-direction: column;
        }

        .guru-sidebar-head {
            padding: 1.25rem 1.1rem 0.85rem;
            border-bottom: 1px solid var(--color-gray);
        }

        .guru-sidebar-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--color-text);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--color-text-light);
        }

        .search-input {
            width: 100%;
            padding: 0.7rem 0.85rem 0.7rem 2.35rem;
            border: 1px solid var(--color-gray);
            border-radius: 10px;
            font-size: 0.88rem;
            background: var(--color-white);
        }

        .search-input:focus {
            outline: none;
            border-color: #93C5FD;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }

        .guru-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.65rem;
        }

        .guru-item {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 12px;
            padding: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            text-align: left;
            transition: all 0.18s ease;
            margin-bottom: 0.35rem;
        }

        .guru-item:hover { background: #F3F4F6; }

        .guru-item.active {
            background: #FFFFFF;
            border-color: #BFDBFE;
            box-shadow: 0 4px 14px rgba(59,130,246,0.1);
        }

        .guru-item-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FFF9E6;
            color: #B45309;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .guru-item-body { min-width: 0; flex: 1; }

        .guru-item-name {
            font-weight: 600;
            font-size: 0.92rem;
            color: var(--color-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .guru-item-email {
            font-size: 0.78rem;
            color: var(--color-text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .guru-count-badge {
            min-width: 28px;
            height: 28px;
            border-radius: 999px;
            background: #E5E7EB;
            color: #374151;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .guru-item.active .guru-count-badge,
        .guru-count-badge.has-assignment {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .assignment-panel {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .assignment-panel-head {
            padding: 1rem 1.25rem 0.85rem;
            border-bottom: 1px solid var(--color-gray);
        }

        .assignment-panel-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .assignment-panel-sub {
            color: var(--color-text-light);
            font-size: 0.86rem;
            margin-bottom: 1rem;
        }

        .toolbar-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .toolbar-row .search-box { flex: 1; min-width: 200px; }

        .tool-btn {
            border: 1px solid var(--color-gray);
            background: var(--color-white);
            color: var(--color-text);
            border-radius: 10px;
            padding: 0.62rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.18s ease;
        }

        .tool-btn:hover {
            border-color: #93C5FD;
            color: #1D4ED8;
            background: #F8FAFC;
        }

        .tool-btn i { width: 15px; height: 15px; }

        .assignment-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 0.85rem 1.25rem 1rem;
            background: #FCFCFD;
        }

        .guru-panel { display: none; }
        .guru-panel.active { display: block; }

        .materi-table-wrap {
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            background: var(--color-white);
            overflow: hidden;
        }

        .materi-table {
            width: 100%;
            border-collapse: collapse;
        }

        .materi-table thead {
            background: #F9FAFB;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .materi-table th {
            padding: 0.55rem 0.85rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: #6B7280;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .materi-table th:first-child { width: 42px; text-align: center; }

        .materi-row {
            cursor: pointer;
            transition: background 0.12s ease;
        }

        .materi-row:hover { background: #F9FAFB; }

        .materi-row.is-selected { background: #FFFBEB; }

        .materi-row.is-hidden { display: none; }

        .materi-table td {
            padding: 0.45rem 0.85rem;
            border-bottom: 1px solid #F3F4F6;
            font-size: 0.86rem;
            color: var(--color-text);
            vertical-align: middle;
        }

        .materi-table td:first-child { text-align: center; }

        .materi-table tbody tr:last-child td { border-bottom: none; }

        .materi-table input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #F8B803;
        }

        .materi-name {
            font-weight: 500;
            line-height: 1.3;
        }

        .materi-table-scroll {
            max-height: 420px;
            overflow-y: auto;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--color-text-light);
        }

        .empty-state i {
            width: 44px;
            height: 44px;
            margin-bottom: 0.85rem;
            opacity: 0.45;
        }

        .empty-filter {
            display: none;
            text-align: center;
            padding: 2rem;
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .sticky-footer {
            border-top: 1px solid var(--color-gray);
            padding: 1rem 1.5rem;
            background: var(--color-white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-note {
            font-size: 0.84rem;
            color: var(--color-text-light);
        }

        .footer-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.78rem 1.25rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
        }

        .btn-primary { background: var(--color-primary); color: #ffffff; }
        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .btn-secondary:hover { background: #D1D5DB; }

        .selection-summary {
            font-size: 0.84rem;
            color: #1D4ED8;
            font-weight: 600;
            margin-top: 0.75rem;
        }

        @media (max-width: 900px) {
            .penugasan-layout { grid-template-columns: 1fr; min-height: auto; }
            .guru-sidebar { border-right: none; border-bottom: 1px solid var(--color-gray); max-height: 280px; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Penugasan Guru Mapel</h1>
            </header>

            <div class="content-area">
                <div style="margin-bottom: 1.25rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <a href="{{ route('tahun-akademik.show', $tahunAkademik->id) }}" class="back-link">
                        <i data-lucide="arrow-left"></i>
                        Kembali ke Detail TA
                    </a>
                    <a href="{{ route('tahun-akademik.index') }}" class="back-link">
                        <i data-lucide="list"></i>
                        Daftar Tahun Akademik
                    </a>
                </div>

                <div class="period-banner">
                    <div class="period-banner-main">
                        <i data-lucide="calendar-range"></i>
                        <div>
                            <strong>{{ $tahunAkademik->periodeLabel() }}</strong>
                            <span>
                                {{ $tahunAkademik->tanggal_mulai->format('d M Y') }} – {{ $tahunAkademik->tanggal_selesai->format('d M Y') }}.
                                Pilih guru di kiri, lalu tentukan mata pelajaran yang diampu.
                            </span>
                        </div>
                    </div>
                    @if($guruList->isNotEmpty())
                        <div class="stats-row">
                            <span class="stat-pill">{{ $guruList->count() }} guru mapel</span>
                            <span class="stat-pill">{{ $materiList->count() }} mata pelajaran</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('tahun-akademik.penugasan.update', $tahunAkademik->id) }}" method="POST" class="penugasan-shell" id="penugasanForm">
                    @csrf
                    @method('PUT')

                    @if($guruList->isEmpty())
                        <div class="empty-state">
                            <i data-lucide="user-x"></i>
                            <h3 style="margin-bottom:0.35rem;color:var(--color-text);">Belum ada guru mapel</h3>
                            <p style="margin-bottom:1rem;">Tambahkan guru mapel di menu Pengguna terlebih dahulu.</p>
                            <a href="{{ route('pengguna.create') }}" class="back-link">
                                <i data-lucide="user-plus"></i>
                                Tambah Guru Mapel
                            </a>
                        </div>
                    @else
                        <div class="penugasan-layout">
                            <aside class="guru-sidebar">
                                <div class="guru-sidebar-head">
                                    <div class="guru-sidebar-title">Guru Mapel</div>
                                    <div class="search-box">
                                        <i data-lucide="search"></i>
                                        <input type="text" id="guruSearch" class="search-input" placeholder="Cari nama atau email...">
                                    </div>
                                </div>
                                <div class="guru-list" id="guruList">
                                    @foreach($guruList as $index => $guru)
                                        @php
                                            $selected = old('penugasan.' . $guru->id, $assignedByGuru->get($guru->id, []));
                                            $initials = collect(explode(' ', $guru->nama))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('');
                                        @endphp
                                        <button type="button"
                                            class="guru-item {{ $index === 0 ? 'active' : '' }}"
                                            data-guru-id="{{ $guru->id }}"
                                            data-guru-name="{{ $guru->nama }}"
                                            data-guru-email="{{ $guru->email }}"
                                            data-search="{{ strtolower($guru->nama . ' ' . $guru->email) }}">
                                            <div class="guru-item-avatar">{{ strtoupper($initials ?: 'G') }}</div>
                                            <div class="guru-item-body">
                                                <div class="guru-item-name">{{ $guru->nama }}</div>
                                                <div class="guru-item-email">{{ $guru->email }}</div>
                                            </div>
                                            <span class="guru-count-badge {{ count($selected) > 0 ? 'has-assignment' : '' }}"
                                                data-count-for="{{ $guru->id }}">{{ count($selected) }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </aside>

                            <section class="assignment-panel">
                                @foreach($guruList as $index => $guru)
                                    @php
                                        $selected = old('penugasan.' . $guru->id, $assignedByGuru->get($guru->id, []));
                                    @endphp
                                    <div class="guru-panel {{ $index === 0 ? 'active' : '' }}" data-panel-for="{{ $guru->id }}">
                                        <div class="assignment-panel-head">
                                            <div class="assignment-panel-title">{{ $guru->nama }}</div>
                                            <div class="assignment-panel-sub">{{ $guru->email }}</div>

                                            <div class="toolbar-row">
                                                <div class="search-box">
                                                    <i data-lucide="search"></i>
                                                    <input type="text" class="search-input materi-search" placeholder="Cari mata pelajaran..." data-search-for="{{ $guru->id }}">
                                                </div>
                                                <button type="button" class="tool-btn select-all-btn" data-target="{{ $guru->id }}">
                                                    <i data-lucide="check-check"></i>
                                                    Pilih Semua
                                                </button>
                                                <button type="button" class="tool-btn clear-all-btn" data-target="{{ $guru->id }}">
                                                    <i data-lucide="x"></i>
                                                    Kosongkan
                                                </button>
                                            </div>
                                            <div class="selection-summary" data-summary-for="{{ $guru->id }}">
                                                {{ count($selected) }} dari {{ $materiList->count() }} mata pelajaran dipilih
                                            </div>
                                        </div>

                                        <div class="assignment-panel-body">
                                            @if($materiList->isEmpty())
                                                <div class="empty-state">
                                                    <i data-lucide="book-x"></i>
                                                    <p>Belum ada mata pelajaran aktif.</p>
                                                </div>
                                            @else
                                                <div class="empty-filter" data-empty-for="{{ $guru->id }}">Tidak ada mata pelajaran yang cocok dengan pencarian.</div>
                                                <div class="materi-table-wrap">
                                                    <div class="materi-table-scroll">
                                                        <table class="materi-table" data-grid-for="{{ $guru->id }}">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" class="toggle-visible-all" data-target="{{ $guru->id }}" title="Pilih yang tampil"></th>
                                                                    <th>Nama Mata Pelajaran</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($materiList as $materi)
                                                                    <tr class="materi-row {{ in_array($materi->id, $selected, true) ? 'is-selected' : '' }}"
                                                                        data-label="{{ strtolower($materi->judul) }}">
                                                                        <td>
                                                                            <input type="checkbox"
                                                                                name="penugasan[{{ $guru->id }}][]"
                                                                                value="{{ $materi->id }}"
                                                                                data-guru-id="{{ $guru->id }}"
                                                                                {{ in_array($materi->id, $selected, true) ? 'checked' : '' }}>
                                                                        </td>
                                                                        <td><span class="materi-name">{{ $materi->judul }}</span></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </section>
                        </div>

                        <div class="sticky-footer">
                            <div class="footer-note">Perubahan disimpan untuk seluruh guru mapel pada periode ini.</div>
                            <div class="footer-actions">
                                <a href="{{ route('tahun-akademik.show', $tahunAkademik->id) }}" class="btn btn-secondary">
                                    <i data-lucide="x"></i>
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="save"></i>
                                    Simpan Penugasan
                                </button>
                            </div>
                        </div>
                    @endif
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

        (function initPenugasanUi() {
            const guruItems = document.querySelectorAll('.guru-item');
            const guruPanels = document.querySelectorAll('.guru-panel');
            const totalMateri = {{ $materiList->count() }};

            function updateGuruStats(guruId) {
                const checked = document.querySelectorAll(`input[data-guru-id="${guruId}"]:checked`).length;
                const badge = document.querySelector(`[data-count-for="${guruId}"]`);
                const summary = document.querySelector(`[data-summary-for="${guruId}"]`);

                if (badge) {
                    badge.textContent = checked;
                    badge.classList.toggle('has-assignment', checked > 0);
                }
                if (summary) {
                    summary.textContent = `${checked} dari ${totalMateri} mata pelajaran dipilih`;
                }
            }

            guruItems.forEach(item => {
                item.addEventListener('click', () => {
                    const guruId = item.dataset.guruId;
                    guruItems.forEach(el => el.classList.remove('active'));
                    guruPanels.forEach(el => el.classList.remove('active'));
                    item.classList.add('active');
                    document.querySelector(`[data-panel-for="${guruId}"]`)?.classList.add('active');
                });
            });

            document.getElementById('guruSearch')?.addEventListener('input', (event) => {
                const query = event.target.value.trim().toLowerCase();
                guruItems.forEach(item => {
                    const visible = !query || (item.dataset.search || '').includes(query);
                    item.style.display = visible ? '' : 'none';
                });
            });

            document.querySelectorAll('.materi-search').forEach(input => {
                input.addEventListener('input', () => {
                    const guruId = input.dataset.searchFor;
                    const query = input.value.trim().toLowerCase();
                    const table = document.querySelector(`table[data-grid-for="${guruId}"]`);
                    const empty = document.querySelector(`[data-empty-for="${guruId}"]`);
                    if (!table) return;

                    let visibleCount = 0;
                    table.querySelectorAll('.materi-row').forEach(row => {
                        const visible = !query || (row.dataset.label || '').includes(query);
                        row.classList.toggle('is-hidden', !visible);
                        if (visible) visibleCount++;
                    });

                    if (empty) {
                        empty.style.display = visibleCount === 0 ? 'block' : 'none';
                    }
                });
            });

            document.querySelectorAll('.select-all-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const guruId = btn.dataset.target;
                    document.querySelectorAll(`input[data-guru-id="${guruId}"]`).forEach(input => {
                        const row = input.closest('.materi-row');
                        if (!row || row.classList.contains('is-hidden')) return;
                        input.checked = true;
                        row.classList.add('is-selected');
                    });
                    updateGuruStats(guruId);
                });
            });

            document.querySelectorAll('.clear-all-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const guruId = btn.dataset.target;
                    document.querySelectorAll(`input[data-guru-id="${guruId}"]`).forEach(input => {
                        input.checked = false;
                        input.closest('.materi-row')?.classList.remove('is-selected');
                    });
                    updateGuruStats(guruId);
                });
            });

            document.querySelectorAll('.toggle-visible-all').forEach(toggle => {
                toggle.addEventListener('change', () => {
                    const guruId = toggle.dataset.target;
                    const checked = toggle.checked;
                    document.querySelectorAll(`input[data-guru-id="${guruId}"]`).forEach(input => {
                        const row = input.closest('.materi-row');
                        if (!row || row.classList.contains('is-hidden')) return;
                        input.checked = checked;
                        row.classList.toggle('is-selected', checked);
                    });
                    updateGuruStats(guruId);
                });
            });

            document.querySelectorAll('.materi-row').forEach(row => {
                row.addEventListener('click', (event) => {
                    if (event.target.tagName === 'INPUT') return;
                    const input = row.querySelector('input[type="checkbox"]');
                    if (!input) return;
                    input.checked = !input.checked;
                    row.classList.toggle('is-selected', input.checked);
                    updateGuruStats(input.dataset.guruId);
                });
            });

            document.querySelectorAll('input[data-guru-id]').forEach(input => {
                input.addEventListener('change', () => {
                    input.closest('.materi-row')?.classList.toggle('is-selected', input.checked);
                    updateGuruStats(input.dataset.guruId);
                });
            });

            @foreach($guruList as $guru)
                updateGuruStats('{{ $guru->id }}');
            @endforeach
        })();
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>
