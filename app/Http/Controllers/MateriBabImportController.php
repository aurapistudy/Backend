<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\MateriBab;
use App\Services\PdfChapterDetectionService;
use App\Services\PdfCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MateriBabImportController extends Controller
{
    public function showForm(Materi $materi)
    {
        return view('dashboard.materi-bab.import', compact('materi'));
    }

    public function tempDetect(Request $request, PdfChapterDetectionService $detectionService)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:102400', // max 100MB
        ]);

        $file = $request->file('pdf_file');
        
        $tempDirectory = storage_path('app/tmp/pdf-import');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $uniqueToken = time() . '_' . uniqid();
        $sourcePath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_temp_source.pdf';
        
        $file->move($tempDirectory, basename($sourcePath));
        
        $pageCount = app(PdfCompressionService::class)->getPageCount($sourcePath);
        $includeOptional = $request->boolean('include_optional');
        $chapters = $detectionService->detectChapters($sourcePath, $includeOptional);
        
        @unlink($sourcePath);

        return response()->json([
            'success' => true,
            'page_count' => $pageCount,
            'chapters' => $chapters,
        ]);
    }

    public function detectExistingPdf(Request $request, PdfChapterDetectionService $detectionService)
    {
        $request->validate([
            'pdf_source_path' => 'required|string',
        ]);

        $pdfSourcePath = $request->input('pdf_source_path');
        if (!Storage::disk('public')->exists($pdfSourcePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File PDF sumber tidak ditemukan.'
            ], 404);
        }

        $absolutePath = Storage::disk('public')->path($pdfSourcePath);
        $pageCount = app(PdfCompressionService::class)->getPageCount($absolutePath);
        $includeOptional = $request->boolean('include_optional');
        $chapters = $detectionService->detectChapters($absolutePath, $includeOptional);

        return response()->json([
            'success' => true,
            'page_count' => $pageCount,
            'chapters' => $chapters,
        ]);
    }

    public function detect(Request $request, Materi $materi, PdfChapterDetectionService $detectionService)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:102400', // max 100MB
        ]);

        $file = $request->file('pdf_file');
        
        $tempDirectory = storage_path('app/tmp/pdf-import');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $uniqueToken = time() . '_' . uniqid();
        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'bab';
        $sourcePath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_source.pdf';
        
        $file->move($tempDirectory, basename($sourcePath));
        
        // Simpan ke directory sources untuk dipakai nanti
        $sourceStoredPath = 'materi/sources/' . $uniqueToken . '_' . $safeBaseName . '.pdf';
        Storage::disk('public')->put($sourceStoredPath, file_get_contents($sourcePath));

        $pageCount = app(PdfCompressionService::class)->getPageCount($sourcePath);
        $includeOptional = $request->boolean('include_optional');
        $chapters = $detectionService->detectChapters($sourcePath, $includeOptional);

        return response()->json([
            'success' => true,
            'source_path' => $sourceStoredPath,
            'page_count' => $pageCount,
            'chapters' => $chapters,
        ]);
    }

    public function store(Request $request, Materi $materi, PdfCompressionService $pdfService)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(600); // 10 menit karena ini berat
        }

        $request->validate([
            'source_path' => 'required|string',
            'chapters' => 'required|array|min:1',
            'chapters.*.judul_bab' => 'required|string|max:200',
            'chapters.*.halaman_awal' => 'required|integer|min:1',
            'chapters.*.halaman_akhir' => 'required|integer|min:1',
        ]);

        $sourcePath = $request->input('source_path');
        if (!Storage::disk('public')->exists($sourcePath)) {
            throw ValidationException::withMessages([
                'source_path' => 'File PDF sumber tidak ditemukan. Silakan upload ulang.'
            ]);
        }

        $tempDirectory = storage_path('app/tmp/pdf-compression');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $localSourcePath = $tempDirectory . DIRECTORY_SEPARATOR . time() . '_' . uniqid() . '_import_source.pdf';
        file_put_contents($localSourcePath, Storage::disk('public')->get($sourcePath));

        $chapters = $request->input('chapters');
        $pageCount = $pdfService->getPageCount($localSourcePath);

        foreach ($chapters as $index => $chapterData) {
            $halamanAwal = (int) $chapterData['halaman_awal'];
            $halamanAkhir = (int) $chapterData['halaman_akhir'];
            $judulBab = $chapterData['judul_bab'] ?? 'Bab ' . ($index + 1);

            if ($halamanAwal > $halamanAkhir) {
                @unlink($localSourcePath);

                throw ValidationException::withMessages([
                    "chapters.$index.halaman_awal" => "Range halaman {$judulBab} terbalik. Halaman awal harus lebih kecil atau sama dengan halaman akhir.",
                ]);
            }

            if ($pageCount !== null && $halamanAkhir > $pageCount) {
                @unlink($localSourcePath);

                throw ValidationException::withMessages([
                    "chapters.$index.halaman_akhir" => "Range halaman {$judulBab} melewati total PDF ({$pageCount} halaman).",
                ]);
            }
        }
        
        // Urutkan array based on halaman_awal
        usort($chapters, function($a, $b) {
            return $a['halaman_awal'] <=> $b['halaman_awal'];
        });

        $nextUrutan = ((int) $materi->bab()->max('urutan')) + 1;
        $maxTargetBytes = 10240 * 1024; // 10MB

        $berhasil = 0;

        foreach ($chapters as $index => $chapterData) {
            $halamanAwal = (int) $chapterData['halaman_awal'];
            $halamanAkhir = (int) $chapterData['halaman_akhir'];
            
            $uniqueToken = time() . '_' . uniqid();
            $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $chapterData['judul_bab']) ?: 'bab';
            $extractedPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_extracted.pdf';
            $compressedPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_compressed.pdf';
            
            $pageList = [];
            for ($i = $halamanAwal; $i <= $halamanAkhir; $i++) {
                $pageList[] = $i;
            }
            $pageListStr = implode(',', $pageList);
            
            // Format for Ghostscript
            $pageSelection = $halamanAwal === $halamanAkhir ? (string)$halamanAwal : "{$halamanAwal}-{$halamanAkhir}";
            
            $selectionResult = $pdfService->extractSelectedPages(
                $localSourcePath,
                $extractedPath,
                $pageSelection
            );

            if (!($selectionResult['success'] ?? false)) {
                @unlink($localSourcePath);

                throw ValidationException::withMessages([
                    "chapters.$index.halaman_awal" => "PDF gagal dipotong untuk {$chapterData['judul_bab']}. Cek range halaman atau tool Ghostscript di server.",
                ]);
            }

            $workingSize = filesize($extractedPath) ?: 0;
            $finalPath = $extractedPath;

            if ($workingSize > $maxTargetBytes) {
                $result = $pdfService->compressToTarget($extractedPath, $compressedPath, $maxTargetBytes);
                $finalPath = $result['output_path'] ?? $extractedPath;
            }

            $storedPath = 'materi/bab/' . time() . '_' . $uniqueToken . '_' . $safeTitle . '.pdf';
            Storage::disk('public')->put($storedPath, file_get_contents($finalPath));

            @unlink($extractedPath);
            @unlink($compressedPath);
            @unlink($extractedPath . '.best');

            MateriBab::create([
                'materi_id' => $materi->id,
                'judul_bab' => $chapterData['judul_bab'],
                'urutan' => $nextUrutan + $index,
                'tipe_konten' => 'file',
                'file_path' => $storedPath,
                'pdf_source_path' => $sourcePath,
                'pdf_page_selection' => $pageListStr,
                'jumlah_halaman' => ($halamanAkhir - $halamanAwal) + 1,
                'status_aktif' => true,
            ]);

            $berhasil++;
        }

        @unlink($localSourcePath);

        if ($berhasil === 0) {
            throw ValidationException::withMessages([
                'chapters' => 'Belum ada bab yang berhasil dipotong. Cek kembali range halaman PDF.',
            ]);
        }

        return redirect()->route('materi.show', $materi->id)
            ->with('success', $berhasil . ' Bab berhasil diimport dari PDF.');
    }
}
