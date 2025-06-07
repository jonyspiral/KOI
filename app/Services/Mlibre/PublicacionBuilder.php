<?php

namespace App\Services\Mlibre;

use App\Services\Inventario\StockService;

class PublicacionBuilder
{
    /**
     * Genera las variaciones de una publicación en base al artículo, color y talles.
     */
    public static function armarVariaciones(string $codArticulo, string $codColor, array $talles): array
    {
        $variaciones = [];

        foreach ($talles as $talle) {
            $stock = StockService::obtenerStock($codArticulo, $codColor, $talle);

            $variaciones[] = [
                'talle' => $talle,
                'stock' => $stock,
                'precio' => 49000, // Podés parametrizarlo después
                'atributos' => [
                    ['id' => 'SIZE', 'value_name' => $talle],
                    ['id' => 'COLOR', 'value_name' => 'BLACK'],
                ],
            ];
        }

        return $variaciones;
    }

}
$variaciones = PublicacionBuilder::armarVariaciones('869', '01', [38, 39, 40, 41]);
