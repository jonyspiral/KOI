<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SkuVariante;

class PoblarSkuVariantes extends Command
{
    protected $signature = 'sku:poblar-desde-view';
    protected $description = 'Pobla la tabla sku_variantes a partir de la vista view_sku_variantes, insertando solo nuevos registros y completando campos faltantes';

    public function handle()
    {
        $this->info('🔄 Poblando sku_variantes desde view_sku_variantes...');

        $insertados = 0;
        $existentes = 0;

        DB::table('view_sku_variantes')
            ->orderBy('var_sku')
            ->chunk(100, function ($variantes) use (&$insertados, &$existentes) {
                foreach ($variantes as $variante) {
                    // Generar var_sku si falta
                    $varSku = $variante->var_sku
                        ?? trim($variante->cod_articulo) . trim($variante->cod_color_articulo) . trim($variante->talle);

                    if (empty($varSku)) {
                        $this->warn("⚠️ Saltado: var_sku no se pudo generar para artículo {$variante->cod_articulo}");
                        continue;
                    }

                    // Si ya existe, saltar
                    if (SkuVariante::where('var_sku', $varSku)->exists()) {
                        $existentes++;
                        continue;
                    }

                    // 📌 Buscar datos adicionales desde colores_por_articulo
                    $cpa = DB::table('colores_por_articulo')
                        ->where('cod_articulo', $variante->cod_articulo)
                        ->where('cod_color_articulo', $variante->cod_color_articulo)
                        ->first();

                    // 📌 Buscar línea desde articulos
                    $articuloInfo = DB::table('articulos')
                        ->where('cod_articulo', $variante->cod_articulo)
                        ->first();

                    // Crear el registro
                    SkuVariante::create([
                        'var_sku'                => $varSku,
                        'cod_articulo'           => $variante->cod_articulo,
                        'cod_color_articulo'     => $variante->cod_color_articulo,
                        'familia'                => $variante->familia ?? null,
                        'sku'                    => $variante->sku ?? null,
                        'ml_name'                => $variante->ml_name ?? null,
                        'color'                  => $variante->color ?? null,
                        'talle'                  => $variante->talle ?? null,
                        'precio'                 => $variante->precio ?? 0,

                      
                        'ml_price'               => $variante->ml_price ?? $cpa->mlibre_precio ?? null,
                        'eshop_price'            => $variante->eshop_price ?? $cpa->ecommerce_price1 ?? null,
                        'segunda_price'          => $variante->segunda_price ?? $cpa->precio_mayorista_usd ?? null,
                        'id_tipo_producto_stock' => $variante->id_tipo_producto_stock ?? $cpa->id_tipo_producto_stock ?? null,
                        'cod_linea'               => $variante->cod_linea ?? $articuloInfo->cod_linea ?? null,

                        'sync_status'            => 'N',
                        'sync_log'               => null,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);

                    $this->line("➕ Insertado nuevo SKU: {$varSku}");
                    $insertados++;
                }
            });

        $this->info("✅ Poblamiento completo.");
        $this->info("   ➕ Nuevos insertados: {$insertados}");
        $this->info("   🔹 Ya existentes: {$existentes}");
    }
}
