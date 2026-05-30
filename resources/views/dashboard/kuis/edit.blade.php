<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kuis - Ruma</title>
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
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.4rem; }
        input[type="text"], textarea, select { width: 100%; padding: 0.7rem 0.8rem; border-radius: 10px; border: 1px solid var(--color-gray); }
        .question { border: 1px solid var(--color-gray); border-radius: 12px; padding: 1rem; margin-top: 1rem; background: #fff; }
        .option-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem; }
        .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .error { color: #B91C1C; font-size: 0.9rem; margin-top: 0.35rem; }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Edit Kuis</h1>
            </header>
            <div class="content-area">
            <div class="page">
                <div class="card">
                    <div class="title">Edit Kuis</div>
                    <div class="desc">Perbarui pertanyaan dan opsi jawaban.</div>
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

        <form action="{{ route('kuis.update', $kuis->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="form-group">
                            <label for="judul">Judul Kuis</label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul', $kuis->judul) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="materi_id">Mata Pelajaran (Opsional)</label>
                            <select id="materi_id" name="materi_id">
                                <option value="">-- Tanpa Mata Pelajaran --</option>
                                @foreach($materiList as $materi)
                                    <option value="{{ $materi->id }}" {{ old('materi_id', $kuis->materi_id) == $materi->id ? 'selected' : '' }}>{{ $materi->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="materi_bab_id">Materi (Opsional)</label>
                            <select id="materi_bab_id" name="materi_bab_id">
                                <option value="">-- Pilih Materi --</option>
                                @foreach($materiList as $materi)
                                    @foreach($materi->bab as $bab)
                                        <option value="{{ $bab->id }}" data-materi-id="{{ $materi->id }}" {{ old('materi_bab_id', $kuis->materi_bab_id) == $bab->id ? 'selected' : '' }}>
                                            {{ $materi->judul }} - Materi {{ $bab->urutan }}: {{ $bab->judul_bab }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $kuis->deskripsi) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="hidden" name="status_aktif" value="0">
                                <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $kuis->status_aktif) ? 'checked' : '' }}>
                                Aktifkan Kuis
                            </label>
                        </div>
                    </div>

                    <div class="card">
                        <div class="actions" style="justify-content: space-between;">
                            <div>
                                <div class="title" style="font-size:1.2rem;">Pertanyaan</div>
                                <div class="desc">Minimal 1 pertanyaan dengan opsi A-D.</div>
                            </div>
                            <button type="button" class="btn btn-secondary" id="addQuestion">Tambah Pertanyaan</button>
                        </div>
                        <div id="questionContainer">
                            @foreach($kuis->pertanyaan as $index => $pertanyaan)
                                @php
                                    $opsi = $pertanyaan->opsi->keyBy('label');
                                    $benar = $pertanyaan->opsi->firstWhere('benar', true)?->label ?? 'A';
                                @endphp
                                <div class="question" data-index="{{ $index }}">
                                    <div class="actions" style="justify-content: space-between; margin-bottom:0.75rem;">
                                        <strong>Pertanyaan {{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-secondary remove-question">Hapus</button>
                                    </div>
                            <div class="form-group">
                                <input type="hidden" name="pertanyaan[{{ $index }}][id]" value="{{ $pertanyaan->id }}">
                                <label>Pertanyaan</label>
                                <input type="text" name="pertanyaan[{{ $index }}][teks]" value="{{ $pertanyaan->pertanyaan }}" required>
                            </div>
                            <div class="form-group">
                                <label>Tipe Soal</label>
                                <select name="pertanyaan[{{ $index }}][tipe]" class="q-type" required>
                                    <option value="pilihan" {{ in_array($pertanyaan->tipe, ['pilihan', 'listening'], true) ? 'selected' : '' }}>Pilihan Ganda</option>
                                    <option value="essay" {{ in_array($pertanyaan->tipe, ['essay', 'speaking'], true) ? 'selected' : '' }}>Essay</option>
                                                                    </select>
                            </div>
                            <div class="form-group q-answer">
                                <label>Jawaban Benar</label>
                                <select name="pertanyaan[{{ $index }}][benar]" required>
                                    <option value="A" {{ $benar === 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ $benar === 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ $benar === 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D" {{ $benar === 'D' ? 'selected' : '' }}>D</option>
                                </select>
                            </div>
                            <div class="option-grid q-choices">
                                <div>
                                    <label>Opsi A</label>
                                    <input type="text" name="pertanyaan[{{ $index }}][opsi][A]" value="{{ $opsi['A']->teks ?? '' }}" required>
                                </div>
                                        <div>
                                            <label>Opsi B</label>
                                            <input type="text" name="pertanyaan[{{ $index }}][opsi][B]" value="{{ $opsi['B']->teks ?? '' }}" required>
                                        </div>
                                        <div>
                                            <label>Opsi C</label>
                                            <input type="text" name="pertanyaan[{{ $index }}][opsi][C]" value="{{ $opsi['C']->teks ?? '' }}" required>
                                        </div>
                                <div>
                                    <label>Opsi D</label>
                                    <input type="text" name="pertanyaan[{{ $index }}][opsi][D]" value="{{ $opsi['D']->teks ?? '' }}" required>
                                </div>
                            </div>
                            <div class="q-essay" style="margin-top:0.75rem; display:none;">
                                <div class="form-group">
                                    <label>Jawaban Contoh</label>
                                    <textarea name="pertanyaan[{{ $index }}][jawaban_teks]" rows="3">{{ $pertanyaan->jawaban_teks }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Keyword (pisah dengan koma)</label>
                                    <input type="text" name="pertanyaan[{{ $index }}][keyword]" value="{{ $pertanyaan->keyword }}" placeholder="contoh: subject, verb, object">
                                </div>
                                <div class="form-group">
                                    <label>Bahasa ASR</label>
                                    <select name="pertanyaan[{{ $index }}][bahasa]">
                                        <option value="id-ID" {{ $pertanyaan->bahasa === 'id-ID' ? 'selected' : '' }}>id-ID</option>
                                        <option value="en-US" {{ $pertanyaan->bahasa === 'en-US' ? 'selected' : '' }}>en-US</option>
                                    </select>
                                </div>
                            </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card actions">
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                        <a href="{{ route('kuis.index') }}" class="btn btn-secondary">Batal</a>
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
            const materiSelect = document.getElementById('materi_id');
            const materiBabSelect = document.getElementById('materi_bab_id');

            function syncBabOptions() {
                if (!materiBabSelect) return;
                const selectedMateriId = materiSelect ? materiSelect.value : '';
                Array.from(materiBabSelect.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }
                    const visible = !selectedMateriId || option.dataset.materiId === selectedMateriId;
                    option.hidden = !visible;
                    if (!visible && option.selected) {
                        materiBabSelect.value = '';
                    }
                });
            }

            function syncMateriFromBabSelection() {
                if (!materiBabSelect || !materiSelect) return;
                const selectedOption = materiBabSelect.options[materiBabSelect.selectedIndex];
                if (!selectedOption || !selectedOption.dataset.materiId) return;
                materiSelect.value = selectedOption.dataset.materiId;
                syncBabOptions();
            }

            if (materiSelect) {
                materiSelect.addEventListener('change', syncBabOptions);
                syncBabOptions();
            }

            if (materiBabSelect) {
                materiBabSelect.addEventListener('change', syncMateriFromBabSelection);
            }

            const container = document.getElementById('questionContainer');
            const addBtn = document.getElementById('addQuestion');

            function attachRemove(btn) {
                btn.addEventListener('click', () => {
                    btn.closest('.question').remove();
                    renumber();
                });
            }

            function buildQuestion(index) {
                const wrapper = document.createElement('div');
                wrapper.className = 'question';
                wrapper.dataset.index = index;
                wrapper.innerHTML = `
                    <div class="actions" style="justify-content: space-between; margin-bottom:0.75rem;">
                        <strong>Pertanyaan ${index + 1}</strong>
                        <button type="button" class="btn btn-secondary remove-question">Hapus</button>
                    </div>
                    <div class="form-group">
                        <label>Pertanyaan</label>
                        <input type="text" name="pertanyaan[${index}][teks]" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Soal</label>
                        <select name="pertanyaan[${index}][tipe]" class="q-type" required>
                            <option value="pilihan">Pilihan Ganda</option>
                            <option value="essay">Essay</option>
                                                                                </select>
                    </div>
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
                attachRemove(wrapper.querySelector('.remove-question'));
                attachToggle(wrapper);
                return wrapper;
            }

            function renumber() {
                const items = container.querySelectorAll('.question');
                items.forEach((item, idx) => {
                    item.dataset.index = idx;
                    item.querySelector('strong').textContent = `Pertanyaan ${idx + 1}`;
                });
            }

            function attachToggle(wrapper) {
                const typeSelect = wrapper.querySelector('.q-type');
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

                typeSelect.addEventListener('change', toggleByType);
                toggleByType();
            }

            container.querySelectorAll('.question').forEach(wrapper => {
                wrapper.querySelector('.remove-question').addEventListener('click', () => {
                    wrapper.remove();
                    renumber();
                });
                attachToggle(wrapper);
            });

            addBtn.addEventListener('click', () => {
                const index = container.querySelectorAll('.question').length;
                container.appendChild(buildQuestion(index));
            });
        })();
    </script>
</body>
</html>

