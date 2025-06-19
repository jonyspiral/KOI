<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Definir comandos programados si es necesario
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
    ];
    
   
    

}

