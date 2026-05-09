<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX TIPE DATA TABEL PENGGUNA ===\n\n";

try {
    // Ubah kolom id menjadi bigint unsigned
    echo "Mengubah kolom id di tabel pengguna menjadi bigint unsigned...\n";
    DB::statement("ALTER TABLE pengguna MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
    echo "✓ Berhasil!\n\n";
    
    // Cek struktur lagi
    echo "--- Struktur tabel pengguna setelah fix ---\n";
    $columns = DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'id'");
    foreach ($columns as $col) {
        echo "{$col->Field}: {$col->Type} | Null: {$col->Null} | Key: {$col->Key}\n";
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Sekarang jalankan: php artisan migrate\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


