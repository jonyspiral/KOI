<?php
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
    protected $description = 'Importa una tabla desde SQL Server 2000 a MySQL y genera sus modelos Eloquent (MySQL y opcionalmente SQL Server) - v2.1';

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

                // 🧠 Traer todos los índices únicos amigables generados
                    $indicesFieldsMeta = $this->generarFieldsMeta($tabla, $columnas, $uniqueFields)['indices'] ?? [];

                    foreach ($indicesFieldsMeta as $nombre => $indexData) {
                        if (!empty($indexData['unique'])) {
                            $table->unique($indexData['columns'], $nombre);
                            echo "🔐 Índice único creado: {$nombre}\n";
                        }
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
       // 🧠 Construir metadata de campos + índices únicos amigables
            $fieldsMeta = $this->generarFieldsMeta($tabla, $columnas, $uniqueFields);

            // 🧠 Generar método fieldsMeta() para insertarlo en el modelo
            $metaCode = "    public static function fieldsMeta()\n    {\n        return " . var_export($fieldsMeta, true) . ";\n    }";

            // 📋 Determinar los campos a incluir en $fillable
            $defaultFields = $fillAll ? array_keys($fieldsMeta) : $uniqueFields;

            if (!in_array('id', $defaultFields)) {
                $defaultFields[] = 'id';
            }


  $modelName = ucfirst(Str::camel(Str::singular($tabla)));
$modelSqlPath = app_path("Models/Sql/{$modelName}.php");
$modelPath    = app_path("Models/{$modelName}.php");

// 🛠 Generar contenido del modelo SQL Server (si se indica)
if ($withSqlModel) {
    $sqlFields = $fillAll ? array_keys($fieldsMeta) : $uniqueFields;

    $modeloSQL = $this->generarModeloSQLSeguro($modelName, $tabla, $sqlFields, $uniqueFields, $metaCode);

    if ($forceModels || !File::exists($modelSqlPath)) {
        File::ensureDirectoryExists(app_path('Models/Sql'));
        File::put($modelSqlPath, $modeloSQL);
        $this->info("✅ Modelo SQL Server generado: App\\Models\\Sql\\{$modelName}");
    }

    // 🧠 Extraer la clave primaria real desde el modelo SQL
    if (file_exists($modelSqlPath)) {
        require_once $modelSqlPath;
        $sqlModelFQN = "App\\Models\\Sql\\{$modelName}";
        $primaryKeySql = property_exists($sqlModelFQN, 'primaryKeySql')
            ? $sqlModelFQN::$primaryKeySql
            : $uniqueFields;
    } else {
        $primaryKeySql = $uniqueFields;
    }
} else {
    $primaryKeySql = $uniqueFields;
}

// ✅ Generar modelo MySQL con primaryKeySql resuelta
$modelo = $this->generarModeloSeguro($modelName, $tabla, $defaultFields, $metaCode, $primaryKeySql);

if ($forceModels || !File::exists($modelPath)) {
    File::ensureDirectoryExists(app_path('Models'));
    File::put($modelPath, $modelo);
    $this->info("✅ Modelo MySQL generado: App\\Models\\{$modelName}");
}




// ✅ Acá debe estar
$this->info("🎉 Finalizado correctamente");
return 0;
}

protected function generarModeloSeguro(string $modelName, string $tabla, array $defaultFields, string $metaCode, array $primaryKeySql): string
{
    $modelo = "<?php\n\n";
    $modelo .= "namespace App\\Models;\n\n";
    $modelo .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
    $modelo .= "class {$modelName} extends Model\n";
    $modelo .= "{\n";
    $modelo .= "    protected \$table = '{$tabla}';\n";
    $modelo .= "    protected \$primaryKey = 'id';\n";
    $modelo .= "    public \$timestamps = true;\n";
    $modelo .= "    public static \$sincronizable = true;\n";
    $modelo .= "    public static array \$primaryKeySql = " . $this->formatArray($primaryKeySql) . ";\n";
    $modelo .= "    protected \$fillable = " . $this->formatArray(array_unique($defaultFields)) . ";\n\n";
    $modelo .= $metaCode . "\n";
    $modelo .= "}\n";

    return $modelo;
}


    
protected function generarModeloSQLSeguro(string $modelName, string $tabla, array $sqlFields, array $uniqueFields, string $metaCode): string
{
    $modelo = "<?php\n\n";
    $modelo .= "namespace App\\Models\\Sql;\n\n";
    $modelo .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
    $modelo .= "class {$modelName} extends Model\n";
    $modelo .= "{\n";
    $modelo .= "    protected \$table = '{$tabla}';\n";
    $modelo .= "    protected \$primaryKey = null; // Clave compuesta, gestionada por KOI\n";
    $modelo .= "    public static array \$primaryKeySql = " . $this->formatArray($uniqueFields) . ";\n";
    $modelo .= "    public \$timestamps = false;\n";
    $modelo .= "    public \$incrementing = false;\n";
    $modelo .= "    protected \$connection = 'sqlsrv_koi';\n";
    $modelo .= "    protected \$fillable = " . $this->formatArray(array_unique($sqlFields)) . ";\n\n";
    $modelo .= $metaCode . "\n";
    $modelo .= "}\n";

    return $modelo;
}

    
    protected function formatArray(array $values): string
    {
        return '[' . implode(', ', array_map(fn($v) => "'$v'", $values)) . ']';
    }

   /**
 * Genera el array fieldsMeta con la metadata de columnas y los índices únicos amigables.
 *
 * @param string $tabla Nombre de la tabla
 * @param array $columnas Columnas obtenidas desde sp_columns
 * @param array $uniqueFields Claves primarias reales detectadas desde sp_pkeys
 * @return array fieldsMeta listo para insertarse en el modelo
 */
protected function generarFieldsMeta(string $tabla, array $columnas, array $uniqueFields): array
{
    $fieldsMeta = [];

    foreach ($columnas as $col) {
        $nombre = $col->COLUMN_NAME;
        if (in_array($nombre, ['created_at', 'updated_at', 'sync_status'])) continue;

        $fieldsMeta[$nombre] = [
            'type'     => strtolower($col->TYPE_NAME),
            'nullable' => ($col->IS_NULLABLE ?? 'YES') === 'YES',
            'default'  => $col->COLUMN_DEF ?? null,
            'primary'  => in_array($nombre, $uniqueFields),
        ];
    }

    // 🧩 Obtener índices únicos desde SQL Server 2000 usando sp_helpindex
    $indexRows = DB::connection('sqlsrv_koi')->select("exec sp_helpindex '$tabla'");

    $indices = [];
    foreach ($indexRows as $row) {
        if (str_contains(strtolower($row->index_description), 'unique')) {
            $columnList = explode(',', str_replace(' ', '', $row->index_keys));
            $nombreAmigable = $this->generarNombreIndiceUnico($columnList);
            $indices[$nombreAmigable] = [
                'columns' => $columnList,
                'unique' => true
            ];
        }
    }

    if (!empty($indices)) {
        $fieldsMeta['indices'] = $indices;
    }

    return $fieldsMeta;
}

/**
 * Genera un nombre amigable y seguro para un índice único compuesto,
 * siguiendo la convención KOI: idx_unico_campo1_campo2...
 * Si excede los 64 caracteres, lo recorta y agrega hash.
 *
 * @param array $columnas
 * @return string
 */
protected function generarNombreIndiceUnico(array $columnas): string
{
    $base = 'idx_unico_' . implode('_', $columnas);

    if (strlen($base) > 60) {
        $hash = substr(md5(implode('_', $columnas)), 0, 6);
        $base = 'idx_unico_' . substr(implode('_', $columnas), 0, 50) . '_' . $hash;
    }

    return $base;
}

    
}
