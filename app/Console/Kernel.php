<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    // Usa horario de Argentina para TODAS las tareas
    protected function scheduleTimezone(): \DateTimeZone|string
    {
        return 'America/Argentina/Buenos_Aires';
    }

    protected function schedule(Schedule $schedule)
    {
        // 🟡 1) Importar publicaciones (lo que ya tenías)
        $schedule->command('mlibre:importar-nuevas')
            ->dailyAt('03:00')
            ->appendOutputTo(storage_path('logs/mlibre_import.log'))
            ->when(fn () => app()->environment('production'));

        // 🟡 2) Sincronizar tablas KOI2 (lo que ya tenías)
        $schedule->command('sync:tablas-diarias')
            ->dailyAt('02:00')
            ->appendOutputTo(storage_path('logs/sync.log'))
            ->when(fn () => app()->environment('production'));

        // 🟢 3) Importador de ÓRDENES pagas — cada 30 min del día (hoy→hoy)
        // Usamos ->call para calcular las fechas en tiempo de ejecución.
        $schedule->call(function () {
                Artisan::call('mlibre:importar-ordenes', [
                    'desde'         => now()->toDateString(),
                    'hasta'         => now()->toDateString(),
                    '--estado'      => 'paid',
                    '--with-docs'   => 1,
                    '--seller'      => env('MLIBRE_USER_ID', 448490530),
                ]);
            })
            ->everyThirtyMinutes()
            ->between('08:00', '23:59')
            ->appendOutputTo(storage_path('logs/mlibre_ordenes_hoy.log'))
            ->when(fn () => app()->environment('production'));

        // 🟢 4) Backfill de AYER (docs + fiscal_documents) — 02:10 AM
        $schedule->call(function () {
                Artisan::call('mlibre:importar-ordenes', [
                    'desde'               => now()->subDay()->toDateString(),
                    'hasta'               => now()->subDay()->toDateString(),
                    '--estado'            => 'paid',
                    '--with-docs'         => 1,
                    '--check-fiscal-docs' => 1,
                    '--seller'            => env('MLIBRE_USER_ID', 448490530),
                ]);
            })
            ->dailyAt('02:10')
            ->appendOutputTo(storage_path('logs/mlibre_ordenes_ayer.log'))
            ->when(fn () => app()->environment('production'));

        // 🟢 5) Completar pack_id faltantes — 03:10 AM (si tenés el comando)
        $schedule->command('mlibre:completar-packids --solo-nulos=1')
            ->dailyAt('03:10')
            ->appendOutputTo(storage_path('logs/mlibre_packids.log'))
            ->when(fn () => app()->environment('production'));

        // 🟢 6) Reconfirmar CAE del día anterior — 01:00 AM (si implementás el comando)
        $schedule->call(function () {
                // Solo si existe el comando mlibre:reconfirmar-cae en tu app
                if (collect(Artisan::all())->keys()->contains('mlibre:reconfirmar-cae')) {
                    Artisan::call('mlibre:reconfirmar-cae', [
                        '--desde' => now()->subDay()->toDateString(),
                        '--hasta' => now()->subDay()->toDateString(),
                    ]);
                }
            })
            ->dailyAt('01:00')
            ->appendOutputTo(storage_path('logs/mlibre_reconfirmar_cae.log'))
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
            \App\Console\Commands\MlibreCompletarDocs::class,
        \App\Console\Commands\MlibreDetectarFacturasMl::class,
            \App\Console\Commands\MlibreCompletarPackIds::class,
        ];
    
   
    

}

