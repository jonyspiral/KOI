<?php
// 📄 Generado por Sofía
// Función: Exportar stock con talles dinámicos y filtros aplicados como columnas
// Fecha: 2025-07-11
// Fuente: Blade exports.stock

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\StockService;
use App\Helpers\FilterProvider;
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
            'cod_articulo' => $item['cod_articulo'],
            'cod_color_articulo' => $item['cod_color_articulo'],
            'denom_articulo' => $item['denom_articulo'] ?? '—',
            'familia' => $item['familia'] ?? '—',
            'linea' => $item['linea'] ?? '—',
            'tipo_producto_stock' => $item['tipo_producto_stock'] ?? '—',
            'forma_comercializacion' => $item['forma_comercializacion'] ?? '—',
        ];

        $total = 0;

        foreach ($item as $key => $valor) {
            if (str_starts_with($key, 'talle_')) {
                $fila[$key] = $valor;
                $talle = str_replace('talle_', '', $key);
                $tallesUnicos[] = $talle;
                $total += $valor;
            }
        }

        $fila['total'] = $total;
        $filas[] = $fila;
    }

    $tallesUnicos = collect($tallesUnicos)->unique()->sort()->values();

    return view('exports.stock', [
        'filas' => $filas,
        'talles' => $tallesUnicos,
    ]);
}


}
