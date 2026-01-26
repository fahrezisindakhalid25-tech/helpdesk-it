<?php
$start = microtime(true);

echo "1. Loading Composer Autoload...\n";
require __DIR__.'/vendor/autoload.php';
echo "   Done: " . round(microtime(true) - $start, 4) . "s\n";

$step2 = microtime(true);
echo "2. Bootstrapping Laravel App...\n";
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "   Done: " . round(microtime(true) - $step2, 4) . "s\n";

$step3 = microtime(true);
echo "3. Connecting to Database & Querying...\n";
try {
    // Force connection
    \DB::connection()->getPdo();
    $user = \App\Models\User::first();
    echo "   User Found: " . ($user ? $user->email : 'None') . "\n";
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}
echo "   Done: " . round(microtime(true) - $step3, 4) . "s\n";

$step4 = microtime(true);
echo "4. Simulating Full HTTP Request (GET /)...\n";
try {
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    echo "   Response Code: " . $response->getStatusCode() . "\n";
    echo "   Content Length: " . strlen($response->getContent()) . "\n";
} catch (\Exception $e) {
    echo "   ERROR Request: " . $e->getMessage() . "\n";
}
echo "   Done: " . round(microtime(true) - $step4, 4) . "s\n";

$total = microtime(true) - $start;
echo "TOTAL: " . round($total, 4) . "s\n";
