<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Articulo; // Cambiar por tu modelo
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\GenericExport; // Lo definimos abajo

class ExportarModeloExcel extends Command
{
    protected $signature = 'exportar:modelo {modelo}';
    protected $description = 'Exporta los primeros 10 registros de un modelo a Excel';

    public function handle()
    {
        $modeloName = $this->argument('modelo');
        $clase = "App\\Models\\$modeloName";

        if (!class_exists($clase)) {
            $this->error("❌ El modelo $modeloName no existe.");
            return;
        }

        $datos = $clase::limit(50)->get();
        $filePath = "exports/{$modeloName}_preview.xlsx";

        Excel::store(new GenericExport($datos), $filePath);

        $this->info("✅ Exportado: storage/app/$filePath");
    }
}
