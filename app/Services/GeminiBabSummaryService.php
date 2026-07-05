<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use App\Services\Concerns\BuildsBabSummaryFromDecodedJson;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GeminiBabSummaryService
{
    use BuildsBabSummaryFromDecodedJson;

    public function generateSummary(Materi $materi, MateriBab $bab): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.text_model', 'gemini-2.5-flash-lite');

        if ($apiKey === '') {
            throw new GeminiCoverException('GEMINI_API_KEY belum dikonfigurasi.');
        }

        $prompt = $this->buildPromptInstructions($materi, $bab);
        $contents = [[
            'parts' => $this->buildParts($materi, $bab, $prompt),
        ]];

        $response = Http::timeout(120)
            ->retry(3, 2000, function ($exception, $request) {
                // Cuma retry kalau error 429 (rate limit) atau 503 (server overload/sibuk).
                // Selain itu (401/403/404/dll) langsung gagal karena itu masalah konfigurasi, bukan transient.
                if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                    return in_array($exception->response->status(), [429, 503], true);
                }
                return false;
            })
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => $contents,
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.65,
                ],
            ]);

        if ($response->failed()) {
            $rawMessage = $response->json('error.message')
                ?: $response->json('message')
                ?: 'Permintaan ke Gemini gagal.';

            Log::warning('Gemini generateSummary gagal.', [
                'status' => $response->status(),
                'model' => $model,
                'body' => mb_substr($response->body(), 0, 1000),
            ]);

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

        try {
            return $this->mapDecodedSummaryToPayload($decoded, $bab);
        } catch (GeminiCoverException $e) {
            throw new GeminiCoverException(
                str_replace('Rangkuman bab belum', 'Rangkuman bab dari Gemini belum', $e->getMessage()),
                $e->status()
            );
        }
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
                . "Model yang sedang dipakai: {$model}. "
                . 'Alternatif: jika bab berisi teks (bukan hanya file PDF), set SUMMARY_TEXT_PROVIDER=huggingface di .env dan pastikan HF_API_TOKEN aktif.';
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
