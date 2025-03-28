<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;

class SyncArticulosToSqlCommand extends Command
{
    protected $signature = 'sync:articulos-to-sql';

    protected $description = 'Sincroniza los artículos desde MySQL a SQL Server 2000';

    public function handle()
    {
        $this->info("Iniciando sincronización de artículos hacia SQL Server...");

        // Traer artículos que estén marcados para sincronizar
        $articulos = Articulo::whereIn('sync_status', ['nuevo', 'editado'])->get();

        if ($articulos->isEmpty()) {
            $this->info("No hay artículos para sincronizar.");
            return 0;
        }

        foreach ($articulos as $articulo) {
            try {
                // Insertar o actualizar en SQL Server
                DB::connection('sqlsrv_koi')->table('articulos')->updateOrInsert(
                    ['cod_articulo' => $articulo->cod_articulo], // Clave única
                    [
                        'descripcion'     => $articulo->descripcion,
                        'categoria'       => $articulo->categoria,
                        'marca'           => $articulo->marca,
                        'activo'          => $articulo->activo,
                        'updated_at'      => now(),
                        // Agregá aquí todos los campos que quieras sincronizar
                    ]
                );

                // Marcar como sincronizado
                $articulo->sync_status = 'sincronizado';
                $articulo->save();

                $this->line("✔ Artículo {$articulo->cod_articulo} sincronizado.");
            } catch (\Exception $e) {
                $this->error("✖ Error al sincronizar {$articulo->cod_articulo}: " . $e->getMessage());
            }
        }

        $this->info("✅ Sincronización completada.");
        return 0;
    }
}
