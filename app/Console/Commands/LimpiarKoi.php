<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LimpiarKoi extends Command
{
    protected $signature = 'koi:limpiar';
    protected $description = '🧼 Limpieza post cambios KOI: vistas, cachés, rutas, config y archivos temporales';

    public function handle()
    {
        $this->info("🚿 Limpiando KOI...");

        $this->line("🧹 Borrando vistas compiladas...");
        File::cleanDirectory(storage_path('framework/views'));

        $this->line("🧠 Borrando caché de configuración...");
        Artisan::call('config:clear');
        $this->info(Artisan::output());

        $this->line("🔁 Borrando caché de rutas...");
        Artisan::call('route:clear');
        $this->info(Artisan::output());

        $this->line("📦 Borrando caché de eventos...");
        Artisan::call('event:clear');
        $this->info(Artisan::output());

        $this->line("🧠 Borrando caché de vista (blade)...");
        Artisan::call('view:clear');
        $this->info(Artisan::output());

        $this->line("📜 Borrando caché general...");
        Artisan::call('cache:clear');
        $this->info(Artisan::output());

        $this->info("✅ KOI limpio y listo. Si hay cambios en rutas o servicios, reiniciá Apache o php-fpm.");
    }
}
