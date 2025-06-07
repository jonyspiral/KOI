<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncStock extends Command
{
    protected $signature = 'sync:tabla {nombre_tabla}';
    protected $description = '🔄 Sincroniza una tabla sincronizable de MySQL a SQL Server';

    public function handle()
    {
        $tabla = $this->argument('nombre_tabla');

        $modeloNombre = Str::studly(Str::singular($tabla));
        $modelo = "App\\Models\\{$modeloNombre}";

        if (!class_exists($modelo)) {
            $this->error("❌ Modelo no encontrado: {$modelo}");
            return;
        }

        $instancia = new $modelo();

        if (!property_exists($modelo, 'primaryKeySql')) {
            $this->error("❌ El modelo {$modelo} no tiene definida la propiedad \$primaryKeySql.");
            return;
        }

        $claves = $modelo::$primaryKeySql;
        $registros = $modelo::all();

        $this->info("🔁 Sincronizando tabla: {$tabla} ({$registros->count()} registros)");

        foreach ($registros as $registro) {
            try {
                $datos = collect($registro->getAttributes())
                    ->except(['created_at', 'updated_at', 'sync_status'])
                    ->toArray();

                $condicion = [];
                foreach ($claves as $campo) {
                    $condicion[$campo] = $registro->$campo;
                }

                // Check existencia usando CAST para evitar error 306
                $query = DB::connection('sqlsrv_koi')->table($tabla);
                foreach ($condicion as $campo => $valor) {
                    $query->whereRaw("CAST({$campo} AS VARCHAR(255)) = ?", [$valor]);
                }

                if ($query->exists()) {
                    $query = DB::connection('sqlsrv_koi')->table($tabla);
                    foreach ($condicion as $campo => $valor) {
                        $query->whereRaw("CAST({$campo} AS VARCHAR(255)) = ?", [$valor]);
                    }
                    $query->update($datos);
                } else {
                    DB::connection('sqlsrv_koi')->table($tabla)->insert($datos);
                }

            } catch (\Exception $e) {
                $id = $registro->id ?? '??';
                $this->error("❌ Error en ID {$id}: " . $e->getMessage());
                continue;
            }
        }

        $this->info('✅ Sincronización finalizada.');
    }
}
