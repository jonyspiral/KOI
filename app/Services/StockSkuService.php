<?php

namespace App\Services;

use App\Models\Sql\Articulo;
use App\Models\Sql\RangoTalle;
use App\Models\Sql\Stock;
use Illuminate\Support\Facades\Log;

class StockSkuService
{
    /**
     * Devuelve el stock total de un SKU para los almacenes dados, según el talle.
     *
     * @param string $codArticulo
     * @param string $codColor
     * @param string $talle
     * @param array $almacenes
     * @return int
     */
    public static function obtenerStockSKU($codArticulo, $codColor, $talle, array $almacenes = ['01'])
{
    $articulo = \App\Models\Sql\Articulo::whereRaw("CAST(cod_articulo AS VARCHAR) = '$codArticulo'")->first();

    if (!$articulo) {
        echo "❌ Artículo no encontrado: $codArticulo\n";
        return 0;
    }

    $codRango = $articulo->cod_rango;
    if (!$codRango) {
        echo "❌ El artículo no tiene cod_rango\n";
        return 0;
    }

    $rango = \App\Models\Sql\RangoTalle::whereRaw("CAST(cod_rango AS VARCHAR) = '$codRango'")->first();

    if (!$rango) {
        echo "❌ Rango no encontrado para $codRango\n";
        return 0;
    }

    for ($i = 1; $i <= 10; $i++) {
        $campo = "posic_$i";
        if (trim((string) $rango->$campo) === $talle) {
            //echo "✅ Talle $talle encontrado en posición $i\n";
            return \App\Models\Sql\Stock::obtenerCantidadPorPosicion($codArticulo, $codColor, $i, $almacenes);
        }
    }

    echo "❌ Talle $talle no encontrado en el rango\n";
    return 0;
}

}
