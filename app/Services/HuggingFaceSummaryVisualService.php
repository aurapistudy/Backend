<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use Illuminate\Support\Facades\Http;

class HuggingFaceSummaryVisualService
{
    public function generateSummaryPoster(Materi $materi, MateriBab $bab, array $summary): array
    {
        $background = $this->generateBackgroundImage($materi, $bab, $summary);
        $svg = $this->buildPosterSvg($materi, $bab, $summary, $background);

        return [
            'binary' => $svg,
            'mime_type' => 'image/svg+xml',
            'extension' => 'svg',
        ];
    }

    private function generateBackgroundImage(Materi $materi, MateriBab $bab, array $summary): array
    {
        $apiToken = (string) config('services.huggingface.api_token');
        $model = (string) config('services.huggingface.image_model', 'black-forest-labs/FLUX.1-schnell');
        $baseUrl = rtrim((string) config('services.huggingface.base_url', 'https://router.huggingface.co/hf-inference/models'), '/');

        if ($apiToken === '') {
            throw new GeminiCoverException('HF_API_TOKEN belum dikonfigurasi.');
        }

        $response = Http::timeout(180)
            ->withToken($apiToken)
            ->accept('image/png')
            ->post("{$baseUrl}/{$model}", [
                'inputs' => $this->buildBackgroundPrompt($materi, $bab, $summary),
                'parameters' => [
                    'num_inference_steps' => 4,
                    'guidance_scale' => 3.5,
                ],
            ]);

        if ($response->failed()) {
            $rawMessage = $response->json('error')
                ?: $response->json('message')
                ?: $response->body()
                ?: 'Permintaan ke Hugging Face gagal.';

            throw new GeminiCoverException(
                $this->buildFriendlyErrorMessage($response->status(), (string) $rawMessage, $model),
                $this->normalizeHttpStatus($response->status())
            );
        }

        $mimeType = (string) $response->header('Content-Type', 'image/png');
        $binary = $response->body();

        if ($binary === '' || !str_starts_with(strtolower($mimeType), 'image/')) {
            throw new GeminiCoverException('Hugging Face tidak mengembalikan background poster rangkuman.');
        }

        return [
            'mime_type' => strtok($mimeType, ';') ?: 'image/png',
            'base64' => base64_encode($binary),
        ];
    }

    private function buildBackgroundPrompt(Materi $materi, MateriBab $bab, array $summary): string
    {
        $keywords = implode(', ', array_slice($summary['summary_keywords'] ?? [], 0, 4));
        $topicHints = implode(', ', array_filter([
            trim((string) ($summary['summary_title'] ?? '')),
            trim((string) ($summary['summary_short'] ?? '')),
            trim((string) $bab->judul_bab),
            $keywords,
        ]));

        return implode("\n", array_filter([
            'Create a clean educational illustration for a student summary poster.',
            'The image must show only objects directly related to the lesson topic.',
            'Style: polished, modern, friendly, simple composition, soft lighting, classroom-appropriate.',
            'Do not include any letters, words, numbers, watermark, logo, paragraph, label, banner, signboard, or typography.',
            'Absolutely avoid unrelated objects, world maps, flags, fish, travel themes, random icons, and generic stock-poster elements unless the topic explicitly requires them.',
            'Leave calm composition with one main scene that matches the topic exactly.',
            "Book title context: {$materi->judul}.",
            "Chapter title context: {$bab->judul_bab}.",
            $topicHints !== '' ? "Illustrate these concepts only: {$topicHints}." : null,
            $keywords !== '' ? "Theme keywords: {$keywords}." : null,
        ]));
    }

    private function buildPosterSvg(Materi $materi, MateriBab $bab, array $summary, array $background): string
    {
        $title = $this->escapeXml((string) ($summary['summary_title'] ?? 'Rangkuman Materi'));
        $short = (string) ($summary['summary_short'] ?? '');
        $memoryTip = (string) ($summary['summary_memory_tip'] ?? '');
        $example = (string) ($summary['summary_example'] ?? '');
        $chapter = $this->escapeXml($bab->judul_bab);
        $subject = $this->escapeXml((string) optional($materi->mataPelajaran)->nama);

        $pointBlocks = $this->buildPointBlocks($summary['summary_key_points'] ?? []);
        $keywordBlocks = $this->buildKeywordBlocks($summary['summary_keywords'] ?? []);

        $fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        $subjectLine = $subject !== '' ? "<text x=\"116\" y=\"132\" font-size=\"24\" fill=\"#64748B\" font-family=\"{$fontFamily}\" font-weight=\"700\">{$subject}</text>" : '';
        $exampleBlock = $example !== ''
            ? "<rect x=\"86\" y=\"1116\" width=\"908\" height=\"92\" rx=\"24\" fill=\"#FFF7ED\" stroke=\"#FDBA74\" />"
                . "<text x=\"118\" y=\"1172\" font-size=\"28\" font-weight=\"800\" fill=\"#9A3412\" font-family=\"{$fontFamily}\">Contoh: {$this->escapeXml($example)}</text>"
            : '';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1080" height="1350" viewBox="0 0 1080 1350" role="img" aria-label="Poster rangkuman AI {$title}">
  <defs>
    <linearGradient id="pageBg" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#FFFDF7" />
      <stop offset="100%" stop-color="#F8FAFC" />
    </linearGradient>
    <linearGradient id="heroShade" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="rgba(15,23,42,0.48)" />
      <stop offset="100%" stop-color="rgba(15,23,42,0.18)" />
    </linearGradient>
    <linearGradient id="summaryBand" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0F172A" />
      <stop offset="100%" stop-color="#1E293B" />
    </linearGradient>
  </defs>
  <rect x="0" y="0" width="1080" height="1350" fill="url(#pageBg)" />
  <rect x="54" y="52" width="972" height="1246" rx="40" fill="#FFFFFF" stroke="#E5E7EB" />
  <text x="116" y="96" font-size="24" font-weight="800" letter-spacing="2.6" fill="#D97706" font-family="{$fontFamily}">POSTER RANGKUMAN AI</text>
  {$subjectLine}
  {$this->buildFittedText($title, 116, 182, 58, 42, '#0F172A', 850, 2, 800)}
  <text x="116" y="230" font-size="22" fill="#64748B" font-family="{$fontFamily}" font-weight="700">Bab: {$chapter}</text>
  <rect x="86" y="266" width="908" height="252" rx="34" fill="#E2E8F0" />
  <image href="data:{$background['mime_type']};base64,{$background['base64']}" x="86" y="266" width="908" height="252" preserveAspectRatio="xMidYMid slice" />
  <rect x="86" y="266" width="908" height="252" rx="34" fill="url(#heroShade)" />
  <rect x="86" y="446" width="908" height="96" rx="28" fill="url(#summaryBand)" />
  <text x="118" y="486" font-size="22" font-weight="800" fill="#FCD34D" font-family="{$fontFamily}">Gambaran Cepat</text>
  {$this->buildFittedText($short, 118, 520, 32, 24, '#FFFFFF', 820, 2, 700)}
  <rect x="86" y="574" width="548" height="506" rx="32" fill="#FFF7E8" stroke="#FCD34D" />
  <text x="124" y="626" font-size="28" font-weight="800" fill="#334155" font-family="{$fontFamily}">3 Poin Utama</text>
  {$pointBlocks}
  <rect x="666" y="574" width="328" height="238" rx="32" fill="#FFFFFF" stroke="#E2E8F0" />
  <text x="704" y="626" font-size="28" font-weight="800" fill="#334155" font-family="{$fontFamily}">Kata Kunci</text>
  {$keywordBlocks}
  <rect x="666" y="842" width="328" height="238" rx="32" fill="#E0F2FE" stroke="#7DD3FC" />
  <text x="704" y="894" font-size="28" font-weight="800" fill="#0F3A67" font-family="{$fontFamily}">Ingat Ini</text>
  {$this->buildFittedText($memoryTip !== '' ? $memoryTip : 'Belum ada tips mengingat.', 704, 938, 28, 22, '#1F2937', 248, 3, 700)}
  {$exampleBlock}
</svg>
SVG;
    }

    private function buildPointBlocks(array $points): string
    {
        $blocks = '';
        $fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        $y = 666;
        $index = 1;

        foreach (array_slice($points, 0, 3) as $point) {
            $blocks .= "<rect x=\"124\" y=\"{$y}\" width=\"472\" height=\"108\" rx=\"24\" fill=\"#FFFFFF\" stroke=\"#E5E7EB\" />";
            $blocks .= "<rect x=\"150\" y=\"" . ($y + 22) . "\" width=\"60\" height=\"60\" rx=\"20\" fill=\"#F8B803\" />";
            $blocks .= "<text x=\"180\" y=\"" . ($y + 60) . "\" text-anchor=\"middle\" font-size=\"26\" font-weight=\"800\" fill=\"#111827\" font-family=\"{$fontFamily}\">{$index}</text>";
            $blocks .= $this->buildFittedText((string) $point, 236, $y + 46, 27, 22, '#111827', 320, 2, 700);
            $y += 126;
            $index++;
        }

        return $blocks;
    }

    private function buildKeywordBlocks(array $keywords): string
    {
        $blocks = '';
        $fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        $x = 694;
        $y = 668;
        $perRow = 2;

        foreach (array_slice(array_values($keywords), 0, 4) as $index => $keyword) {
            $safe = $this->escapeXml((string) $keyword);
            $currentX = $x + (($index % $perRow) * 142);
            $currentY = $y + (int) floor($index / $perRow) * 78;

            $blocks .= "<rect x=\"{$currentX}\" y=\"{$currentY}\" width=\"126\" height=\"48\" rx=\"24\" fill=\"#FEF3C7\" stroke=\"#FCD34D\" />";
            $blocks .= "<text x=\"" . ($currentX + 63) . "\" y=\"" . ($currentY + 31) . "\" text-anchor=\"middle\" font-size=\"18\" font-weight=\"800\" fill=\"#92400E\" font-family=\"{$fontFamily}\">{$safe}</text>";
        }

        return $blocks;
    }

    private function buildFittedText(
        string $text,
        int $x,
        int $y,
        int $maxFontSize,
        int $minFontSize,
        string $color,
        int $maxWidth,
        int $maxLines,
        int $fontWeight = 600
    ): string {
        $fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        $fontSize = $maxFontSize;
        $lines = [];

        while ($fontSize >= $minFontSize) {
            $lines = $this->wrapText($text, $maxWidth, $fontSize, $maxLines);
            if (count($lines) <= $maxLines && !$this->hasOverflownLine($lines, $maxWidth, $fontSize)) {
                break;
            }
            $fontSize -= 2;
        }

        if ($fontSize < $minFontSize) {
            $fontSize = $minFontSize;
            $lines = $this->wrapText($text, $maxWidth, $fontSize, $maxLines);
        }

        $lineHeight = (int) round($fontSize * 1.35);
        $result = '';

        foreach ($lines as $lineIndex => $line) {
            $lineY = $y + ($lineIndex * $lineHeight);
            $safeLine = $this->escapeXml($line);
            $result .= "<text x=\"{$x}\" y=\"{$lineY}\" font-size=\"{$fontSize}\" font-weight=\"{$fontWeight}\" fill=\"{$color}\" font-family=\"{$fontFamily}\">{$safeLine}</text>";
        }

        return $result;
    }

    private function wrapText(string $text, int $maxWidth, int $fontSize, int $maxLines): array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($clean === '') {
            return [''];
        }

        $maxChars = max(10, (int) floor($maxWidth / max(10, $fontSize * 0.52)));
        $words = preg_split('/\s+/', $clean) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }
            $current = $word;

            if (count($lines) >= $maxLines) {
                return $lines;
            }
        }

        if ($current !== '' && count($lines) < $maxLines) {
            $lines[] = $current;
        }

        return $lines;
    }

    private function hasOverflownLine(array $lines, int $maxWidth, int $fontSize): bool
    {
        $maxChars = max(10, (int) floor($maxWidth / max(10, $fontSize * 0.52)));

        foreach ($lines as $line) {
            if (mb_strlen($line) > $maxChars) {
                return true;
            }
        }

        return false;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function buildFriendlyErrorMessage(int $status, string $rawMessage, string $model): string
    {
        $normalized = strtolower($rawMessage);

        if (
            $status === 402
            || $status === 429
            || str_contains($normalized, 'quota')
            || str_contains($normalized, 'rate limit')
            || str_contains($normalized, 'payment')
            || str_contains($normalized, 'credit')
        ) {
            return "Kuota Hugging Face untuk generate poster rangkuman sedang habis atau belum aktif. Model yang dipakai: {$model}.";
        }

        if ($status === 401 || $status === 403 || str_contains($normalized, 'unauthorized') || str_contains($normalized, 'forbidden')) {
            return 'Akses ke Hugging Face ditolak. Pastikan HF_API_TOKEN valid dan token memiliki izin inference.';
        }

        if ($status === 404 || str_contains($normalized, 'not found')) {
            return "Model Hugging Face {$model} tidak ditemukan. Cek nilai HF_IMAGE_MODEL di file .env.";
        }

        if ($status >= 500) {
            return 'Layanan Hugging Face sedang bermasalah saat membuat poster rangkuman. Coba beberapa saat lagi.';
        }

        return $rawMessage;
    }

    private function normalizeHttpStatus(int $status): int
    {
        return $status >= 400 && $status <= 599 ? $status : 422;
    }
}
