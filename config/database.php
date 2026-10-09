<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host_header = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $is_local = (
        str_contains($host_header, 'localhost') ||
        str_contains($host_header, '127.0.0.1') ||
        str_contains($host_header, '.test') ||
        str_contains($host_header, '.local')
    );

    if ($is_local) {
        $host = '127.0.0.1'; $name = 'sonicwave'; $user = 'root'; $pass = ''; $port = '3306';
    } else {
        // Change these for InfinityFree
        $host = 'sqlXXX.infinityfree.com';
        $name = 'if0_XXXXXXX_sonicwave';
        $user = 'if0_XXXXXXX';
        $pass = 'YOUR_PASSWORD';
        $port = '3306';
    }

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log('[SonicWave DB] ' . $e->getMessage());
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Database unavailable', 'details' => $is_local ? $e->getMessage() : null]);
        exit;
    }
}