<?php

require_once __DIR__ . '/env.php';

/**
 * Creates and returns a PDO database connection.
 *
 * @return PDO
 * @throws RuntimeException If database configuration is incomplete.
 */
function databaseConnection(): PDO
{
    $host = envValue('DB_HOST');
    $database = envValue('DB_NAME');
    $user = envValue('DB_USER');
    $password = envValue('DB_PASSWORD');

    if (!$host || !$database || !$user || !$password) {
        throw new RuntimeException('Database configuration is incomplete.');
    }

    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

    // required for Azure MySQL SSL connection
    $sslCaPath = PHP_OS_FAMILY === 'Windows'
        ? 'C:\\php-8.4.3\\extras\\ssl\\cacert.pem' // Windows
        : '/etc/ssl/certs/ca-certificates.crt'; // Ubuntu VM

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => $sslCaPath,
    ]);
}
