<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Level;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Pengguna;
use App\Models\Fiksi;

echo "=== DATA DI MYSQL DATABASE ===\n\n";

echo "--- LEVEL ---\n";
$levels = Level::all();
if ($levels->count() > 0) {
    foreach ($levels as $level) {
        echo "ID: {$level->id} | Nama: {$level->nama} | Status: " . ($level->status_aktif ? 'Aktif' : 'Nonaktif') . "\n";
    }
} else {
    echo "Belum ada data level\n";
}

echo "\n--- MATA PELAJARAN ---\n";
$mataPelajarans = MataPelajaran::all();
if ($mataPelajarans->count() > 0) {
    foreach ($mataPelajarans as $mp) {
        echo "ID: {$mp->id} | Nama: {$mp->nama} | Status: " . ($mp->status_aktif ? 'Aktif' : 'Nonaktif') . "\n";
    }
} else {
    echo "Belum ada data mata pelajaran\n";
}

echo "\n--- MATERI ---\n";
$materi = Materi::with(['level', 'mataPelajaran'])->get();
if ($materi->count() > 0) {
    foreach ($materi as $m) {
        echo "ID: {$m->id} | Judul: {$m->judul} | Level: " . ($m->level ? $m->level->nama : '-') . " | Mata Pelajaran: " . ($m->mataPelajaran ? $m->mataPelajaran->nama : '-') . "\n";
    }
} else {
    echo "Belum ada data materi\n";
}

echo "\n--- PENGGUNA ---\n";
$pengguna = Pengguna::all();
if ($pengguna->count() > 0) {
    foreach ($pengguna as $p) {
        echo "ID: {$p->id} | Nama: {$p->nama} | Email: {$p->email} | Peran: {$p->peran}\n";
    }
} else {
    echo "Belum ada data pengguna\n";
}

echo "\n--- FIKSI ---\n";
$fiksi = Fiksi::all();
if ($fiksi->count() > 0) {
    foreach ($fiksi as $f) {
        echo "ID: {$f->id} | Judul: {$f->judul_buku} | Penulis: {$f->penulis}\n";
    }
} else {
    echo "Belum ada data fiksi\n";
}

echo "\n=== SELESAI ===\n";
echo "Data sekarang tersimpan di MySQL database 'akses'\n";
echo "Bisa dilihat di phpMyAdmin: http://localhost/phpmyadmin\n";


