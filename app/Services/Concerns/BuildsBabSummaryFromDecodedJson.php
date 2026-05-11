<?php

namespace App\Services\Concerns;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;

trait BuildsBabSummaryFromDecodedJson
{
    protected function buildPromptInstructions(Materi $materi, MateriBab $bab): string
    {
        $mataPelajaran = trim((string) optional($materi->mataPelajaran)->nama);
        $level = trim((string) optional($materi->level)->nama);

        return implode("\n", array_filter([
            'Buat rangkuman visual-singkat untuk bab materi pembelajaran berikut.',
            'Output harus berupa JSON murni tanpa markdown.',
            'Gunakan bahasa Indonesia saja untuk semua nilai string di JSON.',
            'Tulislah seperti menjelaskan ke teman sekelas: natural, mudah diucapkan, tidak kaku, tidak bertele-tele.',
            'Dilarang menyisipkan kata atau frasa bahasa Inggris (contoh: tips, focus, key point, remember, dll).',
            'Gunakan bahasa Indonesia yang sederhana, jelas, dan cocok untuk siswa.',
            'Jangan menambahkan fakta di luar materi.',
            'Prioritaskan isi yang singkat, padat, dan mudah dibaca cepat.',
            'Format JSON wajib:',
            '{"judul_ringkasan":"string","ringkasan_singkat":"string","poin_utama":["string"],"kata_kunci":["string"],"tips_mengingat":"string","contoh":"string"}',
            'Buat `judul_ringkasan` maksimal 4 kata.',
            'Buat `ringkasan_singkat` 1–2 kalimat padat, maksimal 28 kata, utuh sampai selesai.',
            'Buat `poin_utama` tepat 3 poin, tiap poin maksimal 14 kata, jangan putus di tengah ide.',
            'Buat `kata_kunci` maksimal 4 item, tiap item 1-2 kata.',
            '`tips_mengingat` = satu kalimat trik atau analogi biar siswa cepat ingat inti bab (bukan poin baru, bukan pertanyaan). Maksimal 16 kata.',
            'Buat `contoh` singkat, maksimal 14 kata. Jika tidak perlu, isi string kosong.',
            "Judul buku: {$materi->judul}.",
            "Judul bab: {$bab->judul_bab}.",
            $mataPelajaran !== '' ? "Mata pelajaran: {$mataPelajaran}." : null,
            $level !== '' ? "Level siswa: {$level}." : null,
        ]));
    }

    /**
     * @param  array<string, mixed>  $decoded
     * @return array<string, mixed>
     */
    protected function mapDecodedSummaryToPayload(array $decoded, MateriBab $bab): array
    {
        $title = $this->limitWords(trim((string) ($decoded['judul_ringkasan'] ?? '')), 4);
        $short = $this->limitWords(trim((string) ($decoded['ringkasan_singkat'] ?? '')), 28);
        $memoryTip = $this->limitWords(trim((string) ($decoded['tips_mengingat'] ?? '')), 16);
        $example = $this->limitWords(trim((string) ($decoded['contoh'] ?? '')), 14);
        $keyPoints = $this->normalizeStringArray($decoded['poin_utama'] ?? [], 3, 14);
        $keywords = $this->normalizeStringArray($decoded['kata_kunci'] ?? [], 4, 2);

        if ($short === '' || $keyPoints === []) {
            throw new GeminiCoverException('Rangkuman bab belum memenuhi format minimal sistem (ringkasan atau poin utama kosong).');
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
}
