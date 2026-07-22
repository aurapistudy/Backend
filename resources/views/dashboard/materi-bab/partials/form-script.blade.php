<script>
    const babForm = document.getElementById('babForm');
    const tipeKontenSelect = document.getElementById('tipe_konten');
    const fileInput = document.getElementById('file_path');
    const pdfSourceSelect = document.getElementById('pdf_source_path');
    const fileUploadHint = document.getElementById('file_upload_hint');
    const pdfSelectionPanel = document.getElementById('pdf_selection_panel');
    const pdfSelectionLoading = document.getElementById('pdf_selection_loading');
    const pdfSelectionSummary = document.getElementById('pdf_selection_summary');
    const pdfPagesGrid = document.getElementById('pdf_pages_grid');
    const pdfSelectionEmpty = document.getElementById('pdf_selection_empty');
    const pdfPageSelectionInput = document.getElementById('pdf_page_selection');
    const pdfPageStartInput = document.getElementById('pdf_page_start');
    const pdfPageEndInput = document.getElementById('pdf_page_end');
    const pdfSelectAllButton = document.getElementById('pdf_select_all');
    const pdfClearAllButton = document.getElementById('pdf_clear_all');
    const existingPdfUrl = @json((isset($bab) && $bab?->tipe_konten === 'file' && $bab?->file_path && str_ends_with(strtolower($bab->file_path), '.pdf')) ? Storage::url($bab->file_path) : null);
    const suggestedPdfPageStart = @json($suggestedPdfPageStart ?? null);
    const initialSelectedPdfPages = new Set((pdfPageSelectionInput?.value || '').split(',').map((value) => Number.parseInt(value.trim(), 10)).filter((value) => Number.isInteger(value) && value > 0));
    let selectedPdfPages = new Set();
    let totalPdfPages = 0;
    let isSyncingPdfInputs = false;
    let currentPdfDoc = null;

    const pdfJsAvailable = typeof window.pdfjsLib !== 'undefined';

    if (pdfJsAvailable) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    } else {
        console.warn('PDF.js gagal dimuat. Upload file tetap tersedia, tetapi preview PDF dinonaktifkan.');
    }

    function updatePdfSelectionSummary() {
        if (!pdfSelectionSummary) return;
        if (!totalPdfPages) {
            pdfSelectionSummary.textContent = 'Belum ada halaman PDF yang dimuat.';
            pdfPageSelectionInput.value = '';
            return;
        }
        const selectedCount = selectedPdfPages.size;
        pdfSelectionSummary.textContent = `Terpilih ${selectedCount} dari ${totalPdfPages} halaman.`;
        pdfPageSelectionInput.value = Array.from(selectedPdfPages).sort((a, b) => a - b).join(',');
    }

    function syncPageRangeInputsFromSelection() {
        if (!pdfPageStartInput || !pdfPageEndInput) return;
        isSyncingPdfInputs = true;
        if (selectedPdfPages.size === 0) {
            pdfPageStartInput.value = '';
            pdfPageEndInput.value = '';
        } else {
            const sortedPages = Array.from(selectedPdfPages).sort((a, b) => a - b);
            pdfPageStartInput.value = sortedPages[0];
            pdfPageEndInput.value = sortedPages[sortedPages.length - 1];
        }
        isSyncingPdfInputs = false;
    }

    function updatePageCardVisual(pageNumber, isSelected) {
        const card = pdfPagesGrid.querySelector(`.pdf-page-card[data-page-number="${pageNumber}"]`);
        if (!card) return;
        card.classList.toggle('selected', isSelected);
        const checkbox = card.querySelector('.pdf-page-check');
        if (checkbox) checkbox.checked = isSelected;
    }

    function applyRangeSelection() {
        if (isSyncingPdfInputs || totalPdfPages === 0) return;
        const startValue = Number.parseInt(pdfPageStartInput.value, 10);
        const endValue = Number.parseInt(pdfPageEndInput.value, 10);
        if (!startValue && !endValue) {
            selectedPdfPages = new Set();
            for (let pageNumber = 1; pageNumber <= totalPdfPages; pageNumber++) updatePageCardVisual(pageNumber, false);
            updatePdfSelectionSummary();
            return;
        }
        if (!startValue || !endValue) return;
        const startPage = Math.max(1, Math.min(startValue, totalPdfPages));
        const endPage = Math.max(1, Math.min(endValue, totalPdfPages));
        if (startPage > endPage) return;
        selectedPdfPages = new Set();
        for (let pageNumber = 1; pageNumber <= totalPdfPages; pageNumber++) {
            const isSelected = pageNumber >= startPage && pageNumber <= endPage;
            if (isSelected) selectedPdfPages.add(pageNumber);
            updatePageCardVisual(pageNumber, isSelected);
        }
        updatePdfSelectionSummary();
        syncPageRangeInputsFromSelection();
    }

    function renderPdfPageCard(pageNumber, viewport, canvas) {
        const card = document.createElement('label');
        const isInitiallySelected = initialSelectedPdfPages.has(pageNumber);
        card.className = `pdf-page-card${isInitiallySelected ? ' selected' : ''}`;
        card.dataset.pageNumber = String(pageNumber);
        const preview = document.createElement('div');
        preview.className = 'pdf-page-preview';
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'pdf-page-check';
        checkbox.checked = isInitiallySelected;
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                selectedPdfPages.add(pageNumber);
                card.classList.add('selected');
            } else {
                selectedPdfPages.delete(pageNumber);
                card.classList.remove('selected');
            }
            updatePdfSelectionSummary();
            syncPageRangeInputsFromSelection();
        });
        const meta = document.createElement('div');
        meta.className = 'pdf-page-meta';
        meta.innerHTML = `<div class="pdf-page-title">Halaman ${pageNumber}</div><div class="pdf-page-subtitle">${Math.round(viewport.width)} x ${Math.round(viewport.height)} px</div>`;
        preview.appendChild(canvas);
        preview.appendChild(checkbox);
        card.appendChild(preview);
        card.appendChild(meta);
        return card;
    }

    async function loadPdfPreview(source) {
        if (!source || !pdfSelectionPanel) return;

        if (!pdfJsAvailable) {
        pdfSelectionPanel.style.display = 'block';

        if (pdfPagesGrid) {
            pdfPagesGrid.innerHTML = '';
        }

        if (pdfSelectionLoading) {
            pdfSelectionLoading.style.display = 'none';
        }

        if (pdfSelectionEmpty) {
            pdfSelectionEmpty.style.display = 'block';
            pdfSelectionEmpty.textContent =
                'Preview PDF tidak tersedia, tetapi file tetap bisa diupload.';
        }

        return;
    }

        const isFileObject = typeof File !== 'undefined' && source instanceof File;
        const isPdf = isFileObject ? (source.type === 'application/pdf' || source.name.toLowerCase().endsWith('.pdf')) : String(source).toLowerCase().includes('.pdf');
        pdfSelectionPanel.style.display = isPdf ? 'block' : 'none';
        if (!isPdf) {
            pdfPagesGrid.innerHTML = '';
            pdfSelectionEmpty.style.display = 'block';
            pdfSelectionEmpty.textContent = 'Preview halaman hanya tersedia untuk file PDF.';
            selectedPdfPages = new Set();
            totalPdfPages = 0;
            currentPdfDoc = null;
            const panel = document.getElementById('pdf_detected_chapters_panel');
            if (panel) panel.style.display = 'none';
            updatePdfSelectionSummary();
            return;
        }
        pdfSelectionLoading.style.display = 'block';
        pdfPagesGrid.innerHTML = '';
        pdfSelectionEmpty.style.display = 'none';
        selectedPdfPages = new Set();
        totalPdfPages = 0;
        currentPdfDoc = null;
        const panel = document.getElementById('pdf_detected_chapters_panel');
        if (panel) panel.style.display = 'none';
        updatePdfSelectionSummary();
        try {
            const documentSource = isFileObject ? { data: await source.arrayBuffer() } : source;
            const pdf = await window.pdfjsLib.getDocument(documentSource).promise;
            currentPdfDoc = pdf;
            totalPdfPages = pdf.numPages;
            detectPdfChapters(source);
            selectedPdfPages = new Set(Array.from(initialSelectedPdfPages).filter((pageNumber) => pageNumber <= totalPdfPages));
            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 0.35 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: context, viewport }).promise;
                pdfPagesGrid.appendChild(renderPdfPageCard(pageNumber, viewport, canvas));
            }
            updatePdfSelectionSummary();
            syncPageRangeInputsFromSelection();
            if (selectedPdfPages.size === 0 && suggestedPdfPageStart && suggestedPdfPageStart <= totalPdfPages) {
                pdfPageStartInput.value = suggestedPdfPageStart;
                pdfPageEndInput.value = suggestedPdfPageStart;
                applyRangeSelection();
                const targetCard = pdfPagesGrid.querySelector(`.pdf-page-card[data-page-number="${suggestedPdfPageStart}"]`);
                if (targetCard) targetCard.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
        } catch (error) {
            pdfPagesGrid.innerHTML = '';
            pdfSelectionEmpty.style.display = 'block';
            pdfSelectionEmpty.textContent = 'Preview PDF gagal dimuat.';
            selectedPdfPages = new Set();
            totalPdfPages = 0;
            currentPdfDoc = null;
            const panel = document.getElementById('pdf_detected_chapters_panel');
            if (panel) panel.style.display = 'none';
            updatePdfSelectionSummary();
            syncPageRangeInputsFromSelection();
        } finally {
            pdfSelectionLoading.style.display = 'none';
        }
    }

    function syncTipeKontenField() {
    if (!tipeKontenSelect) {
        return;
    }

    const tipeKonten = tipeKontenSelect.value;
    const kontenTeksField = document.getElementById('konten_teks_field');
    const filePathField = document.getElementById('file_path_field');
    const kontenTeksInput = kontenTeksField?.querySelector('textarea');

    if (kontenTeksField) {
        kontenTeksField.style.display =
            tipeKonten === 'teks' ? 'block' : 'none';
    }

    if (filePathField) {
        filePathField.style.display =
            tipeKonten === 'file' ? 'block' : 'none';
    }

    if (kontenTeksInput) {
        kontenTeksInput.required = tipeKonten === 'teks';
    }

    /*
     * File tidak langsung dibuat required karena pengguna juga dapat
     * memilih PDF sumber yang sudah pernah diupload.
     */
    if (tipeKonten !== 'file') {
        if (pdfSelectionPanel) {
            pdfSelectionPanel.style.display = 'none';
        }

        return;
    }

    const currentFile = fileInput?.files?.[0] ?? null;
    const selectedSourceUrl =
        pdfSourceSelect?.selectedOptions?.[0]?.dataset?.url ?? '';

    if (currentFile) {
        loadPdfPreview(currentFile);
    } else if (selectedSourceUrl) {
        loadPdfPreview(selectedSourceUrl);
    } else if (existingPdfUrl) {
        loadPdfPreview(existingPdfUrl);
    }
}

if (tipeKontenSelect) {
    tipeKontenSelect.addEventListener('change', syncTipeKontenField);
    syncTipeKontenField();
}

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const currentFile = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (currentFile) {
                if (pdfSourceSelect) pdfSourceSelect.value = '';
                initialSelectedPdfPages.clear();
                loadPdfPreview(currentFile);
            } else {
                pdfSelectionPanel.style.display = 'none';
                pdfPagesGrid.innerHTML = '';
                selectedPdfPages = new Set();
                totalPdfPages = 0;
                updatePdfSelectionSummary();
                syncPageRangeInputsFromSelection();
            }
        });
    }

    if (pdfSourceSelect) {
        pdfSourceSelect.addEventListener('change', () => {
            const selectedSourceUrl = pdfSourceSelect.selectedOptions?.[0]?.dataset?.url || '';
            if (fileInput) fileInput.value = '';
            if (fileUploadHint) {
                fileUploadHint.textContent = selectedSourceUrl
                    ? 'File akan dibuat dari PDF sumber yang dipilih, tanpa upload ulang.'
                    : 'Upload file baru hanya diperlukan jika tidak memakai PDF sumber yang sudah ada.';
            }
            initialSelectedPdfPages.clear();
            if (selectedSourceUrl) {
                loadPdfPreview(selectedSourceUrl);
            } else if (existingPdfUrl && tipeKontenSelect.value === 'file') {
                loadPdfPreview(existingPdfUrl);
            } else {
                pdfSelectionPanel.style.display = 'none';
                pdfPagesGrid.innerHTML = '';
                selectedPdfPages = new Set();
                totalPdfPages = 0;
                updatePdfSelectionSummary();
                syncPageRangeInputsFromSelection();
            }
        });
    }

    if (pdfPageStartInput) pdfPageStartInput.addEventListener('input', applyRangeSelection);
    if (pdfPageEndInput) pdfPageEndInput.addEventListener('input', applyRangeSelection);
    if (pdfSelectAllButton) pdfSelectAllButton.addEventListener('click', () => {
        selectedPdfPages = new Set(Array.from({ length: totalPdfPages }, (_, index) => index + 1));
        pdfPagesGrid.querySelectorAll('.pdf-page-card').forEach((card) => {
            card.classList.add('selected');
            const checkbox = card.querySelector('.pdf-page-check');
            if (checkbox) checkbox.checked = true;
        });
        updatePdfSelectionSummary();
        syncPageRangeInputsFromSelection();
    });
    if (pdfClearAllButton) pdfClearAllButton.addEventListener('click', () => {
        selectedPdfPages = new Set();
        pdfPagesGrid.querySelectorAll('.pdf-page-card').forEach((card) => {
            card.classList.remove('selected');
            const checkbox = card.querySelector('.pdf-page-check');
            if (checkbox) checkbox.checked = false;
        });
        updatePdfSelectionSummary();
        syncPageRangeInputsFromSelection();
    });

    if (babForm) {
        babForm.addEventListener('submit', (event) => {
            const currentFile = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            const selectedSourceUrl = pdfSourceSelect?.selectedOptions?.[0]?.dataset?.url || '';
            const isPdf = currentFile && (currentFile.type === 'application/pdf' || currentFile.name.toLowerCase().endsWith('.pdf'));
            if ((isPdf || selectedSourceUrl) && totalPdfPages > 0 && selectedPdfPages.size === 0) {
                event.preventDefault();
                alert('Pilih minimal satu halaman PDF yang ingin disimpan untuk materi ini.');
            }
        });
    }

    async function renderChapterThumbnail(pdfDoc, pageNumber, container) {
        try {
            if (pageNumber > pdfDoc.numPages) return;
            const page = await pdfDoc.getPage(pageNumber);
            const viewport = page.getViewport({ scale: 0.15 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            await page.render({ canvasContext: context, viewport }).promise;
            container.innerHTML = '';
            container.appendChild(canvas);
        } catch (e) {
            console.error('Failed to render thumbnail:', e);
        }
    }

    function buildChapterCard(judul, halamanAwal, halamanAkhir, isOptional = false) {
        const card = document.createElement('button');
        card.type = 'button';
        card.className = 'chapter-detect-card' + (isOptional ? ' optional' : '');

        const thumb = document.createElement('div');
        thumb.className = 'chapter-detect-thumb';
        const iconName = isOptional ? 'book-marked' : 'file-text';
        thumb.innerHTML = `<i data-lucide="${iconName}" style="width: 20px; height: 20px; color: ${isOptional ? '#6366F1' : '#94a3b8'};"></i>`;

        const info = document.createElement('div');
        info.className = 'chapter-detect-info';

        const titleEl = document.createElement('div');
        titleEl.className = 'chapter-detect-title';
        titleEl.textContent = judul;
        titleEl.title = judul;

        const rangeEl = document.createElement('div');
        rangeEl.className = 'chapter-detect-range';
        rangeEl.textContent = `Halaman ${halamanAwal} - ${halamanAkhir}`;

        info.appendChild(titleEl);
        info.appendChild(rangeEl);
        card.appendChild(thumb);
        card.appendChild(info);

        card.addEventListener('click', () => {
            document.querySelectorAll('.chapter-detect-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            const judulInput = document.querySelector('input[name="judul_bab"]');
            if (judulInput) judulInput.value = judul;

            if (pdfPageStartInput) pdfPageStartInput.value = halamanAwal;
            if (pdfPageEndInput) pdfPageEndInput.value = halamanAkhir;

            applyRangeSelection();

            const targetCard = pdfPagesGrid.querySelector(`.pdf-page-card[data-page-number="${halamanAwal}"]`);
            if (targetCard) targetCard.scrollIntoView({ block: 'center', behavior: 'smooth' });
        });

        return { card, thumb };
    }

    async function detectPdfChapters(source) {
        const chaptersPanel = document.getElementById('pdf_detected_chapters_panel');
        const chaptersList = document.getElementById('pdf_detected_chapters_list');
        const chaptersLoading = document.getElementById('pdf_detected_chapters_loading');
        const extrasSection = document.getElementById('pdf_detected_extras_section');
        const extrasList = document.getElementById('pdf_detected_extras_list');

        if (!chaptersPanel || !chaptersList || !chaptersLoading) return;

        chaptersPanel.style.display = 'block';
        chaptersList.innerHTML = '';
        if (extrasSection) extrasSection.style.display = 'none';
        if (extrasList) extrasList.innerHTML = '';
        chaptersLoading.style.display = 'block';

        const formData = new FormData();
        const csrfTokenInput = document.querySelector('input[name="_token"]');
        formData.append('_token', csrfTokenInput ? csrfTokenInput.value : '');

        let endpoint = '';
        const isFileObject = typeof File !== 'undefined' && source instanceof File;

        if (isFileObject) {
            formData.append('pdf_file', source);
            endpoint = '{{ route("materi.bab.temp-detect") }}';
        } else {
            const selectedOption = pdfSourceSelect?.selectedOptions?.[0];
            const path = selectedOption ? selectedOption.value : '';
            if (!path) {
                chaptersPanel.style.display = 'none';
                return;
            }
            formData.append('pdf_source_path', path);
            endpoint = '{{ route("materi.bab.detect-existing") }}';
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            chaptersLoading.style.display = 'none';

            if (data.success && data.chapters && data.chapters.length > 0) {
                // --- Render main chapter cards ---
                data.chapters.forEach(ch => {
                    const { card, thumb } = buildChapterCard(ch.judul_bab, ch.halaman_awal, ch.halaman_akhir, false);
                    chaptersList.appendChild(card);
                    if (currentPdfDoc) renderChapterThumbnail(currentPdfDoc, ch.halaman_awal, thumb);
                });

                // --- Compute and render optional front / back matter ---
                const pageCount = Number.isInteger(data.page_count) ? data.page_count : (totalPdfPages || null);
                const firstChapter = data.chapters[0];
                const lastChapter = data.chapters[data.chapters.length - 1];
                let hasExtras = false;

                // Front matter: page 1 up to (first chapter start - 1)
                if (firstChapter.halaman_awal > 1) {
                    const frontEnd = firstChapter.halaman_awal - 1;
                    const { card, thumb } = buildChapterCard('Pendahuluan (Cover, Daftar Isi, dll.)', 1, frontEnd, true);
                    if (extrasList) extrasList.appendChild(card);
                    if (currentPdfDoc) renderChapterThumbnail(currentPdfDoc, 1, thumb);
                    hasExtras = true;
                }

                // Back matter: (last chapter end + 1) to total pages
                if (pageCount && lastChapter.halaman_akhir < pageCount) {
                    const backStart = lastChapter.halaman_akhir + 1;
                    const { card, thumb } = buildChapterCard('Penutup (Daftar Pustaka, Profil Penulis, dll.)', backStart, pageCount, true);
                    if (extrasList) extrasList.appendChild(card);
                    if (currentPdfDoc) renderChapterThumbnail(currentPdfDoc, backStart, thumb);
                    hasExtras = true;
                }

                if (hasExtras && extrasSection) extrasSection.style.display = 'block';

                lucide.createIcons();
            } else {
                chaptersPanel.style.display = 'none';
            }
        } catch (error) {
            console.error('Failed to detect chapters:', error);
            chaptersLoading.style.display = 'none';
            chaptersPanel.style.display = 'none';
        }
    }

    lucide.createIcons();
</script>
