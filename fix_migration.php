<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIX MIGRATION ===\n\n";

// Cek tabel yang sudah ada
$existingTables = [
    'pengguna',
    'siswa',
    'guru',
    'materi',
    'fiksi',
    'level',
    'mata_pelajaran',
    'pengaturan_gaze',
    'profil_kalibrasi',
    'sesi_baca',
    'log_perintah_suara',
    'log_akses_materi',
];

$migrations = [
    '2025_12_05_151920_create_pengguna_table' => 'pengguna',
    '2025_12_05_151923_create_siswa_table' => 'siswa',
    '2025_12_05_151926_create_guru_table' => 'guru',
    '2025_12_05_151929_create_materi_table' => 'materi',
    '2025_12_05_151932_create_pengaturan_gaze_table' => 'pengaturan_gaze',
    '2025_12_05_151935_create_profil_kalibrasi_table' => 'profil_kalibrasi',
    '2025_12_05_151939_create_sesi_baca_table' => 'sesi_baca',
    '2025_12_05_151942_create_log_perintah_suara_table' => 'log_perintah_suara',
    '2025_12_05_151945_create_log_akses_materi_table' => 'log_akses_materi',
    '2025_12_05_153230_add_remember_token_to_pengguna_table' => 'pengguna',
    '2025_12_06_111021_create_fiksi_table' => 'fiksi',
    '2025_12_06_125523_create_level_table' => 'level',
    '2025_12_06_130226_create_mata_pelajaran_table' => 'mata_pelajaran',
    '2025_12_06_130329_update_materi_table_add_foreign_keys' => 'materi',
    '2025_12_06_130501_update_level_table_add_fields' => 'level',
];

// Cek apakah tabel migrations ada
if (!Schema::hasTable('migrations')) {
    echo "Tabel migrations belum ada, akan dibuat...\n";
    DB::statement("CREATE TABLE IF NOT EXISTS migrations (
        id int(10) unsigned NOT NULL AUTO_INCREMENT,
        migration varchar(255) NOT NULL,
        batch int(11) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Tabel migrations berhasil dibuat!\n\n";
}

// Cek migration yang sudah dijalankan
$executedMigrations = DB::table('migrations')->pluck('migration')->toArray();

$batch = 1;
if (count($executedMigrations) > 0) {
    $batch = DB::table('migrations')->max('batch') + 1;
}

echo "Batch saat ini: $batch\n\n";

// Insert migration yang sudah ada tabelnya
foreach ($migrations as $migration => $table) {
    if (!in_array($migration, $executedMigrations)) {
        if (Schema::hasTable($table)) {
            echo "✓ Tabel '$table' sudah ada, mark migration '$migration' sebagai sudah dijalankan\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
        } else {
            echo "✗ Tabel '$table' belum ada, migration '$migration' akan dijalankan nanti\n";
        }
    } else {
        echo "- Migration '$migration' sudah tercatat\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Sekarang jalankan: php artisan migrate\n";


