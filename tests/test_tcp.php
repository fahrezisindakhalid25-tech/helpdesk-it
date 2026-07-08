<?php
$fp = @fsockopen('127.0.0.1', 3306, $errno, $errstr, 5);
if ($fp) {
    echo "TCP port 3306 is OPEN on 127.0.0.1\n";
    fclose($fp);
} else {
    echo "TCP port 3306 is CLOSED on 127.0.0.1: [$errno] $errstr\n";
}

// Also try localhost
$fp2 = @fsockopen('localhost', 3306, $errno2, $errstr2, 5);
if ($fp2) {
    echo "TCP port 3306 is OPEN on localhost\n";
    fclose($fp2);
} else {
    echo "TCP port 3306 is CLOSED on localhost: [$errno2] $errstr2\n";
}

// Try PDO connection
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "PDO connected to 127.0.0.1:3306\n";
} catch(Exception $e) {
    echo "PDO 127.0.0.1 error: " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
    echo "PDO connected to localhost:3306\n";
} catch(Exception $e) {
    echo "PDO localhost error: " . $e->getMessage() . "\n";
}
