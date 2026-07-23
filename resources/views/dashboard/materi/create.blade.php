<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Pelajaran - Ruma Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @include('components.dashboard-shell-styles')
    <style>
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .book-hero-grid {
            display: grid;
            grid-template-columns: minmax(220px, 280px) minmax(0, 1fr);
            gap: 1.75rem;
            align-items: start;
            margin-bottom: 1.75rem;
        }

        .book-cover-column,
        .book-detail-column {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 20px;
            background: #FFFFFF;
        }

        .book-cover-column {
            padding: 1.25rem;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
        }

        .book-detail-column {
            padding: 1.5rem;
        }

        .book-cover-stage {
            width: 100%;
            aspect-ratio: 3 / 4;
            border-radius: 18px;
            border: 1px dashed rgba(17, 24, 39, 0.14);
            background: linear-gradient(180deg, rgba(248, 184, 3, 0.12) 0%, rgba(255, 255, 255, 0.94) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .book-cover-stage:hover {
            transform: translateY(-2px);
            border-color: rgba(248, 184, 3, 0.55);
            box-shadow: 0 14px 28px rgba(17, 24, 39, 0.08);
        }

        .book-cover-stage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .book-cover-placeholder {
            text-align: center;
            padding: 1.5rem;
            color: var(--color-text-light);
        }

        .book-cover-placeholder i {
            width: 42px;
            height: 42px;
            color: var(--color-accent);
            margin-bottom: 0.75rem;
        }

        .book-cover-placeholder strong {
            display: block;
            color: var(--color-text);
            font-size: 1rem;
            margin-bottom: 0.35rem;
        }

        .book-cover-caption {
            margin-top: 0.9rem;
            padding: 0.9rem 1rem;
            border-radius: 14px;
            background: rgba(17, 24, 39, 0.03);
            color: var(--color-text-light);
            font-size: 0.88rem;
            line-height: 1.55;
        }

        .cover-stage-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.85rem;
        }

        .cover-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.56);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 2200;
        }

        .cover-modal.is-open {
            display: flex;
        }

        .cover-modal-dialog {
            width: min(980px, 100%);
            max-height: calc(100vh - 3rem);
            overflow-y: auto;
            border-radius: 24px;
            background: #FFFFFF;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.26);
            border: 1px solid rgba(17, 24, 39, 0.08);
        }

        .cover-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.4rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(17, 24, 39, 0.08);
        }

        .cover-modal-close {
            border: none;
            background: rgba(17, 24, 39, 0.06);
            color: var(--color-text);
            width: 42px;
            height: 42px;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cover-modal-grid {
            display: grid;
            grid-template-columns: minmax(240px, 300px) minmax(0, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        .cover-modal-preview-card {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            padding: 1rem;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
            position: sticky;
            top: 0;
        }

        .cover-modal-preview-card img {
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 14px;
            display: none;
            background: #F8FAFC;
        }

        .cover-modal-preview-empty {
            width: 100%;
            aspect-ratio: 3 / 4;
            border-radius: 14px;
            border: 1px dashed rgba(17, 24, 39, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--color-text-light);
            padding: 1rem;
            background: rgba(255, 255, 255, 0.85);
        }

        .cover-modal-panel {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            padding: 1rem;
            background: #FFFFFF;
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
            border-color: var(--color-accent);
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
            background: var(--color-accent);
            color: #1F2937;
        }
        
        .btn-primary:hover {
            background: #E6A500;
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

        .ta-period-banner {
            background: #E8F5E9;
            border: 1px solid rgba(56, 142, 60, 0.35);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #1B5E20;
            margin-bottom: 1.25rem;
        }

        .ta-period-banner.warning {
            background: #FFF9E6;
            border-color: rgba(248, 184, 3, 0.45);
            color: #7A4A00;
        }

        .ta-period-banner i {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ta-period-banner strong {
            display: block;
            margin-bottom: 0.25rem;
        }

        .ta-period-banner span {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        
        .required {
            color: #DC2626;
        }

        .back-link-clean {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--color-primary);
    text-decoration: none;
    transition: 0.2s ease;
}

.back-link-clean i {
    width: 18px;
    height: 18px;
}

.back-link-clean:hover {
    color: var(--color-accent);
    transform: translateX(-3px);
}

        .hint {
            color: var(--color-text-light);
            font-size: 0.85rem;
            margin-top: 0.35rem;
            display: block;
        }

        .cover-mode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .cover-mode-card {
            border: 1px solid rgba(17, 24, 39, 0.1);
            border-radius: 16px;
            padding: 1rem;
            background: #FFFFFF;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .cover-mode-card.active {
            border-color: var(--color-accent);
            box-shadow: 0 10px 24px rgba(248, 184, 3, 0.18);
            transform: translateY(-1px);
        }

        .cover-mode-card input {
            margin-right: 0.55rem;
        }

        .cover-mode-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--color-text);
        }

        .cover-mode-desc {
            margin-top: 0.4rem;
            font-size: 0.86rem;
            color: var(--color-text-light);
            line-height: 1.5;
        }

        .cover-ai-panel {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid var(--color-gray);
            border-radius: 16px;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
        }

        .cover-ai-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .cover-ai-preview {
            margin-top: 1rem;
            display: none;
            grid-template-columns: minmax(220px, 280px) 1fr;
            gap: 1rem;
            align-items: start;
        }

        .cover-ai-preview-card {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            padding: 0.8rem;
            background: #FFFFFF;
            box-shadow: 0 8px 20px rgba(17, 24, 39, 0.08);
        }

        .cover-ai-preview-card img {
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 12px;
            display: block;
            background: #F8FAFC;
        }

        .cover-ai-preview-meta {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 18px;
            padding: 1rem;
            background: #FFFFFF;
        }

        .cover-ai-status {
            margin-top: 0.8rem;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .cover-ai-status.pending {
            color: #92400E;
        }

        .cover-ai-status.confirmed {
            color: #166534;
        }

        .cover-ai-prompt {
            margin-top: 0.85rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: #F8FAFC;
            color: var(--color-text-light);
            font-size: 0.83rem;
            line-height: 1.5;
            white-space: pre-wrap;
        }

        .cover-ai-loading {
            display: none;
            margin-top: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: #FFF7D6;
            color: #8A6500;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .chapter-builder {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid var(--color-gray);
            border-radius: 16px;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
        }

        .chapter-builder-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .chapter-builder-note {
            padding: 0.9rem 1rem;
            border-radius: 12px;
            background: #F8FAFC;
            color: var(--color-text-light);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .chapter-list {
            display: grid;
            gap: 1rem;
        }

        .chapter-item {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 16px;
            padding: 1rem;
            background: var(--color-white);
            box-shadow: 0 6px 16px rgba(17, 24, 39, 0.06);
        }

        .chapter-item-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.9rem;
        }

        .chapter-item-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-text);
        }

        .chapter-quiz-note {
            margin-top: 0.85rem;
            padding: 0.8rem 0.95rem;
            border-radius: 12px;
            background: #FFF7D6;
            color: #8A6500;
            font-size: 0.87rem;
        }

        .pdf-selection-panel {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid var(--color-gray);
            border-radius: 14px;
            background: linear-gradient(180deg, #FFFCF2 0%, #FFFFFF 100%);
        }

        .pdf-flow-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--color-text);
            margin-bottom: 0.35rem;
        }

        .pdf-flow-copy {
            color: var(--color-text-light);
            font-size: 0.9rem;
            line-height: 1.55;
            margin-bottom: 1rem;
        }

        .pdf-mode-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .pdf-mode-card {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            border: 1px solid rgba(17, 24, 39, 0.1);
            border-radius: 14px;
            background: #FFFFFF;
            padding: 0.95rem;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .pdf-mode-card.active {
            border-color: var(--color-accent);
            box-shadow: 0 10px 22px rgba(248, 184, 3, 0.16);
        }

        .pdf-mode-card input {
            margin-top: 0.2rem;
            accent-color: var(--color-accent);
        }

        .pdf-mode-title {
            display: block;
            font-weight: 800;
            color: var(--color-text);
            margin-bottom: 0.25rem;
        }

        .pdf-mode-desc {
            display: block;
            color: var(--color-text-light);
            font-size: 0.86rem;
            line-height: 1.45;
        }

        .pdf-plan-panel {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            background: #FFFFFF;
        }

        .pdf-plan-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.85rem;
        }

        .pdf-plan-actions .btn {
            flex: 0 0 auto;
        }

        .pdf-selection-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .pdf-selection-summary {
            font-size: 0.9rem;
            color: var(--color-text);
            font-weight: 600;
        }

        .pdf-selection-range {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 180px));
            gap: 0.9rem;
            margin-bottom: 1rem;
        }

        .pdf-selection-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .pdf-action-btn {
            border: 1px solid rgba(31, 41, 55, 0.12);
            background: var(--color-white);
            color: var(--color-text);
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .pdf-pages-grid {
            display: none;
        }

        .pdf-page-card {
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 16px;
            background: var(--color-white);
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(17, 24, 39, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .pdf-page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.1);
        }

        .pdf-page-card.selected {
            border-color: rgba(248, 184, 3, 0.95);
            background: linear-gradient(180deg, #FFF5CC 0%, #FFF0B3 100%);
            box-shadow: 0 14px 28px rgba(248, 184, 3, 0.28);
        }

        .pdf-page-preview {
            position: relative;
            aspect-ratio: 3 / 4;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .pdf-page-preview canvas {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .pdf-page-check {
            position: absolute;
            top: 0.6rem;
            right: 0.6rem;
            width: 24px;
            height: 24px;
            accent-color: var(--color-accent);
            cursor: pointer;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.18));
        }

        .pdf-page-meta {
            padding: 0.85rem 0.9rem 1rem;
        }

        .pdf-page-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--color-text);
        }

        .pdf-page-subtitle {
            margin-top: 0.2rem;
            font-size: 0.8rem;
            color: var(--color-text-light);
        }

        .pdf-selection-empty {
            padding: 1rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .pdf-selection-loading {
            padding: 1rem;
            border-radius: 12px;
            background: #FFF7D6;
            color: #8A6500;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .book-cover-column .cover-mode-grid,
        .book-cover-column .cover-ai-preview {
            grid-template-columns: 1fr;
        }

        .book-cover-column .cover-mode-card,
        .book-cover-column .cover-ai-panel,
        .book-cover-column .cover-ai-preview-card,
        .book-cover-column .cover-ai-preview-meta {
            border-radius: 14px;
        }

        @media (max-width: 640px) {.book-hero-grid { grid-template-columns: 1fr; } .cover-modal-grid { grid-template-columns: 1fr; } .cover-ai-preview { grid-template-columns: 1fr; } .pdf-selection-range, .pdf-mode-grid { grid-template-columns: 1fr; }}

        /* === Chapter auto-detect cards (create page) === */
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
        .chapter-detect-card.active { border-color: #E6A500; background: #FFF9E6; }
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
        .chapter-detect-thumb canvas { width: 100%; height: 100%; object-fit: contain; display: block; }
        .chapter-detect-info { display: flex; flex-direction: column; min-width: 0; }
        .chapter-detect-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--color-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chapter-detect-range { font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem; }
        .chapter-detect-card.optional { border-color: rgba(99,102,241,0.2); background: #F5F3FF; }
        .chapter-detect-card.optional:hover { border-color: #6366F1; background: #EEF2FF; box-shadow: 0 4px 12px rgba(99,102,241,0.15); }
        .chapter-detect-card.optional.active { border-color: #4F46E5; background: #E0E7FF; }
        .chapter-detect-card.optional .chapter-detect-thumb { background: #E0E7FF; }
        .chapter-detect-card.optional .chapter-detect-title { color: #3730A3; }
        .chapter-optional-label {
            font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.05em; color: #6366F1;
            margin-top: 1rem; margin-bottom: 0.5rem;
            display: flex; align-items: center; gap: 0.35rem;
        }
        </style>
</head>
<body>
    <div class="dashboard-container">
        @include('components.dashboard-sidebar')

        
        <!-- Main Content -->
        <main class="main-content">
           
            
            <!-- Header Bar -->
            <header class="header-bar">
                <h1 class="header-title">Tambah Mata Pelajaran Baru</h1>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <div style="margin-bottom: 1.5rem;">
                    <a href="{{ route('materi.index') }}" class="back-link-clean">
                    <i data-lucide="arrow-left"></i>
                    Daftar Mata Pelajaran
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

                @if($tahunAktif)
                    <div class="ta-period-banner">
                        <i data-lucide="calendar-check"></i>
                        <div>
                            <strong>Periode aktif: Semester {{ $tahunAktif->semesterLabel() }} — {{ $tahunAktif->nama }}</strong>
                            <span>Materi yang Anda buat otomatis masuk ke semester ini. Admin dapat mengganti periode aktif di menu Tahun Akademik.</span>
                        </div>
                    </div>
                @else
                    <div class="ta-period-banner warning">
                        <i data-lucide="alert-triangle"></i>
                        <div>
                            <strong>Belum ada tahun akademik aktif</strong>
                            <span>Hubungi admin untuk mengaktifkan periode semester sebelum menambah mata pelajaran.</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data" class="form-container" @if(!$tahunAktif) onsubmit="return false;" @endif>
                    @csrf
                    <input type="hidden" name="generated_cover_temp_path" id="generated_cover_temp_path" value="{{ old('generated_cover_temp_path') }}">
                    <input type="hidden" name="use_generated_cover" id="use_generated_cover" value="{{ old('use_generated_cover', 0) }}">

                    <div class="section-title"><i data-lucide="book-open-text"></i> Detail Mata Pelajaran</div>
                    <div class="section-subtitle">Lengkapi cover, judul, deskripsi, dan level terlebih dulu sebelum mengisi materi pertama.</div>

                    <div class="book-hero-grid">
                        <div class="book-cover-column">
                            <button type="button" id="open_cover_modal_btn" class="book-cover-stage">
                                <img id="book_cover_stage_image" src="" alt="Preview cover mata pelajaran">
                                <div id="book_cover_stage_placeholder" class="book-cover-placeholder">
                                    <i data-lucide="image-plus"></i>
                                    <strong>Tambahkan cover mata pelajaran</strong>
                                    Cover akan tampil di daftar mata pelajaran dan halaman detail mata pelajaran.
                                </div>
                            </button>

                            <div class="book-cover-caption">
                                Cover ini akan dipakai sebagai identitas utama mata pelajaran di daftar, halaman detail, dan area baca siswa.
                            </div>

                            <button type="button" id="open_cover_modal_secondary_btn" class="btn btn-secondary cover-stage-trigger">
                                <i data-lucide="image-plus"></i>
                                Atur Cover Mata Pelajaran
                            </button>
                        </div>

                        <div class="book-detail-column">
                            <div class="form-group">
                                <label class="form-label">
                                    Judul Mata Pelajaran <span class="required">*</span>
                                </label>
                                <input type="text" name="judul" value="{{ old('judul') }}" class="form-input" required>
                                <span class="hint">Masukkan judul mata pelajaran utama yang nanti bisa dipecah menjadi beberapa materi.</span>
                                @error('judul')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Deskripsi Mata Pelajaran</label>
                                <textarea name="deskripsi" rows="6" class="form-textarea">{{ old('deskripsi') }}</textarea>
                                <span class="hint">Deskripsi ini membantu guru dan siswa memahami fokus mata pelajaran sebelum masuk ke materi-materinya.</span>
                                @error('deskripsi')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Level</label>
                                <select name="level_id" class="form-select">
                                    <option value="">Pilih Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="book-cover-caption" style="margin-top: 1.25rem;">
                                Cover dikelola lewat popup agar form mata pelajaran tetap rapi. Klik preview cover di samping atau tombol <strong>Atur Cover Mata Pelajaran</strong>.
                            </div>
                        </div>
                    </div>

                    <div id="cover_modal" class="cover-modal" aria-hidden="true">
                        <div class="cover-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="cover_modal_title">
                            <div class="cover-modal-header">
                                <div>
                                    <div id="cover_modal_title" class="section-title" style="margin-bottom: 0.2rem;"><i data-lucide="image"></i> Atur Cover Mata Pelajaran</div>
                                    <div class="section-subtitle" style="margin-bottom: 0;">Pilih upload manual atau generate dengan Gemini. Cover ini akan dipakai sebagai identitas mata pelajaran.</div>
                                </div>
                                <button type="button" id="close_cover_modal_btn" class="cover-modal-close" aria-label="Tutup popup cover">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>
                            <div class="cover-modal-body">
                                <div class="cover-modal-grid">
                                    <div class="cover-modal-preview-card">
                                        <img id="cover_modal_preview_image" src="" alt="Preview cover di popup">
                                        <div id="cover_modal_preview_empty" class="cover-modal-preview-empty">
                                            Preview cover akan muncul di sini setelah kamu upload atau generate.
                                        </div>
                                        <div class="book-cover-caption">
                                            Setelah cover dikonfirmasi, preview di halaman utama akan langsung ikut diperbarui.
                                        </div>
                                    </div>

                                    <div class="cover-modal-panel">
                                        <div class="cover-mode-grid">
                                            <label class="cover-mode-card active" data-cover-mode-card="manual">
                                                <div>
                                                    <input type="radio" name="cover_mode" value="manual" checked>
                                                    <span class="cover-mode-title">Upload Manual</span>
                                                </div>
                                                <div class="cover-mode-desc">Pilih gambar cover sendiri dari perangkat.</div>
                                            </label>
                                            <label class="cover-mode-card" data-cover-mode-card="ai">
                                                <div>
                                                    <input type="radio" name="cover_mode" value="ai">
                                                    <span class="cover-mode-title">Generate dengan AI</span>
                                                </div>
                                                <div class="cover-mode-desc">Buat cover otomatis dari judul, level, dan deskripsi.</div>
                                            </label>
                                        </div>

                                        <div id="cover_manual_panel" class="form-group">
                                            <label class="form-label">Cover Image (JPG/PNG/WebP)</label>
                                            <input type="file" name="cover_path" id="cover_path" accept=".jpg,.jpeg,.png,.webp" class="form-input">
                                            <small class="hint">Opsional. Jika kosong, sistem menampilkan cover default berbasis judul buku.</small>
                                            <div id="cover_compress_hint" class="hint" style="display:none;"></div>
                                            @error('cover_path')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div id="cover_ai_panel" class="cover-ai-panel" style="display: none;">
                                            <div class="form-group" style="margin-bottom: 0;">
                                                <label class="form-label">Prompt Tambahan (Opsional)</label>
                                                <textarea id="cover_ai_prompt_tambahan" class="form-textarea" rows="3" placeholder="Contoh: gunakan warna cerah, ilustrasi anak belajar, nuansa sains modern."></textarea>
                                                <span class="hint">Kalau kosong, sistem tetap membuat prompt otomatis dari field buku.</span>
                                            </div>

                                            <div class="cover-ai-actions">
                                                <button type="button" id="generate_ai_cover_btn" class="btn btn-primary" style="flex: 0 0 auto;">
                                                    <i data-lucide="sparkles"></i>
                                                    Generate Cover
                                                </button>
                                                <button type="button" id="regenerate_ai_cover_btn" class="btn btn-secondary" style="flex: 0 0 auto; display: none;">
                                                    <i data-lucide="refresh-cw"></i>
                                                    Generate Ulang
                                                </button>
                                                <button type="button" id="discard_ai_cover_btn" class="btn btn-secondary" style="flex: 0 0 auto; display: none;">
                                                    <i data-lucide="trash-2"></i>
                                                    Batal Pakai
                                                </button>
                                            </div>

                                            <div id="cover_ai_loading" class="cover-ai-loading">Model sedang memproses preview cover...</div>

                                            <div id="cover_ai_preview" class="cover-ai-preview">
                                                <div class="cover-ai-preview-card">
                                                    <img id="cover_ai_preview_image" src="" alt="Preview cover AI">
                                                </div>
                                                <div class="cover-ai-preview-meta">
                                                    <div class="section-title" style="margin-bottom: 0;">Preview Cover AI</div>
                                                    <div class="section-subtitle" style="margin-top: 0.35rem; margin-bottom: 0;">Preview ini belum dipakai sebelum kamu konfirmasi.</div>
                                                    <div id="cover_ai_status" class="cover-ai-status pending">Status: menunggu konfirmasi.</div>
                                                    <div class="cover-ai-actions">
                                                        <button type="button" id="confirm_ai_cover_btn" class="btn btn-primary" style="flex: 0 0 auto;">
                                                            <i data-lucide="check"></i>
                                                            Gunakan Cover Ini
                                                        </button>
                                                    </div>
                                                    <div id="cover_ai_prompt_preview" class="cover-ai-prompt" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-title" style="margin-top: 1.5rem;"><i data-lucide="layers"></i> Materi 1 - Konten Awal</div>
                    <div class="section-subtitle">Setelah mata pelajaran dibuat, isi konten pertama ini akan otomatis disimpan sebagai Materi 1.</div>

                    <div class="form-group">
                        <label class="form-label">
                            Judul Materi 1 <span class="required">*</span>
                        </label>
                        <input type="text" name="judul_bab_pertama" value="{{ old('judul_bab_pertama') }}" class="form-input" required placeholder="Contoh: Pengenalan Huruf, Bab 1, Anggota Tubuh">
                        <span class="hint">Judul ini akan dipakai sebagai nama bab pertama.</span>
                        @error('judul_bab_pertama')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Tipe Konten Materi 1 <span class="required">*</span>
                        </label>
                        <select name="tipe_konten" id="tipe_konten" class="form-select" required>
                            <option value="">Pilih Tipe Konten Materi 1</option>
                            <option value="teks" {{ old('tipe_konten') == 'teks' ? 'selected' : '' }}>Teks</option>
                            <option value="file" {{ old('tipe_konten') == 'file' ? 'selected' : '' }}>File</option>
                        </select>
                        <span class="hint">Pilih Teks untuk isi langsung atau File untuk upload dokumen sebagai Materi 1.</span>
                        @error('tipe_konten')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="konten_teks_field" class="form-group" style="display: none;">
                        <label class="form-label">
                            Konten Teks Materi 1 <span class="required">*</span>
                        </label>
                        <textarea name="konten_teks" rows="8" class="form-textarea">{{ old('konten_teks') }}</textarea>
                        @error('konten_teks')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="file_path_field" class="form-group" style="display: none;">
                        <label class="form-label">
                            File Materi 1 (PDF, Word, PowerPoint, TXT) <span class="required">*</span>
                        </label>
                        <input type="file" name="file_path" id="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.odt,.odp,.rtf,.txt" class="form-input">
                        <small class="hint">PDF di atas 10 MB akan dicoba dikompres otomatis sampai 10 MB. File Word, PowerPoint, ODT/ODP, RTF, dan TXT tetap maksimal 10 MB.</small>
                        <input type="hidden" name="pdf_page_selection" id="pdf_page_selection" value="">
                        <div id="pdf_selection_panel" class="pdf-selection-panel" style="display: none;">
                            <div id="pdf_selection_loading" class="pdf-selection-loading" style="display: none;">Sedang membaca jumlah halaman PDF...</div>
                            <div class="pdf-flow-title">Pilih cara menyimpan PDF Materi 1</div>
                            <div id="pdf_selection_summary" class="pdf-selection-summary" style="margin-bottom: 0.8rem;">Belum ada halaman PDF yang dimuat.</div>

                            <div class="pdf-mode-grid">
                                <label class="pdf-mode-card active" data-pdf-mode-card="all">
                                    <input type="radio" name="pdf_save_mode" value="all" checked>
                                    <span>
                                        <span class="pdf-mode-title">Simpan PDF utuh</span>
                                        <span class="pdf-mode-desc">Pakai semua halaman sebagai Materi 1. Ini pilihan paling sederhana.</span>
                                    </span>
                                </label>

                                <label class="pdf-mode-card" data-pdf-mode-card="range">
                                    <input type="radio" name="pdf_save_mode" value="range">
                                    <span>
                                        <span class="pdf-mode-title">Ambil halaman tertentu</span>
                                        <span class="pdf-mode-desc">Isi halaman awal dan akhir kalau Materi 1 cuma sebagian dari PDF.</span>
                                    </span>
                                </label>
                            </div>

                            <div class="pdf-selection-range" id="pdf_range_fields" style="display: none;">
                                <div>
                                    <label class="form-label">Halaman Awal</label>
                                    <input type="number" id="pdf_page_start" class="form-input" min="1" placeholder="Contoh: 3">
                                </div>
                                <div>
                                    <label class="form-label">Halaman Akhir</label>
                                    <input type="number" id="pdf_page_end" class="form-input" min="1" placeholder="Contoh: 12">
                                </div>
                            </div>
                            <div id="pdf_pages_grid" class="pdf-pages-grid"></div>
                            <div id="pdf_selection_empty" class="pdf-selection-empty">PDF akan disimpan utuh. Bagian Materi Tambahan di bawah boleh dilewati jika tidak ingin membuat materi lain.</div>
                            
                            <!-- Auto-detect chapters panel -->
                            <div id="pdf_detect_panel" style="display:none; margin-top:1rem; padding:1rem; border:1px solid rgba(17,24,39,0.08); border-radius:14px; background:linear-gradient(180deg,#F8FAFC 0%,#FFFFFF 100%);">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:0.25rem;">
                                    <div style="font-weight:700; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem;">
                                        <i data-lucide="sparkles" style="width:16px;height:16px;color:#E6A500;"></i>
                                        Bab Berhasil Terdeteksi
                                    </div>
                                </div>
                                <span class="hint" style="display:block; margin-bottom:0.85rem;">Klik salah satu kartu bab di bawah untuk menyalin strukturnya ke form. Sistem akan otomatis mengurutkan semua materi secara kronologis dari halaman paling awal.</span>

                                <div id="pdf_detect_loading" class="pdf-selection-loading" style="display:none;">
                                    Menganalisis bab dari PDF... <span style="font-weight:400;">(mungkin butuh beberapa detik)</span>
                                </div>

                                <div id="pdf_detect_chapters_list" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:0.65rem; margin-bottom:0.5rem;">
                                    <!-- Main chapter cards -->
                                </div>

                                <div id="pdf_detect_extras_section" style="display:none;">
                                    <div class="chapter-optional-label" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <i data-lucide="layers" style="width:14px;height:14px;"></i>
                                            Bagian Tambahan (Opsional)
                                        </div>
                                    </div>
                                    <span class="hint" style="display:block;margin-bottom:0.65rem;">Halaman di luar bab utama — cover, daftar isi, penutup, dll. Klik jika ingin ditambahkan sebagai Materi 1.</span>
                                    
                                    <label class="form-checkbox" style="margin-bottom: 1rem; background: #FFFBEB; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(248,184,3,0.3);">
                                        <input type="checkbox" id="auto_include_extras" value="1" checked>
                                        <span style="font-size: 0.85rem; font-weight: 600; color: #92400E;">Sertakan juga Pendahuluan & Penutup ke daftar materi tambahan saat menyimpan.</span>
                                    </label>

                                    <div id="pdf_detect_extras_list" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:0.65rem;">
                                    </div>
                                </div>

                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed rgba(17,24,39,0.1); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size: 0.85rem; font-weight: 600; color: #059669; display:flex; align-items:center; gap:0.4rem;">
                                        <i data-lucide="check-circle-2" style="width:16px;height:16px;"></i> Pengelompokan bab otomatis selesai.
                                    </span>
                                    <button type="button" id="scroll_to_builder_btn" class="btn btn-secondary" style="padding: 0.5rem 0.85rem; font-size: 0.85rem; border-radius: 8px;">
                                        Lihat Pembagian Materi di Bawah <i data-lucide="arrow-down" style="width:14px;height:14px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('file_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error('pdf_page_selection')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="chapter_builder" class="chapter-builder">
                        <div class="chapter-builder-head">
                            <div>
                                <div class="section-title" style="margin-bottom:0.2rem;"><i data-lucide="library-big"></i> Materi Tambahan (Opsional)</div>
                                <div class="section-subtitle" style="margin-bottom:0;">Kalau perlu, kamu bisa langsung menambahkan Materi 2, Materi 3, dan seterusnya pada saat mata pelajaran dibuat.</div>
                            </div>
                            <button type="button" id="add_chapter_btn" class="btn btn-primary" style="flex: 0 0 auto;">
                                <i data-lucide="plus-circle"></i>
                                Tambah Materi
                            </button>
                        </div>
                        <div class="chapter-builder-note">
                            Materi 1 dibuat dari konten utama di atas. Bagian ini hanya untuk materi tambahan agar struktur mata pelajaran langsung lengkap sejak awal.
                        </div>
                        <div id="chapter_list" class="chapter-list"></div>
                    </div>

                    <div class="section-title" style="margin-top: 1.5rem;"><i data-lucide="settings"></i> Pengaturan</div>
                    <div class="section-subtitle">Atur status buku sebelum disimpan.</div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', true) ? 'checked' : '' }}>
                            <span>Status Mata Pelajaran Aktif</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i>
                            Simpan Mata Pelajaran
                        </button>
                        <a href="{{ route('materi.index') }}" class="btn btn-secondary">
                            <i data-lucide="x"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        const materiForm = document.querySelector('.form-container');
        const tipeKontenSelect = document.getElementById('tipe_konten');
        const fileInput = document.getElementById('file_path');
        const pdfSelectionPanel = document.getElementById('pdf_selection_panel');
        const pdfSelectionLoading = document.getElementById('pdf_selection_loading');
        const pdfSelectionSummary = document.getElementById('pdf_selection_summary');
        const pdfPagesGrid = document.getElementById('pdf_pages_grid');
        const pdfSelectionEmpty = document.getElementById('pdf_selection_empty');
        const pdfPageSelectionInput = document.getElementById('pdf_page_selection');
        const pdfPageStartInput = document.getElementById('pdf_page_start');
        const pdfPageEndInput = document.getElementById('pdf_page_end');
        const pdfRangeFields = document.getElementById('pdf_range_fields');
        const pdfSaveModeInputs = document.querySelectorAll('input[name="pdf_save_mode"]');
        const pdfModeCards = document.querySelectorAll('[data-pdf-mode-card]');
        const pdfChapterPlanInput = document.getElementById('pdf_chapter_plan');
        const applyPdfPlanButton = document.getElementById('apply_pdf_plan_btn');
        const serverUploadLimitKb = @json($maxUploadKb ?? 40960);
        const serverUploadLimitBytes = serverUploadLimitKb * 1024;
        let selectedPdfPages = new Set();
        let totalPdfPages = 0;
        let isSyncingPdfInputs = false;

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        function updatePdfSelectionSummary() {
            if (!pdfSelectionSummary) {
                return;
            }

            if (!totalPdfPages) {
                pdfSelectionSummary.textContent = 'Belum ada halaman PDF yang dimuat.';
                pdfPageSelectionInput.value = '';
                return;
            }

            const selectedMode = getPdfSaveMode();
            const selectedCount = selectedPdfPages.size;

            if (selectedMode === 'all') {
                pdfSelectionSummary.textContent = `Mode: simpan PDF utuh (${totalPdfPages} halaman).`;
                pdfPageSelectionInput.value = '';
                return;
            }

            pdfSelectionSummary.textContent = selectedCount > 0
                ? `Mode: ambil ${selectedCount} dari ${totalPdfPages} halaman.`
                : `Mode: ambil halaman tertentu. Isi halaman awal dan akhir.`;
            pdfPageSelectionInput.value = selectedCount === 0 || selectedCount === totalPdfPages
                ? ''
                : Array.from(selectedPdfPages).sort((a, b) => a - b).join(',');
        }

        function getPdfSaveMode() {
            const checked = document.querySelector('input[name="pdf_save_mode"]:checked');
            return checked ? checked.value : 'all';
        }

        function syncPdfModeUi() {
            const selectedMode = getPdfSaveMode();
            pdfModeCards.forEach((card) => {
                card.classList.toggle('active', card.dataset.pdfModeCard === selectedMode);
            });

            if (pdfRangeFields) {
                pdfRangeFields.style.display = selectedMode === 'range' ? 'grid' : 'none';
            }

            if (selectedMode === 'all') {
                selectedPdfPages = totalPdfPages > 0
                    ? new Set(Array.from({ length: totalPdfPages }, (_, index) => index + 1))
                    : new Set();
                if (pdfPageStartInput) pdfPageStartInput.value = '';
                if (pdfPageEndInput) pdfPageEndInput.value = '';
                if (pdfSelectionEmpty && totalPdfPages > 0) {
                    pdfSelectionEmpty.textContent = 'PDF akan disimpan utuh sebagai Materi 1. Bagian Materi Tambahan di bawah boleh dilewati.';
                }
            } else if (pdfSelectionEmpty && totalPdfPages > 0) {
                pdfSelectionEmpty.textContent = 'Isi Halaman Awal dan Halaman Akhir untuk mengambil sebagian PDF sebagai Materi 1.';
            }

            updatePdfSelectionSummary();
        }

        function syncPageRangeInputsFromSelection() {
            if (!pdfPageStartInput || !pdfPageEndInput) {
                return;
            }

            isSyncingPdfInputs = true;

            if (selectedPdfPages.size === 0 || selectedPdfPages.size === totalPdfPages) {
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
            if (!card) {
                return;
            }

            card.classList.toggle('selected', isSelected);
            const checkbox = card.querySelector('.pdf-page-check');
            if (checkbox) {
                checkbox.checked = isSelected;
            }
        }

        function applyRangeSelection() {
            if (isSyncingPdfInputs || totalPdfPages === 0) {
                return;
            }

            const selectedMode = getPdfSaveMode();
            if (selectedMode !== 'range') {
                return;
            }

            const startValue = Number.parseInt(pdfPageStartInput.value, 10);
            const endValue = Number.parseInt(pdfPageEndInput.value, 10);

            if (!startValue && !endValue) {
                selectedPdfPages = new Set();
                updatePdfSelectionSummary();
                return;
            }

            if (!startValue || !endValue) {
                return;
            }

            const startPage = Math.max(1, Math.min(startValue, totalPdfPages));
            const endPage = Math.max(1, Math.min(endValue, totalPdfPages));

            if (startPage > endPage) {
                return;
            }

            selectedPdfPages = new Set();

            for (let pageNumber = 1; pageNumber <= totalPdfPages; pageNumber++) {
                const isSelected = pageNumber >= startPage && pageNumber <= endPage;
                if (isSelected) {
                    selectedPdfPages.add(pageNumber);
                }
            }

            updatePdfSelectionSummary();
        }

        function renderPdfPageCard(pageNumber, viewport, canvas) {
            const card = document.createElement('label');
            card.className = 'pdf-page-card';
            card.dataset.pageNumber = String(pageNumber);

            const preview = document.createElement('div');
            preview.className = 'pdf-page-preview';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'pdf-page-check';
            checkbox.checked = false;
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

        async function loadPdfPreview(file) {
            if (!file || !pdfSelectionPanel) {
                return;
            }

            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            pdfSelectionPanel.style.display = isPdf ? 'block' : 'none';

            if (file.size > serverUploadLimitBytes && pdfSelectionEmpty) {
                pdfSelectionEmpty.style.display = 'block';
                pdfSelectionEmpty.textContent = `File ini ${(file.size / 1024 / 1024).toFixed(1)} MB, sementara batas upload server sekitar ${(serverUploadLimitBytes / 1024 / 1024).toFixed(0)} MB. File harus dikompres atau batas upload PHP dinaikkan agar bisa disimpan.`;
            }

            if (!isPdf) {
                pdfPagesGrid.innerHTML = '';
                pdfSelectionEmpty.style.display = 'block';
                pdfSelectionEmpty.textContent = 'Pilihan halaman hanya tersedia untuk file PDF.';
                selectedPdfPages = new Set();
                totalPdfPages = 0;
                updatePdfSelectionSummary();
                return;
            }

            pdfSelectionLoading.style.display = 'block';
            pdfPagesGrid.innerHTML = '';
            pdfSelectionEmpty.style.display = 'block';
            pdfSelectionEmpty.textContent = 'PDF sedang dibaca. Setelah selesai, pilih mode simpan di bawah.';
            selectedPdfPages = new Set();
            totalPdfPages = 0;
            updatePdfSelectionSummary();

            try {
                const buffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({ data: buffer }).promise;
                totalPdfPages = pdf.numPages;
                selectedPdfPages = new Set(Array.from({ length: totalPdfPages }, (_, index) => index + 1));
                pdfSelectionEmpty.textContent = 'PDF akan disimpan utuh sebagai Materi 1. Bagian Materi Tambahan di bawah boleh dilewati.';

                syncPdfModeUi();
            } catch (error) {
                pdfPagesGrid.innerHTML = '';
                pdfSelectionEmpty.style.display = 'block';
                pdfSelectionEmpty.textContent = 'Jumlah halaman PDF gagal dibaca. Kamu masih bisa upload file utuh tanpa memilih halaman.';
                selectedPdfPages = new Set();
                totalPdfPages = 0;
                updatePdfSelectionSummary();
                syncPdfModeUi();
            } finally {
                pdfSelectionLoading.style.display = 'none';
            }
        }

        tipeKontenSelect.addEventListener('change', function() {
            const tipeKonten = this.value;
            const kontenTeksField = document.getElementById('konten_teks_field');
            const filePathField = document.getElementById('file_path_field');

            if (tipeKonten === 'teks') {
                kontenTeksField.style.display = 'block';
                filePathField.style.display = 'none';
                if (chapterBuilder) {
                    chapterBuilder.style.display = 'block';
                }
                kontenTeksField.querySelector('textarea').required = true;
                filePathField.querySelector('input').required = false;
            } else if (tipeKonten === 'file') {
                kontenTeksField.style.display = 'none';
                filePathField.style.display = 'block';
                if (chapterBuilder) {
                    chapterBuilder.style.display = 'block';
                }
                kontenTeksField.querySelector('textarea').required = false;
                filePathField.querySelector('input').required = true;
            } else {
                kontenTeksField.style.display = 'none';
                filePathField.style.display = 'none';
                if (chapterBuilder) {
                    chapterBuilder.style.display = 'block';
                }
                kontenTeksField.querySelector('textarea').required = false;
                filePathField.querySelector('input').required = false;
            }

            const currentFile = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (currentFile) {
                loadPdfPreview(currentFile);
            } else if (pdfSelectionPanel) {
                pdfSelectionPanel.style.display = 'none';
                pdfPagesGrid.innerHTML = '';
                pdfSelectionEmpty.style.display = 'block';
                selectedPdfPages = new Set();
                totalPdfPages = 0;
                updatePdfSelectionSummary();
                syncPageRangeInputsFromSelection();
            }
        });

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                const currentFile = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                if (currentFile) {
                    loadPdfPreview(currentFile);
                    const isPdf = currentFile.type === 'application/pdf' || currentFile.name.toLowerCase().endsWith('.pdf');
                    if (isPdf) {
                        detectChaptersForCreate(currentFile);
                    } else {
                        resetCreateDetectPanel();
                    }
                } else {
                    pdfSelectionPanel.style.display = 'none';
                    pdfPagesGrid.innerHTML = '';
                    selectedPdfPages = new Set();
                    totalPdfPages = 0;
                    updatePdfSelectionSummary();
                    syncPageRangeInputsFromSelection();
                    resetCreateDetectPanel();
                }
            });
        }

        function pagesToSelection(start, end) {
            const pages = [];
            for (let page = start; page <= end; page++) {
                pages.push(page);
            }
            return pages.join(',');
        }

        function parsePlannedChapterLine(line, index) {
            const trimmed = line.trim();
            if (!trimmed) return null;

            const rangeMatch = trimmed.match(/(?:\||,|;|:)?\s*(\d+)\s*(?:-|–|—|sampai|sd|s\/d|to)\s*(\d+)\s*$/i);
            if (!rangeMatch) {
                return { title: trimmed, start: null, end: null };
            }

            const start = parseInt(rangeMatch[1], 10);
            const end = parseInt(rangeMatch[2], 10);
            const title = trimmed.slice(0, rangeMatch.index).replace(/[|,;:\s]+$/, '').trim() || `Bab ${index + 1}`;

            return { title, start, end };
        }

        function setPdfSaveMode(mode) {
            const input = document.querySelector(`input[name="pdf_save_mode"][value="${mode}"]`);
            if (input) {
                input.checked = true;
            }
            syncPdfModeUi();
        }

        function applyPdfChapterPlan() {
            const chapters = (pdfChapterPlanInput?.value || '')
                .split(/\r?\n/)
                .map(parsePlannedChapterLine)
                .filter(Boolean);

            if (chapters.length === 0) {
                alert('Tempel rencana bab dulu. Contoh: Bab 1 | 1-12');
                return;
            }

            const invalid = chapters.find((chapter) => !chapter.start || !chapter.end || chapter.start > chapter.end);
            if (invalid) {
                alert(`Rencana "${invalid.title}" belum punya range halaman yang valid. Pakai format: Judul Bab | 1-12`);
                return;
            }

            if (totalPdfPages > 0) {
                const outsidePdf = chapters.find((chapter) => chapter.end > totalPdfPages);
                if (outsidePdf) {
                    alert(`Range "${outsidePdf.title}" melewati total PDF (${totalPdfPages} halaman).`);
                    return;
                }
            }

            const firstChapter = chapters[0];
            const firstTitleInput = document.querySelector('input[name="judul_bab_pertama"]');
            if (firstTitleInput) {
                firstTitleInput.value = firstChapter.title;
            }
            if (pdfPageStartInput) pdfPageStartInput.value = firstChapter.start;
            if (pdfPageEndInput) pdfPageEndInput.value = firstChapter.end;
            setPdfSaveMode('range');
            selectedPdfPages = new Set(Array.from({ length: firstChapter.end - firstChapter.start + 1 }, (_, offset) => firstChapter.start + offset));
            updatePdfSelectionSummary();

            if (chapterList) {
                chapterList.innerHTML = '';
                chapters.slice(1).forEach((chapter, offset) => {
                    chapterList.appendChild(buildChapterItem(offset, {
                        judul_bab: chapter.title,
                        urutan: offset + 2,
                        tipe_konten: 'file',
                        pdf_source_mode: 'first_bab',
                        pdf_page_selection: pagesToSelection(chapter.start, chapter.end),
                        status_aktif: true,
                    }));
                });
                renumberChapterItems();
            }

            alert(`${chapters.length} materi berhasil disiapkan dari satu PDF. Cek sebentar, lalu klik Simpan Mata Pelajaran.`);
        }

        if (applyPdfPlanButton) {
            applyPdfPlanButton.addEventListener('click', applyPdfChapterPlan);
        }

        if (pdfPageStartInput) {
            pdfPageStartInput.addEventListener('input', applyRangeSelection);
        }

        if (pdfPageEndInput) {
            pdfPageEndInput.addEventListener('input', applyRangeSelection);
        }

        pdfSaveModeInputs.forEach((input) => {
            input.addEventListener('change', () => {
                if (input.value === 'range') {
                    selectedPdfPages = new Set();
                }
                syncPdfModeUi();
            });
        });

        if (materiForm) {
            materiForm.addEventListener('submit', (event) => {
                const currentFile = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                const isPdf = currentFile && (currentFile.type === 'application/pdf' || currentFile.name.toLowerCase().endsWith('.pdf'));
                const selectedCoverMode = getSelectedCoverMode();

                if (currentFile && currentFile.size > serverUploadLimitBytes) {
                    event.preventDefault();
                    alert(`File ${(currentFile.size / 1024 / 1024).toFixed(1)} MB melebihi batas upload server ${(serverUploadLimitBytes / 1024 / 1024).toFixed(0)} MB. Kompres PDF dulu atau naikkan upload_max_filesize dan post_max_size di php.ini.`);
                    return;
                }

                if (isPdf && getPdfSaveMode() === 'range' && totalPdfPages > 0 && selectedPdfPages.size === 0) {
                    event.preventDefault();
                    alert('Isi Halaman Awal dan Halaman Akhir, atau pilih mode Simpan PDF utuh.');
                    return;
                }

                renumberChapterItems();

                if (
                    selectedCoverMode === 'ai'
                    && generatedCoverTempPathInput.value
                    && useGeneratedCoverInput.value !== '1'
                ) {
                    event.preventDefault();
                    alert('Preview cover AI sudah dibuat, tapi belum dikonfirmasi. Klik "Gunakan Cover Ini" atau "Batal Pakai" dulu.');
                }
            });
        }

        // ----- Auto-detect chapters from uploaded PDF (create page) -----
        let createPdfDoc = null;

        function resetCreateDetectPanel() {
            const panel = document.getElementById('pdf_detect_panel');
            const list  = document.getElementById('pdf_detect_chapters_list');
            const extras = document.getElementById('pdf_detect_extras_section');
            const extrasList = document.getElementById('pdf_detect_extras_list');
            if (panel) panel.style.display = 'none';
            if (list) list.innerHTML = '';
            if (extras) extras.style.display = 'none';
            if (extrasList) extrasList.innerHTML = '';
            createPdfDoc = null;
        }

        async function renderCreateThumbnail(pageNumber, container) {
            if (!createPdfDoc) return;
            try {
                if (pageNumber > createPdfDoc.numPages) return;
                const page = await createPdfDoc.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 0.15 });
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: ctx, viewport }).promise;
                container.innerHTML = '';
                container.appendChild(canvas);
            } catch (e) { /* silently fail */ }
        }

        function buildCreateDetectCard(judul, halamanAwal, halamanAkhir, isOptional, allChapters, sequenceIndex, extraChapters = []) {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'chapter-detect-card' + (isOptional ? ' optional' : '');

            const thumb = document.createElement('div');
            thumb.className = 'chapter-detect-thumb';
            thumb.innerHTML = `<i data-lucide="${isOptional ? 'book-marked' : 'file-text'}" style="width:20px;height:20px;color:${isOptional ? '#6366F1' : '#94a3b8'};"></i>`;

            const info = document.createElement('div');
            info.className = 'chapter-detect-info';

            const badgeText = isOptional ? 'OPSIONAL' : `Materi ${sequenceIndex}`;
            const badgeColor = isOptional ? '#6366F1' : '#B45309';
            const badgeBg = isOptional ? '#E0E7FF' : '#FEF3C7';

            const badgeEl = document.createElement('div');
            badgeEl.style = `font-size: 0.65rem; font-weight: 800; color: ${badgeColor}; background: ${badgeBg}; padding: 0.15rem 0.4rem; border-radius: 4px; display: inline-block; width: max-content; margin-bottom: 0.25rem;`;
            badgeEl.textContent = badgeText;

            const titleEl = document.createElement('div');
            titleEl.className = 'chapter-detect-title';
            titleEl.textContent = judul;
            titleEl.title = judul;

            const rangeEl = document.createElement('div');
            rangeEl.className = 'chapter-detect-range';
            rangeEl.textContent = `Halaman ${halamanAwal} – ${halamanAkhir}`;

            info.appendChild(badgeEl);
            info.appendChild(titleEl);
            info.appendChild(rangeEl);
            card.appendChild(thumb);
            card.appendChild(info);

            card.addEventListener('click', () => {
                document.querySelectorAll('.chapter-detect-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');

                let selectedChapters = [];
                
                if (!isOptional && Array.isArray(allChapters) && allChapters.length > 0) {
                    selectedChapters = [...allChapters];
                } else if (isOptional) {
                    selectedChapters = [{ judul_bab: judul, halaman_awal: halamanAwal, halaman_akhir: halamanAkhir }];
                }

                const includeExtrasCb = document.getElementById('auto_include_extras');
                if (!isOptional && includeExtrasCb && includeExtrasCb.checked && Array.isArray(extraChapters)) {
                    selectedChapters.push(...extraChapters);
                }

                // Sort chronologically by starting page
                selectedChapters.sort((a, b) => a.halaman_awal - b.halaman_awal);

                if (selectedChapters.length > 0) {
                    const firstItem = selectedChapters[0];

                    // Fill "Judul Materi 1"
                    const judulPertama = document.querySelector('input[name="judul_bab_pertama"]');
                    if (judulPertama) judulPertama.value = firstItem.judul_bab;

                    // Set page range and apply selection
                    setPdfSaveMode('range');
                    if (pdfPageStartInput) pdfPageStartInput.value = firstItem.halaman_awal;
                    if (pdfPageEndInput) pdfPageEndInput.value = firstItem.halaman_akhir;
                    applyRangeSelection();

                    // Fill Chapter Builder (Materi 2+)
                    const chapterListEl = document.getElementById('chapter_list');
                    const chapterBuilderEl = document.getElementById('chapter_builder');
                    const addBtn = document.getElementById('add_chapter_btn');

                    if (chapterListEl) {
                        chapterListEl.innerHTML = '';
                        const restChapters = selectedChapters.slice(1);
                        restChapters.forEach((ch, offset) => {
                            chapterListEl.appendChild(buildChapterItem(offset, {
                                judul_bab: ch.judul_bab,
                                urutan: offset + 2,
                                tipe_konten: 'file',
                                pdf_source_mode: 'first_bab',
                                pdf_page_selection: pagesToSelection(ch.halaman_awal, ch.halaman_akhir),
                                status_aktif: true,
                            }));
                        });
                        renumberChapterItems();
                    }
                    
                    if (chapterBuilderEl && selectedChapters.length > 1) {
                        chapterBuilderEl.style.display = 'block';
                        if (addBtn) addBtn.style.display = 'none';
                    }
                }
            });

            return { card, thumb };
        }

        async function detectChaptersForCreate(file) {
            resetCreateDetectPanel();
            const panel  = document.getElementById('pdf_detect_panel');
            const list   = document.getElementById('pdf_detect_chapters_list');
            const loading = document.getElementById('pdf_detect_loading');
            const extrasSection = document.getElementById('pdf_detect_extras_section');
            const extrasList    = document.getElementById('pdf_detect_extras_list');
            const addBtn = document.getElementById('add_chapter_btn');
            
            if (addBtn) addBtn.style.display = 'inline-flex'; // Reset manual add btn
            if (!panel || !list || !loading) return;

            // Load PDF locally for thumbnail rendering
            try {
                const buf = await file.arrayBuffer();
                createPdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            } catch (e) { createPdfDoc = null; }

            panel.style.display = 'block';
            loading.style.display = 'block';

            const formData = new FormData();
            const csrfEl = document.querySelector('input[name="_token"]');
            formData.append('_token', csrfEl ? csrfEl.value : '');
            formData.append('pdf_file', file);

            try {
                const response = await fetch('{{ route("materi.bab.temp-detect") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                loading.style.display = 'none';

                if (data.success && Array.isArray(data.chapters) && data.chapters.length > 0) {
                    const allChapters = data.chapters;
                    const pageCount = (typeof data.page_count === 'number')
                        ? data.page_count
                        : (createPdfDoc ? createPdfDoc.numPages : null);

                    let firstCard = null;

                    // Pre-calculate extras
                    const extraChapters = [];
                    const first = allChapters[0];
                    const last  = allChapters[allChapters.length - 1];

                    if (first.halaman_awal > 1) {
                        extraChapters.push({
                            judul_bab: 'Pendahuluan (Cover, Daftar Isi, dll.)',
                            halaman_awal: 1,
                            halaman_akhir: first.halaman_awal - 1
                        });
                    }

                    if (pageCount && last.halaman_akhir < pageCount) {
                        extraChapters.push({
                            judul_bab: 'Penutup (Daftar Pustaka, Profil Penulis, dll.)',
                            halaman_awal: last.halaman_akhir + 1,
                            halaman_akhir: pageCount
                        });
                    }

                    // Render main chapter cards
                    allChapters.forEach((ch, idx) => {
                        const seqIndex = idx + 1; // 1-based index for badges
                        const { card, thumb } = buildCreateDetectCard(
                            ch.judul_bab, ch.halaman_awal, ch.halaman_akhir, false, allChapters, seqIndex, extraChapters
                        );
                        if (idx === 0) firstCard = card;
                        list.appendChild(card);
                        renderCreateThumbnail(ch.halaman_awal, thumb);
                    });

                    // Render optional front/back matter
                    let hasExtras = extraChapters.length > 0;
                    if (hasExtras) {
                        extraChapters.forEach((extraCh) => {
                            const { card, thumb } = buildCreateDetectCard(
                                extraCh.judul_bab, extraCh.halaman_awal, extraCh.halaman_akhir, true, [], 0, []
                            );
                            if (extrasList) { extrasList.appendChild(card); renderCreateThumbnail(extraCh.halaman_awal, thumb); }
                        });
                    }

                    if (hasExtras && extrasSection) extrasSection.style.display = 'block';
                    lucide.createIcons();

                    // AUTO APPLY THE FIRST CHAPTER SO THE USER DOESN'T HAVE TO
                    if (firstCard) {
                        firstCard.click();
                    }

                } else {
                    // No chapters detected — hide panel silently
                    panel.style.display = 'none';
                }
            } catch (err) {
                console.error('Chapter detect failed:', err);
                loading.style.display = 'none';
                panel.style.display = 'none';
            }
        }
        
        // Scroll button listener
        document.addEventListener('DOMContentLoaded', () => {
            const scrollBtn = document.getElementById('scroll_to_builder_btn');
            if (scrollBtn) {
                scrollBtn.addEventListener('click', () => {
                    const builder = document.getElementById('chapter_builder');
                    if (builder) {
                        builder.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        builder.style.transition = 'box-shadow 0.3s ease';
                        builder.style.boxShadow = '0 0 0 4px rgba(248,184,3,0.3)';
                        setTimeout(() => { builder.style.boxShadow = 'none'; }, 1500);
                    }
                });
            }
        });
        // ----------------------------------------------------------------

        // Trigger on page load if value exists
        if (tipeKontenSelect.value) {
            tipeKontenSelect.dispatchEvent(new Event('change'));
        }

        function handleLogout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout", [], false) }}';
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <script>
    lucide.createIcons();
    </script>
    <script>
        const coverImageInput = document.getElementById('cover_path');
        const coverImageHint = document.getElementById('cover_compress_hint');
        const coverModeInputs = document.querySelectorAll('input[name="cover_mode"]');
        const coverModeCards = document.querySelectorAll('[data-cover-mode-card]');
        const coverManualPanel = document.getElementById('cover_manual_panel');
        const coverAiPanel = document.getElementById('cover_ai_panel');
        const coverModal = document.getElementById('cover_modal');
        const openCoverModalButton = document.getElementById('open_cover_modal_btn');
        const openCoverModalSecondaryButton = document.getElementById('open_cover_modal_secondary_btn');
        const closeCoverModalButton = document.getElementById('close_cover_modal_btn');
        const coverModalPreviewImage = document.getElementById('cover_modal_preview_image');
        const coverModalPreviewEmpty = document.getElementById('cover_modal_preview_empty');
        const generatedCoverTempPathInput = document.getElementById('generated_cover_temp_path');
        const useGeneratedCoverInput = document.getElementById('use_generated_cover');
        const generateAiCoverButton = document.getElementById('generate_ai_cover_btn');
        const regenerateAiCoverButton = document.getElementById('regenerate_ai_cover_btn');
        const discardAiCoverButton = document.getElementById('discard_ai_cover_btn');
        const confirmAiCoverButton = document.getElementById('confirm_ai_cover_btn');
        const coverAiLoading = document.getElementById('cover_ai_loading');
        const coverAiPreview = document.getElementById('cover_ai_preview');
        const coverAiPreviewImage = document.getElementById('cover_ai_preview_image');
        const coverAiStatus = document.getElementById('cover_ai_status');
        const coverAiPromptPreview = document.getElementById('cover_ai_prompt_preview');
        const coverAiPromptTambahan = document.getElementById('cover_ai_prompt_tambahan');
        const chapterBuilder = document.getElementById('chapter_builder');
        const chapterList = document.getElementById('chapter_list');
        const addChapterButton = document.getElementById('add_chapter_btn');
        const bookCoverStageImage = document.getElementById('book_cover_stage_image');
        const bookCoverStagePlaceholder = document.getElementById('book_cover_stage_placeholder');
        const COVER_MAX_IMAGE_BYTES = 5 * 1024 * 1024;
        const initialBabData = @json(old('bab', []));
        let manualCoverPreviewUrl = null;

        function setCoverImageHint(message, isError = false) {
            if (!coverImageHint) {
                return;
            }

            coverImageHint.style.display = message ? 'block' : 'none';
            coverImageHint.textContent = message || '';
            coverImageHint.style.color = isError ? '#DC2626' : 'var(--color-text-light)';
        }

        function getSelectedCoverMode() {
            const checkedInput = document.querySelector('input[name="cover_mode"]:checked');
            return checkedInput ? checkedInput.value : 'manual';
        }

        function revokeManualCoverPreview() {
            if (manualCoverPreviewUrl) {
                URL.revokeObjectURL(manualCoverPreviewUrl);
                manualCoverPreviewUrl = null;
            }
        }

        function setBookCoverStage(url) {
            if (!bookCoverStageImage || !bookCoverStagePlaceholder) {
                return;
            }

            if (url) {
                bookCoverStageImage.src = url;
                bookCoverStageImage.style.display = 'block';
                bookCoverStagePlaceholder.style.display = 'none';
                return;
            }

            bookCoverStageImage.src = '';
            bookCoverStageImage.style.display = 'none';
            bookCoverStagePlaceholder.style.display = 'block';
        }

        function setCoverModalPreview(url) {
            if (!coverModalPreviewImage || !coverModalPreviewEmpty) {
                return;
            }

            if (url) {
                coverModalPreviewImage.src = url;
                coverModalPreviewImage.style.display = 'block';
                coverModalPreviewEmpty.style.display = 'none';
                return;
            }

            coverModalPreviewImage.src = '';
            coverModalPreviewImage.style.display = 'none';
            coverModalPreviewEmpty.style.display = 'flex';
        }

        function syncBookCoverStage() {
            if (getSelectedCoverMode() === 'ai' && generatedCoverTempPathInput.value && coverAiPreviewImage.src) {
                setBookCoverStage(coverAiPreviewImage.src);
                setCoverModalPreview(coverAiPreviewImage.src);
                return;
            }

            if (coverImageInput && coverImageInput.files && coverImageInput.files[0]) {
                revokeManualCoverPreview();
                manualCoverPreviewUrl = URL.createObjectURL(coverImageInput.files[0]);
                setBookCoverStage(manualCoverPreviewUrl);
                setCoverModalPreview(manualCoverPreviewUrl);
                return;
            }

            revokeManualCoverPreview();
            setBookCoverStage('');
            setCoverModalPreview('');
        }

        function openCoverModal() {
            if (!coverModal) {
                return;
            }

            coverModal.classList.add('is-open');
            coverModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            syncBookCoverStage();
            lucide.createIcons();
        }

        function closeCoverModal() {
            if (!coverModal) {
                return;
            }

            coverModal.classList.remove('is-open');
            coverModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function syncCoverModeUi() {
            const selectedMode = getSelectedCoverMode();

            coverModeCards.forEach((card) => {
                card.classList.toggle('active', card.dataset.coverModeCard === selectedMode);
            });

            if (coverManualPanel) {
                coverManualPanel.style.display = selectedMode === 'manual' ? 'block' : 'none';
            }

            if (coverAiPanel) {
                coverAiPanel.style.display = selectedMode === 'ai' ? 'block' : 'none';
            }

            if (selectedMode === 'manual') {
                useGeneratedCoverInput.value = '0';
            }

            syncBookCoverStage();
        }

        function resetGeneratedCoverSelection() {
            useGeneratedCoverInput.value = '0';
            if (coverAiStatus) {
                coverAiStatus.textContent = 'Status: menunggu konfirmasi.';
                coverAiStatus.className = 'cover-ai-status pending';
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildChapterItem(index, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'chapter-item';
            wrapper.dataset.chapterIndex = String(index);
            const babTitle = data.judul_bab || '';
            const urutan = data.urutan || (index + 1);
            const tipeKonten = data.tipe_konten || 'teks';
            const kontenTeks = data.konten_teks || '';
            const pdfSourceMode = data.pdf_source_mode || 'upload';
            const pdfSelection = data.pdf_page_selection || '';
            const isAktif = data.status_aktif === undefined ? true : Boolean(Number(data.status_aktif) || data.status_aktif === true || data.status_aktif === '1');

            wrapper.innerHTML = `
                <div class="chapter-item-head">
                    <div class="chapter-item-title">Materi ${index + 1}</div>
                    <button type="button" class="btn btn-secondary remove-chapter-btn" style="flex: 0 0 auto;">
                        <i data-lucide="trash-2"></i>
                        Hapus
                    </button>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Judul Materi <span class="required">*</span></label>
                        <input type="text" name="bab[${index}][judul_bab]" value="${escapeHtml(babTitle)}" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan <span class="required">*</span></label>
                        <input type="number" name="bab[${index}][urutan]" value="${urutan}" min="1" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Konten Materi <span class="required">*</span></label>
                    <select name="bab[${index}][tipe_konten]" class="form-select chapter-type-select">
                        <option value="teks" ${tipeKonten === 'teks' ? 'selected' : ''}>Teks</option>
                        <option value="file" ${tipeKonten === 'file' ? 'selected' : ''}>File</option>
                    </select>
                </div>
                <div class="form-group chapter-text-field" style="display:${tipeKonten === 'teks' ? 'block' : 'none'};">
                    <label class="form-label">Konten Teks Materi <span class="required">*</span></label>
                    <textarea name="bab[${index}][konten_teks]" rows="7" class="form-textarea">${escapeHtml(kontenTeks)}</textarea>
                </div>
                <div class="chapter-file-field" style="display:${tipeKonten === 'file' ? 'block' : 'none'};">
                    <div class="form-group">
                        <label class="form-label">Sumber File</label>
                        <select name="bab[${index}][pdf_source_mode]" class="form-select chapter-source-select">
                            <option value="upload" ${pdfSourceMode === 'upload' ? 'selected' : ''}>Upload file baru</option>
                            <option value="first_bab" ${pdfSourceMode === 'first_bab' ? 'selected' : ''}>Pakai PDF Materi 1</option>
                        </select>
                        <span class="hint">Gunakan PDF Materi 1 jika materi ini berasal dari file yang sama, lalu isi range halaman di bawah.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Materi (PDF, Word, PowerPoint, TXT) <span class="required">*</span></label>
                        <input type="file" name="bab_files[${index}]" accept=".pdf,.doc,.docx,.ppt,.pptx,.odt,.odp,.rtf,.txt" class="form-input chapter-file-input">
                        <span class="hint chapter-file-hint">Kalau PDF, isi pilihan halaman dengan nomor yang dipisahkan koma. Contoh: 1,2,3,4</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilihan Halaman PDF</label>
                        <input type="text" name="bab[${index}][pdf_page_selection]" value="${escapeHtml(pdfSelection)}" class="form-input" placeholder="Contoh: 1,2,3,4">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="bab[${index}][status_aktif]" value="1" ${isAktif ? 'checked' : ''}>
                        <span>Materi Aktif</span>
                    </label>
                </div>
                <div class="chapter-quiz-note">
                    Kuis per materi akan dibuat setelah mata pelajaran disimpan. Sistem akan menyediakan tombol cepat ke menu kuis dari halaman detail mata pelajaran.
                </div>
            `;

            const removeButton = wrapper.querySelector('.remove-chapter-btn');
            const typeSelect = wrapper.querySelector('.chapter-type-select');
            const textField = wrapper.querySelector('.chapter-text-field');
            const fileField = wrapper.querySelector('.chapter-file-field');
            const textarea = textField.querySelector('textarea');
            const fileInputLocal = wrapper.querySelector('.chapter-file-input');
            const sourceSelect = wrapper.querySelector('.chapter-source-select');
            const fileHint = wrapper.querySelector('.chapter-file-hint');

            function syncChapterSource() {
                const sourceValue = sourceSelect.value;
                fileInputLocal.required = typeSelect.value === 'file' && sourceValue === 'upload';
                fileInputLocal.disabled = typeSelect.value === 'file' && sourceValue === 'first_bab';
                if (fileInputLocal.disabled) {
                    fileInputLocal.value = '';
                }
                fileHint.textContent = sourceValue === 'first_bab'
                    ? 'Tidak perlu upload ulang. Sistem akan membuat file materi dari PDF Materi 1 sesuai pilihan halaman.'
                    : 'Kalau PDF, isi pilihan halaman dengan nomor yang dipisahkan koma. Contoh: 1,2,3,4';
            }

            function syncChapterType() {
                const typeValue = typeSelect.value;
                textField.style.display = typeValue === 'teks' ? 'block' : 'none';
                fileField.style.display = typeValue === 'file' ? 'block' : 'none';
                textarea.required = typeValue === 'teks';
                syncChapterSource();
            }

            typeSelect.addEventListener('change', syncChapterType);
            sourceSelect.addEventListener('change', syncChapterSource);
            syncChapterType();

            removeButton.addEventListener('click', () => {
                wrapper.remove();
                renumberChapterItems();
            });

            return wrapper;
        }

        function renumberChapterItems() {
            if (!chapterList) {
                return;
            }

            Array.from(chapterList.children).forEach((item, index) => {
                item.dataset.chapterIndex = String(index);
                const title = item.querySelector('.chapter-item-title');
                if (title) {
                    title.textContent = `Materi ${index + 1}`;
                }

                item.querySelectorAll('input, textarea, select').forEach((field) => {
                    if (!field.name) {
                        return;
                    }

                    if (field.name.startsWith('bab[')) {
                        field.name = field.name.replace(/bab\[\d+\]/, `bab[${index}]`);
                    }

                    if (field.name.startsWith('bab_files[')) {
                        field.name = field.name.replace(/bab_files\[\d+\]/, `bab_files[${index}]`);
                    }
                });

                const urutanInput = item.querySelector('input[name$="[urutan]"]');
                if (urutanInput && !urutanInput.value) {
                    urutanInput.value = index + 1;
                }
            });
            lucide.createIcons();
        }

        function ensureInitialChapterItems() {
            if (!chapterList) {
                return;
            }

            chapterList.innerHTML = '';
            if (Array.isArray(initialBabData) && initialBabData.length > 0) {
                initialBabData.forEach((item, index) => {
                    chapterList.appendChild(buildChapterItem(index, item));
                });
            }
            renumberChapterItems();
        }

        function applyGeneratedCoverPreview(payload) {
            generatedCoverTempPathInput.value = payload.temp_path || '';
            useGeneratedCoverInput.value = '0';
            coverAiPreviewImage.src = payload.url || '';
            coverAiPreview.style.display = payload.url ? 'grid' : 'none';
            coverAiStatus.textContent = 'Status: menunggu konfirmasi.';
            coverAiStatus.className = 'cover-ai-status pending';
            coverAiPromptPreview.style.display = payload.prompt ? 'block' : 'none';
            coverAiPromptPreview.textContent = payload.prompt || '';
            regenerateAiCoverButton.style.display = payload.url ? 'inline-flex' : 'none';
            discardAiCoverButton.style.display = payload.url ? 'inline-flex' : 'none';
            syncBookCoverStage();
        }

        async function discardGeneratedCover({ silent = false } = {}) {
            const tempPath = generatedCoverTempPathInput.value;

            generatedCoverTempPathInput.value = '';
            useGeneratedCoverInput.value = '0';
            coverAiPreviewImage.src = '';
            coverAiPreview.style.display = 'none';
            coverAiPromptPreview.style.display = 'none';
            regenerateAiCoverButton.style.display = 'none';
            discardAiCoverButton.style.display = 'none';
            resetGeneratedCoverSelection();
            syncBookCoverStage();

            if (!tempPath) {
                return;
            }

            try {
                await fetch('{{ route("materi.discard-cover-preview", [], false) }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ temp_path: tempPath }),
                });
            } catch (error) {
                if (!silent) {
                    alert('Preview cover sempat dibersihkan di form, tetapi server gagal menghapus file sementara.');
                }
            }
        }

        async function generateAiCover() {
            const judulInput = document.querySelector('input[name="judul"]');
            const deskripsiInput = document.querySelector('textarea[name="deskripsi"]');
            const levelSelect = document.querySelector('select[name="level_id"]');
            const judul = judulInput ? judulInput.value.trim() : '';

            if (!judul) {
                alert('Isi judul materi dulu sebelum generate cover AI.');
                if (judulInput) {
                    judulInput.focus();
                }
                return;
            }

            const selectedLevelText = levelSelect && levelSelect.selectedIndex >= 0
                ? levelSelect.options[levelSelect.selectedIndex].text
                : '';

            coverAiLoading.style.display = 'block';
            generateAiCoverButton.disabled = true;
            regenerateAiCoverButton.disabled = true;
            discardAiCoverButton.disabled = true;
            confirmAiCoverButton.disabled = true;

            try {
                const response = await fetch('{{ route("materi.generate-cover-preview", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        judul,
                        deskripsi: deskripsiInput ? deskripsiInput.value.trim() : '',
                        mata_pelajaran: '',
                        level: selectedLevelText && selectedLevelText !== 'Pilih Level' ? selectedLevelText : '',
                        prompt_tambahan: coverAiPromptTambahan ? coverAiPromptTambahan.value.trim() : '',
                        previous_temp_path: generatedCoverTempPathInput.value || '',
                    }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    const errorMessage = payload.message
                        || payload.error
                        || (payload.errors ? Object.values(payload.errors).flat()[0] : null)
                        || 'Generate cover gagal.';
                    throw new Error(errorMessage);
                }

                applyGeneratedCoverPreview(payload);
            } catch (error) {
                alert(error.message || 'Generate cover AI gagal.');
            } finally {
                coverAiLoading.style.display = 'none';
                generateAiCoverButton.disabled = false;
                regenerateAiCoverButton.disabled = false;
                discardAiCoverButton.disabled = false;
                confirmAiCoverButton.disabled = false;
                lucide.createIcons();
            }
        }

        async function fileToImageBitmap(file) {
            const imageUrl = URL.createObjectURL(file);

            try {
                const image = new Image();
                image.decoding = 'async';
                image.src = imageUrl;
                await image.decode();
                return image;
            } finally {
                URL.revokeObjectURL(imageUrl);
            }
        }

        async function compressCoverImage(file) {
            if (file.size <= COVER_MAX_IMAGE_BYTES || file.type === 'image/svg+xml') {
                return file;
            }

            const image = await fileToImageBitmap(file);
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            let width = image.naturalWidth || image.width;
            let height = image.naturalHeight || image.height;
            let quality = 0.9;
            let bestBlob = null;
            const outputType = file.type === 'image/png' ? 'image/webp' : (file.type === 'image/webp' ? 'image/webp' : 'image/jpeg');

            for (let attempt = 0; attempt < 10; attempt++) {
                canvas.width = Math.max(1, Math.round(width));
                canvas.height = Math.max(1, Math.round(height));
                context.clearRect(0, 0, canvas.width, canvas.height);
                context.drawImage(image, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise((resolve) => canvas.toBlob(resolve, outputType, quality));
                if (!blob) {
                    break;
                }

                if (!bestBlob || blob.size < bestBlob.size) {
                    bestBlob = blob;
                }

                if (blob.size <= COVER_MAX_IMAGE_BYTES) {
                    bestBlob = blob;
                    break;
                }

                if (quality > 0.45) {
                    quality -= 0.1;
                } else {
                    width *= 0.85;
                    height *= 0.85;
                }
            }

            if (!bestBlob || bestBlob.size > COVER_MAX_IMAGE_BYTES) {
                return null;
            }

            const extension = outputType === 'image/webp' ? 'webp' : 'jpg';
            const baseName = file.name.replace(/\.[^.]+$/, '');

            return new File([bestBlob], `${baseName}.${extension}`, {
                type: outputType,
                lastModified: Date.now(),
            });
        }

        if (coverImageInput) {
            coverImageInput.addEventListener('change', async () => {
                const file = coverImageInput.files && coverImageInput.files[0] ? coverImageInput.files[0] : null;
                setCoverImageHint('');
                if (file) {
                    useGeneratedCoverInput.value = '0';
                }
                syncBookCoverStage();

                if (!file || file.size <= COVER_MAX_IMAGE_BYTES) {
                    return;
                }

                setCoverImageHint('Cover melebihi 5MB. Sedang dicoba dikompres otomatis...');

                try {
                    const compressedFile = await compressCoverImage(file);

                    if (!compressedFile) {
                        coverImageInput.value = '';
                        setCoverImageHint('Cover tidak berhasil dikompres sampai 5MB. Coba pilih gambar lain atau kompres manual.', true);
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    coverImageInput.files = dataTransfer.files;

                    setCoverImageHint(`Cover berhasil dikompres dari ${(file.size / 1024 / 1024).toFixed(2)} MB menjadi ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB.`);
                    syncBookCoverStage();
                } catch (error) {
                    coverImageInput.value = '';
                    setCoverImageHint('Terjadi kesalahan saat kompres cover otomatis. Coba lagi dengan file lain.', true);
                    syncBookCoverStage();
                }
            });
        }

        coverModeInputs.forEach((input) => {
            input.addEventListener('change', syncCoverModeUi);
        });

        if (openCoverModalButton) {
            openCoverModalButton.addEventListener('click', openCoverModal);
        }

        if (openCoverModalSecondaryButton) {
            openCoverModalSecondaryButton.addEventListener('click', openCoverModal);
        }

        if (closeCoverModalButton) {
            closeCoverModalButton.addEventListener('click', closeCoverModal);
        }

        if (coverModal) {
            coverModal.addEventListener('click', (event) => {
                if (event.target === coverModal) {
                    closeCoverModal();
                }
            });
        }

        if (generateAiCoverButton) {
            generateAiCoverButton.addEventListener('click', generateAiCover);
        }

        if (regenerateAiCoverButton) {
            regenerateAiCoverButton.addEventListener('click', generateAiCover);
        }

        if (confirmAiCoverButton) {
            confirmAiCoverButton.addEventListener('click', () => {
                if (!generatedCoverTempPathInput.value) {
                    alert('Generate cover dulu sebelum dikonfirmasi.');
                    return;
                }

                if (coverImageInput) {
                    coverImageInput.value = '';
                }

                useGeneratedCoverInput.value = '1';
                coverAiStatus.textContent = 'Status: cover AI akan dipakai saat materi disimpan.';
                coverAiStatus.className = 'cover-ai-status confirmed';
                syncBookCoverStage();
                closeCoverModal();
            });
        }

        if (discardAiCoverButton) {
            discardAiCoverButton.addEventListener('click', () => {
                discardGeneratedCover();
            });
        }

        if (addChapterButton) {
            addChapterButton.addEventListener('click', () => {
                const nextIndex = chapterList.children.length;
                chapterList.appendChild(buildChapterItem(nextIndex));
                renumberChapterItems();
            });
        }

        syncCoverModeUi();
        syncBookCoverStage();
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && coverModal && coverModal.classList.contains('is-open')) {
                closeCoverModal();
            }
        });

    </script>
</body>
</html>
