<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=db_helpdesk_ptpn;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT count(*) FROM information_schema.tables WHERE table_schema = 'db_helpdesk_ptpn' AND table_name = 'locations'");
    $exists = $stmt->fetchColumn();
    
    echo "Locations table exists: " . ($exists ? "YES" : "NO") . "\n";
    
    if ($exists) {
        $stmt = $pdo->query("SELECT count(*) FROM locations");
        echo "Locations count: " . $stmt->fetchColumn() . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
