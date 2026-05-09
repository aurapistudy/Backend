<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX KOLOM SEMUA TABEL ===\n\n";

$tables = ['siswa', 'guru', 'materi', 'fiksi', 'level', 'mata_pelajaran'];

foreach ($tables as $table) {
    try {
        $columns = DB::select("SHOW COLUMNS FROM $table");
        $hasDibuatPada = false;
        $hasDiperbaruiPada = false;
        
        foreach ($columns as $col) {
            if ($col->Field === 'dibuat_pada') $hasDibuatPada = true;
            if ($col->Field === 'diperbarui_pada') $hasDiperbaruiPada = true;
        }
        
        if ($hasDibuatPada) {
            echo "Mengubah 'dibuat_pada' menjadi 'created_at' di tabel $table...\n";
            DB::statement("ALTER TABLE $table CHANGE dibuat_pada created_at TIMESTAMP NULL");
            echo "✓ Berhasil!\n";
        }
        
        if ($hasDiperbaruiPada) {
            echo "Mengubah 'diperbarui_pada' menjadi 'updated_at' di tabel $table...\n";
            DB::statement("ALTER TABLE $table CHANGE diperbarui_pada updated_at TIMESTAMP NULL");
            echo "✓ Berhasil!\n";
        }
        
    } catch (Exception $e) {
        echo "Tabel $table: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Sekarang jalankan: php artisan db:seed\n";


