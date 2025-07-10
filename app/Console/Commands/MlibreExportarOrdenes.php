<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MlibreExportarOrdenes extends Command
{
    protected $signature = 'mlibre:exportar-ordenes
                            {--desde= : Fecha desde (YYYY-MM-DD)}
                            {--hasta= : Fecha hasta (YYYY-MM-DD)}
                            {--sku= : Filtrar por SCF}
                            {--modelo= : Filtrar por modelo}';

    protected $description = 'Exporta registros de la vista vista_ordenes_ml_detalle a un archivo CSV.';

    public function handle()
    {
        $this->info('📤 Exportando órdenes desde vista_ordenes_ml_detalle...');

        // Armar query base
        $query = DB::table('vista_ordenes_ml_detalle');

        // Filtros dinámicos
        if ($desde = $this->option('desde')) {
            $query->where('date_created', '>=', $desde);
        }

        if ($hasta = $this->option('hasta')) {
            $query->where('date_created', '<=', $hasta);
        }

        if ($sku = $this->option('sku')) {
            $query->where('scf', $sku);
        }

        if ($modelo = $this->option('modelo')) {
            $query->where('modelo', $modelo);
        }

        $registros = $query->get();

        if ($registros->isEmpty()) {
            $this->warn('⚠️ No hay datos para exportar con esos filtros.');
            return;
        }

        // Crear carpeta si no existe
        Storage::makeDirectory('private/mlibre');

        // Nombre del archivo
        $timestamp = now()->format('Ymd_His');
        $filename = "mlibre/export_ordenes_{$timestamp}.csv";
        $path = storage_path('app/private/' . $filename);

        // Guardar archivo
        $handle = fopen($path, 'w');
        fputcsv($handle, array_keys((array) $registros->first()));

        foreach ($registros as $fila) {
            fputcsv($handle, (array) $fila);
        }

        fclose($handle);

        $this->info("✅ Exportación completada: {$filename}");
    }
}
