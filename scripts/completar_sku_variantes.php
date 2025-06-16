<?php

use App\Models\SkuVariante;
use App\Models\Articulo;
use App\Models\ColoresPorArticulo;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Buscando SKU Variantes con campos incompletos...\n";

SkuVariante::whereNull('id_tipo_producto_stock')
    ->orWhereNull('cod_linea')
    ->chunk(100, function ($registros) {
        foreach ($registros as $registro) {
            $articulo = Articulo::whereRaw("cod_articulo LIKE ?", [$registro->cod_articulo])->first();
            $color    = ColoresPorArticulo::whereRaw("cod_articulo LIKE ?", [$registro->cod_articulo])
                                           ->whereRaw("cod_color_articulo LIKE ?", [$registro->cod_color_articulo])
                                           ->first();

            if ($color && $articulo) {
                $registro->id_tipo_producto_stock = $color->id_tipo_producto_stock ?? null;
                $registro->cod_linea = $articulo->cod_linea ?? null;
                $registro->save();
                echo "✅ Actualizado: {$registro->var_sku}\n";
            } else {
                echo "⚠️  No encontrado: {$registro->var_sku}\n";
            }
        }
    });

echo "🎯 Completado.\n";
