<?php
// config.php
$dbHost   = 'localhost';
$dbName   = 'your_db_name';
$dbUser   = 'your_user';
$dbPass   = 'your_pass';
$charset  = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    echo 'DB Connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
