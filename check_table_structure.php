<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK STRUKTUR TABEL ===\n\n";

// Cek struktur tabel pengguna
echo "--- Struktur tabel pengguna ---\n";
$columns = DB::select("SHOW COLUMNS FROM pengguna");
foreach ($columns as $col) {
    echo "{$col->Field}: {$col->Type} | Null: {$col->Null} | Key: {$col->Key}\n";
}

echo "\n--- Cek apakah ada data di pengguna ---\n";
$count = DB::table('pengguna')->count();
echo "Jumlah data: $count\n";

if ($count > 0) {
    $first = DB::table('pengguna')->first();
    echo "ID pertama: {$first->id} (tipe: " . gettype($first->id) . ")\n";
}


