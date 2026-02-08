<?php

declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'municipio',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
