<?php

return [
    'driver' => env('DB_DRIVER', 'sqlite'),
    'sqlite' => [
        'path' => __DIR__ . '/../storage/db/app.db',
    ],
    'mysql' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'cctv_db'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'pgsql' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'name' => env('DB_NAME', 'cctv_db'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
    ],
];
