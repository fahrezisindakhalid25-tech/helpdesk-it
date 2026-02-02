<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

try {
    $envToken = env('WA_API_TOKEN');
    $configToken = config('services.fonnte.token');
    
    echo "--- CONFIG CHECK ---\n";
    echo "Loaded Token: " . ($envToken ? substr($envToken, 0, 10) . '...' : 'NULL') . "\n";
    
    echo "\n--- SENDING TEST REQUEST ---\n";
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => $envToken
    ])->post('https://api.fonnte.com/send', [
         // Use a safe dummy number or the user's own number if known, but here just testing auth
        'target' => '08123456789', 
        'message' => 'Test Config Reload'
    ]);
    
    echo "Response Body: " . $response->body() . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
