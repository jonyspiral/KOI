<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use App\Database\ODBCConnection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;   // <-- ESTE es el import correcto


class AppServiceProvider extends ServiceProvider
{
    public function boot()
{

       // AdminLTE 4 = Bootstrap 5
    Paginator::useBootstrapFive();

    // Si usás AdminLTE 3 (Bootstrap 4), en cambio:
    // Paginator::useBootstrapFour();
    Blade::component('components.koi-menu', 'koi-menu');
    Connection::resolverFor('odbc', function ($connection, $database, $prefix, $config) {
        $dsn = $config['dsn'];
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;

        $pdo = new \PDO("odbc:{$dsn}", $username, $password);

        return new ODBCConnection($pdo, $database, $prefix, $config);
    });
/*       Http::macro('ml', function ($token) {
        return Http::withHeaders([
            'Authorization' => "Bearer $token",
            'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ]);
    });
 */

}

}