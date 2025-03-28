<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class TestTablaKoi extends Command
{
    protected $signature = 'test:tabla';
    protected $description = '🧪 Prueba de creación de tabla con clave única detectada automáticamente';

    public function handle()
    {
        $tabla = 'Pasos_rutas_produccion';

        $this->info("🧪 Probando creación de tabla '$tabla'...");

        // Obtenemos claves primarias desde SQL Server
        $pkeys = DB::connection('sqlsrv_koi')->select("exec sp_pkeys '$tabla'");
        $uniqueFields = array_map(fn($r) => $r->COLUMN_NAME, $pkeys);
        $this->info("🔑 Clave única detectada: " . implode(', ', $uniqueFields));

        // Obtenemos estructura de columnas
        $columnas = DB::connection('sqlsrv_koi')->select("exec sp_columns '$tabla'");
        if (empty($columnas)) {
            $this->error("❌ No se encontró la tabla '$tabla' en SQL Server.");
            return;
        }

        // Si existe en MySQL, la borramos
        if (Schema::hasTable($tabla)) {
            Schema::drop($tabla);
            $this->warn("⚠️ Tabla '$tabla' eliminada.");
        }

        // Creamos tabla con campos + clave única
        Schema::create($tabla, function (Blueprint $table) use ($columnas, $uniqueFields, $tabla) {
            $table->id(); // campo 'id' auto increment

            foreach ($columnas as $columna) {
                $nombre = $columna->COLUMN_NAME;
                $tipo = $columna->TYPE_NAME;
                $largo = $columna->LENGTH ?? 255;
                $nullable = ($columna->IS_NULLABLE ?? 'YES') === 'YES';

                $columnaNueva = match (strtolower($tipo)) {
                    'varchar', 'nvarchar' => $table->string($nombre, $largo),
                    'int', 'smallint', 'tinyint' => $table->integer($nombre),
                    'datetime', 'smalldatetime' => $table->dateTime($nombre),
                    'text', 'ntext' => $table->text($nombre),
                    'char' => $table->char($nombre, $largo),
                    'float', 'numeric' => $table->decimal($nombre, 10, 2),
                    default => $table->string($nombre),
                };

                if ($nullable) {
                    $columnaNueva->nullable();
                }
            }

            // Solución para índice con nombre muy largo
            if ($uniqueFields) {
                $indexName = Str::limit("{$tabla}_" . implode('_', $uniqueFields) . "_unique", 60);
                $table->unique($uniqueFields, $indexName);
            }
        });

        $this->info("✅ Tabla '$tabla' creada correctamente en MySQL con clave única.");
    }
}
