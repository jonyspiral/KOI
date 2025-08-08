<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\SkuVariante;

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/**
 * ⚠️ Lista fija para esta ejecución puntual
 * Editar este array directamente cuando sea necesario volver a correrlo
 */
$articulos = [
 '1001', '1971', '1980', '1989', '3062', '3067', '3093', '3142', '3146',
    '3175', '3180', '3187', '3188', '3190', '3191', '3201', '3202', '6000',
    '6054', '6141', '6143', '6144', '6150', '6240', '6243', '6246', '8011',
    '8012', '8014', '8015', '8016', '8017', '8018', '8019', '8020', '8023',
    '8024', '8025', '8026', '8030', '8031', '8032', '8033', '869', '965'
];

if (empty($articulos)) {
    echo "❌ No se especificaron artículos válidos.\n";
    exit(1);
}

echo "🔄 Poblando SKU variantes para artículos: " . implode(', ', $articulos) . "\n";

$nuevos = 0;
$existentes = 0;

DB::table('view_sku_variantes')
    ->whereIn('cod_articulo', $articulos)
    ->orderBy('var_sku')
    ->chunk(100, function ($variantes) use (&$nuevos, &$existentes) {
        foreach ($variantes as $variante) {

            // Generar var_sku si no viene
            $varSku = $variante->var_sku 
                ?? trim($variante->cod_articulo) . trim($variante->cod_color_articulo) . trim($variante->talle);

            if (empty($varSku)) {
                echo "⚠️ Saltado: var_sku no se pudo generar para artículo {$variante->cod_articulo}\n";
                continue;
            }

            // Saltar si ya existe
            if (SkuVariante::where('var_sku', $varSku)->exists()) {
                $existentes++;
                continue;
            }

            // 📌 Traer info extra desde colores_por_articulo
            $cpa = DB::table('colores_por_articulo')
                ->where('cod_articulo', $variante->cod_articulo)
                ->where('cod_color_articulo', $variante->cod_color_articulo)
                ->first();

            // 📌 Traer info de la línea desde articulos
            $articuloInfo = DB::table('articulos')
                ->where('cod_articulo', $variante->cod_articulo)
                ->first();

            // Insertar
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

                // Campos recuperados
                'ml_price'               => $cpa->mlibre_precio ?? null,
                'eshop_price'            => $cpa->ecommerce_price1 ?? null,
                'segunda_price'          => $cpa->precio_mayorista_usd ?? null,
                'id_tipo_producto_stock' => $cpa->id_tipo_producto_stock ?? null,
                'cod_linea'               => $articuloInfo->cod_linea ?? null,

                'sync_status'            => 'N',
                'sync_log'               => null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            echo "➕ Insertado nuevo SKU: {$varSku}\n";
            $nuevos++;
        }
    });

echo "✅ Proceso finalizado.\n";
echo "   ➕ Nuevos insertados: {$nuevos}\n";
echo "   🔹 Ya existentes: {$existentes}\n";
