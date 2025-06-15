<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SkuVariante;
use App\Services\StockSkuService;
use Carbon\Carbon;

class PoblarSkuVariantes extends Command
{
    protected $signature = 'sku:poblar-desde-view';

    protected $description = 'Pobla la tabla sku_variantes a partir de la vista view_sku_variantes';

    public function handle()
{
    $this->info('🔄 Poblando sku_variantes desde view_sku_variantes...');

    $total = DB::table('view_sku_variantes')->count();
    $this->info("📦 Total de registros a procesar: $total");

    $bar = $this->output->createProgressBar($total);
    $bar->start();

    DB::table('view_sku_variantes')
        ->orderBy('var_sku')
        ->chunk(100, function ($variantes) use ($bar) {
            foreach ($variantes as $variante) {
                $stockEcom = \App\Services\StockSkuService::obtenerStockSKU($variante->cod_articulo, $variante->cod_color_articulo, $variante->talle, ['01', '14']);
                $stock2da  = \App\Services\StockSkuService::obtenerStockSKU($variante->cod_articulo, $variante->cod_color_articulo, $variante->talle, ['02']);

                \App\Models\SkuVariante::updateOrInsert(
                    ['var_sku' => $variante->var_sku],
                    [
                        'cod_articulo'      => $variante->cod_articulo,
                        'cod_color_articulo'=> $variante->cod_color_articulo,
                        'familia'           => $variante->familia ?? null,
                        'sku'               => $variante->sku ?? null,
                        'ml_name'           => $variante->ml_name ?? null,
                        'color'             => $variante->color ?? null,
                        'talle'             => $variante->talle ?? null,
                        'precio'            => $variante->precio ?? 0,
                        'stock'             => $stockEcom,
                        'stock_ecommerce'   => $stockEcom,
                        'stock_2da'         => $stock2da,
                        'stock_fulfillment' => 0,
                        'sync_status'       => 'N',
                        'sync_log'          => null,
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ]
                );

                $bar->advance();
            }
        });

    $bar->finish();
    $this->newLine(2);
    $this->info('✅ Poblamiento completo.');
}

}
