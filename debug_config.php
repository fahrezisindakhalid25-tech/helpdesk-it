<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "--- CONFIG START ---\n";
print_r(config('database.connections.mysql'));
echo "--- CONFIG END ---\n";
