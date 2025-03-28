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
    protected $signature = 'importar:tabla {nombre_tabla} {--force-models} {--force-table} {--with-sql-model} {--unique=} {--fill-all}';
    protected $description = 'Importa una tabla desde SQL Server 2000 a MySQL y genera sus modelos Eloquent (MySQL y opcionalmente SQL Server)';

    public function handle()
    {
        $tabla = $this->argument('nombre_tabla');
        $this->info("📦 Importando tabla: $tabla");

        $forceModels = $this->option('force-models');
        $forceTable = $this->option('force-table');
        $withSqlModel = $this->option('with-sql-model');
        $uniqueKey = $this->option('unique');
        $fillAll = $this->option('fill-all');

        if (!$uniqueKey) {
            $pkeys = DB::connection('sqlsrv_koi')->select("exec sp_pkeys '$tabla'");
            if (!empty($pkeys)) {
                $uniqueKey = implode(',', array_map(fn($r) => $r->COLUMN_NAME, $pkeys));
                $this->info("🔑 Clave única detectada automáticamente: $uniqueKey");
            }
        }
        $uniqueFields = $uniqueKey ? array_map('trim', explode(',', $uniqueKey)) : [];

        $tabla_sql = str_replace("'", "''", $tabla);
        $columnas = DB::connection('sqlsrv_koi')->select("exec sp_columns '$tabla_sql'");
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
                $table->id();

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

                $table->timestamps();
                $table->string('sync_status')->nullable();

                if (!empty($uniqueFields)) {
                    $indexName = Str::limit("{$tabla}_" . implode('_', $uniqueFields) . "_unique", 60);
                    $table->unique($uniqueFields, $indexName);
                    echo "🔐 Índice único creado: {$indexName}\n";
                }
            });

            $this->info("✅ Tabla '$tabla' creada en MySQL.");
        }

        $datos = DB::connection('sqlsrv_koi')->table($tabla)->get();
        $this->info("📄 {$datos->count()} registros obtenidos desde SQL Server.");

        $batchSize = 500;
        $chunks = array_chunk($datos->toArray(), $batchSize);
        $tablaVacia = DB::table($tabla)->count() === 0;

        foreach ($chunks as $i => $chunk) {
            foreach ($chunk as $fila) {
                $data = (array) $fila;

                if (empty($uniqueFields) || $tablaVacia) {
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
            $this->info("📅 Procesado lote " . ($i + 1));
        }

        $this->info("✅ Datos importados exitosamente en '$tabla'.");

        $campos = array_map(fn($col) => $col->COLUMN_NAME, $columnas);
        $fillable = array_unique(array_merge($uniqueFields, $fillAll ? $campos : []));

        $modelName = ucfirst(Str::singular(Str::camel($tabla)));

        // Modelo MySQL
        $modelPath = app_path("Models/{$modelName}.php");
        $modelo = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table = '{$tabla}';
    public \$timestamps = false;
    protected \$fillable = {$this->formatArray($fillable)};
}

PHP;

        if ($forceModels || !File::exists($modelPath)) {
            File::ensureDirectoryExists(app_path('Models'));
            File::put($modelPath, $modelo);
            $this->info("✅ Modelo MySQL generado: App\\Models\\{$modelName}");
        }

        // Modelo SQL Server
        if ($withSqlModel) {
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
    protected \$fillable = {$this->formatArray($fillable)};
}

PHP;
            if ($forceModels || !File::exists($modelSqlPath)) {
                File::ensureDirectoryExists(app_path('Models/Sql'));
                File::put($modelSqlPath, $modeloSQL);
                $this->info("✅ Modelo SQL Server generado: App\\Models\\Sql\\{$modelName}");
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
