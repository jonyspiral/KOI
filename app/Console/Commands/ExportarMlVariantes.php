<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\MlVariantesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportarMlVariantes extends Command
{
    protected $signature = 'mlibre:exportar-variantes';
    protected $description = 'Exporta la tabla ml_variantes a un archivo Excel';

    public function handle()
    {
        $path = storage_path('app/private/mlibre/ml_variantes_export.xlsx');
        Excel::store(new MlVariantesExport, 'private/mlibre/ml_variantes_export.xlsx');
        $this->info("✅ Archivo exportado en: $path");
    }
}
