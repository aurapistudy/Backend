<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kuis - Ruma</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .page { max-width: 1000px; margin: 0 auto; padding: 0 1rem; }
        .card { background: var(--color-white); border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 1rem; }
        .card-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .card-title { font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
        .card-desc { color: var(--color-text-light); font-size: 0.95rem; margin-top: 0.25rem; }
        .title { font-size: 1.5rem; font-weight: 700; }
        .desc { color: var(--color-text-light); margin-top: 0.35rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.7rem 1.1rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; gap: 0.4rem; }
        .btn-primary { background: var(--color-accent); color: #1F2937; }
        .btn-secondary { background: var(--color-gray); color: var(--color-text); }
        .btn-outline { background: #FFFDF5; color: #92400E; border: 2px solid #F8B803; }
        .btn-outline:hover { background: #FFF5DB; }
        .btn-sm { padding: 0.4rem 0.7rem; border-radius: 8px; font-size: 0.85rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.4rem; }
        input[type="text"], textarea, select { width: 100%; padding: 0.75rem 0.9rem; border-radius: 10px; border: 1px solid var(--color-gray); background: #fff; }
        input[type="text"]:focus, textarea:focus, select:focus { outline: none; border-color: #F8B803; box-shadow: 0 0 0 3px rgba(248, 184, 3, 0.15); }
        .question { border: 1px solid var(--color-gray); border-radius: 14px; padding: 1.1rem; margin-top: 1rem; background: #FFFEF7; border-left: 4px solid var(--color-accent); }
        .option-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem; }
        .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .error { color: #B91C1C; font-size: 0.9rem; margin-top: 0.35rem; }
        .section-title { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
        .section-subtitle { color: var(--color-text-light); font-size: 0.95rem; margin-top: 0.25rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
        .field-hint { font-size: 0.8rem; color: var(--color-text-light); margin-top: 0.25rem; }
        .divider { height: 1px; background: var(--color-gray); margin: 1rem 0; }
        .question-header { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem; }
        .question-title { font-weight: 700; }
        .question-badge { font-size: 0.75rem; background: rgba(248, 184, 3, 0.15); color: #B35E00; padding: 0.2rem 0.5rem; border-radius: 999px; }
        .section-note { background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 12px; padding: 0.75rem 1rem; font-size: 0.9rem; color: var(--color-text-light); }
        .form-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: flex-start; }
        .btn-ghost { background: transparent; border: 1px solid var(--color-gray); color: var(--color-text); }
        .btn-ghost:hover { background: #F9FAFB; }
        .inline-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; }
        .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .type-picker { margin-bottom: 1rem; }
        .type-picker-title { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.7rem; font-weight: 700; color: var(--color-text); }
        .type-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; }
        .type-card { position: relative; display: flex; align-items: flex-start; gap: 0.75rem; min-height: 92px; padding: 0.9rem; border: 1px solid #E5E7EB; border-radius: 12px; background: #FFFFFF; color: var(--color-text); text-align: left; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease, background 0.2s ease; }
        .type-card:hover { transform: translateY(-1px); border-color: rgba(248, 184, 3, 0.65); box-shadow: 0 8px 20px rgba(17, 24, 39, 0.08); }
        .type-card.active { background: #FFF9E6; border-color: var(--color-accent); box-shadow: 0 0 0 3px rgba(248, 184, 3, 0.16); }
        .type-card-icon { flex: 0 0 auto; width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: #F3F4F6; color: #374151; }
        .type-card.active .type-card-icon { background: var(--color-accent); color: #1F2937; }
        .type-card-title { display: block; font-weight: 700; margin-bottom: 0.2rem; }
        .type-card-desc { display: block; color: var(--color-text-light); font-size: 0.8rem; line-height: 1.35; }
        .type-card-check { position: absolute; right: 0.65rem; top: 0.65rem; width: 18px; height: 18px; border-radius: 50%; border: 1px solid #D1D5DB; background: #FFFFFF; }
        .type-card.active .type-card-check { border-color: var(--color-accent); background: var(--color-accent); box-shadow: inset 0 0 0 4px #FFFFFF; }
        .ai-generate-panel { border: 1px solid #FDE68A; background: linear-gradient(180deg, #FFFBEF 0%, #FFFFFF 100%); border-radius: 14px; padding: 1rem; margin-top: 1rem; }
        .ai-generate-toolbar { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; align-items: end; }
        .ai-generate-status { display: none; margin-top: 0.85rem; padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.92rem; }
        .ai-generate-status.info { display: block; background: #FFF7D6; color: #8A6500; }
        .ai-generate-status.success { display: block; background: #ECFDF3; color: #166534; }
        .ai-generate-status.error { display: block; background: #FEF2F2; color: #B91C1C; }
        .ai-generate-note { margin-top: 0.8rem; font-size: 0.84rem; color: var(--color-text-light); }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Buat Kuis</h1>
            </header>
            <div class="content-area">
            <div class="page">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="title"><i data-lucide="clipboard-list"></i> Buat Kuis</div>
                            <div class="desc">Lengkapi informasi kuis dan tambahkan pertanyaan.</div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="card" style="border-left:4px solid #B91C1C;">
                        <div class="desc">Periksa kembali input berikut.</div>
                        <ul style="margin-top:0.5rem; padding-left:1.2rem;">
                            @foreach($errors->all() as $error)
                                <li class="error">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

        <form action="{{ route('kuis.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="section-title"><i data-lucide="file-text"></i> Informasi Kuis</div>
                                <div class="section-subtitle">Atur judul, mata pelajaran, dan status kuis.</div>
                            </div>
                            <span class="question-badge">Wajib</span>
                        </div>
                        <div class="divider"></div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="judul">Judul Kuis</label>
                                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required>
                                <div class="field-hint">Contoh: Kuis Bahasa Indonesia Materi 1</div>
                            </div>
                            <div class="form-group">
                                <label for="materi_id">Mata Pelajaran (Opsional)</label>
                                <select id="materi_id" name="materi_id">
                                    <option value="">-- Tanpa Mata Pelajaran --</option>
                                    @foreach($materiList as $materi)
                                        <option value="{{ $materi->id }}" {{ old('materi_id', $prefillMateriId ?? '') == $materi->id ? 'selected' : '' }}>{{ $materi->judul }}</option>
                                    @endforeach
                                </select>
                                <div class="field-hint">Pilih mata pelajaran jika kuis terkait mata pelajaran tertentu.</div>
                            </div>
                            <div class="form-group">
                                <label for="materi_bab_id">Materi (Opsional)</label>
                                <select id="materi_bab_id" name="materi_bab_id">
                                    <option value="">-- Pilih Materi --</option>
                                    @foreach($materiList as $materi)
                                        @foreach($materi->bab as $bab)
                                            <option value="{{ $bab->id }}" data-materi-id="{{ $materi->id }}" {{ old('materi_bab_id', $prefillMateriBabId ?? '') == $bab->id ? 'selected' : '' }}>
                                                {{ $materi->judul }} - Materi {{ $bab->urutan }}: {{ $bab->judul_bab }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <div class="field-hint">Jika dipilih, kuis akan menempel ke materi tertentu di dalam mata pelajaran.</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Status Kuis</label>
                            <div class="inline-row">
                                <label style="display:flex; align-items:center; gap:0.5rem; font-weight:600;">
                                    <input type="hidden" name="status_aktif" value="0">
                                    <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', true) ? 'checked' : '' }}>
                                    Aktifkan Kuis
                                </label>
                                <div class="field-hint">Jika nonaktif, kuis tidak tampil ke siswa.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="section-title"><i data-lucide="help-circle"></i> Pertanyaan</div>
                                <div class="section-subtitle">Minimal 1 pertanyaan dengan opsi A-D. Bisa dibuat manual atau dibantu Gemini dari materi yang dipilih.</div>
                            </div>
                            <button type="button" class="btn btn-outline btn-sm" id="addQuestion">
                                <i data-lucide="plus-circle"></i>
                                Tambah
                            </button>
                        </div>
                        <div class="section-note">Tips: Pilihan ganda untuk evaluasi cepat, essay untuk jawaban mendalam dengan koreksi keyword.</div>
                        <div class="ai-generate-panel">
                            <div class="section-title"><i data-lucide="sparkles"></i> Generate Soal dari Materi</div>
                            <div class="section-subtitle">Gemini akan membuat draft pilihan ganda dari isi materi yang dipilih. Hasilnya tetap bisa kamu edit sebelum simpan.</div>
                            <div class="ai-generate-toolbar">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="ai_jenis_soal">Jenis Soal</label>
                                    <select id="ai_jenis_soal">
                                        <option value="pilihan" selected>Pilihan Ganda</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="ai_jumlah_soal">Jumlah Soal</label>
                                    <select id="ai_jumlah_soal">
                                        <option value="3">3 soal</option>
                                        <option value="5" selected>5 soal</option>
                                        <option value="7">7 soal</option>
                                        <option value="10">10 soal</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="ai_kesulitan">Tingkat Kesulitan</label>
                                    <select id="ai_kesulitan">
                                        <option value="mudah">Mudah</option>
                                        <option value="sedang" selected>Sedang</option>
                                        <option value="sulit">Sulit</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <button type="button" class="btn btn-primary" id="generateQuizAiBtn" style="width:100%;">
                                        <i data-lucide="sparkles"></i>
                                        Generate Draft
                                    </button>
                                </div>
                            </div>
                            <div id="aiGenerateStatus" class="ai-generate-status"></div>
                            <div class="ai-generate-note">Versi ini paling optimal untuk materi teks atau file PDF. Jika form sudah berisi pertanyaan, draft baru akan menggantikan pertanyaan lama setelah konfirmasi.</div>
                        </div>
                        <div id="questionContainer"></div>
                    </div>

                    <div class="card">
                        <div class="form-actions">
                            <button class="btn btn-primary" type="submit">
                                <i data-lucide="save"></i>
                                Simpan Kuis
                            </button>
                            <a href="{{ route('kuis.index') }}" class="btn btn-ghost">
                                <i data-lucide="arrow-left"></i>
                                Batal
                            </a>
                        </div>
                </div>
                </form>
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

    <script>
        (function() {
            const container = document.getElementById('questionContainer');
            const addBtn = document.getElementById('addQuestion');
            const materiSelect = document.getElementById('materi_id');
            const materiBabSelect = document.getElementById('materi_bab_id');
            const judulInput = document.getElementById('judul');
            const deskripsiInput = document.getElementById('deskripsi');
            const aiJumlahSoalSelect = document.getElementById('ai_jumlah_soal');
            const aiKesulitanSelect = document.getElementById('ai_kesulitan');
            const aiJenisSoalSelect = document.getElementById('ai_jenis_soal');
            const generateQuizAiBtn = document.getElementById('generateQuizAiBtn');
            const aiGenerateStatus = document.getElementById('aiGenerateStatus');

            function syncBabOptions() {
                if (!materiBabSelect) {
                    return;
                }

                const selectedMateriId = materiSelect ? materiSelect.value : '';
                Array.from(materiBabSelect.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const optionMateriId = option.dataset.materiId || '';
                    const visible = !selectedMateriId || selectedMateriId === optionMateriId;
                    option.hidden = !visible;

                    if (!visible && option.selected) {
                        materiBabSelect.value = '';
                    }
                });
            }

            function syncMateriFromBabSelection() {
                if (!materiBabSelect || !materiSelect) {
                    return;
                }

                const selectedOption = materiBabSelect.options[materiBabSelect.selectedIndex];
                if (!selectedOption || !selectedOption.dataset.materiId) {
                    return;
                }

                materiSelect.value = selectedOption.dataset.materiId;
                syncBabOptions();
            }

            function buildQuestion(index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'question';
                wrapper.dataset.index = index;
                wrapper.innerHTML = `
                    <div class="question-header">
                        <div class="question-title"><span class="question-number">Pertanyaan ${index + 1}</span> <span class="question-badge">Wajib</span></div>
                        <button type="button" class="btn btn-secondary btn-sm remove-question">
                            <i data-lucide="trash-2"></i>
                            Hapus
                        </button>
                    </div>
                    <div class="form-group">
                        <label>Pertanyaan</label>
                        <input type="text" name="pertanyaan[${index}][teks]" required>
                    </div>
                    <div class="type-picker">
                        <div class="type-picker-title">
                            <i data-lucide="layout-list"></i>
                            <span>Pilih Jenis Soal</span>
                        </div>
                        <select name="pertanyaan[${index}][tipe]" class="q-type visually-hidden" required aria-label="Tipe Soal">
                            <option value="pilihan">Pilihan Ganda</option>
                            <option value="essay">Essay</option>
                        </select>
                        <div class="type-grid" role="group" aria-label="Pilih jenis soal">
                            <button type="button" class="type-card active" data-type="pilihan">
                                <span class="type-card-icon"><i data-lucide="list-checks"></i></span>
                                <span>
                                    <span class="type-card-title">Pilihan Ganda</span>
                                    <span class="type-card-desc">Opsi A-D dengan satu jawaban benar.</span>
                                </span>
                                <span class="type-card-check"></span>
                            </button>
                            <button type="button" class="type-card" data-type="essay">
                                <span class="type-card-icon"><i data-lucide="file-pen-line"></i></span>
                                <span>
                                    <span class="type-card-title">Essay</span>
                                    <span class="type-card-desc">Jawaban teks dengan keyword koreksi.</span>
                                </span>
                                <span class="type-card-check"></span>
                            </button>
                        </div>
                    </div>
                    <div class="inline-row">
                        <div class="form-group q-answer">
                            <label>Jawaban Benar</label>
                            <select name="pertanyaan[${index}][benar]" required>
                                <option value="">-- Pilih --</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="option-grid q-choices">
                        <div>
                            <label>Opsi A</label>
                            <input type="text" name="pertanyaan[${index}][opsi][A]" required>
                        </div>
                        <div>
                            <label>Opsi B</label>
                            <input type="text" name="pertanyaan[${index}][opsi][B]" required>
                        </div>
                        <div>
                            <label>Opsi C</label>
                            <input type="text" name="pertanyaan[${index}][opsi][C]" required>
                        </div>
                        <div>
                            <label>Opsi D</label>
                            <input type="text" name="pertanyaan[${index}][opsi][D]" required>
                        </div>
                    </div>
                    <div class="q-essay" style="margin-top:0.75rem; display:none;">
                        <div class="form-group">
                            <label>Jawaban Contoh</label>
                            <textarea name="pertanyaan[${index}][jawaban_teks]" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Keyword (pisah dengan koma)</label>
                            <input type="text" name="pertanyaan[${index}][keyword]" placeholder="contoh: subject, verb, object">
                        </div>
                        <div class="form-group">
                            <label>Bahasa ASR</label>
                            <select name="pertanyaan[${index}][bahasa]">
                                <option value="id-ID">id-ID</option>
                                <option value="en-US">en-US</option>
                            </select>
                        </div>
                    </div>
                `;
                wrapper.querySelector('.remove-question').addEventListener('click', () => {
                    wrapper.remove();
                    renumber();
                });
                const typeSelect = wrapper.querySelector('.q-type');
                const typeCards = Array.from(wrapper.querySelectorAll('.type-card'));
                const choices = wrapper.querySelector('.q-choices');
                const answerWrap = wrapper.querySelector('.q-answer');
                const answerSelect = answerWrap.querySelector('select');
                const choiceInputs = Array.from(choices.querySelectorAll('input'));
                const essay = wrapper.querySelector('.q-essay');
                const essayFields = Array.from(essay.querySelectorAll('input, textarea, select'));
                const essayJawaban = essay.querySelector('textarea[name$="[jawaban_teks]"]');
                const essayKeyword = essay.querySelector('input[name$="[keyword]"]');
                function setRequired(elements, required) {
                    elements.forEach(el => {
                        if (required) {
                            el.setAttribute('required', 'required');
                        } else {
                            el.removeAttribute('required');
                        }
                    });
                }

                function setDisabled(elements, disabled) {
                    elements.forEach(el => {
                        el.disabled = disabled;
                    });
                }

                function toggleByType() {
                    const val = typeSelect.value;
                    typeCards.forEach(card => {
                        const active = card.dataset.type === val;
                        card.classList.toggle('active', active);
                        card.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });

                    if (val === 'essay') {
                        choices.style.display = 'none';
                        answerWrap.style.display = 'none';
                        essay.style.display = 'block';
                        setRequired(choiceInputs, false);
                        setRequired([answerSelect], false);
                        setRequired([essayJawaban, essayKeyword], true);
                        setDisabled([...choiceInputs, answerSelect], true);
                        setDisabled(essayFields, false);
                    } else {
                        choices.style.display = 'grid';
                        answerWrap.style.display = 'block';
                        essay.style.display = 'none';
                        setRequired(choiceInputs, true);
                        setRequired([answerSelect], true);
                        setRequired([essayJawaban, essayKeyword], false);
                        setDisabled([...choiceInputs, answerSelect], false);
                        setDisabled(essayFields, true);
                    }
                }

                typeCards.forEach(card => {
                    card.addEventListener('click', () => {
                        typeSelect.value = card.dataset.type;
                        typeSelect.dispatchEvent(new Event('change'));
                    });
                });

                typeSelect.addEventListener('change', toggleByType);
                toggleByType();
                return wrapper;
            }

            function setAiStatus(message, type = 'info') {
                if (!aiGenerateStatus) {
                    return;
                }

                if (!message) {
                    aiGenerateStatus.textContent = '';
                    aiGenerateStatus.className = 'ai-generate-status';
                    return;
                }

                aiGenerateStatus.textContent = message;
                aiGenerateStatus.className = `ai-generate-status ${type}`;
            }

            function fillQuestionData(wrapper, questionData) {
                const textInput = wrapper.querySelector('input[name$="[teks]"]');
                const typeSelect = wrapper.querySelector('.q-type');
                const benarSelect = wrapper.querySelector('select[name$="[benar]"]');
                const opsiA = wrapper.querySelector('input[name$="[opsi][A]"]');
                const opsiB = wrapper.querySelector('input[name$="[opsi][B]"]');
                const opsiC = wrapper.querySelector('input[name$="[opsi][C]"]');
                const opsiD = wrapper.querySelector('input[name$="[opsi][D]"]');
                const essayJawaban = wrapper.querySelector('.q-essay textarea[name$="[jawaban_teks]"]');
                const essayKeyword = wrapper.querySelector('.q-essay input[name$="[keyword]"]');
                const essayBahasa = wrapper.querySelector('.q-essay select[name$="[bahasa]"]');
                if (textInput) textInput.value = questionData.teks || '';
                if (typeSelect) {
                    typeSelect.value = questionData.tipe || 'pilihan';
                    typeSelect.dispatchEvent(new Event('change'));
                }
                if (opsiA) opsiA.value = questionData.opsi?.A || '';
                if (opsiB) opsiB.value = questionData.opsi?.B || '';
                if (opsiC) opsiC.value = questionData.opsi?.C || '';
                if (opsiD) opsiD.value = questionData.opsi?.D || '';
                if (benarSelect) benarSelect.value = questionData.benar || '';
                if (essayJawaban) essayJawaban.value = questionData.jawaban_teks || '';
                if (essayKeyword) essayKeyword.value = questionData.keyword || '';
                if (essayBahasa) essayBahasa.value = questionData.bahasa || 'id-ID';
            }

            function replaceQuestions(questions) {
                container.innerHTML = '';
                questions.forEach((questionData, index) => {
                    const wrapper = buildQuestion(index);
                    container.appendChild(wrapper);
                    fillQuestionData(wrapper, questionData);
                });
                renumber();
                lucide.createIcons();
            }

            async function generateDraftFromMateri() {
                const materiId = materiSelect ? materiSelect.value : '';
                const materiBabId = materiBabSelect ? materiBabSelect.value : '';
                if (!materiId) {
                    setAiStatus('Pilih mata pelajaran dulu sebelum generate draft kuis.', 'error');
                    materiSelect?.focus();
                    return;
                }

                const existingQuestions = container.querySelectorAll('.question').length;
                if (existingQuestions > 0) {
                    const hasContent = Array.from(container.querySelectorAll('input[name$="[teks]"]')).some((input) => input.value.trim() !== '');
                    if (hasContent && !confirm('Draft dari Gemini akan menggantikan pertanyaan yang sedang ada di form. Lanjutkan?')) {
                        return;
                    }
                }

                setAiStatus('Gemini sedang membuat draft kuis dari materi yang dipilih...', 'info');
                generateQuizAiBtn.disabled = true;

                try {
                    const response = await fetch('{{ route("kuis.generate-from-materi", [], false) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            materi_id: materiId,
                            materi_bab_id: materiBabId || null,
                            jumlah_soal: Number(aiJumlahSoalSelect?.value || 5),
                            kesulitan: aiKesulitanSelect?.value || 'sedang',
                            jenis_soal: aiJenisSoalSelect?.value || 'pilihan',
                        }),
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(
                            payload.message
                            || (payload.errors ? Object.values(payload.errors).flat()[0] : null)
                            || 'Generate draft kuis gagal.'
                        );
                    }

                    const draft = payload.data || {};
                    if (judulInput && (!judulInput.value || judulInput.value.trim() === '')) {
                        judulInput.value = draft.judul || '';
                    }
                    if (deskripsiInput && (!deskripsiInput.value || deskripsiInput.value.trim() === '')) {
                        deskripsiInput.value = draft.deskripsi || '';
                    }

                    replaceQuestions(Array.isArray(draft.pertanyaan) ? draft.pertanyaan : []);
                    setAiStatus('Draft kuis berhasil dibuat. Periksa dan edit lagi sebelum disimpan.', 'success');
                } catch (error) {
                    setAiStatus(error.message || 'Generate draft kuis gagal.', 'error');
                } finally {
                    generateQuizAiBtn.disabled = false;
                    lucide.createIcons();
                }
            }

            function renumber() {
                const items = container.querySelectorAll('.question');
                items.forEach((item, idx) => {
                    item.dataset.index = idx;
                    const label = item.querySelector('.question-number');
                    if (label) {
                        label.textContent = `Pertanyaan ${idx + 1}`;
                    }
                });
            }

            addBtn.addEventListener('click', () => {
                const index = container.querySelectorAll('.question').length;
                container.appendChild(buildQuestion(index));
                lucide.createIcons();
            });

            if (materiSelect) {
                materiSelect.addEventListener('change', syncBabOptions);
                syncBabOptions();
            }

            if (materiBabSelect) {
                materiBabSelect.addEventListener('change', syncMateriFromBabSelection);
            }

            if (generateQuizAiBtn) {
                generateQuizAiBtn.addEventListener('click', generateDraftFromMateri);
            }

            // default 1 question
            container.appendChild(buildQuestion(0));
            lucide.createIcons();
        })();
    </script>
</body>
</html>

