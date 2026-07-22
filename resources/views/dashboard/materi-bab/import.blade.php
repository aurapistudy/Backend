<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Bab dari PDF - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @include('components.dashboard-shell-styles')
    <style>
        .back-link-clean {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-primary);
            text-decoration: none;
            transition: 0.2s ease;
            margin-bottom: 1rem;
        }

        .back-link-clean:hover {
            color: var(--color-accent);
            transform: translateX(-3px);
        }

        .form-container {
            background: var(--color-white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0,0,0,0.04);
            max-width: 1080px;
        }

        .hero-panel {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-card {
            border-radius: 18px;
            padding: 1.25rem 1.35rem;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
            border: 1px solid rgba(248, 184, 3, 0.18);
        }

        .hero-title {
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--color-text);
        }

        .hero-subtitle {
            color: var(--color-text-light);
            line-height: 1.7;
            font-size: 0.96rem;
        }

        .hero-book-name {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-top: 0.9rem;
            padding: 0.7rem 0.9rem;
            border-radius: 12px;
            background: #FFFFFF;
            border: 1px solid rgba(17, 24, 39, 0.08);
            font-weight: 700;
        }

        .step-2 { display: none; }
        
        .dropzone {
            border: 2px dashed var(--color-gray);
            border-radius: 16px;
            padding: 3.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #F8FAFC;
        }

        .dropzone:hover, .dropzone.dragover {
            border-color: var(--color-accent);
            background: #FFFBEB;
        }

        .dropzone-icon {
            width: 56px;
            height: 56px;
            color: #94A3B8;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .dropzone:hover .dropzone-icon {
            color: var(--color-accent);
            transform: scale(1.1);
        }

        .dropzone-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }

        .dropzone-desc {
            color: var(--color-text-light);
            font-size: 0.95rem;
        }

        .chapter-row {
            display: grid;
            grid-template-columns: 70px 1fr 120px 120px auto;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
            background: #F8FAFC;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .chapter-row-thumb {
            width: 50px;
            height: 65px;
            border-radius: 6px;
            background: #E2E8F0;
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chapter-row-thumb canvas {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--color-gray);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 4px rgba(248, 184, 3, 0.1);
        }

        .btn {
            padding: 0.95rem 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--color-accent);
            color: #1F2937;
        }

        .btn-primary:hover:not(:disabled) {
            background: #E6A500;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(248, 184, 3, 0.4);
        }

        .btn-secondary {
            background: var(--color-gray);
            color: var(--color-text);
        }

        .btn-secondary:hover:not(:disabled) {
            background: #D1D5DB;
        }

        .btn-danger {
            background: #FEE2E2;
            color: #991B1B;
            padding: 0.75rem;
            border-radius: 8px;
        }

        .btn-danger:hover {
            background: #FECACA;
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .alert {
            padding: 1rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
        }

        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-info {
            background: #EFF6FF;
            color: #1E3A8A;
            border: 1px solid #BFDBFE;
        }

        .review-tools {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.9rem;
            padding: 1rem;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            background: #F8FAFC;
            margin-bottom: 1rem;
        }

        .tool-group {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .tool-label {
            color: var(--color-text-light);
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .tool-inline {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .planned-chapters-tool {
            grid-column: span 2;
        }

        .planned-chapters-tool textarea {
            min-height: 92px;
            resize: vertical;
        }

        .chapters-panel {
            max-height: 420px;
            overflow: auto;
            padding-right: 0.35rem;
            margin-bottom: 1rem;
        }

        .form-actions-sticky {
            position: sticky;
            bottom: 0;
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 0.75rem;
            padding-top: 1rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.78), #FFFFFF 35%);
        }

        .loading-overlay {
            display: none;
            text-align: center;
            padding: 2rem;
            background: #F8FAFC;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            margin-top: 1.5rem;
        }

        .spinner {
            border: 4px solid rgba(0,0,0,0.1);
            border-left-color: var(--color-accent);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .col-header {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-text-light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .chapter-header-row {
            display: grid;
            grid-template-columns: 70px 1fr 120px 120px auto;
            gap: 1rem;
            padding: 0 1rem 0.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 2px solid var(--color-gray);
        }

        @media (max-width: 768px) {
            .chapter-row, .chapter-header-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            .chapter-header-row { display: none; }
            .planned-chapters-tool { grid-column: auto; }
            .review-tools, .form-actions-sticky { grid-template-columns: 1fr; }
            .chapters-panel { max-height: 55vh; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        <main class="main-content">
            <header class="header-bar">
                <h1 class="header-title">Import Bab Otomatis</h1>
            </header>

            <div class="content-area">
                <a href="{{ route('materi.show', $materi->id) }}" class="back-link-clean">
                    <i data-lucide="arrow-left"></i>
                    Kembali ke detail mata pelajaran
                </a>

                <div class="form-container">
                    <div class="hero-panel">
                        <div class="hero-card">
                            <div class="hero-title">Auto-Split PDF ke Beberapa Bab</div>
                            <div class="hero-subtitle">
                                Upload satu file PDF buku yang utuh. Sistem AI (Gemini) dan deteksi struktur PDF akan mencari daftar isi, lalu otomatis membaginya menjadi bab-bab terpisah untuk Anda.
                            </div>
                            <div class="hero-book-name">
                                <i data-lucide="book-open"></i>
                                <span>{{ $materi->judul }}</span>
                            </div>
                        </div>
                    </div>

                    <div id="errorAlert" class="alert alert-error" style="display: none;"></div>

                    <!-- STEP 1: UPLOAD -->
                    <div id="step1" class="step-1">
                        <form id="uploadForm">
                            @csrf
                            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" style="display:none">
                            
                            <div class="dropzone" id="dropzone" onclick="document.getElementById('pdf_file').click()">
                                <i data-lucide="file-down" class="dropzone-icon"></i>
                                <div class="dropzone-title">Klik atau seret file PDF ke sini</div>
                                <div class="dropzone-desc" id="file_name_display">Maksimal 100 MB</div>
                            </div>
                            
                            <div class="loading-overlay" id="loadingOverlay">
                                <div class="spinner"></div>
                                <div style="font-weight: 700; font-size: 1.1rem; color: #111827;">Menganalisis Dokumen...</div>
                                <div style="color: #6B7280; margin-top: 0.5rem; font-size: 0.9rem;">Sistem sedang mencari daftar isi dan mendeteksi halaman bab.</div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%;" id="btnUpload" disabled>
                                <i data-lucide="scan-search"></i>
                                Mulai Deteksi Otomatis
                            </button>
                        </form>
                    </div>

                    <!-- STEP 2: REVIEW & STORE -->
                    <div id="step2" class="step-2">
                        <div style="background: #ECFDF3; color: #166534; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #A7F3D0; display: flex; gap: 0.75rem; align-items: flex-start;">
                            <i data-lucide="check-circle" style="flex-shrink: 0;"></i>
                            <div>
                                <strong style="display: block; margin-bottom: 0.25rem;">Berhasil Mendeteksi Bab</strong>
                                Cek hasil deteksi di bawah ini. Anda dapat memperbaiki judul atau menyesuaikan halaman awal dan akhir sebelum menyimpannya.
                            </div>
                        </div>

                        <form id="storeForm" method="POST" action="{{ route('materi.bab.import.store', $materi->id) }}">
                            @csrf
                            <input type="hidden" name="source_path" id="source_path">

                            <div class="alert alert-info" id="pageInfo" style="display: none;"></div>

                            <div class="review-tools">
                                <div class="tool-group">
                                    <label class="tool-label" for="pagesPerChapter">Pecah cepat</label>
                                    <div class="tool-inline">
                                        <input type="number" id="pagesPerChapter" class="form-input" min="1" placeholder="Hal/bab">
                                        <button type="button" class="btn btn-secondary" onclick="splitByPageCount()" title="Pecah PDF per jumlah halaman">
                                            <i data-lucide="split"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="tool-group">
                                    <label class="tool-label" for="bulkTitles">Judul bab</label>
                                    <input type="text" id="bulkTitles" class="form-input" placeholder="Pisahkan dengan koma">
                                </div>

                                <div class="tool-group planned-chapters-tool">
                                    <label class="tool-label" for="plannedChapters">Rencana bab</label>
                                    <textarea id="plannedChapters" class="form-input" placeholder="Satu baris per bab. Contoh:&#10;Bab 1 - Pendahuluan | 1-12&#10;Bab 2 - Pembahasan | 13-28"></textarea>
                                </div>

                                <div class="tool-group">
                                    <label class="tool-label">Aksi</label>
                                    <button type="button" class="btn btn-secondary" onclick="applyBulkTitles()">
                                        <i data-lucide="list-plus"></i>
                                        Terapkan Judul
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="applyPlannedChapters()">
                                        <i data-lucide="clipboard-list"></i>
                                        Pakai Rencana
                                    </button>
                                </div>
                            </div>
                            
                            <div class="chapter-header-row">
                                <div class="col-header">Preview</div>
                                <div class="col-header">Judul Bab</div>
                                <div class="col-header">Hal. Awal</div>
                                <div class="col-header">Hal. Akhir</div>
                                <div></div>
                            </div>

                            <div class="chapters-panel">
                                <div id="chaptersList"></div>
                            </div>

                            <div class="form-actions-sticky">
                                <button type="button" class="btn btn-secondary" onclick="addChapterRow()" style="border: 2px dashed var(--color-gray); background: #FFFFFF;">
                                    <i data-lucide="plus"></i> Tambah Baris
                                </button>
                                
                                <button type="submit" class="btn btn-primary" id="btnSave">
                                    <i data-lucide="save"></i>
                                    Simpan & Potong PDF Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
        
        let chapterIndex = 0;
        let detectedPageCount = null;
        
        const fileInput = document.getElementById('pdf_file');
        const dropzone = document.getElementById('dropzone');
        const fileNameDisplay = document.getElementById('file_name_display');
        const btnUpload = document.getElementById('btnUpload');

        // Drag & drop logic
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                updateFileInfo();
            }
        }, false);

        fileInput.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (file.type !== 'application/pdf') {
                    alert('Harap pilih file PDF.');
                    fileInput.value = '';
                    fileNameDisplay.textContent = 'Maksimal 100 MB';
                    btnUpload.disabled = true;
                    return;
                }
                fileNameDisplay.innerHTML = `<strong style="color: var(--color-text)">${file.name}</strong> (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
                btnUpload.disabled = false;
            }
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

        function addChapterRow(judul = '', halAwal = '', halAkhir = '') {
            const container = document.getElementById('chaptersList');
            const row = document.createElement('div');
            row.className = 'chapter-row';
            row.innerHTML = `
                <div class="chapter-row-thumb">
                    <i data-lucide="file-text" style="width: 20px; height: 20px; color: #94a3b8;"></i>
                </div>
                <input type="text" name="chapters[${chapterIndex}][judul_bab]" class="form-input chapter-title-input" placeholder="Contoh: Bab 1 - Pendahuluan" required>
                <input type="number" name="chapters[${chapterIndex}][halaman_awal]" class="form-input chapter-start-input" placeholder="Hal. Awal" min="1" required>
                <input type="number" name="chapters[${chapterIndex}][halaman_akhir]" class="form-input chapter-end-input" placeholder="Hal. Akhir" min="1" required>
                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" title="Hapus baris">
                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                </button>
            `;
            container.appendChild(row);
            row.querySelector('.chapter-title-input').value = judul;
            
            const startInput = row.querySelector('.chapter-start-input');
            const endInput = row.querySelector('.chapter-end-input');
            const thumb = row.querySelector('.chapter-row-thumb');
            
            startInput.value = halAwal;
            endInput.value = halAkhir;
            
            const updateRowThumb = () => {
                const pageNum = parseInt(startInput.value, 10);
                if (pageNum && window.currentPdfDoc) {
                    renderChapterThumbnail(window.currentPdfDoc, pageNum, thumb);
                }
            };
            
            startInput.addEventListener('change', updateRowThumb);
            startInput.addEventListener('input', updateRowThumb);
            
            if (halAwal && window.currentPdfDoc) {
                renderChapterThumbnail(window.currentPdfDoc, parseInt(halAwal, 10), thumb);
            }
            
            chapterIndex++;
            lucide.createIcons();
        }

        function clearChapterRows() {
            document.getElementById('chaptersList').innerHTML = '';
            chapterIndex = 0;
        }

        function splitByPageCount() {
            const pagesPerChapter = parseInt(document.getElementById('pagesPerChapter').value, 10);
            if (!detectedPageCount || detectedPageCount < 1) {
                alert('Total halaman PDF belum bisa dibaca. Isi range halaman secara manual.');
                return;
            }
            if (!pagesPerChapter || pagesPerChapter < 1) {
                alert('Isi jumlah halaman per bab terlebih dahulu.');
                return;
            }

            clearChapterRows();
            let chapterNumber = 1;
            for (let start = 1; start <= detectedPageCount; start += pagesPerChapter) {
                const end = Math.min(start + pagesPerChapter - 1, detectedPageCount);
                addChapterRow(`Bab ${chapterNumber}`, start, end);
                chapterNumber++;
            }
        }

        function applyBulkTitles() {
            const titles = document.getElementById('bulkTitles').value
                .split(',')
                .map(title => title.trim())
                .filter(Boolean);
            const rows = Array.from(document.querySelectorAll('.chapter-row'));

            if (titles.length === 0) {
                alert('Isi judul bab, pisahkan dengan koma.');
                return;
            }

            while (rows.length < titles.length) {
                addChapterRow('', '', '');
                rows.push(document.querySelectorAll('.chapter-row')[rows.length]);
            }

            titles.forEach((title, index) => {
                rows[index].querySelector('.chapter-title-input').value = title;
            });
        }

        function parsePlannedChapterLine(line) {
            const trimmed = line.trim();
            if (!trimmed) return null;

            const rangeMatch = trimmed.match(/(?:\||,|;)?\s*(\d+)\s*(?:-|–|—|sampai|sd|s\/d|to)\s*(\d+)\s*$/i)
                || trimmed.match(/(?:hal(?:aman)?\.?|page|p)\s*(\d+)\s*(?:-|–|—|sampai|sd|s\/d|to)\s*(\d+)\s*$/i);

            if (!rangeMatch) {
                return { title: trimmed, start: '', end: '' };
            }

            const start = parseInt(rangeMatch[1], 10);
            const end = parseInt(rangeMatch[2], 10);
            const title = trimmed.slice(0, rangeMatch.index).replace(/[|,;:\s]+$/, '').trim() || `Bab ${chapterIndex + 1}`;

            return { title, start, end };
        }

        function applyPlannedChapters() {
            const lines = document.getElementById('plannedChapters').value
                .split(/\r?\n/)
                .map(parsePlannedChapterLine)
                .filter(Boolean);

            if (lines.length === 0) {
                alert('Tempel rencana bab dulu, satu baris per bab.');
                return;
            }

            clearChapterRows();
            lines.forEach((chapter) => addChapterRow(chapter.title, chapter.start, chapter.end));
        }

        function validateChapterRows() {
            const rows = Array.from(document.querySelectorAll('.chapter-row'));
            if (rows.length === 0) {
                alert('Tambahkan minimal satu bab untuk dipotong dari PDF.');
                return false;
            }

            for (const row of rows) {
                const title = row.querySelector('.chapter-title-input').value.trim();
                const start = parseInt(row.querySelector('.chapter-start-input').value, 10);
                const end = parseInt(row.querySelector('.chapter-end-input').value, 10);

                if (!title || !start || !end) {
                    alert('Lengkapi judul, halaman awal, dan halaman akhir di semua baris.');
                    return false;
                }

                if (start > end) {
                    alert(`Range halaman "${title}" terbalik. Halaman awal harus lebih kecil atau sama dengan halaman akhir.`);
                    return false;
                }

                if (detectedPageCount && end > detectedPageCount) {
                    alert(`Range halaman "${title}" melewati total PDF (${detectedPageCount} halaman).`);
                    return false;
                }
            }

            return true;
        }

        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const file = fileInput.files[0];
            if (!file) return;
            
            btnUpload.style.display = 'none';
            dropzone.style.display = 'none';
            document.getElementById('loadingOverlay').style.display = 'block';
            document.getElementById('errorAlert').style.display = 'none';
            
            const formData = new FormData();
            formData.append('pdf_file', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const res = await fetch('{{ route("materi.bab.import.detect", $materi->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                
                if (res.ok && data.success) {
                    if (typeof window.pdfjsLib !== 'undefined') {
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                        try {
                            const buffer = await file.arrayBuffer();
                            window.currentPdfDoc = await window.pdfjsLib.getDocument({ data: buffer }).promise;
                        } catch (pdfErr) {
                            console.error('Failed to load PDF locally:', pdfErr);
                        }
                    }
                    
                    document.getElementById('step1').style.display = 'none';
                    document.getElementById('step2').style.display = 'block';
                    document.getElementById('source_path').value = data.source_path;
                    detectedPageCount = Number.isInteger(data.page_count) ? data.page_count : null;

                    const pageInfo = document.getElementById('pageInfo');
                    if (detectedPageCount) {
                        pageInfo.textContent = `PDF terbaca ${detectedPageCount} halaman. Jika hasil deteksi AI kosong, gunakan pecah cepat per jumlah halaman atau isi range manual.`;
                        pageInfo.style.display = 'block';
                    } else {
                        pageInfo.textContent = 'Total halaman belum bisa dibaca otomatis. Deteksi bab tetap bisa dipakai, atau isi range halaman manual.';
                        pageInfo.style.display = 'block';
                    }
                    
                    if (data.chapters && data.chapters.length > 0) {
                        data.chapters.forEach(ch => addChapterRow(ch.judul_bab, ch.halaman_awal, ch.halaman_akhir));
                    } else {
                        addChapterRow('Materi Lengkap', 1, detectedPageCount || '');
                        const alertBox = document.getElementById('errorAlert');
                        alertBox.innerHTML = 'Deteksi otomatis belum menemukan bab. File tetap berhasil diupload; gunakan pecah cepat, terapkan judul bab, atau edit range halaman di panel bawah.';
                        alertBox.style.display = 'block';
                    }
                } else {
                    throw new Error(data.message || data.error || 'Gagal memproses file PDF.');
                }
            } catch (err) {
                const alertBox = document.getElementById('errorAlert');
                alertBox.textContent = err.message || 'Terjadi kesalahan koneksi saat upload.';
                alertBox.style.display = 'block';
                
                btnUpload.style.display = 'flex';
                dropzone.style.display = 'block';
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        });
        
        document.getElementById('storeForm').addEventListener('submit', (event) => {
            if (!validateChapterRows()) {
                event.preventDefault();
                return;
            }

            const btn = document.getElementById('btnSave');
            btn.disabled = true;
            btn.innerHTML = '<div class="spinner" style="width: 20px; height: 20px; border-width: 2px; margin: 0;"></div> Menyimpan & Memotong PDF...';
        });

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
    @include('components.modal')
</body>
</html>
