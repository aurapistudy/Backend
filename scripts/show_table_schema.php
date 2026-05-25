<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['pengguna', 'mata_pelajaran', 'guru_mata_pelajaran'] as $table) {
    echo "=== {$table} ===" . PHP_EOL;
    try {
        $result = $app['db']->select("SHOW CREATE TABLE `{$table}`");
        echo $result[0]->{'Create Table'} . PHP_EOL . PHP_EOL;
    } catch (Throwable $e) {
        echo $e->getMessage() . PHP_EOL . PHP_EOL;
    }
}
