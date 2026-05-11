<?php

namespace App\Services;

use App\Exceptions\GeminiCoverException;
use App\Models\Materi;
use App\Models\MateriBab;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingFaceSummaryVisualService
{
    public function generateSummaryPoster(Materi $materi, MateriBab $bab, array $summary): array
    {
        $background = $this->generateBackgroundImage($materi, $bab, $summary);
        $svg = $this->buildPosterSvg($materi, $bab, $summary, $background);
        $png = $this->rasterizeSvgToPng($svg);

        return [
            'binary' => $png,
            'mime_type' => 'image/png',
            'extension' => 'png',
        ];
    }

    private function generateBackgroundImage(Materi $materi, MateriBab $bab, array $summary): array
    {
        $apiToken = (string) config('services.huggingface.api_token');
        $fallbackModel = (string) config('services.huggingface.image_model', 'black-forest-labs/FLUX.1-schnell');
        $model = trim((string) (config('services.huggingface.summary_poster_model') ?: $fallbackModel));
        $baseUrl = rtrim((string) config('services.huggingface.base_url', 'https://router.huggingface.co/hf-inference/models'), '/');

        if ($apiToken === '') {
            throw new GeminiCoverException('HF_API_TOKEN belum dikonfigurasi.');
        }

        $stepsRaw = config('services.huggingface.summary_poster_inference_steps');
        $modelLower = strtolower($model);
        if ($stepsRaw === null || $stepsRaw === '') {
            $numInferenceSteps = str_contains($modelLower, 'schnell') ? 4 : 22;
        } else {
            $numInferenceSteps = max(1, (int) $stepsRaw);
        }

        $guidanceRaw = config('services.huggingface.summary_poster_guidance_scale');
        $guidanceScale = ($guidanceRaw === null || $guidanceRaw === '')
            ? 3.5
            : (float) $guidanceRaw;

        $response = Http::timeout(180)
            ->withToken($apiToken)
            ->accept('image/png')
            ->post("{$baseUrl}/{$model}", [
                'inputs' => $this->buildBackgroundPrompt($materi, $bab, $summary),
                'parameters' => [
                    'num_inference_steps' => $numInferenceSteps,
                    'guidance_scale' => $guidanceScale,
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
        // Jangan kirim judul/ringkasan teks ke model gambar — sering memicu kata ditulis di dalam gambar.
        $keywords = implode(', ', array_slice($summary['summary_keywords'] ?? [], 0, 6));
        $topicHints = implode(', ', array_filter([
            trim((string) $bab->judul_bab),
            $keywords !== '' ? $keywords : null,
        ]));

        return implode("\n", array_filter([
            'Pure illustration only: draw objects and scenes, zero readable characters.',
            'Create a clean educational illustration background for a summary poster.',
            'The scene must reflect the lesson topic using visuals only (people, objects, nature, symbols without letters).',
            'Style: polished, modern, friendly, simple composition, soft lighting, classroom-appropriate, sharp focus, cohesive palette, not cluttered.',
            'ABSOLUTELY NO text of any kind: no letters, words, numbers, captions, headlines, titles on image, subtitles, logos, watermarks, signs with writing, book covers with title, magazine layout, infographic labels, speech bubbles with text, keyboard keys with letters, UI mockups with text.',
            'Do not spell topic names or keywords as typography inside the image — express the idea only through drawings.',
            'Never show world maps, globes, or national flags unless the lesson is explicitly about geography, geopolitics, or state symbols.',
            'For science, energy, or technology lessons, show concrete objects and scenes; no maps or flags unless required.',
            'Absolutely avoid unrelated clutter: fish, vacation posters, random icons, unless the topic truly needs them.',
            'Leave calm composition with one main scene matching the topic.',
            "Course context (do not paint this as text): {$materi->judul}.",
            "Chapter theme (visual meaning only): {$bab->judul_bab}.",
            $topicHints !== '' ? "Visual concepts to depict without words: {$topicHints}." : null,
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

        $fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        $subjectLine = $subject !== '' ? "<text x=\"116\" y=\"132\" font-size=\"24\" fill=\"#64748B\" font-family=\"{$fontFamily}\" font-weight=\"700\">{$subject}</text>" : '';

        $heroTop = 266;
        $heroH = 252;
        $bandTop = $heroTop + $heroH + 8;

        $shortFit = $this->fitTextLines($short, 820, 30, 16, 700);
        $shortLh = $this->lineHeightPx($shortFit['fontSize']);
        $shortTextH = max(1, count($shortFit['lines'])) * $shortLh;
        $bandLabelY = $bandTop + 26;
        $shortBaseline = $bandTop + 54;
        $bandH = (int) max(96, ($shortBaseline - $bandTop) + $shortTextH + 26);

        $panelTop = (int) round($bandTop + $bandH + 10);
        $leftPanelX = 86;
        $leftPanelW = 548;
        $rightPanelX = 666;

        $points = array_slice($summary['summary_key_points'] ?? [], 0, 3);
        $pointsLayout = $this->layoutPointBlocks($points, $fontFamily, $panelTop);

        $kwLayout = $this->layoutKeywordBlocks($summary['summary_keywords'] ?? [], $panelTop, $fontFamily);

        $memoryBody = $memoryTip !== '' ? $memoryTip : 'Belum ada tips mengingat.';
        $memoryFit = $this->fitTextLines($memoryBody, 248, 26, 14, 700);
        $memoryLh = $this->lineHeightPx($memoryFit['fontSize']);
        $memoryTextH = max(1, count($memoryFit['lines'])) * $memoryLh;
        $kwEnd = $panelTop + $kwLayout['blockHeight'];
        $pointsBottom = $pointsLayout['contentBottomY'];
        // Kotak tips langsung di bawah kata kunci (hindari "jembatan" kosong saat poin kiri tinggi).
        $memoryTop = (int) round($kwEnd + 8);
        $memoryTitleY = $memoryTop + 32;
        $memoryTextY = $memoryTop + 58;
        $memoryBoxH = (int) max(100, ($memoryTextY - $memoryTop) + $memoryTextH + 22);
        $memoryBottom = $memoryTop + $memoryBoxH;
        $sectionBottom = max($memoryBottom, $pointsBottom + 12);
        $leftPanelH = (int) max(
            $pointsLayout['leftPanelHeight'],
            $sectionBottom - $panelTop + 16
        );

        $shortSvg = $this->emitTextLines($shortFit['lines'], $shortFit['fontSize'], 118, $shortBaseline, '#FFFFFF', 700, $fontFamily);
        $memorySvg = $this->emitTextLines($memoryFit['lines'], $memoryFit['fontSize'], 704, $memoryTextY, '#1F2937', 700, $fontFamily);

        $exampleBlock = '';
        $svgHeight = (int) round($sectionBottom + 28);
        if ($example !== '') {
            $exFit = $this->fitTextLines('Contoh: ' . $example, 820, 24, 14, 700);
            $exLh = $this->lineHeightPx($exFit['fontSize']);
            $exTextH = max(1, count($exFit['lines'])) * $exLh;
            $exampleTop = (int) round($sectionBottom + 12);
            $exampleH = (int) round(24 + $exTextH + 28);
            $exampleBlock = "<rect x=\"86\" y=\"{$exampleTop}\" width=\"908\" height=\"{$exampleH}\" rx=\"24\" fill=\"#FFF7ED\" stroke=\"#FDBA74\" />"
                . $this->emitTextLines($exFit['lines'], $exFit['fontSize'], 118, (int) round($exampleTop + 26), '#7C2D12', 700, $fontFamily);
            $svgHeight = (int) round($exampleTop + $exampleH + 36);
        }

        $svgHeight = max(1350, $svgHeight);
        $cardH = $svgHeight - 104;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1080" height="{$svgHeight}" viewBox="0 0 1080 {$svgHeight}" role="img" aria-label="Poster rangkuman bab: {$title}">
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
  <rect x="0" y="0" width="1080" height="{$svgHeight}" fill="url(#pageBg)" />
  <rect x="54" y="52" width="972" height="{$cardH}" rx="40" fill="#FFFFFF" stroke="#E5E7EB" />
  <text x="116" y="96" font-size="22" font-weight="800" letter-spacing="1.8" fill="#D97706" font-family="{$fontFamily}">RANGKUMAN VISUAL BAB</text>
  {$subjectLine}
  {$this->buildFittedText($title, 116, 182, 56, 30, '#0F172A', 850, 6, 800)}
  <text x="116" y="230" font-size="22" fill="#64748B" font-family="{$fontFamily}" font-weight="700">Bab: {$chapter}</text>
  <rect x="86" y="{$heroTop}" width="908" height="{$heroH}" rx="34" fill="#E2E8F0" />
  <image href="data:{$background['mime_type']};base64,{$background['base64']}" x="86" y="{$heroTop}" width="908" height="{$heroH}" preserveAspectRatio="xMidYMid slice" />
  <rect x="86" y="{$heroTop}" width="908" height="{$heroH}" rx="34" fill="url(#heroShade)" />
  <rect x="86" y="{$bandTop}" width="908" height="{$bandH}" rx="28" fill="url(#summaryBand)" />
  <text x="118" y="{$bandLabelY}" font-size="22" font-weight="800" fill="#FCD34D" font-family="{$fontFamily}">Gambaran Cepat</text>
  {$shortSvg}
  <rect x="{$leftPanelX}" y="{$panelTop}" width="{$leftPanelW}" height="{$leftPanelH}" rx="32" fill="#FFF7E8" stroke="#FCD34D" />
  <text x="124" y="{$panelTop}" dy="52" font-size="28" font-weight="800" fill="#334155" font-family="{$fontFamily}">3 Poin Utama</text>
  {$pointsLayout['svg']}
  <rect x="{$rightPanelX}" y="{$panelTop}" width="328" height="{$kwLayout['blockHeight']}" rx="32" fill="#FFFFFF" stroke="#E2E8F0" />
  <text x="704" y="{$panelTop}" dy="52" font-size="28" font-weight="800" fill="#334155" font-family="{$fontFamily}">Kata Kunci</text>
  {$kwLayout['svg']}
  <rect x="{$rightPanelX}" y="{$memoryTop}" width="328" height="{$memoryBoxH}" rx="32" fill="#E0F2FE" stroke="#7DD3FC" />
  <text x="704" y="{$memoryTitleY}" font-size="22" font-weight="800" fill="#0F3A67" font-family="{$fontFamily}">Tips mengingat</text>
  {$memorySvg}
  {$exampleBlock}
</svg>
SVG;
    }

    private function rasterizeSvgToPng(string $svg): string
    {
        $rsvgResult = $this->convertSvgWithRsvg($svg);
        if ($rsvgResult !== null) {
            return $rsvgResult;
        }

        $imagickResult = $this->convertSvgWithImagick($svg);
        if ($imagickResult !== null) {
            return $imagickResult;
        }

        throw new GeminiCoverException(
            'Konversi poster rangkuman ke PNG gagal. Pastikan rsvg-convert (librsvg2-bin) atau ekstensi Imagick dengan dukungan SVG terpasang di server.'
        );
    }

    private function convertSvgWithRsvg(string $svg): ?string
    {
        if (!function_exists('proc_open')) {
            return null;
        }

        $binary = $this->locateRsvgConvert();
        if ($binary === null) {
            return null;
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open(
            [$binary, '--format=png', '--dpi-x=144', '--dpi-y=144'],
            $descriptors,
            $pipes
        );

        if (!is_resource($process)) {
            Log::warning('rsvg-convert gagal di-spawn untuk poster rangkuman.');

            return null;
        }

        fwrite($pipes[0], $svg);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0 || !is_string($output) || $output === '') {
            Log::warning('rsvg-convert mengembalikan output kosong / non-zero exit.', [
                'exit_code' => $exitCode,
                'stderr' => is_string($error) ? mb_substr($error, 0, 500) : null,
            ]);

            return null;
        }

        return $output;
    }

    private function locateRsvgConvert(): ?string
    {
        $candidates = ['rsvg-convert'];
        $isWindows = stripos(PHP_OS_FAMILY, 'Win') === 0;
        if ($isWindows) {
            $candidates = ['rsvg-convert.exe', 'rsvg-convert'];
        }

        foreach ($candidates as $candidate) {
            $cmd = $isWindows ? "where {$candidate}" : "command -v {$candidate}";
            $path = @shell_exec($cmd . ' 2>/dev/null');
            if (is_string($path)) {
                $path = trim(strtok($path, "\n") ?: '');
                if ($path !== '' && @is_executable($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    private function convertSvgWithImagick(string $svg): ?string
    {
        if (!class_exists(\Imagick::class) || !class_exists(\ImagickPixel::class)) {
            Log::warning('Imagick tidak tersedia untuk konversi poster rangkuman.');

            return null;
        }

        try {
            $imagick = new \Imagick();
            $imagick->setBackgroundColor(new \ImagickPixel('white'));
            $imagick->setResolution(144, 144);
            $imagick->readImageBlob($svg);
            $imagick->setImageFormat('png');

            $png = $imagick->getImagesBlob();
            $imagick->clear();
            $imagick->destroy();

            if (!is_string($png) || $png === '') {
                return null;
            }

            return $png;
        } catch (\Throwable $exception) {
            Log::warning('Imagick gagal merasterisasi SVG poster rangkuman.', [
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array{svg: string, leftPanelHeight: int, contentBottomY: int}
     */
    private function layoutPointBlocks(array $points, string $fontFamily, int $panelTop): array
    {
        $svg = '';
        $y = $panelTop + 72;
        $index = 1;
        $gap = 8;
        $contentBottomY = $panelTop + 80;

        foreach ($points as $point) {
            $fit = $this->fitTextLines((string) $point, 328, 25, 14, 600);
            $lh = $this->lineHeightPx($fit['fontSize']);
            $textH = max(1, count($fit['lines'])) * $lh;
            $cardH = (int) max(76, 18 + $textH + 20);
            $badgeCy = $y + (int) round($cardH / 2);

            $svg .= "<rect x=\"124\" y=\"{$y}\" width=\"472\" height=\"{$cardH}\" rx=\"24\" fill=\"#FFFFFF\" stroke=\"#E5E7EB\" />";
            $svg .= '<rect x="134" y="' . ($badgeCy - 26) . '" width="52" height="52" rx="16" fill="#F8B803" />';
            $svg .= "<text x=\"160\" y=\"" . ($badgeCy + 8) . "\" text-anchor=\"middle\" font-size=\"22\" font-weight=\"800\" fill=\"#111827\" font-family=\"{$fontFamily}\">{$index}</text>";
            $textStartY = $y + (int) round(20 + ($cardH - 20 - $textH) / 2);
            $svg .= $this->emitTextLines($fit['lines'], $fit['fontSize'], 248, $textStartY, '#1E293B', 600, $fontFamily);

            $contentBottomY = $y + $cardH;
            $y += $cardH + $gap;
            $index++;
        }

        if ($svg === '') {
            $placeholder = $this->escapeXml('Belum ada poin utama.');
            $svg = "<text x=\"148\" y=\"" . ($panelTop + 120) . "\" font-size=\"22\" fill=\"#64748B\" font-family=\"{$fontFamily}\">{$placeholder}</text>";
            $contentBottomY = $panelTop + 150;
        }

        $leftPanelHeight = (int) round($contentBottomY - $panelTop + 16);

        return [
            'svg' => $svg,
            'leftPanelHeight' => max(160, $leftPanelHeight),
            'contentBottomY' => $contentBottomY,
        ];
    }

    /**
     * @return array{svg: string, blockHeight: int}
     */
    private function layoutKeywordBlocks(array $keywords, int $panelTop, string $fontFamily): array
    {
        $x = 694;
        $pillBaseY = $panelTop + 66;
        $kw = array_values(array_slice($keywords, 0, 4));
        $fits = [];
        $pillHs = [];

        foreach ($kw as $keyword) {
            $t = trim((string) $keyword);
            if ($t === '') {
                $fits[] = null;
                $pillHs[] = 0;

                continue;
            }
            $fit = $this->fitTextLines($t, 122, 15, 11, 700);
            $lh = $this->lineHeightPx($fit['fontSize']);
            $textH = max(1, count($fit['lines'])) * $lh;
            $pillHs[] = (int) max(36, 8 + $textH + 8);
            $fits[] = $fit;
        }

        while (count($pillHs) < 4) {
            $pillHs[] = 0;
            $fits[] = null;
        }

        $svg = '';
        $y = $pillBaseY;

        for ($row = 0; $row < 2; $row++) {
            $i0 = $row * 2;
            $i1 = $row * 2 + 1;
            $rowH = (int) max(40, $pillHs[$i0], $pillHs[$i1]);

            foreach ([0, 1] as $col) {
                $idx = $row * 2 + $col;
                if ($pillHs[$idx] === 0 || $fits[$idx] === null) {
                    continue;
                }
                $fit = $fits[$idx];
                $pillH = $pillHs[$idx];
                $currentX = $x + ($col * 148);
                $pillY = $y + (int) round(($rowH - $pillH) / 2);

                $svg .= "<rect x=\"{$currentX}\" y=\"{$pillY}\" width=\"138\" height=\"{$pillH}\" rx=\"22\" fill=\"#FEF3C7\" stroke=\"#FCD34D\" />";
                $lh = $this->lineHeightPx($fit['fontSize']);
                $textH = max(1, count($fit['lines'])) * $lh;
                $ty = $pillY + (int) round(($pillH - $textH) / 2 + $fit['fontSize'] * 0.82);
                $svg .= $this->emitTextLines($fit['lines'], $fit['fontSize'], $currentX + 69, $ty, '#92400E', 700, $fontFamily, true);
            }

            $y += $rowH + 6;
        }

        $blockHeight = (int) max(120, $y - $panelTop + 6);

        return [
            'svg' => $svg,
            'blockHeight' => $blockHeight,
        ];
    }

    /**
     * @return array{fontSize: int, lines: list<string>}
     */
    private function fitTextLines(string $text, int $maxWidth, int $maxFont, int $minFont, int $fontWeight): array
    {
        $fontSize = $maxFont;
        while ($fontSize >= $minFont) {
            $lines = $this->wrapText($text, $maxWidth, $fontSize, 0);
            if (!$this->hasOverflownLine($lines, $maxWidth, $fontSize)) {
                return ['fontSize' => $fontSize, 'lines' => $lines];
            }
            $fontSize -= 2;
        }

        $lines = $this->wrapText($text, $maxWidth, $minFont, 0);

        return ['fontSize' => $minFont, 'lines' => $lines];
    }

    private function lineHeightPx(int $fontSize): int
    {
        return (int) max(13, round($fontSize * 1.4));
    }

    /**
     * @param list<string> $lines
     */
    private function emitTextLines(
        array $lines,
        int $fontSize,
        int $x,
        int $yStart,
        string $color,
        int $fontWeight,
        string $fontFamily,
        bool $anchorMiddle = false
    ): string {
        $lh = $this->lineHeightPx($fontSize);
        $result = '';
        foreach ($lines as $i => $line) {
            $lineY = $yStart + ($i * $lh);
            $safe = $this->escapeXml($line);
            $anchor = $anchorMiddle ? ' text-anchor="middle"' : '';

            $result .= "<text x=\"{$x}\" y=\"{$lineY}\" font-size=\"{$fontSize}\" font-weight=\"{$fontWeight}\" fill=\"{$color}\" font-family=\"{$fontFamily}\"{$anchor}>{$safe}</text>";
        }

        return $result;
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

        $wrapLimit = $maxLines <= 0 ? 0 : $maxLines;

        while ($fontSize >= $minFontSize) {
            $lines = $this->wrapText($text, $maxWidth, $fontSize, $wrapLimit);
            $fitsWidth = !$this->hasOverflownLine($lines, $maxWidth, $fontSize);
            $fitsLines = $maxLines <= 0 || count($lines) <= $maxLines;
            if ($fitsWidth && $fitsLines) {
                break;
            }
            $fontSize -= 2;
        }

        if ($fontSize < $minFontSize) {
            $fontSize = $minFontSize;
            $lines = $this->wrapText($text, $maxWidth, $fontSize, $wrapLimit);
        }

        $lineHeight = (int) max(14, round($fontSize * 1.48));
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
        $allLines = [];
        $current = '';

        foreach ($words as $word) {
            foreach ($this->splitWordToFitWidth($word, $maxChars) as $chunk) {
                $candidate = $current === '' ? $chunk : $current . ' ' . $chunk;
                if (mb_strlen($candidate) <= $maxChars) {
                    $current = $candidate;
                    continue;
                }
                if ($current !== '') {
                    $allLines[] = $current;
                }
                $current = $chunk;
            }
        }

        if ($current !== '') {
            $allLines[] = $current;
        }

        if ($allLines === []) {
            return [''];
        }

        if ($maxLines <= 0 || count($allLines) <= $maxLines) {
            return $allLines;
        }

        $out = array_slice($allLines, 0, $maxLines);
        $budget = max(4, $maxChars - 3);
        $out[$maxLines - 1] = rtrim(mb_substr($out[$maxLines - 1], 0, $budget)) . '...';

        return $out;
    }

    /**
     * @return list<string>
     */
    private function splitWordToFitWidth(string $word, int $maxChars): array
    {
        if ($maxChars < 4) {
            return [$word];
        }
        if (mb_strlen($word) <= $maxChars) {
            return [$word];
        }

        $chunks = [];
        $len = mb_strlen($word);
        for ($i = 0; $i < $len; $i += $maxChars) {
            $chunks[] = mb_substr($word, $i, $maxChars);
        }

        return $chunks;
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
            return "Model Hugging Face {$model} tidak ditemukan. Cek HF_SUMMARY_POSTER_MODEL atau HF_IMAGE_MODEL di file .env.";
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
