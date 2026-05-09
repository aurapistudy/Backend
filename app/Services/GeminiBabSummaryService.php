<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiBabSummaryService
{
    public function generateSummary(Materi $materi, MateriBab $bab): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.text_model', 'gemini-2.5-flash-lite');

        if ($apiKey === '') {
            throw new GeminiCoverException('GEMINI_API_KEY belum dikonfigurasi.');
        }

        $prompt = $this->buildPrompt($materi, $bab);
        $contents = [[
            'parts' => $this->buildParts($materi, $bab, $prompt),
        ]];

        $response = Http::timeout(120)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => $contents,
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if ($response->failed()) {
            $rawMessage = $response->json('error.message')
                ?: $response->json('message')
                ?: 'Permintaan ke Gemini gagal.';

            throw new GeminiCoverException(
                $this->buildFriendlyErrorMessage($response->status(), $rawMessage, $model),
                $this->normalizeHttpStatus($response->status())
            );
        }

        $text = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));
        if ($text === '') {
            throw new GeminiCoverException('Gemini tidak mengembalikan rangkuman bab.');
        }

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new GeminiCoverException('Format rangkuman bab dari Gemini tidak valid.');
        }

        $title = $this->limitWords(trim((string) ($decoded['judul_ringkasan'] ?? '')), 4);
        $short = $this->limitWords(trim((string) ($decoded['ringkasan_singkat'] ?? '')), 18);
        $memoryTip = $this->limitWords(trim((string) ($decoded['tips_mengingat'] ?? '')), 10);
        $example = $this->limitWords(trim((string) ($decoded['contoh'] ?? '')), 8);
        $keyPoints = $this->normalizeStringArray($decoded['poin_utama'] ?? [], 3, 8);
        $keywords = $this->normalizeStringArray($decoded['kata_kunci'] ?? [], 4, 2);

        if ($short === '' || $keyPoints === []) {
            throw new GeminiCoverException('Rangkuman bab dari Gemini belum memenuhi format minimal sistem.');
        }

        return [
            'summary_title' => $title !== '' ? $title : 'Rangkuman ' . $bab->judul_bab,
            'summary_short' => $short,
            'summary_key_points' => $keyPoints,
            'summary_keywords' => $keywords,
            'summary_memory_tip' => $memoryTip !== '' ? $memoryTip : null,
            'summary_example' => $example !== '' ? $example : null,
        ];
    }

    private function buildPrompt(Materi $materi, MateriBab $bab): string
    {
        $mataPelajaran = trim((string) optional($materi->mataPelajaran)->nama);
        $level = trim((string) optional($materi->level)->nama);

        return implode("\n", array_filter([
            'Buat rangkuman visual-singkat untuk bab materi pembelajaran berikut.',
            'Output harus berupa JSON murni tanpa markdown.',
            'Gunakan bahasa Indonesia yang sederhana, jelas, dan cocok untuk siswa.',
            'Jangan menambahkan fakta di luar materi.',
            'Prioritaskan isi yang sangat singkat, padat, dan mudah dibaca cepat.',
            'Format JSON wajib:',
            '{"judul_ringkasan":"string","ringkasan_singkat":"string","poin_utama":["string"],"kata_kunci":["string"],"tips_mengingat":"string","contoh":"string"}',
            'Buat `judul_ringkasan` maksimal 4 kata.',
            'Buat `ringkasan_singkat` hanya 1 kalimat pendek, maksimal 18 kata.',
            'Buat `poin_utama` tepat 3 poin, tiap poin maksimal 8 kata.',
            'Buat `kata_kunci` maksimal 4 item, tiap item 1-2 kata.',
            'Buat `tips_mengingat` hanya 1 kalimat pendek, maksimal 10 kata.',
            'Buat `contoh` singkat sekali, maksimal 8 kata. Jika tidak perlu, isi string kosong.',
            "Judul buku: {$materi->judul}.",
            "Judul bab: {$bab->judul_bab}.",
            $mataPelajaran !== '' ? "Mata pelajaran: {$mataPelajaran}." : null,
            $level !== '' ? "Level siswa: {$level}." : null,
        ]));
    }

    private function buildParts(Materi $materi, MateriBab $bab, string $prompt): array
    {
        $trimmedText = trim((string) $bab->konten_teks);

        if ($trimmedText !== '') {
            return [[
                'text' => $prompt . "\n\nIsi bab:\n" . mb_substr($trimmedText, 0, 18000),
            ]];
        }

        if ($bab->file_path) {
            $publicDisk = Storage::disk('public');
            if (!$publicDisk->exists($bab->file_path)) {
                throw new GeminiCoverException('File bab tidak ditemukan di storage.');
            }

            $extension = strtolower((string) pathinfo($bab->file_path, PATHINFO_EXTENSION));
            $mimeType = match ($extension) {
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                default => null,
            };

            if ($mimeType === null) {
                throw new GeminiCoverException('Format file bab belum didukung untuk generate rangkuman.');
            }

            return [
                ['text' => $prompt],
                [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => base64_encode($publicDisk->get($bab->file_path)),
                    ],
                ],
            ];
        }

        $fallbackContext = trim(implode("\n", array_filter([
            'Judul buku: ' . $materi->judul,
            'Judul bab: ' . $bab->judul_bab,
            $materi->deskripsi ? 'Deskripsi buku: ' . $materi->deskripsi : null,
        ])));

        if ($fallbackContext === '') {
            throw new GeminiCoverException('Bab ini belum punya konten yang cukup untuk dibuatkan rangkuman.');
        }

        return [[
            'text' => $prompt . "\n\nKonteks bab:\n" . $fallbackContext,
        ]];
    }

    private function normalizeStringArray(mixed $values, int $maxItems, ?int $maxWordsPerItem = null): array
    {
        if (!is_array($values)) {
            return [];
        }

        $normalized = [];
        foreach ($values as $value) {
            $item = trim((string) $value);
            if ($maxWordsPerItem !== null) {
                $item = $this->limitWords($item, $maxWordsPerItem);
            }
            if ($item === '') {
                continue;
            }
            $normalized[] = $item;
            if (count($normalized) >= $maxItems) {
                break;
            }
        }

        return $normalized;
    }

    private function limitWords(string $text, int $maxWords): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($text === '' || $maxWords < 1) {
            return '';
        }

        $words = preg_split('/\s+/', $text) ?: [];
        if (count($words) <= $maxWords) {
            return $text;
        }

        return implode(' ', array_slice($words, 0, $maxWords));
    }

    private function buildFriendlyErrorMessage(int $status, string $rawMessage, string $model): string
    {
        $normalized = strtolower($rawMessage);

        if (
            $status === 429
            || str_contains($normalized, 'quota')
            || str_contains($normalized, 'rate limit')
            || str_contains($normalized, 'resource_exhausted')
        ) {
            return "Kuota Gemini untuk generate rangkuman bab sedang habis atau belum aktif pada project ini. "
                . "Cek billing dan rate limit di Google AI Studio, atau coba lagi nanti. "
                . "Model yang sedang dipakai: {$model}.";
        }

        if ($status === 403 || str_contains($normalized, 'permission')) {
            return "Akses ke model Gemini ditolak. Pastikan API key benar dan model {$model} tersedia untuk akun kamu.";
        }

        if ($status === 404 || str_contains($normalized, 'not found')) {
            return "Model Gemini {$model} tidak ditemukan. Cek nilai GEMINI_TEXT_MODEL di file .env.";
        }

        if ($status >= 500) {
            return 'Layanan Gemini sedang bermasalah saat membuat rangkuman bab. Coba beberapa saat lagi.';
        }

        return $rawMessage;
    }

    private function normalizeHttpStatus(int $status): int
    {
        return $status >= 400 && $status <= 599 ? $status : 422;
    }
}
