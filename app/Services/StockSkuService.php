<?php

namespace App\Services;

use App\Models\Sql\Articulo;
use App\Models\Sql\RangoTalle;
use App\Models\Sql\Stock;

class StockSkuService
{
    /**
     * Obtiene la cantidad total de stock para un SKU específico, sumando los almacenes indicados.
     *
     * @param string $codArticulo
     * @param string $codColor
     * @param string $talle
     * @param array $almacenes
     * @return int
     */
    public static function obtenerStockSKU($codArticulo, $codColor, $talle, array $almacenes = ['01'])
    {
        // 1. Buscar el artículo para conocer su rango de talle
        $articulo = Articulo::whereRaw("CAST(cod_articulo AS VARCHAR) = '$codArticulo'")->first();
        if (!$articulo || !$articulo->cod_rango_talle) {
            return 0;
        }

        // 2. Obtener el rango de talle asociado
        $rango = RangoTalle::whereRaw("CAST(cod_rango_talle AS VARCHAR) = '{$articulo->cod_rango_talle}'")->first();
        if (!$rango) {
            return 0;
        }

        // 3. Buscar la posición dentro del rango que coincida con el talle
        for ($i = 1; $i <= 10; $i++) {
            $campoTalle = "talle_$i";
            if (trim($rango->$campoTalle) === $talle) {
                // 4. Obtener la cantidad desde el stock por posición y almacenes
                return Stock::obtenerCantidadPorPosicion($codArticulo, $codColor, $i, $almacenes);
            }
        }

        return 0;
    }
}
