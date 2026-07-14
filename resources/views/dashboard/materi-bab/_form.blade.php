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
