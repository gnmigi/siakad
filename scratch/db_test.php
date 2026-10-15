<?php
$host = 'localhost';
$db   = 'u176502720_siamin';
$user = 'u176502720_alba';
$pass = 'Sera6327';
$charset = 'utf8mb4';

echo "Testing localhost...\n";
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT            => 3, // 3 seconds timeout
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Success with localhost!\n";
} catch (\PDOException $e) {
    echo "Failed with localhost: " . $e->getMessage() . "\n";
}

echo "Testing 127.0.0.1...\n";
try {
    $dsn = "mysql:host=127.0.0.1;dbname=$db;charset=$charset;port=3306";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Success with 127.0.0.1!\n";
    
    // Check log_aktivitas count
    $stmt = $pdo->query('SELECT COUNT(*) FROM log_aktivitas');
    echo "Total log_aktivitas rows: " . $stmt->fetchColumn() . "\n";
    
    // Check recent log
    $stmt = $pdo->query('SELECT id, user_id, action, created_at FROM log_aktivitas ORDER BY id DESC LIMIT 5');
    print_r($stmt->fetchAll());
} catch (\PDOException $e) {
    echo "Failed with 127.0.0.1: " . $e->getMessage() . "\n";
}
