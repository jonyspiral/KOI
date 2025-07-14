<?php
// 📄 Generado por Sofía
// Función: Servicio para exportar stock a Excel
// Fecha: 2025-07-11
// Fuente: controlador AnalisisStockController

namespace App\Services\Produccion;

use App\Exports\StockExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\ColoresPorArticulo;

class StockExportService
{
    public function export(Request $request)
    {
        $registros = ColoresPorArticulo::with(['articulo.rango'])
            ->when($request->filled('cod_articulo'), fn($q) =>
                $q->where('cod_articulo', 'like', '%' . $request->cod_articulo . '%'))
            ->get();

        return Excel::download(new StockExport($registros, $request), 'stock_export.xlsx');
    }
}
