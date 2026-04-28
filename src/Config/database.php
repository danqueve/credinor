<?php
// src/Config/database.php
return [
    'driver'   => 'mysql',
    'host'     => $_ENV['DB_HOST']     ?? 'localhost',
    'port'     => $_ENV['DB_PORT']     ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? throw new \RuntimeException('DB_DATABASE no configurado en .env'),
    'username' => $_ENV['DB_USERNAME'] ?? throw new \RuntimeException('DB_USERNAME no configurado en .env'),
    'password' => $_ENV['DB_PASSWORD'] ?? throw new \RuntimeException('DB_PASSWORD no configurado en .env'),
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ],
];
