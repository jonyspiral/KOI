<?php
// 📄 Generado por Sofía
// Función: Exportar stock con talles dinámicos y filtros aplicados como columnas
// Fecha: 2025-07-11
// Fuente: Blade exports.stock

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\StockService;

class StockExport implements FromView
{
    protected $registros;
    protected $request;

    public function __construct($registros, $request)
    {
        $this->registros = $registros;
        $this->request = $request;
    }

    public function view(): View
    {
        $filas = [];
        $tallesUnicos = [];

        foreach ($this->registros as $item) {
            $fila = [
                'cod_articulo' => $item->cod_articulo,
                'cod_color_articulo' => $item->cod_color_articulo,
                'denom_articulo' => $item->articulo->denom_articulo ?? '—',
            ];

            $rango = $item->articulo?->rango;
            $total = 0;

            if ($rango) {
                for ($i = 1; $i <= 10; $i++) {
                    $talle = $rango->{'posic_' . $i};
                    if (!$talle) continue;

                    $cantidad = StockService::stockPorPosicionIndexada(
                        $item->cod_articulo,
                        $item->cod_color_articulo,
                        $i,
                        (array) ($this->request->almacen ?: ['01'])
                    );

                    $fila["talle_{$talle}"] = $cantidad;
                    $tallesUnicos[] = $talle;
                    $total += $cantidad;
                }
            }

            $fila['total'] = $total;

            // Agregamos los filtros aplicados como columnas
            foreach ($this->request->all() as $campo => $valor) {
                if (in_array($campo, ['_token', 'aplicar', 'sort', 'dir', 'page', 'reset'])) continue;
                $valorTexto = is_array($valor) ? implode(', ', $valor) : $valor;
                $fila["filtro_{$campo}"] = $valorTexto;
            }

            $filas[] = $fila;
        }

        $tallesUnicos = collect($tallesUnicos)->unique()->sort()->values();

        return view('exports.stock', [
            'filas' => $filas,
            'talles' => $tallesUnicos,
        ]);
    }
}
