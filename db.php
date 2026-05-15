<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'careerconnect');
define('DB_USER', 'root');
define('DB_PASS', 'Frenik@1954');
define('DB_CHARSET', 'utf8mb4');

function db() {
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $exception) {
        die('Database connection failed. Check db.php settings and import database.sql in MySQL Workbench.');
    }

    return $pdo;
}
?>
