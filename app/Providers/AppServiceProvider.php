<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use App\Database\ODBCConnection;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
{
    Blade::component('components.koi-menu', 'koi-menu');
    Connection::resolverFor('odbc', function ($connection, $database, $prefix, $config) {
        $dsn = $config['dsn'];
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;

        $pdo = new \PDO("odbc:{$dsn}", $username, $password);

        return new ODBCConnection($pdo, $database, $prefix, $config);
    });
}

}