<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sql\Stock as StockSql;
use App\Models\Stock as StockMysql;

class SyncStockDesdeSql extends Command
{
    protected $signature = 'sync:stock-sql';
    protected $description = 'Sincroniza el stock desde SQL Server (Encinitas) a MySQL (KOI2)';

    public function handle()
    {
        $this->info('🔄 Iniciando sincronización de stock con cursor y updateOrCreate...');

        $query = StockSql::select([
            'cod_almacen',
            'cod_articulo',
            'cod_color_articulo',
            'cantidad',
            'cant_1',
            'cant_2',
            'cant_3',
            'cant_4',
            'cant_5',
            'cant_6',
            'cant_7',
            'cant_8',
            'cant_9',
            'cant_10',
        ])
        ->whereRaw("CAST(cod_almacen AS VARCHAR) IN ('01', '14', '20')");

        $total = $query->count();
        $this->info("📦 Registros a procesar: $total");

        $progress = $this->output->createProgressBar($total);
        $progress->start();

        foreach ($query->orderBy('cod_articulo')->cursor() as $registro) {
            StockMysql::updateOrCreate(
                [
                    'cod_almacen' => $registro->cod_almacen,
                    'cod_articulo' => $registro->cod_articulo,
                    'cod_color_articulo' => $registro->cod_color_articulo,
                ],
                [
                    'cantidad' => $registro->cantidad,
                    'cant_1' => $registro->cant_1,
                    'cant_2' => $registro->cant_2,
                    'cant_3' => $registro->cant_3,
                    'cant_4' => $registro->cant_4,
                    'cant_5' => $registro->cant_5,
                    'cant_6' => $registro->cant_6,
                    'cant_7' => $registro->cant_7,
                    'cant_8' => $registro->cant_8,
                    'cant_9' => $registro->cant_9,
                    'cant_10' => $registro->cant_10,
                    'sync_status' => 'U',
                ]
            );

            $progress->advance();
        }

        $progress->finish();
        $this->newLine();
        $this->info('✅ Sincronización completada.');
    }
}
