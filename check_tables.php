<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CEK TABEL YANG ADA ===\n\n";

$requiredTables = [
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

foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✓ Tabel '$table' ada\n";
    } else {
        echo "✗ Tabel '$table' TIDAK ADA\n";
    }
}

echo "\n=== SELESAI ===\n";


