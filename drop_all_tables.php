<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=db_helpdesk_ptpn;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable foreign key checks to force drop
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $tables = [
        'ticket_comments',
        'tickets',
        'slas',
        'categories',
        'locations',
        'users',
        'cache',
        'cache_locks',
        'failed_jobs',
        'jobs',
        'job_batches',
        'migrations',
        'password_reset_tokens',
        'sessions'
    ];
    
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "Dropped $table\n";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "All tables dropped successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
