<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class PdfChapterDetectionService
{
    /**
     * @return array<int, array{judul_bab: string, halaman_awal: int, halaman_akhir: int}>
     */
    public function detectChapters(string $pdfPath, bool $includeOptional = false): array
    {
        $chapters = $this->detectViaBookmarks($pdfPath, $includeOptional);
        if (!empty($chapters)) {
            return $chapters;
        }

        return $this->detectViaGemini($pdfPath, $includeOptional);
    }

    private function detectViaBookmarks(string $pdfPath, bool $includeOptional): array
    {
        $pdftk = $this->findBinary('pdftk');
        if (!$pdftk) return [];

        $command = escapeshellcmd($pdftk) . ' ' . escapeshellarg($pdfPath) . ' dump_data 2>&1';
        $output = [];
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) return [];

        $chapters = [];
        $currentTitle = null;
        $currentPage = null;

        foreach ($output as $line) {
            if (str_starts_with($line, 'BookmarkTitle: ')) {
                $currentTitle = trim(substr($line, 15));
            } elseif (str_starts_with($line, 'BookmarkPageNumber: ')) {
                $currentPage = (int) trim(substr($line, 20));
                if ($currentTitle && $currentPage > 0) {
                    // Skip obvious front/back matter bookmarks if not including optional
                    if ($includeOptional || !$this->isFrontOrBackMatter($currentTitle)) {
                        $chapters[] = [
                            'judul_bab'    => $currentTitle,
                            'halaman_awal' => $currentPage,
                            'halaman_akhir' => null,
                        ];
                    }
                    $currentTitle = null;
                    $currentPage  = null;
                }
            }
        }

        if (empty($chapters)) return [];

        // Hitung halaman akhir
        $pdfCompressionService = app(PdfCompressionService::class);
        $totalPages = $pdfCompressionService->getPageCount($pdfPath);

        for ($i = 0; $i < count($chapters); $i++) {
            if (isset($chapters[$i + 1])) {
                $chapters[$i]['halaman_akhir'] = max($chapters[$i]['halaman_awal'], $chapters[$i + 1]['halaman_awal'] - 1);
            } else {
                $chapters[$i]['halaman_akhir'] = $totalPages ?: $chapters[$i]['halaman_awal'];
            }
        }

        return $chapters;
    }

    private function detectViaGemini(string $pdfPath, bool $includeOptional): array
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            $apiKey = Setting::get('gemini_api_key');
        }
        
        if (empty($apiKey)) {
            return [];
        }

        $model = config('services.gemini.text_model', 'gemini-2.5-flash-lite');
        $fileData = base64_encode(file_get_contents($pdfPath));

        if ($includeOptional) {
            $prompt = <<<'PROMPT'
You are analyzing a PDF textbook or learning material. Your task is to detect ALL sections of this book, including Front Matter, Main Chapters, and Back Matter.

STRICT RULES — READ CAREFULLY:

1. PHYSICAL PAGE NUMBERS ONLY.
   You MUST use the physical page index of the PDF file (1 = first page of the PDF file, 2 = second page, etc.).
   DO NOT use printed page numbers written on the book pages. Count from the very first page of the PDF file.

2. INCLUDE FRONT MATTER:
   Identify sections like Cover/Sampul, Kata Pengantar, Daftar Isi.

3. INCLUDE MAIN CHAPTERS:
   Identify numbered chapters like "Bab 1 - ...", "Bab 2 - ...".

4. INCLUDE BACK MATTER:
   Identify sections like Daftar Pustaka, Glosarium, Indeks, Profil Penulis.

5. Each section should have both halaman_awal and halaman_akhir using PHYSICAL PDF page numbers.
   - halaman_akhir of section N = (halaman_awal of section N+1) - 1
   - halaman_akhir of the LAST section = the final page of the book.

Return ONLY a valid JSON array. No explanation, no markdown. Format:
[
  {"judul_bab": "Sampul", "halaman_awal": 1, "halaman_akhir": 2},
  {"judul_bab": "Daftar Isi", "halaman_awal": 3, "halaman_akhir": 5},
  {"judul_bab": "Bab 1 - Pendahuluan", "halaman_awal": 6, "halaman_akhir": 20}
]
PROMPT;
        } else {
            $prompt = <<<'PROMPT'
You are analyzing a PDF textbook or learning material. Your task is to detect ONLY the main content chapters (Bab/Chapter) of this book.

STRICT RULES — READ CAREFULLY:

1. PHYSICAL PAGE NUMBERS ONLY.
   You MUST use the physical page index of the PDF file (1 = first page of the PDF file, 2 = second page, etc.).
   DO NOT use printed page numbers written on the book pages. Count from the very first page of the PDF file.
   Example: If the PDF has 4 pages of front matter before the actual page "1", then what the book calls "Halaman 1" is physical page 5 of the PDF.

2. EXCLUDE FRONT MATTER completely. Do NOT include any of these as a chapter:
   - Cover page / Sampul
   - Half-title page
   - Title page
   - Copyright / Hak Cipta
   - Dedication / Persembahan
   - Preface / Prakata / Kata Pengantar / Sambutan
   - Table of contents / Daftar Isi
   - List of figures / Daftar Gambar / Daftar Tabel
   - Introduction / Pendahuluan (if it is a pre-chapter intro, not a numbered chapter)
   - Foreword / Sekapur Sirih
   - About the Author (if in the front)
   - Kompetensi Dasar / KI-KD / Capaian Pembelajaran (if standalone, not part of a chapter)
   - Any roman-numeral-numbered pages

3. EXCLUDE BACK MATTER completely. Do NOT include any of these as a chapter:
   - Bibliography / Daftar Pustaka / Daftar Referensi
   - Glossary / Glosarium / Kamus Istilah
   - Index / Indeks
   - Appendix / Lampiran
   - Author biography / Profil Penulis / Tentang Penulis
   - Closing / Penutup (standalone closing page, not a numbered chapter)
   - Acknowledgements / Ucapan Terima Kasih
   - Answer Key / Kunci Jawaban
   - Any pages after the last numbered chapter

4. ONLY INCLUDE genuine numbered chapters:
   - "Bab 1", "Bab 2", ... / "Chapter 1", "Chapter 2", ...
   - Named chapters that are numbered (e.g., "Bab I – Pengenalan", "Unit 1 – ...")
   - Each chapter should have both halaman_awal and halaman_akhir using PHYSICAL PDF page numbers.
   - halaman_akhir of chapter N = (halaman_awal of chapter N+1) - 1
   - halaman_akhir of the LAST chapter = the page just BEFORE any back matter begins (do NOT include back matter pages in the last chapter's range)

5. VERIFY your physical page counts:
   - Count pages visually from the very beginning of the PDF.
   - The first chapter should NOT start on physical page 1 unless there is literally no front matter at all.
   - If you are unsure, err on the side of starting the chapter 1-2 pages LATER rather than earlier.

Return ONLY a valid JSON array. No explanation, no markdown. Format:
[
  {"judul_bab": "Bab 1 - Judul Bab", "halaman_awal": 10, "halaman_akhir": 25},
  {"judul_bab": "Bab 2 - Judul Bab", "halaman_awal": 26, "halaman_akhir": 45}
]
PROMPT;
        }

        $response = Http::timeout(120)->withHeaders([
            'x-goog-api-key' => $apiKey,
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => 'application/pdf',
                                'data' => $fileData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.1,
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini chapter detection failed: ' . $response->body());
            return [];
        }

        $text = trim(data_get($response->json(), 'candidates.0.content.parts.0.text', ''));
        if (empty($text)) return [];

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) return [];

        $chapters = [];
        foreach ($decoded as $item) {
            if (isset($item['judul_bab']) && isset($item['halaman_awal']) && isset($item['halaman_akhir'])) {
                $judul = trim((string) $item['judul_bab']);
                $halamanAwal = (int) $item['halaman_awal'];
                $halamanAkhir = (int) $item['halaman_akhir'];

                // Safety filter: skip obvious front matter / back matter based on title keywords if not including optional
                if (!$includeOptional && $this->isFrontOrBackMatter($judul)) {
                    Log::info("PdfChapterDetection: skipped front/back matter entry: \"{$judul}\"");
                    continue;
                }

                if ($halamanAwal < 1 || $halamanAkhir < $halamanAwal) {
                    continue;
                }

                $chapters[] = [
                    'judul_bab'    => $judul,
                    'halaman_awal' => $halamanAwal,
                    'halaman_akhir' => $halamanAkhir,
                ];
            }
        }

        return $chapters;
    }

    /**
     * Returns true if the given title looks like front matter or back matter
     * that should NOT be treated as a main learning chapter.
     */
    private function isFrontOrBackMatter(string $judul): bool
    {
        $lower = mb_strtolower($judul);

        $frontBackKeywords = [
            // Front matter
            'cover', 'sampul', 'halaman judul', 'hak cipta', 'copyright',
            'persembahan', 'dedication',
            'kata pengantar', 'prakata', 'sambutan', 'sekapur sirih', 'foreword',
            'daftar isi', 'table of content', 'daftar gambar', 'daftar tabel',
            'daftar singkatan', 'daftar lambang',
            'tentang buku', 'petunjuk penggunaan',
            'kompetensi inti', 'kompetensi dasar', 'ki-kd', 'capaian pembelajaran',
            // Back matter
            'daftar pustaka', 'bibliography', 'referensi', 'daftar referensi',
            'glosarium', 'glossary', 'kamus istilah',
            'indeks', 'index',
            'lampiran', 'appendix', 'apendiks',
            'profil penulis', 'tentang penulis', 'about the author', 'biografi penulis',
            'kunci jawaban', 'answer key',
            'ucapan terima kasih', 'acknowledgement',
        ];

        foreach ($frontBackKeywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        // Also skip if title starts with roman numerals only (e.g. "i", "ii", "iii", "iv", "v", "vi")
        // which are typical of front matter sections
        if (preg_match('/^(i{1,3}|iv|v|vi{0,3}|ix|x)[\s\.\-:]?\s*$/i', $lower)) {
            return true;
        }

        return false;
    }

    private function findBinary(string $name): ?string
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'where.exe ' . $name . ' 2>NUL' : 'command -v ' . escapeshellarg($name) . ' 2>/dev/null';
        $output = [];
        exec($command, $output, $exitCode);
        return ($exitCode === 0 && !empty($output[0])) ? trim($output[0]) : null;
    }
}
