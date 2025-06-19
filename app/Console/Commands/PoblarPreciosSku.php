<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SkuVariante;
use App\Models\ColoresPorArticulo;

class PoblarPreciosSku extends Command
{
    protected $signature = 'poblar:precios-sku';
    protected $description = 'Pobla los precios ml_price, eshop_price y segunda_price en sku_variantes desde colores_por_articulo';

    public function handle()
    {
        $this->info('🚀 Iniciando población de precios desde colores_por_articulo...');

        $variantes = SkuVariante::all();
        $actualizados = 0;

        foreach ($variantes as $sku) {
            $cpa = ColoresPorArticulo::where('cod_articulo', $sku->cod_articulo)
                ->where('cod_color_articulo', $sku->cod_color_articulo)
                ->first();

            if ($cpa) {
                $sku->ml_price       = $cpa->mlibre_precio;
                $sku->eshop_price    = $cpa->ecommerce_price1;
                $sku->segunda_price  = $cpa->precio_mayorista_usd;
                $sku->save();
                $actualizados++;
            }
        }

        $this->info("✅ Precios actualizados en $actualizados SKU variantes.");
    }
}
