<?php

// Script untuk memperbaiki konfigurasi MySQL di .env
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "File .env tidak ditemukan!\n";
    exit(1);
}

$content = file_get_contents($envFile);

// Update konfigurasi MySQL
$replacements = [
    'DB_CONNECTION=sqlite' => 'DB_CONNECTION=mysql',
    'DB_DATABASE=laravel' => 'DB_DATABASE=akses',
    // Jika DB_DATABASE tidak ada, tambahkan
];

// Cek apakah DB_DATABASE sudah ada
if (strpos($content, 'DB_DATABASE=') === false) {
    // Tambahkan setelah DB_PASSWORD
    $content = preg_replace(
        '/(DB_PASSWORD=.*)/',
        "$1\nDB_DATABASE=akses",
        $content
    );
} else {
    // Replace yang sudah ada
    $content = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=akses', $content);
}

// Replace DB_CONNECTION
$content = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=mysql', $content);

file_put_contents($envFile, $content);

echo "Konfigurasi MySQL sudah diperbaiki!\n";
echo "Pastikan:\n";
echo "1. Database 'akses' sudah dibuat di MySQL\n";
echo "2. User 'akses' punya akses ke database 'akses'\n";
echo "3. Jika user 'akses' punya password, tambahkan di .env: DB_PASSWORD=password_anda\n";


