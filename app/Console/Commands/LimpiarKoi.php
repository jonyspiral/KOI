<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class LimpiarKoi extends Command
{
    protected $signature = 'koi:limpiar';
    protected $description = '🧼 Limpieza post cambios KOI: vistas, cachés, rutas, autoload y migraciones';

    public function handle()
    {
        $this->info("🚿 Limpiando KOI en: " . base_path());

        $this->line("🧹 Borrando vistas compiladas...");
        File::cleanDirectory(storage_path('framework/views'));

        $this->line("🧠 Borrando caché de configuración...");
        Artisan::call('config:clear');
        $this->info(Artisan::output());

        $this->line("🔁 Borrando caché de rutas...");
        Artisan::call('route:clear');
        $this->info(Artisan::output());

        $this->line("📦 Borrando caché general...");
        Artisan::call('cache:clear');
        $this->info(Artisan::output());

        $this->line("🎨 Borrando vistas (blade)...");
        Artisan::call('view:clear');
        $this->info(Artisan::output());

        $this->line("🎯 Borrando caché de eventos...");
        Artisan::call('event:clear');
        $this->info(Artisan::output());

        $this->line("🔄 Ejecutando composer dump-autoload...");
        $process = Process::fromShellCommandline('composer dump-autoload', base_path());
        $process->setTimeout(300); // 5 minutos
        $process->run();
        $this->info($process->getOutput());

        $this->line("🛠 Ejecutando migraciones pendientes...");
        Artisan::call('migrate', ['--force' => true]);
        $this->info(Artisan::output());

        $this->info("✅ KOI limpio, migrado y listo.");
    }
}
