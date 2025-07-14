<?php
namespace App\Services;

use App\Models\Sql\Articulo;
use App\Models\Sql\RangoTalle;
use App\Models\Sql\Stock;
use Illuminate\Support\Facades\DB;


class StockService
{
    public static function stockPorSKU(string $codArticulo, string $codColor, string $talle, array $almacenes = ['01']): int
    {
        $articulo = Articulo::whereRaw("CAST(cod_articulo AS VARCHAR) = ?", [$codArticulo])->first();
        if (!$articulo || !$articulo->cod_rango) return 0;

        $rango = RangoTalle::whereRaw("CAST(cod_rango AS VARCHAR) = ?", [$articulo->cod_rango])->first();
        if (!$rango) return 0;

        for ($i = 1; $i <= 10; $i++) {
            if (trim((string) $rango->{"posic_$i"}) === $talle) {
                return self::stockPorPosicion($codArticulo, $codColor, $i, $almacenes);
            }
        }

        return 0;
    }

    public static function stockPorPosicion(string $codArticulo, string $codColor, int $pos, array $almacenes): int
    {
        $campo = "cant_$pos";

        return Stock::where('cod_articulo', $codArticulo)
            ->where('cod_color_articulo', $codColor)
            ->whereIn('cod_almacen', $almacenes)
            ->sum($campo);
    }
 public static function stockTotalPorArticuloColor(string $codArticulo, string $codColor, array $almacenes): float
    {
        $almacenesFormateados = collect($almacenes)
            ->map(fn($a) => "CAST('$a' AS VARCHAR)")
            ->implode(', ');

        return Stock::whereRaw("CAST(cod_articulo AS VARCHAR) = CAST(? AS VARCHAR)", [$codArticulo])
            ->whereRaw("CAST(cod_color_articulo AS VARCHAR) = CAST(? AS VARCHAR)", [$codColor])
            ->whereRaw("CAST(cod_almacen AS VARCHAR) IN ($almacenesFormateados)")
            ->sum('cantidad');
    }





    public static function stockPorArticuloColorAgrupado(string $codArticulo, string $codColor, array $almacenes): array
    {
        return Stock::where('cod_articulo', $codArticulo)
            ->where('cod_color_articulo', $codColor)
            ->whereIn('cod_almacen', $almacenes)
            ->get()
            ->mapWithKeys(function ($registro) {
                return [
                    $registro->cod_almacen => [
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
                    ]
                ];
            })->toArray();
    }
    public static function mapRegistroConStock($item, $codAlmacen)
{
    $articulo = $item->articulo;
    $rango = $articulo->rango;

    $cantidades = [];
    $total = 0;

    for ($i = 1; $i <= 10; $i++) {
        $campoTalle = 'posic_' . $i;
        $talle = $rango->$campoTalle ?? null;

        if ($talle !== null && $talle !== '') {
            $cantidad = \App\Models\Sql\Stock::obtenerCantidadPorPosicion(
                $item->cod_articulo,
                $item->cod_color_articulo,
                $i,
                [$codAlmacen]
            );
            $cantidades[$talle] = $cantidad;
            $total += $cantidad;
        }
    }

    return (object)[
        'cod_articulo' => $item->cod_articulo,
        'cod_color_articulo' => $item->cod_color_articulo,
        'denom_articulo' => $articulo->denom_articulo ?? '—',
        'cantidades' => $cantidades,
        'total' => $total
    ];
}
public static function stockPorPosicionIndexada(string $codArticulo, string $codColor, int $pos, array $almacenes = ['01']): int
{
    $campo = "cant_$pos";

    $almacenesFormateados = collect($almacenes)
        ->map(fn($a) => "CAST('$a' AS VARCHAR)")
        ->implode(', ');

    return Stock::whereRaw("CAST(cod_articulo AS VARCHAR) = CAST(? AS VARCHAR)", [$codArticulo])
        ->whereRaw("CAST(cod_color_articulo AS VARCHAR) = CAST(? AS VARCHAR)", [$codColor])
        ->whereRaw("CAST(cod_almacen AS VARCHAR) IN ($almacenesFormateados)")
        ->sum($campo);
}


}
