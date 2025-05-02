<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericoExport;
use Illuminate\Support\Str;

class ExportarModeloXlsx extends Command
{
    protected $signature = 'exportar:modelo-xlsx {--modelo=}';
    protected $description = 'Exporta los primeros 10 registros del modelo indicado a un archivo Excel (.xlsx)';

    public function handle()
    {
        $modeloNombre = $this->option('modelo');

        if (!$modeloNombre) {
            $this->error('⚠️ Debes indicar un modelo con --modelo=NombreModelo');
            return;
        }

        $modeloCompleto = "App\\Models\\$modeloNombre";

        if (!class_exists($modeloCompleto)) {
            $this->error("❌ Modelo no encontrado: $modeloCompleto");
            return;
        }

        $archivo = Str::snake($modeloNombre) . '_preview.xlsx';

        Excel::store(new GenericoExport($modeloCompleto), $archivo);

        $this->info("✅ Exportado a storage/app/$archivo");
    }
}
