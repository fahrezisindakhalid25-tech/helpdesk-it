<?php
$start = microtime(true);

echo "1. Starting Benchmark...\n";

// Load ENV manually to avoid Laravel overhead for now
$env = parse_ini_file('.env');
$host = $env['DB_HOST'] ?? '127.0.0.1';
$db   = $env['DB_DATABASE'];
$user = $env['DB_USERNAME'];
$pass = $env['DB_PASSWORD'];

echo "2. Connecting to Database ($host)...\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $connTime = microtime(true) - $start;
    echo "3. Connected! Time taken: " . round($connTime, 4) . " seconds.\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit;
}

$queryStart = microtime(true);
$pdo->query("SELECT 1");
$queryTime = microtime(true) - $queryStart;
echo "4. Simple Query (SELECT 1) Time: " . round($queryTime, 4) . " seconds.\n";

$totalTime = microtime(true) - $start;
echo "5. Total Script Time: " . round($totalTime, 4) . " seconds.\n";
