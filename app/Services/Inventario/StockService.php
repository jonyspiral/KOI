<?php
namespace App\Services\Inventario;

use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Obtiene el stock para un artículo, color y talle específico.
     */
    public static function getStockPorArticuloColorTalle(string $codArticulo, string $codColorArticulo, $talle): ?int
    {
        $stock = DB::connection('sqlsrv_koi')
            ->table('stock_01_14_20_por_talle_v')
            ->where('cod_articulo', $codArticulo)
            ->where('cod_color_articulo', $codColorArticulo)
            ->where('Talle', $talle)
            ->value('cant_1');

        return $stock !== null ? (int) $stock : null;
    }

    /**
     * Devuelve un array con todas las posiciones de talle y cantidades para un artículo y color.
     *
     * @param string $codArticulo
     * @param string $codColorArticulo
     * @return array<int, int>  // [talle => cantidad]
     */
    public static function getStockPorArticuloColor(string $codArticulo, string $codColorArticulo): array
    {
        return DB::connection('sqlsrv_koi')
            ->table('stock_01_14_20_por_talle_v')
            ->where('cod_articulo', $codArticulo)
            ->where('cod_color_articulo', $codColorArticulo)
            ->whereNotNull('Talle')
            ->pluck('cant_1', 'Talle')
            ->toArray();
    }
    /**
 * Devuelve todo el stock disponible agrupado por artículo, color y talle.
 *
 * @return array<string, array<string, array<int, int>>>
 *         Formato: [cod_articulo => [cod_color_articulo => [talle => cantidad]]]
 */
public static function getTodoElStockAgrupado(): array
{
    $registros = DB::connection('sqlsrv_koi')
        ->table('stock_01_14_20_por_talle_v')
        ->whereNotNull('Talle')
        ->get(['cod_articulo', 'cod_color_articulo', 'Talle', 'cant_1']);

    $resultado = [];

    foreach ($registros as $fila) {
        $art = $fila->cod_articulo;
        $color = $fila->cod_color_articulo;
        $talle = (int) $fila->Talle;
        $cant = (int) $fila->cant_1;

        $resultado[$art][$color][$talle] = $cant;
    }

    return $resultado;
}

}
