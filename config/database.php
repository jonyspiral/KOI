<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    | Esta conexión se usa por defecto si no se especifica otra en los modelos
    | o en los llamados a DB::connection(). Por convención, usamos 'mysql'.
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    | Acá se definen todas las conexiones que usa KOI.
    */
    'connections' => [

        // 🔌 Conexión ODBC genérica (fallback, no se recomienda para producción)
        'odbc' => [
            'driver' => 'odbc',
            'dsn' => env('DB_DSN', 'MiSQLServer'),
            'database' => env('DB_DATABASE', 'koi2'),
            'username' => env('DB_USERNAME', 'Koi'),
            'password' => env('DB_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // 🐬 Conexión principal a MySQL (es la conexión por defecto del sistema)
  // 🐬 Conexión principal a MySQL (por defecto)
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

        // 🧠 Conexión SQL Server 2000 (usada por el importador KOI)
// 🧠 Conexión a SQL Server 2000 vía FreeTDS/ODBC
        'sqlsrv_koi' => [
            'driver'   => 'odbc',
            'dsn'      => env('DB_KOI_DSN', 'MiSQLServer'),
            'database' => env('DB_KOI_DATABASE', 'desarrollo'),
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset'  => 'utf8',
            'prefix'   => '',
        ],

        // 🏢 Conexión a Encinitas (otra base del sistema)
        'sqlsrv_encinitas' => [
            'driver' => 'odbc',
            'dsn' => 'ENCINITAS_DSN',
            'database' => 'encinitas',
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        // 🏭 Conexión a Spiral (segunda base opcional)
        'sqlsrv_spiral' => [
            'driver' => 'odbc',
            'dsn' => 'SPIRAL_DSN',
            'database' => 'spiral',
            'username' => env('DB_KOI_USERNAME', 'Koi'),
            'password' => env('DB_KOI_PASSWORD', 'koisys'),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        // 🧪 Conexión SQL Server alternativa con driver nativo (no se usa en producción)
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

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    | Laravel guarda un historial de migraciones en esta tabla.
    */
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    | Configuración de Redis (se usa opcionalmente para cache o colas).
    */
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
