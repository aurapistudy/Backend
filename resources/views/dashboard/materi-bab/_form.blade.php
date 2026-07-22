@php
    $isEdit = isset($bab);
    $currentTipe = old('tipe_konten', $bab->tipe_konten ?? 'teks');
    $currentPdfSelection = old('pdf_page_selection', $bab->pdf_page_selection ?? '');
    $pdfSourceOptions = $pdfSourceOptions ?? [];
    $currentPdfSourcePath = old('pdf_source_path', $bab->pdf_source_path ?? '');
@endphp

<div class="form-group">
    <label class="form-label">Judul Materi <span class="required">*</span></label>
    <input type="text" name="judul_bab" value="{{ old('judul_bab', $bab->judul_bab ?? '') }}" class="form-input" required>
</div>

<div class="form-group">
    <label class="form-label">Urutan Materi <span class="required">*</span></label>
    <input type="number" name="urutan" value="{{ old('urutan', $bab->urutan ?? $nextUrutan ?? 1) }}" min="1" class="form-input" required>
</div>

<div class="form-group">
    <label class="form-label">Tipe Konten <span class="required">*</span></label>
    <select name="tipe_konten" id="tipe_konten" class="form-select" required>
        <option value="teks" {{ $currentTipe === 'teks' ? 'selected' : '' }}>Teks</option>
        <option value="file" {{ $currentTipe === 'file' ? 'selected' : '' }}>File</option>
    </select>
</div>

<div id="konten_teks_field" class="form-group" style="display: {{ $currentTipe === 'teks' ? 'block' : 'none' }};">
    <label class="form-label">Konten Teks <span class="required">*</span></label>
    <textarea name="konten_teks" rows="10" class="form-textarea">{{ old('konten_teks', $bab->konten_teks ?? '') }}</textarea>
</div>

<div id="file_path_field" class="form-group" style="display: {{ $currentTipe === 'file' ? 'block' : 'none' }};">
    @if(!empty($pdfSourceOptions))
        <div class="form-group">
            <label class="form-label">PDF Sumber / Master</label>
            <select name="pdf_source_path" id="pdf_source_path" class="form-select">
                <option value="">Upload file baru</option>
                @foreach($pdfSourceOptions as $option)
                    <option
                        value="{{ $option['path'] }}"
                        data-url="{{ $option['url'] }}"
                        {{ $currentPdfSourcePath === $option['path'] ? 'selected' : '' }}
                    >
                        Pakai {{ $option['label'] }} ({{ $option['file_name'] }})
                    </option>
                @endforeach
            </select>
            <span class="hint">Hanya PDF sumber asli yang ditampilkan di sini. File potongan per bab tidak akan muncul sebagai sumber.</span>
        </div>
    @else
        <input type="hidden" name="pdf_source_path" id="pdf_source_path" value="">
    @endif

    <label class="form-label">File Materi (PDF, Word, PowerPoint, TXT) <span class="required">*</span></label>
    <input type="file" name="file_path" id="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.odt,.odp,.rtf,.txt" class="form-input">
    <span class="hint" id="file_upload_hint">Upload file baru hanya diperlukan jika tidak memakai PDF sumber yang sudah ada.</span>
    <input type="hidden" name="pdf_page_selection" id="pdf_page_selection" value="{{ $currentPdfSelection }}">
    @if($isEdit && !empty($bab->file_path))
        <div class="current-file">File saat ini: <a href="{{ Storage::url($bab->file_path) }}" target="_blank">{{ basename($bab->file_path) }}</a></div>
    @endif
    <style>
    .chapter-detect-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 2px solid rgba(17, 24, 39, 0.08);
        border-radius: 12px;
        background: #FFFFFF;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        width: 100%;
    }
    .chapter-detect-card:hover {
        border-color: var(--color-accent);
        background: #FFFDF5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(248, 184, 3, 0.15);
    }
    .chapter-detect-card.active {
        border-color: var(--color-accent-dark);
        background: #FFF9E6;
    }
    .chapter-detect-thumb {
        width: 46px;
        height: 60px;
        border-radius: 6px;
        background: #F1F5F9;
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .chapter-detect-thumb canvas {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .chapter-detect-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .chapter-detect-title {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--color-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chapter-detect-range {
        font-size: 0.78rem;
        color: var(--color-muted);
        margin-top: 0.15rem;
    }
    .chapter-detect-card.optional {
        border-color: rgba(99, 102, 241, 0.2);
        background: #F5F3FF;
    }
    .chapter-detect-card.optional:hover {
        border-color: #6366F1;
        background: #EEF2FF;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    .chapter-detect-card.optional.active {
        border-color: #4F46E5;
        background: #E0E7FF;
    }
    .chapter-detect-card.optional .chapter-detect-thumb {
        background: #E0E7FF;
    }
    .chapter-detect-card.optional .chapter-detect-title {
        color: #3730A3;
    }
    .chapter-optional-label {
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6366F1;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    </style>

    <!-- Panel Deteksi Bab Otomatis -->
    <div id="pdf_detected_chapters_panel" style="display: none; margin-top: 1rem; margin-bottom: 1.5rem; padding: 1.25rem; border: 1px solid #E5E7EB; border-radius: 14px; background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);">
        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; margin-bottom: 0.25rem;">
            <i data-lucide="sparkles" style="color: var(--color-accent-dark); width: 18px; height: 18px;"></i>
            <span>Daftar Bab Terdeteksi (Pilihan Cepat)</span>
        </label>
        <span class="hint" style="margin-bottom: 1rem;">Klik pada salah satu bab di bawah untuk otomatis mengisi judul materi dan rentang halaman PDF.</span>
        
        <div id="pdf_detected_chapters_loading" class="pdf-selection-loading" style="display: none; color: var(--color-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
            <div class="spinner" style="width: 20px; height: 20px; border-width: 2px; display: inline-block; margin-right: 0.5rem; vertical-align: middle;"></div>
            Menganalisis bab dari PDF...
        </div>
        
        <div id="pdf_detected_chapters_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.75rem;">
            <!-- Chapter cards will be appended here dynamically -->
        </div>

        <!-- Optional front/back matter section -->
        <div id="pdf_detected_extras_section" style="display: none;">
            <div class="chapter-optional-label">
                <i data-lucide="layers" style="width: 14px; height: 14px;"></i>
                Bagian Tambahan (Opsional)
            </div>
            <span class="hint" style="display: block; margin-bottom: 0.65rem;">Halaman di luar bab utama — cover, daftar isi, penutup, dll. Klik jika ingin ditambahkan sebagai materi tersendiri.</span>
            <div id="pdf_detected_extras_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.75rem;">
                <!-- Optional cards appended here -->
            </div>
        </div>
    </div>

    <div id="pdf_selection_panel" class="pdf-selection-panel" style="display: none;">
        <div id="pdf_selection_loading" class="pdf-selection-loading" style="display: none;">Sedang menyiapkan preview halaman PDF...</div>
        <div class="pdf-selection-range">
            <div>
                <label class="form-label">Halaman Awal</label>
                <input type="number" id="pdf_page_start" class="form-input" min="1">
            </div>
            <div>
                <label class="form-label">Halaman Akhir</label>
                <input type="number" id="pdf_page_end" class="form-input" min="1">
            </div>
        </div>
        <div class="pdf-selection-toolbar">
            <div id="pdf_selection_summary" class="pdf-selection-summary">Belum ada halaman PDF yang dimuat.</div>
            <div class="pdf-selection-actions">
                <button type="button" id="pdf_select_all" class="pdf-action-btn">Pilih Semua</button>
                <button type="button" id="pdf_clear_all" class="pdf-action-btn">Reset Pilihan</button>
            </div>
        </div>
        <div id="pdf_pages_grid" class="pdf-pages-grid"></div>
        <div id="pdf_selection_empty" class="pdf-selection-empty">Pilih file PDF untuk menampilkan halaman dan centang halaman yang ingin disimpan.</div>
    </div>
</div>

<div class="form-group">
    <label class="form-checkbox">
        <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $bab->status_aktif ?? true) ? 'checked' : '' }}>
        <span>Materi Aktif</span>
    </label>
</div>
