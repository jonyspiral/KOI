<?php

// KOI Importador de tablas v1.8
// Incluye:
// - Creación condicional del campo 'id' en MySQL si no existe en SQL Server
// - Generación de modelos con opción --fill-all o solo claves primarias
// - Creación opcional del modelo SQL Server (--with-sql-model)
// - Agregado de timestamps y campo sync_status para futura sincronización
// - Generación automática del método fieldsMeta() en ambos modelos
// - Flag --skip-data para evitar la importación de registros
// - NUEVO: Flag --insert-simple para insertar sin updateOrInsert
// - FIX: Se usa hash MD5 para nombre del índice único cuando supera límite

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ImportarTablaKoi extends Command
{
    protected $signature = 'importar:tabla {nombre_tabla} {--force-models} {--force-table} {--with-sql-model} {--fill-all} {--skip-data} {--insert-simple}';
    protected $description = 'Importa una tabla desde SQL Server 2000 a MySQL y genera sus modelos Eloquent (MySQL y opcionalmente SQL Server)';

    public function handle()
    {
        $tabla = $this->argument('nombre_tabla');
        $this->info("📦 Importando tabla: $tabla");

        $forceModels = $this->option('force-models');
        $forceTable = $this->option('force-table');
        $withSqlModel = $this->option('with-sql-model');
        $fillAll = $this->option('fill-all');
        $skipData = $this->option('skip-data');
        $insertSimple = $this->option('insert-simple');

        $pkeys = DB::connection('sqlsrv_koi')->select("exec sp_pkeys '$tabla'");
        $uniqueFields = !empty($pkeys)
            ? array_map(fn($r) => $r->COLUMN_NAME, $pkeys)
            : [];

        $columnas = DB::connection('sqlsrv_koi')->select("exec sp_columns '$tabla'");

        if (empty($columnas)) {
            $this->error("❌ No se encontró la tabla '$tabla' en SQL Server.");
            return;
        }

        if ($forceTable && Schema::hasTable($tabla)) {
            Schema::drop($tabla);
            $this->warn("⚠️ Tabla '$tabla' eliminada por --force-table");
        }

        if (!Schema::hasTable($tabla)) {
            Schema::create($tabla, function (Blueprint $table) use ($columnas, $uniqueFields, $tabla) {

                $tieneIdSQL = false;

                foreach ($columnas as $col) {
                    $nombre = strtolower($col->COLUMN_NAME);
                    $tipo = strtolower($col->TYPE_NAME);
                    $esPK = in_array($col->COLUMN_NAME, $uniqueFields);
                    $identity = property_exists($col, 'IS_IDENTITY') && $col->IS_IDENTITY === 'YES';

                    if ($nombre === 'id' && $identity && $esPK) {
                        $tieneIdSQL = true;
                        break;
                    }
                }

                if (!$tieneIdSQL) {
                    $table->id();
                }

                foreach ($columnas as $columna) {
                    $nombre = $columna->COLUMN_NAME;
                    $tipo = $columna->TYPE_NAME;
                    $largo = $columna->LENGTH ?? 255;
                    $nullable = ($columna->IS_NULLABLE ?? 'YES') === 'YES';

                    if (strtolower($nombre) === 'id' && !$tieneIdSQL) {
                        continue;
                    }

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

                $table->timestamps();
                $table->string('sync_status')->nullable();

                if (!empty($uniqueFields)) {
                    $hash = substr(md5(implode('_', $uniqueFields)), 0, 8);
                    $indexName = "{$tabla}_u_{$hash}";
                    $table->unique($uniqueFields, $indexName);
                    echo "🔐 Índice único creado: {$indexName}\n";
                }
            });

            $this->info("✅ Tabla '$tabla' creada en MySQL.");
        }

        if (!$skipData) {
            $datos = DB::connection('sqlsrv_koi')->table($tabla)->get();
            $this->info("📄 {$datos->count()} registros obtenidos desde SQL Server.");

            $batchSize = 500;
            $chunks = array_chunk($datos->toArray(), $batchSize);
            $tablaVacia = DB::table($tabla)->count() === 0;

            foreach ($chunks as $i => $chunk) {
                foreach ($chunk as $fila) {
                    $data = (array) $fila;

                    if ($insertSimple || empty($uniqueFields) || $tablaVacia) {
                        DB::table($tabla)->insert($data);
                    } else {
                        $conditions = [];
                        foreach ($uniqueFields as $field) {
                            if (isset($data[$field])) {
                                $conditions[$field] = $data[$field];
                            }
                        }
                        if (count($conditions) === count($uniqueFields)) {
                            DB::table($tabla)->updateOrInsert($conditions, $data);
                        } else {
                            DB::table($tabla)->insert($data);
                        }
                    }
                }
                $this->info("🗓️ Procesado lote " . ($i + 1));
            }

            $this->info("✅ Datos importados exitosamente en '$tabla'.");
        } else {
            $this->warn("⚠️ Flag --skip-data activado. No se importaron registros desde SQL Server.");
        }

        $fieldsMeta = [];
        foreach ($columnas as $col) {
            $nombre = $col->COLUMN_NAME;
            if (in_array($nombre, ['created_at', 'updated_at', 'sync_status'])) continue;

            $fieldsMeta[$nombre] = [
                'type' => strtolower($col->TYPE_NAME),
                'nullable' => ($col->IS_NULLABLE ?? 'YES') === 'YES',
                'default' => $col->COLUMN_DEF ?? null,
                'primary' => in_array($nombre, $uniqueFields),
            ];
        }

        $metaCode = "    public static function fieldsMeta()\n    {\n        return " . var_export($fieldsMeta, true) . ";\n    }";

        $defaultFields = $fillAll ? array_keys($fieldsMeta) : $uniqueFields;

        if (!in_array('id', $defaultFields)) {
            $defaultFields[] = 'id';
        }

        $modelName = ucfirst(Str::camel(Str::singular($tabla)));
        $modelPath = app_path("Models/{$modelName}.php");

        $modelo = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table = '{$tabla}';
    public \$timestamps = false;
    protected \$fillable = {$this->formatArray(array_unique($defaultFields))};

{$metaCode}
}
PHP;

        if ($forceModels || !File::exists($modelPath)) {
            File::ensureDirectoryExists(app_path('Models'));
            File::put($modelPath, $modelo);
            $this->info("✅ Modelo MySQL generado: App\Models\{$modelName}");
        }

        if ($withSqlModel) {
            $sqlFields = $fillAll ? array_keys($fieldsMeta) : $uniqueFields;

            $modelSqlPath = app_path("Models/Sql/{$modelName}.php");
            $modeloSQL = <<<PHP
<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table = '{$tabla}';
    protected \$connection = 'sqlsrv_koi';
    public \$timestamps = false;
    protected \$fillable = {$this->formatArray(array_unique($sqlFields))};

{$metaCode}
}
PHP;
            if ($forceModels || !File::exists($modelSqlPath)) {
                File::ensureDirectoryExists(app_path('Models/Sql'));
                File::put($modelSqlPath, $modeloSQL);
                $this->info("✅ Modelo SQL Server generado: App\Models\Sql\{$modelName}");
            }
        }

        $this->info("🎉 Finalizado correctamente");
        return 0;
    }

    protected function formatArray(array $values): string
    {
        return '[\'' . implode("', '", $values) . '\']';
    }
}
