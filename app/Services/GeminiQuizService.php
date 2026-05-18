<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiQuizService
{
    public function generateFromMateri(
        Materi $materi,
        int $jumlahSoal = 5,
        string $kesulitan = 'sedang',
        string $jenisSoal = 'pilihan',
        ?MateriBab $bab = null
    ): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.text_model', 'gemini-2.5-flash-lite');

        if ($apiKey === '') {
            throw new GeminiCoverException('GEMINI_API_KEY belum dikonfigurasi.');
        }

        $jumlahSoal = max(1, min($jumlahSoal, 10));
        $jenisSoal = in_array($jenisSoal, ['pilihan', 'essay'], true)
            ? $jenisSoal
            : 'pilihan';

        $prompt = $this->buildPrompt($materi, $jumlahSoal, $kesulitan, $jenisSoal, $bab);
        $contents = [[
            'parts' => $this->buildParts($materi, $prompt, $bab),
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
            throw new GeminiCoverException('Gemini tidak mengembalikan draft kuis.');
        }

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new GeminiCoverException('Format draft kuis dari Gemini tidak valid.');
        }

        $quizTitle = trim((string) ($decoded['judul'] ?? ''));
        $quizDescription = trim((string) ($decoded['deskripsi'] ?? ''));
        $questions = $decoded['pertanyaan'] ?? null;

        if (!is_array($questions) || $questions === []) {
            throw new GeminiCoverException('Gemini tidak menghasilkan pertanyaan kuis yang valid.');
        }

        $normalizedQuestions = [];
        foreach ($questions as $index => $question) {
            $textQuestion = trim((string) ($question['teks'] ?? ''));

            if ($textQuestion === '') {
                continue;
            }

            $normalizedQuestion = $this->normalizeQuestionByType($question, $textQuestion, $jenisSoal);
            if ($normalizedQuestion !== null) {
                $normalizedQuestions[] = $normalizedQuestion;
            }
        }

        if ($normalizedQuestions === []) {
            throw new GeminiCoverException('Draft kuis dari Gemini tidak lolos validasi sistem.');
        }

        return [
            'judul' => $quizTitle !== '' ? $quizTitle : 'Kuis ' . $materi->judul,
            'deskripsi' => $quizDescription !== '' ? $quizDescription : 'Kuis otomatis dari materi ' . $materi->judul,
            'pertanyaan' => $normalizedQuestions,
        ];
    }

    private function normalizeQuestionByType(array $question, string $textQuestion, string $jenisSoal): ?array
    {
        if ($jenisSoal === 'essay') {
            $jawabanTeks = trim((string) ($question['jawaban_teks'] ?? ''));
            $keyword = trim((string) ($question['keyword'] ?? ''));

            if ($jawabanTeks === '' || $keyword === '') {
                return null;
            }

            return [
                'teks' => $textQuestion,
                'tipe' => 'essay',
                'jawaban_teks' => $jawabanTeks,
                'keyword' => $keyword,
                'bahasa' => 'id-ID',
            ];
        }

        $opsi = $question['opsi'] ?? [];
        $benar = strtoupper(trim((string) ($question['benar'] ?? '')));

        if (
            !is_array($opsi)
            || trim((string) ($opsi['A'] ?? '')) === ''
            || trim((string) ($opsi['B'] ?? '')) === ''
            || trim((string) ($opsi['C'] ?? '')) === ''
            || trim((string) ($opsi['D'] ?? '')) === ''
            || !in_array($benar, ['A', 'B', 'C', 'D'], true)
        ) {
            return null;
        }

        return [
            'teks' => $textQuestion,
            'tipe' => 'pilihan',
            'opsi' => [
                'A' => trim((string) $opsi['A']),
                'B' => trim((string) $opsi['B']),
                'C' => trim((string) $opsi['C']),
                'D' => trim((string) $opsi['D']),
            ],
            'benar' => $benar,
        ];
    }

    private function buildParts(Materi $materi, string $prompt, ?MateriBab $bab = null): array
    {
        $parts = [];
        $contentSource = $bab ?? $materi;
        $trimmedText = trim((string) $contentSource->konten_teks);

        if ($trimmedText !== '') {
            $parts[] = [
                'text' => $prompt . "\n\nMateri:\n" . mb_substr($trimmedText, 0, 18000),
            ];

            return $parts;
        }

        if ($contentSource->file_path) {
            $publicDisk = Storage::disk('public');
            if (!$publicDisk->exists($contentSource->file_path)) {
                throw new GeminiCoverException('File materi tidak ditemukan di storage.');
            }

            $extension = strtolower((string) pathinfo($contentSource->file_path, PATHINFO_EXTENSION));
            $mimeType = match ($extension) {
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                default => null,
            };

            if ($mimeType === null) {
                throw new GeminiCoverException('Format file materi belum didukung untuk generate kuis.');
            }

            $parts[] = [
                'text' => $prompt,
            ];
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $mimeType,
                    'data' => base64_encode($publicDisk->get($contentSource->file_path)),
                ],
            ];

            return $parts;
        }

        $fallbackContext = trim(implode("\n", array_filter([
            'Judul Buku: ' . $materi->judul,
            $bab ? 'Judul Bab: ' . $bab->judul_bab : null,
            $materi->deskripsi ? 'Deskripsi: ' . $materi->deskripsi : null,
        ])));

        if ($fallbackContext === '') {
            throw new GeminiCoverException('Materi belum punya konten yang cukup untuk dibuatkan kuis.');
        }

        return [[
            'text' => $prompt . "\n\nRingkasan materi:\n" . $fallbackContext,
        ]];
    }

    private function buildPrompt(Materi $materi, int $jumlahSoal, string $kesulitan, string $jenisSoal, ?MateriBab $bab = null): string
    {
        $mataPelajaran = trim((string) optional($materi->mataPelajaran)->nama);
        $level = trim((string) optional($materi->level)->nama);

        $typeInstruction = match ($jenisSoal) {
            'essay' => implode("\n", [
                'Buat draft kuis essay berdasarkan materi pembelajaran yang diberikan.',
                'Setiap soal wajib memiliki `jawaban_teks` sebagai jawaban contoh.',
                'Setiap soal wajib memiliki `keyword` berisi kata kunci penilaian, dipisahkan koma dalam satu string.',
                'Format JSON wajib:',
                '{"judul":"string","deskripsi":"string","pertanyaan":[{"teks":"string","jawaban_teks":"string","keyword":"kata1, kata2, kata3"}]}',
            ]),
            default => implode("\n", [
                'Buat draft kuis pilihan ganda berdasarkan materi pembelajaran yang diberikan.',
                'Setiap soal harus punya 4 opsi: A, B, C, D.',
                'Hanya boleh ada 1 jawaban benar per soal.',
                'Format JSON wajib:',
                '{"judul":"string","deskripsi":"string","pertanyaan":[{"teks":"string","opsi":{"A":"string","B":"string","C":"string","D":"string"},"benar":"A"}]}',
            ]),
        };

        return implode("\n", array_filter([
            $typeInstruction,
            "Jumlah soal: {$jumlahSoal}.",
            "Tingkat kesulitan: {$kesulitan}.",
            $bab ? "Fokuskan kuis hanya pada bab ini: {$bab->judul_bab}." : null,
            $mataPelajaran !== '' ? "Mata pelajaran: {$mataPelajaran}." : null,
            $level !== '' ? "Level siswa: {$level}." : null,
            'Gunakan bahasa Indonesia yang jelas dan sesuai untuk siswa.',
            'Soal harus setia pada isi materi dan jangan menambahkan fakta di luar materi.',
            'Kembalikan JSON murni tanpa markdown, tanpa penjelasan tambahan.',
        ]));
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
            return "Kuota Gemini untuk generate kuis sedang habis atau belum aktif pada project ini. "
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
            return 'Layanan Gemini sedang bermasalah saat membuat draft kuis. Coba beberapa saat lagi.';
        }

        return $rawMessage;
    }

    private function normalizeHttpStatus(int $status): int
    {
        return $status >= 400 && $status <= 599 ? $status : 422;
    }
}
