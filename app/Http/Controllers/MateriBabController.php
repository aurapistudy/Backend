<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersByAssignedMapel;
use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use App\Services\GeminiBabSummaryService;
use App\Services\HuggingFaceBabSummaryService;
use App\Services\HuggingFaceSummaryVisualService;
use App\Services\PdfCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MateriBabController extends Controller
{
    use FiltersByAssignedMapel;

    private const PDF_TARGET_MAX_KB = 10240;
    private const MATERI_FILE_MIMES = 'pdf,doc,docx,ppt,pptx,odt,odp,rtf,txt';

    public function create(Materi $materi)
    {
        $this->authorizeMateriAccess($materi);
        $nextUrutan = ((int) $materi->bab()->max('urutan')) + 1;
        $pdfSourceOptions = $this->buildPdfSourceOptions($materi);
        $suggestedPdfPageStart = $this->suggestNextPdfPageStart($materi);

        return view('dashboard.materi-bab.create', compact('materi', 'nextUrutan', 'pdfSourceOptions', 'suggestedPdfPageStart'));
    }

    public function store(Request $request, Materi $materi)
    {
        $this->authorizeMateriAccess($materi);
        $validated = $this->validateBabPayload($request, true);
        $payload = $this->prepareBabPayload($request, $validated);
        $payload['materi_id'] = $materi->id;

        $bab = MateriBab::create($payload);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'message' => 'Materi berhasil ditambahkan.',
                'data' => $this->formatBabResponse($bab),
            ], 201);
        }

        return redirect()->route('materi.show', $materi->id)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function show(Request $request, Materi $materi, MateriBab $bab)
    {
        $this->authorizeMateriAccess($materi);
        abort_unless((int) $bab->materi_id === (int) $materi->id, 404);

        if ($this->isApiRequest($request)) {
            return response()->json($this->formatBabResponse($bab));
        }

        $bab->loadMissing('kuis');
        $bab->loadCount('kuis');

        return view('dashboard.materi-bab.show', compact('materi', 'bab'));
    }

    public function edit(Materi $materi, MateriBab $bab)
    {
        $this->authorizeMateriAccess($materi);
        abort_unless((int) $bab->materi_id === (int) $materi->id, 404);

        $pdfSourceOptions = $this->buildPdfSourceOptions($materi);
        $suggestedPdfPageStart = null;

        return view('dashboard.materi-bab.edit', compact('materi', 'bab', 'pdfSourceOptions', 'suggestedPdfPageStart'));
    }

    public function update(Request $request, Materi $materi, MateriBab $bab)
    {
        $this->authorizeMateriAccess($materi);
        abort_unless((int) $bab->materi_id === (int) $materi->id, 404);

        $validated = $this->validateBabPayload($request, false);
        $payload = $this->prepareBabPayload($request, $validated, $bab);

        $bab->update($payload);

        if ($this->isApiRequest($request)) {
            return response()->json([
                'message' => 'Materi berhasil diperbarui.',
                'data' => $this->formatBabResponse($bab->fresh()),
            ]);
        }

        return redirect()->route('materi.show', $materi->id)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Request $request, Materi $materi, MateriBab $bab)
    {
        $this->authorizeMateriAccess($materi);
        abort_unless((int) $bab->materi_id === (int) $materi->id, 404);

        $filePath = $bab->file_path;
        $summaryVisualPath = $bab->summary_visual_path;
        $bab->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        if ($summaryVisualPath && Storage::disk('public')->exists($summaryVisualPath)) {
            Storage::disk('public')->delete($summaryVisualPath);
        }

        if ($this->isApiRequest($request)) {
            return response()->json([
                'message' => 'Materi berhasil dihapus.',
            ]);
        }

        return redirect()->route('materi.show', $materi->id)
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function generateSummary(
        Request $request,
        Materi $materi,
        MateriBab $bab,
        GeminiBabSummaryService $geminiBabSummaryService,
        HuggingFaceBabSummaryService $huggingFaceBabSummaryService,
        HuggingFaceSummaryVisualService $summaryVisualService
    )
    {
        abort_unless((int) $bab->materi_id === (int) $materi->id, 404);
        $this->authorizeMateriAccess($materi);

        try {
            $materi->loadMissing(['mataPelajaran', 'level']);
            $provider = (string) config('services.bab_summary.text_provider', 'gemini');
            $summaryService = strtolower($provider) === 'huggingface'
                ? $huggingFaceBabSummaryService
                : $geminiBabSummaryService;
            $summary = $summaryService->generateSummary($materi, $bab);
            $poster = $summaryVisualService->generateSummaryPoster($materi, $bab, $summary);
            $posterPath = 'summary/posters/' . now()->format('YmdHis') . '_' . uniqid('', true) . '.' . $poster['extension'];

            Storage::disk('public')->put($posterPath, $poster['binary']);

            if ($bab->summary_visual_path && Storage::disk('public')->exists($bab->summary_visual_path)) {
                Storage::disk('public')->delete($bab->summary_visual_path);
            }

            $bab->update(array_merge($summary, [
                'summary_visual_path' => $posterPath,
                'summary_generated_at' => now(),
            ]));

            if ($this->isApiRequest($request)) {
                return response()->json([
                    'message' => 'Rangkuman AI untuk materi berhasil dibuat.',
                    'data' => $this->formatBabResponse($bab->fresh()),
                ]);
            }

            return redirect()->route('materi.show', $materi->id)
                ->with('success', 'Rangkuman AI untuk materi berhasil dibuat.');
        } catch (GeminiCoverException $exception) {
            if ($this->isApiRequest($request)) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], $exception->status());
            }

            return redirect()->route('materi.show', $materi->id)
                ->with('error', $exception->getMessage());
        }
    }

    public function index(Request $request, Materi $materi)
    {
        $this->authorizeMateriAccess($materi);
        $bab = $materi->bab()
            ->with('kuis')
            ->withCount('kuis')
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'materi_id' => $materi->id,
            'total' => $bab->count(),
            'data' => $bab->map(fn (MateriBab $item) => $this->formatBabResponse($item)),
        ]);
    }

    private function validateBabPayload(Request $request, bool $isCreate): array
    {
        $maxUploadKb = $this->getServerUploadLimitInKb();

        return $request->validate([
            'judul_bab' => 'required|string|max:200',
            'urutan' => 'required|integer|min:1',
            'tipe_konten' => 'required|in:teks,file',
            'konten_teks' => 'nullable|string|required_if:tipe_konten,teks',
            'file_path' => ($isCreate ? 'nullable|file|mimes:' . self::MATERI_FILE_MIMES . '|max:' . $maxUploadKb : 'nullable|file|mimes:' . self::MATERI_FILE_MIMES . "|max:{$maxUploadKb}"),
            'pdf_source_path' => 'nullable|string',
            'pdf_page_selection' => 'nullable|string',
            'status_aktif' => 'boolean',
        ], [
            'judul_bab.required' => 'Judul materi wajib diisi.',
            'urutan.required' => 'Urutan materi wajib diisi.',
            'tipe_konten.required' => 'Tipe konten wajib dipilih.',
            'konten_teks.required_if' => 'Konten teks wajib diisi jika tipe konten adalah teks.',
            'file_path.required_if' => 'File wajib diupload jika tipe konten adalah file.',
            'file_path.mimes' => 'Format file materi harus PDF, Word, PowerPoint, ODT/ODP, RTF, atau TXT.',
        ]);
    }

    private function prepareBabPayload(Request $request, array $validated, ?MateriBab $bab = null): array
    {
        if (($validated['tipe_konten'] ?? null) === 'file' && !$request->hasFile('file_path') && !$request->filled('pdf_source_path') && !$bab?->file_path) {
            throw ValidationException::withMessages([
                'file_path' => 'Pilih PDF sumber atau upload file baru untuk materi berbentuk file.',
            ]);
        }

        if (($validated['tipe_konten'] ?? null) === 'file' && $request->filled('pdf_source_path') && trim((string) $request->input('pdf_page_selection')) === '') {
            throw ValidationException::withMessages([
                'pdf_page_selection' => 'Pilih range halaman dari PDF sumber untuk materi ini.',
            ]);
        }

        if ($request->hasFile('file_path')) {
            $storedFile = $this->storeMateriFile(
                $request->file('file_path'),
                $request->input('pdf_page_selection')
            );

            if ($bab?->file_path && $bab->file_path !== $bab?->pdf_source_path && Storage::disk('public')->exists($bab->file_path)) {
                Storage::disk('public')->delete($bab->file_path);
            }

            $validated['file_path'] = $storedFile['path'];
            $validated['pdf_source_path'] = $storedFile['source_path'] ?? null;
            $validated['pdf_page_selection'] = $request->input('pdf_page_selection');
        } elseif ($request->filled('pdf_source_path')) {
            $materiId = (int) ($bab?->materi_id ?? $request->route('materi')?->id ?? 0);
            $sourcePath = $this->validatePdfSourcePath($request->input('pdf_source_path'), $materiId);
            $storedFile = $this->storeFromExistingPdfSource($sourcePath, $request->input('pdf_page_selection'));

            if ($bab?->file_path && Storage::disk('public')->exists($bab->file_path)) {
                Storage::disk('public')->delete($bab->file_path);
            }

            $validated['file_path'] = $storedFile['path'];
            $validated['pdf_source_path'] = $sourcePath;
            $validated['pdf_page_selection'] = $request->input('pdf_page_selection');
        } else {
            $validated['file_path'] = $bab?->file_path;
            $validated['pdf_source_path'] = $bab?->pdf_source_path;
            $validated['pdf_page_selection'] = $bab?->pdf_page_selection;
        }

        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        return $validated;
    }

    private function storeMateriFile($file, $pageSelection = null): array
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $originalSize = $file->getSize() ?: 0;
        $maxTargetBytes = self::PDF_TARGET_MAX_KB * 1024;
        $pdfService = app(PdfCompressionService::class);

        if ($extension !== 'pdf' && $originalSize > $maxTargetBytes) {
            throw ValidationException::withMessages([
                'file_path' => 'File non-PDF maksimal 10 MB. Kompres otomatis hanya diterapkan untuk PDF.',
            ]);
        }

        if ($extension !== 'pdf' && $pageSelection) {
            throw ValidationException::withMessages([
                'file_path' => 'Pilihan halaman hanya berlaku untuk file PDF.',
            ]);
        }

        if ($extension !== 'pdf') {
            $fileName = time() . '_' . $file->getClientOriginalName();
            return [
                'path' => $file->storeAs('materi/bab', $fileName, 'public'),
                'source_path' => null,
                'page_count' => null,
            ];
        }

        $tempDirectory = storage_path('app/tmp/pdf-compression');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName) ?: 'bab';
        $uniqueToken = time() . '_' . uniqid();

        $sourcePath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_source.pdf';
        $selectedPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_selected.pdf';
        $targetPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_compressed.pdf';
        $bestEffortPath = $sourcePath . '.best';

        $file->move($tempDirectory, basename($sourcePath));

        $sourceStoredPath = 'materi/sources/' . $uniqueToken . '_' . $safeBaseName . '.pdf';
        Storage::disk('public')->put($sourceStoredPath, file_get_contents($sourcePath));

        $workingPath = $sourcePath;
        $pageCount = $pdfService->getPageCount($sourcePath);
        $selectedPages = $this->parseSelectedPages($pageSelection);

        if (!empty($selectedPages)) {
            if ($pageCount === null) {
                @unlink($sourcePath);
                Storage::disk('public')->delete($sourceStoredPath);
                throw ValidationException::withMessages([
                    'file_path' => 'Jumlah halaman PDF tidak bisa dibaca. Pastikan Ghostscript terpasang dengan benar.',
                ]);
            }

            $invalidPage = collect($selectedPages)->first(fn (int $page) => $page < 1 || $page > $pageCount);
            if ($invalidPage !== null) {
                @unlink($sourcePath);
                Storage::disk('public')->delete($sourceStoredPath);
                throw ValidationException::withMessages([
                    'file_path' => "Pilihan halaman tidak valid. Halaman {$invalidPage} berada di luar total {$pageCount} halaman.",
                ]);
            }

            if (count($selectedPages) < $pageCount) {
                $selectionResult = $pdfService->extractSelectedPages(
                    $sourcePath,
                    $selectedPath,
                    $this->buildGhostscriptPageList($selectedPages)
                );

                if (!($selectionResult['success'] ?? false)) {
                    @unlink($sourcePath);
                    @unlink($selectedPath);
                    Storage::disk('public')->delete($sourceStoredPath);
                    throw ValidationException::withMessages([
                        'file_path' => 'PDF gagal dipotong sesuai halaman yang dicentang.',
                    ]);
                }

                $workingPath = $selectedPath;
            }

            $pageCount = count($selectedPages);
        }

        $workingSize = filesize($workingPath) ?: 0;

        if ($workingSize > $maxTargetBytes) {
            $result = $pdfService->compressToTarget(
                $workingPath,
                $targetPath,
                $maxTargetBytes
            );

            $finalPath = $result['output_path'] ?? $workingPath;
            $finalSize = $result['final_size'] ?? 0;

            if (!is_file($finalPath) || $finalSize > $maxTargetBytes) {
                @unlink($sourcePath);
                @unlink($selectedPath);
                @unlink($targetPath);
                @unlink($bestEffortPath);
                Storage::disk('public')->delete($sourceStoredPath);

                $message = ($result['tool'] ?? null) === null
                    ? 'PDF di atas 10 MB diterima, tetapi server belum memiliki tool kompres PDF otomatis.'
                    : 'PDF gagal dikompres hingga 10 MB.';

                throw ValidationException::withMessages([
                    'file_path' => $message,
                ]);
            }
        } else {
            $finalPath = $workingPath;
        }

        $storedFileName = time() . '_' . $safeBaseName . '.pdf';
        $storedPath = 'materi/bab/' . $storedFileName;

        Storage::disk('public')->put($storedPath, file_get_contents($finalPath));

        @unlink($sourcePath);
        @unlink($selectedPath);
        @unlink($targetPath);
        @unlink($bestEffortPath);

        return [
            'path' => $storedPath,
            'source_path' => $sourceStoredPath,
            'page_count' => $pageCount,
        ];
    }

    private function storeFromExistingPdfSource(string $sourceStoragePath, ?string $pageSelection = null): array
    {
        if (!Storage::disk('public')->exists($sourceStoragePath)) {
            throw ValidationException::withMessages([
                'pdf_source_path' => 'PDF sumber tidak ditemukan. Upload ulang file PDF atau pilih sumber lain.',
            ]);
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $tempDirectory = storage_path('app/tmp/pdf-compression');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($sourceStoragePath, PATHINFO_FILENAME)) ?: 'bab';
        $uniqueToken = time() . '_' . uniqid();
        $sourcePath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_source.pdf';
        $selectedPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_selected.pdf';
        $targetPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_compressed.pdf';
        $bestEffortPath = $sourcePath . '.best';

        file_put_contents($sourcePath, Storage::disk('public')->get($sourceStoragePath));

        $pdfService = app(PdfCompressionService::class);
        $maxTargetBytes = self::PDF_TARGET_MAX_KB * 1024;
        $workingPath = $sourcePath;
        $pageCount = $pdfService->getPageCount($sourcePath);
        $selectedPages = $this->parseSelectedPages($pageSelection);

        if (!empty($selectedPages)) {
            if ($pageCount === null) {
                @unlink($sourcePath);
                throw ValidationException::withMessages([
                    'pdf_source_path' => 'Jumlah halaman PDF sumber tidak bisa dibaca.',
                ]);
            }

            $invalidPage = collect($selectedPages)->first(fn (int $page) => $page < 1 || $page > $pageCount);
            if ($invalidPage !== null) {
                @unlink($sourcePath);
                throw ValidationException::withMessages([
                    'pdf_page_selection' => "Pilihan halaman tidak valid. Halaman {$invalidPage} berada di luar total {$pageCount} halaman.",
                ]);
            }

            if (count($selectedPages) < $pageCount) {
                $selectionResult = $pdfService->extractSelectedPages(
                    $sourcePath,
                    $selectedPath,
                    $this->buildGhostscriptPageList($selectedPages)
                );

                if (!($selectionResult['success'] ?? false)) {
                    @unlink($sourcePath);
                    @unlink($selectedPath);
                    throw ValidationException::withMessages([
                        'pdf_page_selection' => 'PDF sumber gagal dipotong sesuai halaman yang dipilih.',
                    ]);
                }

                $workingPath = $selectedPath;
            }

            $pageCount = count($selectedPages);
        }

        $workingSize = filesize($workingPath) ?: 0;
        if ($workingSize > $maxTargetBytes) {
            $result = $pdfService->compressToTarget($workingPath, $targetPath, $maxTargetBytes);
            $finalPath = $result['output_path'] ?? $workingPath;
            $finalSize = $result['final_size'] ?? 0;

            if (!is_file($finalPath) || $finalSize > $maxTargetBytes) {
                @unlink($sourcePath);
                @unlink($selectedPath);
                @unlink($targetPath);
                @unlink($bestEffortPath);
                throw ValidationException::withMessages([
                    'pdf_page_selection' => 'PDF hasil potongan masih terlalu besar atau gagal dikompres hingga 10 MB.',
                ]);
            }
        } else {
            $finalPath = $workingPath;
        }

        $storedPath = 'materi/bab/' . time() . '_' . $safeBaseName . '.pdf';
        Storage::disk('public')->put($storedPath, file_get_contents($finalPath));

        @unlink($sourcePath);
        @unlink($selectedPath);
        @unlink($targetPath);
        @unlink($bestEffortPath);

        return [
            'path' => $storedPath,
            'source_path' => $sourceStoragePath,
            'page_count' => $pageCount,
        ];
    }

    private function validatePdfSourcePath(?string $sourcePath, int $materiId): string
    {
        $normalized = trim(str_replace('\\', '/', (string) $sourcePath), '/');

        if ($normalized === '' || !str_ends_with(strtolower($normalized), '.pdf')) {
            throw ValidationException::withMessages([
                'pdf_source_path' => 'PDF sumber tidak valid.',
            ]);
        }

        $known = MateriBab::query()
            ->where('materi_id', $materiId)
            ->where('pdf_source_path', $normalized)
            ->exists();

        if (!$known) {
            throw ValidationException::withMessages([
                'pdf_source_path' => 'PDF sumber tidak terhubung dengan mata pelajaran ini.',
            ]);
        }

        return $normalized;
    }

    private function buildPdfSourceOptions(Materi $materi): array
    {
        return $materi->bab()
            ->orderBy('urutan')
            ->get(['id', 'judul_bab', 'urutan', 'pdf_source_path'])
            ->filter(fn (MateriBab $bab) => $bab->pdf_source_path && str_ends_with(strtolower($bab->pdf_source_path), '.pdf'))
            ->map(function (MateriBab $bab) {
                return [
                    'path' => $bab->pdf_source_path,
                    'url' => Storage::url($bab->pdf_source_path),
                    'label' => 'PDF sumber dari Materi ' . $bab->urutan . ' - ' . $bab->judul_bab,
                    'file_name' => basename($bab->pdf_source_path),
                ];
            })
            ->unique('path')
            ->values()
            ->all();
    }

    private function suggestNextPdfPageStart(Materi $materi): ?int
    {
        $lastSelection = $materi->bab()
            ->whereNotNull('pdf_page_selection')
            ->orderByDesc('urutan')
            ->value('pdf_page_selection');

        $pages = $this->parseSelectedPages($lastSelection);

        return $pages === [] ? null : max($pages) + 1;
    }

    private function parseSelectedPages(?string $pageSelection): array
    {
        if ($pageSelection === null || trim($pageSelection) === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*/', trim($pageSelection)) ?: [];
        $pages = [];

        foreach ($parts as $part) {
            if ($part === '' || !ctype_digit($part)) {
                continue;
            }

            $page = (int) $part;
            if ($page > 0) {
                $pages[] = $page;
            }
        }

        $pages = array_values(array_unique($pages));
        sort($pages);

        return $pages;
    }

    private function buildGhostscriptPageList(array $pages): string
    {
        sort($pages);

        $ranges = [];
        $start = null;
        $previous = null;

        foreach ($pages as $page) {
            if ($start === null) {
                $start = $page;
                $previous = $page;
                continue;
            }

            if ($page === $previous + 1) {
                $previous = $page;
                continue;
            }

            $ranges[] = $start === $previous ? (string) $start : "{$start}-{$previous}";
            $start = $page;
            $previous = $page;
        }

        if ($start !== null) {
            $ranges[] = $start === $previous ? (string) $start : "{$start}-{$previous}";
        }

        return implode(',', $ranges);
    }

    private function getServerUploadLimitInKb(): int
    {
        $uploadMax = $this->convertIniSizeToKb((string) ini_get('upload_max_filesize'));
        $postMax = $this->convertIniSizeToKb((string) ini_get('post_max_size'));
        $effectiveLimit = min($uploadMax, $postMax);

        return $effectiveLimit > 0 ? $effectiveLimit : self::PDF_TARGET_MAX_KB;
    }

    private function convertIniSizeToKb(string $value): int
    {
        $value = trim($value);

        if ($value === '') {
            return self::PDF_TARGET_MAX_KB;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) round($number * 1024 * 1024),
            'm' => (int) round($number * 1024),
            'k' => (int) round($number),
            default => (int) round($number / 1024),
        };
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->wantsJson() || $request->is('api/*');
    }

    private function formatBabResponse(MateriBab $bab): MateriBab
    {
        return $bab->loadMissing('kuis')->loadCount('kuis');
    }
}
