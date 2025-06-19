<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnosticoKoi extends Command
{
    protected $signature = 'koi:diagnostico';
    protected $description = '🔎 Diagnóstico rápido del entorno KOI (env, DB, rutas, permisos)';

    public function handle()
    {
        $this->info("\n✅ Ejecutando diagnóstico KOI...");

        // ENV
        $this->line("\n📁 APP_ENV: " . config('app.env'));
        $this->line("🔑 APP_KEY: " . (config('app.key') ? '✅ definida' : '❌ faltante'));
        $this->line("🌐 APP_URL: " . config('app.url'));

        // DB
        try {
            $db = DB::connection()->getDatabaseName();
            $this->line("🗄️  Base de datos conectada: " . $db);
        } catch (\Throwable $e) {
            $this->error("❌ Error al conectar a la base de datos: " . $e->getMessage());
        }

        // Ruta base
        $this->line("📦 Laravel base path: " . base_path());

        // Comprobación de archivo .env
        if (file_exists(base_path('.env'))) {
            $this->line("📄 Archivo .env encontrado ✔");
        } else {
            $this->error("❌ No se encontró el archivo .env");
        }

        // Permisos básicos
        $this->line("\n🔐 Permisos de carpetas clave:");
        $this->checkPermissions(storage_path());
        $this->checkPermissions(base_path('bootstrap/cache'));

        // Ruta raíz
        $routes = \Route::getRoutes();
        $hasRoot = collect($routes)->contains(function ($route) {
            return $route->uri === '/' && in_array('GET', $route->methods);
        });
        $this->line("\n🚪 Ruta '/' definida: " . ($hasRoot ? '✅ sí' : '❌ no'));

        $this->info("\n📋 Diagnóstico KOI finalizado.\n");
    }

    protected function checkPermissions($path)
    {
        $perm = substr(sprintf('%o', fileperms($path)), -4);
        $owner = posix_getpwuid(fileowner($path))['name'] ?? 'unknown';
        $this->line("- {$path}: {$perm} (owner: {$owner})");
    }
}
