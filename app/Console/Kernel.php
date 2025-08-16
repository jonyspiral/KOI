<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
{
    // 🟡 1. Importar publicaciones desde ML a las 3:00 AM
    $schedule->command('mlibre:importar-nuevas')
        ->dailyAt('03:00')
        ->appendOutputTo(storage_path('logs/mlibre_import.log'))
        ->when(fn () => app()->environment('production'));

    // 🟡 2. Sincronizar tablas KOI2 diarias a las 2:00 AM
    $schedule->command('sync:tablas-diarias')
        ->dailyAt('02:00')
        ->appendOutputTo(storage_path('logs/sync.log'))
        ->when(fn () => app()->environment('production'));
}


    protected function commands()
    {

        $this->load(__DIR__.'/Commands');
    }

    protected $commands = [
          \App\Console\Commands\DescargarPublicacionesML::class,
        \App\Console\Commands\ImportarTablaKoi::class,
       \App\Console\Commands\SyncTablaCommand::class,
       \App\Console\Commands\SetupAbmStubs::class,
        \App\Console\Commands\ImportarPublicacionesML::class,
        \App\Console\Commands\MlibreTestVariations::class,
         \App\Console\Commands\VerSkusMlibreCommand::class,
         \App\Console\Commands\MlibreActualizarSku::class,
          \App\Console\Commands\SyncStockDesdeSql::class,
          \App\Console\Commands\MlibreParsearJsonVariantes::class,
         \App\Console\Commands\RenovarTokenMl::class,
         \App\Console\Commands\DeployKoi2::class,
          \App\Console\Commands\DescargarPublicacionML::class,
          \App\Console\Commands\MlibreActualizarStockSeguro::class,
          \App\Console\Commands\LimpiarKoi::class, 
          \App\Console\Commands\MlibreActualizarPublicacion::class,
          \App\Console\Commands\SyncMlVariantes::class,
           \App\Console\Commands\PoblarPreciosSku::class,
           \App\Console\Commands\MlibreGenerarPendientes::class, 
     \App\Console\Commands\SyncCampaignsML::class,
        \App\Console\Commands\MlibreSincronizarOrdenes::class,
        \App\Console\Commands\MlibreExportarOrdenes::class,
        \App\Console\Commands\MlibreActualizarScf::class,
         \App\Console\Commands\MlibreImportarNuevas::class,
        \App\Console\Commands\MlibreImportarOrdenes::class,
         \App\Console\Commands\ArcaFacturar::class,

        ];
    
   
    

}

