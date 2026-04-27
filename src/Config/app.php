<?php
// src/Config/app.php
return [
    'name'     => $_ENV['APP_NAME']  ?? 'Crédinor Préstamos',
    'env'      => $_ENV['APP_ENV']   ?? 'production',
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url'      => $_ENV['APP_URL']   ?? 'http://localhost/credinor/public',
    'timezone' => 'America/Argentina/Buenos_Aires',
    'locale'   => 'es_AR',
    'session'  => [
        'lifetime' => 480,   // minutos
        'name'     => 'credinor_sess',
    ],
    'roles' => ['admin', 'vendedor', 'cobrador'],
];
