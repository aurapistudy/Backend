<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIX ALL MIGRATIONS ===\n\n";

// Mark tabel yang sudah ada sebagai sudah dijalankan
$tablesToMark = [
    'fiksi' => '2025_12_06_111021_create_fiksi_table',
];

$batch = DB::table('migrations')->max('batch') ?? 1;
$batch++;

foreach ($tablesToMark as $table => $migration) {
    if (Schema::hasTable($table)) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if (!$exists) {
            echo "✓ Mark migration '$migration' sebagai sudah dijalankan\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
        }
    }
}

echo "\n=== SELESAI ===\n";
echo "Sekarang jalankan: php artisan migrate\n";


