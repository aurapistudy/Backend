<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoverGeneratorService
{
    /**
     * Generate cover image dari halaman pertama file PDF menggunakan pdftoppm.
     * Return path relatif (disk 'public'), atau null kalau gagal.
     */
    public function generateFromPdf(string $absolutePdfPath, string $baseFileName): ?string
    {
        if (!function_exists('proc_open')) {
            Log::warning('proc_open tidak tersedia, skip auto-generate cover.');
            return null;
        }

        $binary = $this->locatePdftoppm();
        if ($binary === null) {
            Log::warning('pdftoppm tidak ditemukan di PATH.');
            return null;
        }

        // pdftoppm butuh output ke FILE (bukan stdout), jadi kita render ke folder temp dulu
        $tempDir = storage_path('app/temp-covers');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        $tempPrefix = $tempDir . DIRECTORY_SEPARATOR . 'cover_' . Str::random(12);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open(
            [
                $binary,
                '-png',
                '-f', '1',
                '-l', '1',
                '-r', '150',
                '-singlefile',
                $absolutePdfPath,
                $tempPrefix,
            ],
            $descriptors,
            $pipes
        );

        if (!is_resource($process)) {
            Log::warning('pdftoppm gagal di-spawn.');
            return null;
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $generatedFile = $tempPrefix . '.png';

        if ($exitCode !== 0 || !file_exists($generatedFile)) {
            Log::warning('pdftoppm gagal generate cover.', [
                'exit_code' => $exitCode,
                'stderr' => is_string($error) ? mb_substr($error, 0, 500) : null,
            ]);
            @unlink($generatedFile);
            return null;
        }

        try {
            $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseFileName);
            $coverName = time() . '_autocover_' . $safeBaseName . '.png';
            $relativePath = 'fiksi/covers/' . $coverName;

            $binaryContent = file_get_contents($generatedFile);
            Storage::disk('public')->put($relativePath, $binaryContent);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan cover hasil pdftoppm.', [
                'error' => $e->getMessage(),
            ]);
            return null;
        } finally {
            @unlink($generatedFile);
        }
    }

    private function locatePdftoppm(): ?string
    {
        $isWindows = stripos(PHP_OS_FAMILY, 'Win') === 0;

        // Cek lokasi umum dulu (termasuk config .env) sebelum mengandalkan PATH —
        // proses PHP yang sudah berjalan lama sering belum me-reload PATH terbaru.
        $configuredPath = (string) config('services.poppler.pdftoppm_path', '');
        $wellKnownPaths = array_filter([
            $configuredPath !== '' ? $configuredPath : null,
            // Sesuaikan baris di bawah ini dengan lokasi extract poppler kamu:
            'D:\\D\\COOLYEAH\\SEMESTER 8\\SKRIPSI\\FIKSI\\Release-26.02.0-0\\poppler-26.02.0\\Library\\bin\\pdftoppm.exe',
            'C:\\ProgramData\\chocolatey\\bin\\pdftoppm.exe',
            '/usr/bin/pdftoppm',
            '/usr/local/bin/pdftoppm',
        ]);

        foreach ($wellKnownPaths as $path) {
            if ($path !== null && @is_executable($path)) {
                return $path;
            }
        }

        $candidate = $isWindows ? 'pdftoppm.exe' : 'pdftoppm';
        $cmd = $isWindows ? "where {$candidate}" : "command -v {$candidate}";
        $path = @shell_exec($cmd . ' 2>/dev/null');

        if (is_string($path)) {
            $path = trim(strtok($path, "\n") ?: '');
            if ($path !== '' && @is_executable($path)) {
                return $path;
            }
        }

        Log::warning('pdftoppm tidak ditemukan di lokasi umum maupun PATH.', [
            'checked_well_known_paths' => $wellKnownPaths,
        ]);

        return null;
    }
}