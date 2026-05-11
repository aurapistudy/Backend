<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use App\Services\Concerns\BuildsBabSummaryFromDecodedJson;
use Illuminate\Support\Facades\Http;

/**
 * Rangkuman teks via Inference API Hugging Face (kuota terpisah dari Gemini).
 * Catatan: bab berisi file PDF/DOC saja belum didukung — pakai konten teks atau provider gemini.
 */
class HuggingFaceBabSummaryService
{
    use BuildsBabSummaryFromDecodedJson;

    public function generateSummary(Materi $materi, MateriBab $bab): array
    {
        $apiToken = (string) config('services.huggingface.api_token');
        $model = (string) config('services.huggingface.summary_text_model', 'Qwen/Qwen2.5-3B-Instruct');
        $baseUrl = rtrim((string) config('services.huggingface.base_url', 'https://router.huggingface.co/hf-inference/models'), '/');

        if ($apiToken === '') {
            throw new GeminiCoverException('HF_API_TOKEN belum dikonfigurasi.');
        }

        $fullPrompt = $this->buildFullPrompt($materi, $bab);

        $response = Http::timeout(180)
            ->withToken($apiToken)
            ->acceptJson()
            ->post("{$baseUrl}/{$model}", [
                'inputs' => $fullPrompt,
                'parameters' => [
                    'max_new_tokens' => (int) config('services.huggingface.summary_text_max_new_tokens', 900),
                    'return_full_text' => false,
                    'temperature' => 0.45,
                    'top_p' => 0.9,
                ],
            ]);

        if ($response->failed()) {
            $rawMessage = $response->json('error')
                ?: $response->json('message')
                ?: $response->body()
                ?: 'Permintaan rangkuman teks ke Hugging Face gagal.';

            throw new GeminiCoverException(
                $this->buildFriendlyErrorMessage($response->status(), (string) $rawMessage, $model),
                $this->normalizeHttpStatus($response->status())
            );
        }

        $json = $response->json();
        $text = $this->extractGeneratedText($json);
        if ($text === '') {
            $raw = trim($response->body());
            if ($raw !== '' && !str_starts_with($raw, '{')) {
                $text = $raw;
            }
        }
        if ($text === '') {
            throw new GeminiCoverException('Hugging Face tidak mengembalikan teks rangkuman.');
        }

        $decoded = $this->decodeJsonFromModelText($text);
        if (!is_array($decoded)) {
            throw new GeminiCoverException('Format JSON rangkuman dari Hugging Face tidak valid. Coba generate ulang atau ganti HF_SUMMARY_TEXT_MODEL.');
        }

        return $this->mapDecodedSummaryToPayload($decoded, $bab);
    }

    private function buildFullPrompt(Materi $materi, MateriBab $bab): string
    {
        $instructions = $this->buildPromptInstructions($materi, $bab);
        $trimmedText = trim((string) $bab->konten_teks);

        if ($trimmedText !== '') {
            return $instructions . "\n\nIsi bab:\n" . mb_substr($trimmedText, 0, 12000);
        }

        if ($bab->file_path) {
            throw new GeminiCoverException(
                'Provider Hugging Face untuk rangkuman teks hanya mendukung bab berisi teks. '
                . 'Untuk bab ber-file PDF/DOC, isi konten teks di editor atau set SUMMARY_TEXT_PROVIDER=gemini di .env.'
            );
        }

        $fallbackContext = trim(implode("\n", array_filter([
            'Judul buku: ' . $materi->judul,
            'Judul bab: ' . $bab->judul_bab,
            $materi->deskripsi ? 'Deskripsi buku: ' . $materi->deskripsi : null,
        ])));

        if ($fallbackContext === '') {
            throw new GeminiCoverException('Bab ini belum punya konten yang cukup untuk dibuatkan rangkuman.');
        }

        return $instructions . "\n\nKonteks bab:\n" . $fallbackContext;
    }

    private function extractGeneratedText(mixed $json): string
    {
        if (is_string($json)) {
            return trim($json);
        }

        if (!is_array($json)) {
            return '';
        }

        if (isset($json[0]) && is_array($json[0]) && array_key_exists('generated_text', $json[0])) {
            return trim((string) $json[0]['generated_text']);
        }

        if (array_key_exists('generated_text', $json)) {
            return trim((string) $json['generated_text']);
        }

        if (isset($json[0]) && is_string($json[0])) {
            return trim($json[0]);
        }

        return '';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonFromModelText(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/', '', $text) ?? $text;
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function buildFriendlyErrorMessage(int $status, string $rawMessage, string $model): string
    {
        $normalized = strtolower($rawMessage);

        if (
            $status === 402
            || $status === 429
            || str_contains($normalized, 'quota')
            || str_contains($normalized, 'rate limit')
            || str_contains($normalized, 'too many requests')
        ) {
            return "Kuota atau batas rate Hugging Face untuk rangkuman teks sedang habis. "
                . "Cek Usage di huggingface.co atau tunggu sebentar. Model: {$model}.";
        }

        if ($status === 401 || $status === 403) {
            return 'Akses Hugging Face ditolak. Pastikan HF_API_TOKEN valid dan punya akses inference.';
        }

        if ($status === 404 || str_contains($normalized, 'not found')) {
            return "Model teks {$model} tidak ditemukan di inference. Cek HF_SUMMARY_TEXT_MODEL di .env.";
        }

        if ($status >= 500 || str_contains($normalized, 'loading')) {
            return "Model Hugging Face {$model} sedang dimuat atau server sibuk. Coba lagi dalam 1–2 menit.";
        }

        return $rawMessage;
    }

    private function normalizeHttpStatus(int $status): int
    {
        return $status >= 400 && $status <= 599 ? $status : 422;
    }
}
