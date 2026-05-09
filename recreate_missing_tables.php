<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== MEMBUAT ULANG TABEL YANG HILANG ===\n\n";

// 1. Buat tabel mata_pelajaran
if (!Schema::hasTable('mata_pelajaran')) {
    echo "Membuat tabel 'mata_pelajaran'...\n";
    DB::statement("
        CREATE TABLE `mata_pelajaran` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `nama` VARCHAR(100) NOT NULL UNIQUE,
            `deskripsi` TEXT NULL,
            `status_aktif` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabel 'mata_pelajaran' berhasil dibuat!\n\n";
} else {
    echo "✓ Tabel 'mata_pelajaran' sudah ada\n\n";
}

// 2. Buat tabel siswa
if (!Schema::hasTable('siswa')) {
    echo "Membuat tabel 'siswa'...\n";
    DB::statement("
        CREATE TABLE `siswa` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `pengguna_id` BIGINT UNSIGNED NOT NULL,
            `nama_sekolah` VARCHAR(150) NULL,
            `jenjang` VARCHAR(50) NULL COMMENT 'misal: Kelas 4 SD',
            `catatan` TEXT NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabel 'siswa' berhasil dibuat!\n\n";
} else {
    echo "✓ Tabel 'siswa' sudah ada\n\n";
}

// 3. Buat tabel guru
if (!Schema::hasTable('guru')) {
    echo "Membuat tabel 'guru'...\n";
    DB::statement("
        CREATE TABLE `guru` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `pengguna_id` BIGINT UNSIGNED NOT NULL,
            `nama_sekolah` VARCHAR(150) NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabel 'guru' berhasil dibuat!\n\n";
} else {
    echo "✓ Tabel 'guru' sudah ada\n\n";
}

echo "=== SELESAI ===\n";
echo "Semua tabel sudah dibuat ulang!\n";


