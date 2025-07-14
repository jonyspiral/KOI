<?php

namespace App\Helpers;

use App\Models\FamiliasProducto;
use App\Models\LineasProducto;
use App\Models\Almacen;
use App\Models\TipoProductoStock;
use Illuminate\Support\Str;

class FilterProvider
{
    public static function getActiveLabels(array $filters): array
    {
        $labels = [];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            $isLike = is_string($value) && (Str::contains($value, '%') || Str::contains($value, '_'));

            switch ($field) {
                case 'vigente':
                    foreach ((array) $value as $v) {
                        $labels[] = $v === 'S' ? 'Active' : 'Inactive';
                    }
                    break;

                case 'tipo_producto_stock':
                    $tipos = TipoProductoStock::whereIn('id_tipo_producto_stock', (array)$value)
                        ->pluck('denom_tipo_producto', 'id_tipo_producto_stock');
                    foreach ((array)$value as $v) {
                        $labels[] = $tipos[$v] ?? $v;
                    }
                    break;

                case 'familia':
                    $familias = FamiliasProducto::whereIn('id', (array)$value)->pluck('nombre')->toArray();
                    $labels = array_merge($labels, $familias);
                    break;

                case 'linea':
                    $lineas = LineasProducto::whereIn('cod_linea', (array)$value)->pluck('denom_linea')->toArray();
                    $labels = array_merge($labels, $lineas);
                    break;

                case 'almacen':
                    $almacenes = Almacen::whereIn('cod_almacen', (array)$value)->pluck('denom_almacen', 'cod_almacen');
                    foreach ((array)$value as $v) {
                        $labels[] = $almacenes[$v] ?? $v;
                    }
                    break;

                case 'forma_comercializacion':
                    foreach ((array)$value as $v) {
                        $labels[] = $v; // se muestra el texto tal como se guarda
                    }
                    break;

                case 'denominacion':
                case 'denom_articulo':
                    $labels[] = '*' . strtoupper($value) . '*';
                    break;

                case 'cod_articulo':
                case 'color':
                    foreach ((array)$value as $v) {
                        $labels[] = $v;
                    }
                    break;

                default:
                    if (is_array($value)) {
                        $labels = array_merge($labels, $value);
                    } else {
                        $labels[] = $value;
                    }
                    break;
            }
        }

        return $labels;
    }
}
