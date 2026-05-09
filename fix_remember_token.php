<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX REMEMBER_TOKEN ===\n\n";

try {
    // Cek apakah kolom remember_token sudah ada
    $columns = DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'remember_token'");
    
    if (empty($columns)) {
        echo "Kolom 'remember_token' belum ada, menambahkan...\n";
        
        // Cek posisi kolom kata_sandi
        $kataSandiPos = DB::select("SHOW COLUMNS FROM pengguna WHERE Field = 'kata_sandi'");
        if (!empty($kataSandiPos)) {
            // Tambahkan kolom remember_token setelah kata_sandi
            DB::statement("ALTER TABLE pengguna ADD COLUMN remember_token VARCHAR(100) NULL AFTER kata_sandi");
            echo "✓ Kolom 'remember_token' berhasil ditambahkan!\n";
        } else {
            // Jika kata_sandi tidak ada, tambahkan di akhir
            DB::statement("ALTER TABLE pengguna ADD COLUMN remember_token VARCHAR(100) NULL");
            echo "✓ Kolom 'remember_token' berhasil ditambahkan!\n";
        }
    } else {
        echo "✓ Kolom 'remember_token' sudah ada\n";
    }
    
    // Verifikasi
    echo "\n--- Struktur tabel pengguna setelah fix ---\n";
    $allColumns = DB::select("SHOW COLUMNS FROM pengguna");
    foreach ($allColumns as $col) {
        echo "{$col->Field}: {$col->Type}\n";
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Sekarang coba login lagi!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


