<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX KOLOM TABEL PENGGUNA ===\n\n";

try {
    // Cek kolom yang ada
    $columns = DB::select("SHOW COLUMNS FROM pengguna");
    echo "Kolom yang ada:\n";
    foreach ($columns as $col) {
        echo "- {$col->Field}\n";
    }
    
    // Ubah nama kolom jika perlu
    if (DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'dibuat_pada'")) {
        echo "\nMengubah 'dibuat_pada' menjadi 'created_at'...\n";
        DB::statement("ALTER TABLE pengguna CHANGE dibuat_pada created_at TIMESTAMP NULL");
        echo "✓ Berhasil!\n";
    }
    
    if (DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'diperbarui_pada'")) {
        echo "Mengubah 'diperbarui_pada' menjadi 'updated_at'...\n";
        DB::statement("ALTER TABLE pengguna CHANGE diperbarui_pada updated_at TIMESTAMP NULL");
        echo "✓ Berhasil!\n";
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Sekarang jalankan: php artisan db:seed\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


