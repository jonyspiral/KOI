<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        // 🔌 Fallback ODBC genérica (no usar en producción)
        'odbc' => [
            'driver' => 'odbc',
            'dsn' => env('DB_KOI_DSN', 'sqlsrv_spiral'),
            'database' => env('DB_KOI_DATABASE', 'spiral'),
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // 🐬 MySQL (Laravel, KOI2, Ecomexperts, etc.)
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'koi2'),
            'username' => env('DB_USERNAME', 'jony'),
            'password' => env('DB_PASSWORD', 'Route667'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        // 🧠 SQL Server KOI (usa variables genéricas)
        'sqlsrv_koi' => [
            'driver' => 'odbc',
            'dsn' => env('DB_KOI_DSN', 'sqlsrv_spiral'),
            'database' => env('DB_KOI_DATABASE', 'spiral'),
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        // 🏢 Encinitas (consulta ocasional)
        'sqlsrv_encinitas' => [
            'driver' => 'odbc',
            'dsn' => 'ENCINITAS_DSN',
            'database' => 'encinitas',
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        // 🧪 (no se usa, solo para pruebas)
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],

];
