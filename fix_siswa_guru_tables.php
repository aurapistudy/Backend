<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIX TABEL SISWA DAN GURU ===\n\n";

// Cek tipe data id di tabel pengguna
$penggunaId = DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'id'");
$idType = $penggunaId[0]->Type ?? '';

echo "Tipe data id di tabel pengguna: $idType\n\n";

// Buat tabel siswa tanpa foreign key dulu
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
            INDEX `siswa_pengguna_id_index` (`pengguna_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabel 'siswa' berhasil dibuat!\n\n";
} else {
    echo "✓ Tabel 'siswa' sudah ada\n\n";
}

// Buat tabel guru tanpa foreign key dulu
if (!Schema::hasTable('guru')) {
    echo "Membuat tabel 'guru'...\n";
    DB::statement("
        CREATE TABLE `guru` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `pengguna_id` BIGINT UNSIGNED NOT NULL,
            `nama_sekolah` VARCHAR(150) NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            INDEX `guru_pengguna_id_index` (`pengguna_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabel 'guru' berhasil dibuat!\n\n";
} else {
    echo "✓ Tabel 'guru' sudah ada\n\n";
}

echo "=== SELESAI ===\n";
echo "Tabel siswa dan guru sudah dibuat (tanpa foreign key constraint untuk sementara)\n";


