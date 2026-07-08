<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "Connected to MySQL!\n";
    
    // Show databases
    $stmt = $pdo->query('SHOW DATABASES');
    echo "Databases:\n";
    while($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
    
    // Check if db_helpdesk_ptpn exists
    $pdo->exec('USE db_helpdesk_ptpn');
    echo "\ndb_helpdesk_ptpn exists!\n";
    
    // Get categories
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY id');
    echo "\nCategories:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID: {$row['id']} - Name: {$row['name']}\n";
    }
    
    // Get locations
    $stmt = $pdo->query('SELECT * FROM locations ORDER BY id');
    echo "\nLocations:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID: {$row['id']} - Name: {$row['name']}\n";
    }
    
    // Get SLAs
    $stmt = $pdo->query('SELECT * FROM slas ORDER BY id');
    echo "\nSLAs:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID: {$row['id']} - Name: {$row['name']}\n";
    }
    
    // Get settings
    $stmt = $pdo->query('SELECT * FROM settings');
    echo "\nSettings:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['key']} = {$row['value']}\n";
    }
    
    // Count users
    $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM users');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nTotal users: {$row['cnt']}\n";
    
    // Count master_lapors
    $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM master_lapors');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total master_lapors: {$row['cnt']}\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
