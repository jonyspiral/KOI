<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ColoresPorArticuloExport;

class ExportarColoresPorArticuloXlsx extends Command
{
    protected $signature = 'exportar:colores-xlsx';
    protected $description = 'Exporta los primeros 10 registros de ColoresPorArticulo a un archivo Excel (.xlsx)';

    public function handle()
    {
        $archivo = 'colores_por_articulo_preview.xlsx';

        Excel::store(new ColoresPorArticuloExport, $archivo);

        $this->info("✅ Archivo generado: storage/app/$archivo");
    }
}
