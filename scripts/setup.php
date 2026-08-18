<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbServer = getenv('DB_SERVER') ?: '';
$dbName = getenv('DB_NAME') ?: '';
$dbUser = getenv('DB_USER') ?: '';
$dbPassword = getenv('DB_PASSWORD') ?: '';

if ($dbServer === '' || $dbName === '' || $dbUser === '') {
    throw new RuntimeException('Database configuration is incomplete.');
}

if (!preg_match('/\A[A-Za-z0-9_]+\z/', $dbName)) {
    throw new RuntimeException('Database name contains unsupported characters.');
}

try {
    $mysqli = new mysqli($dbServer, $dbUser, $dbPassword);
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
    $mysqli->close();

    $mysqli = new mysqli($dbServer, $dbUser, $dbPassword, $dbName);
    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `users` (
            name varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            auth_verifier varchar(255) DEFAULT NULL,
            protocol_version int NOT NULL DEFAULT 1,
            kdf_salt varchar(64) DEFAULT NULL,
            lastaccessed timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            content mediumtext,
            PRIMARY KEY (name)
        )"
    );
    $columns = [
        ['auth_verifier', 'varchar(255) DEFAULT NULL'],
        ['protocol_version', 'int NOT NULL DEFAULT 1'],
        ['kdf_salt', 'varchar(64) DEFAULT NULL']
    ];
    foreach ($columns as [$columnName, $definition]) {
        $result = $mysqli->query("SHOW COLUMNS FROM `users` LIKE '{$columnName}'");
        if ($result->num_rows === 0) {
            $mysqli->query("ALTER TABLE `users` ADD COLUMN {$columnName} {$definition}");
        }
    }
    // Sessions are no longer part of the authentication protocol.
    $mysqli->query("DROP TABLE IF EXISTS `sessions`");
    $mysqli->close();
} catch (Throwable $error) {
    error_log("Database initialization failed: {$error->getMessage()}");
    exit(1);
}
