<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$email = $argv[1] ?? 'aurapitaloka98@gmail.com';
$record = DB::table('password_reset_tokens')->where('email', $email)->orderBy('created_at', 'desc')->first();
if ($record) {
    echo "FOUND\n";
    print_r($record);
} else {
    echo "NOT FOUND\n";
}
