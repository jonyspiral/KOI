<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Config;

class ImportarTablaKoi extends Command
{
  protected $signature = 'importar:tabla
    {nombre_tabla : Nombre de la tabla a importar (origen SQL Server)}
    {--connection=sqlsrv_koi : Conexión ORIGEN (Laravel) hacia SQL Server}
    {--to=mysql : Conexión DESTINO (Laravel) hacia MySQL}
    {--schema= : Nombre FÍSICO de la BD destino (si se omite, usa la de la conexión --to)}

    {--mirror : Replica 1:1 la PK de SQL Server (sin id artificial) + agrega timestamps KOI2}
    {--stage-only : Fuerza destino mysql_k1 + schema koi1_stage, para evitar errores}
    {--money-precision= : Si se define (ej. 19,4), convierte columnas monetarias a DECIMAL(p,s)}

    {--post-ddl : (opcional) Paso 3 clásico (FK/índices/collation); suele NO usarse en mirror}
    {--no-fks : No crear FKs en el post‑DDL}
    {--no-indexes : No crear índices en el post‑DDL}
    {--abm-tables= : (modo clásico) Tablas maestras con UNIQUE de negocio}

    {--force-models : (EXISTENTE)}
    {--force-table : (EXISTENTE)}
    {--with-sql-model : (EXISTENTE)}
    {--fill-all : (EXISTENTE)}
    {--skip-data : (EXISTENTE)}
    {--insert-simple : (EXISTENTE)}
';

  

    protected $description = 'Importa una tabla desde SQL Server 2000 a MySQL y genera sus modelos Eloquent (MySQL y opcionalmente SQL Server) - v2.1';

   public function handle()
{
    $tabla      = $this->argument('nombre_tabla');
    $conexion   = $this->option('connection') ?? 'sqlsrv_koi';
    
    $stageOnly  = (bool)$this->option('stage-only');
    $toConn     = $this->option('to') ?? 'mysql';
    $schema     = $this->option('schema') ?? DB::connection($toConn)->getDatabaseName();
    $mirror     = (bool)$this->option('mirror');
    $forceTable = (bool)$this->option('force-table');
    $skipData   = (bool)$this->option('skip-data');
    $insertSimple = (bool)$this->option('insert-simple');






// Conexión destino y schema

$destConn   = DB::connection($toConn);
$schema     = $this->option('schema') ?: $destConn->getDatabaseName();

// 🔒 Red de seguridad: forzar staging cuando --stage-only
if ($stageOnly) {
    $toConn   = 'mysql_k1';
    $schema   = 'koi1_stage';
    $destConn = DB::connection($toConn);
}

// A partir de acá, TODO usa esta conexión por defecto
Config::set('database.default', $toConn);

$this->info("📦 Importando tabla: {$tabla} desde [{$conexion}] → [{$toConn}.{$schema}]");







    $this->info("📦 Importando tabla: {$tabla} desde [{$conexion}] → [{$toConn}.{$schema}]");

    // === 1. PK y columnas desde SQL Server ===
    $pkeys = DB::connection($conexion)->select("exec sp_pkeys '$tabla'");
    $uniqueFields = !empty($pkeys) ? array_map(fn($r) => $r->COLUMN_NAME, $pkeys) : [];
    $columnas = DB::connection($conexion)->select("exec sp_columns '$tabla'");
    if (empty($columnas)) {
        $this->error("❌ No se encontró la tabla '$tabla' en SQL Server.");
        return 1;
    }

    // === 2. Drop si --force-table ===
    if ($forceTable) {
        $pdo = DB::connection($toConn)->getPdo();
        $stmt = $pdo->prepare("DROP TABLE IF EXISTS `{$schema}`.`$tabla`");
        $stmt->execute();
        $this->warn("⚠️ Tabla '$schema.$tabla' eliminada por --force-table");
    }

    // === 3. Crear tabla si no existe ===
    if (!Schema::connection($toConn)->hasTable($tabla)) {
        Schema::connection($toConn)->create($tabla, function (Blueprint $table) use ($columnas, $uniqueFields, $mirror) {
            $tieneIdSQL = false;
            foreach ($columnas as $col) {
                $nombre   = strtolower($col->COLUMN_NAME);
                $esPK     = in_array($col->COLUMN_NAME, $uniqueFields);
                $identity = property_exists($col, 'IS_IDENTITY') && $col->IS_IDENTITY === 'YES';
                if ($nombre === 'id' && $identity && $esPK) {
                    $tieneIdSQL = true; break;
                }
            }

            if (!$mirror && !$tieneIdSQL) {
                $table->id(); // modo clásico
            }

            foreach ($columnas as $columna) {
                $nombre   = $columna->COLUMN_NAME;
                if (in_array($nombre, ['created_at','updated_at','sync_status'])) continue;
                if (!$mirror && strtolower($nombre) === 'id' && !$tieneIdSQL) continue;

                $tipo     = strtolower($columna->TYPE_NAME);
                $largo    = $columna->LENGTH ?? 255;
                $nullable = ($columna->IS_NULLABLE ?? 'YES') === 'YES';

                $colNueva = match ($tipo) {
                    'varchar','nvarchar' => $table->string($nombre, $largo),
                    'int','smallint','tinyint' => $table->integer($nombre),
                    'datetime','smalldatetime' => $table->dateTime($nombre),
                    'text','ntext' => $table->text($nombre),
                    'char' => $table->char($nombre, $largo),
                    'float','numeric','money','decimal' => $table->decimal($nombre, 19, 4),
                    default => $table->string($nombre),
                };
                if ($nullable) $colNueva->nullable();
            }

            $table->timestamps();
            $table->string('sync_status')->nullable();
        });

        $this->info("✅ Tabla '$schema.$tabla' creada en MySQL.");

        // En mirror: aplicar PK exacta
        if ($mirror && !empty($uniqueFields)) {
            $colsList = '`'.implode('`,`', $uniqueFields).'`';
            DB::connection($toConn)->statement("ALTER TABLE `{$schema}`.`$tabla` ADD PRIMARY KEY ({$colsList})");
            $this->info("🔑 PK (mirror) creada: (".implode(',', $uniqueFields).")");
        }
    }

    // === 4. Migrar datos si no --skip-data ===
    if (!$skipData) {
        $datos = DB::connection($conexion)->table($tabla)->get();
        $this->info("📄 {$datos->count()} registros obtenidos desde SQL Server.");
        $batchSize = 500;
        $chunks = array_chunk($datos->toArray(), $batchSize);
        $tablaVacia = DB::connection($toConn)->table($tabla)->count() === 0;

        foreach ($chunks as $i => $chunk) {
            foreach ($chunk as $fila) {
                $data = (array)$fila;
                $data['created_at'] = now();
                $data['updated_at'] = now();
                $data['sync_status'] = 'U';

                if ($insertSimple || empty($uniqueFields) || $tablaVacia) {
                    DB::connection($toConn)->table($tabla)->insert($data);
                } else {
                    DB::connection($toConn)->table($tabla)->updateOrInsert(
                        array_intersect_key($data, array_flip($uniqueFields)),
                        $data
                    );
                }
            }
            $this->info("🗓️ Procesado lote ".($i+1));
        }
        $this->info("✅ Datos importados exitosamente en '$schema.$tabla'.");
    } else {
        $this->warn("⚠️ --skip-data activo, no se migraron registros.");
    }

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


        protected function generarModeloSQLSeguro(string $modelName, string $tabla, array $sqlFields, array $uniqueFields, string $metaCode, string $conexion): string
                
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
                    $modelo .= "    protected \$connection = '{$conexion}';\n";
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
    protected function generarFieldsMeta(string $tabla, array $columnas, array $uniqueFields, string $conexion): array

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
            $indexRows = DB::connection($conexion)->select("exec sp_helpindex '$tabla'");

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
                    // 👇 Agregar manualmente campos comunes de Laravel
                $fieldsMeta['created_at'] = [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => null,
                    'primary' => false,
                ];
                $fieldsMeta['updated_at'] = [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => null,
                    'primary' => false,
                ];
                $fieldsMeta['sync_status'] = [
                    'type' => 'varchar',
                    'nullable' => true,
                    'default' => null,
                    'primary' => false,
                ];
                if (!array_key_exists('id', $fieldsMeta)) {
                    $fieldsMeta['id'] = [
                        'type'     => 'int',
                        'nullable' => false,
                        'default'  => null,
                        'primary'  => true, // ✅ Clave primaria lógica para Laravel
                    ];
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
       private function postDdl($conn, string $schema, string $table, array $abmTables, int $p, int $s, bool $withFks, bool $withIndexes): void
    {
        // Collation/charset consistentes con Modern_Spanish_CI_AS
        $this->convertTableCollation($conn, $schema, $table, 'utf8mb4', 'utf8mb4_0900_as_ci');

        // ABM (maestras) vs transaccionales (PK compuesta)
        if (in_array(strtolower($table), array_map('strtolower', $abmTables))) {
            if ($table === 'articulos') $this->ensureUnique($conn, $schema, $table, 'uq_articulos_cod', ['cod_articulo']);
            if ($table === 'clientes')  $this->ensureUnique($conn, $schema, $table, 'uq_clientes_cod',  ['cod_cli']);
            // (si tu importador ya crea id autoincrement, no lo toco)
        } else {
            $pks = [
                'colores_por_articulo' => ['cod_articulo','cod_color_articulo'],
                'pedidos_c'            => ['empresa','nro_pedido'],
                'pedidos_d'            => ['empresa','nro_pedido','nro_item'],
            ];
            if (isset($pks[$table])) $this->ensurePrimaryKey($conn, $schema, $table, $pks[$table]);
        }

        // Monetarios → DECIMAL(p,s)
        $moneyMap = [
            'articulos' => ['precio_lista','precio_oferta'],
            'pedidos_c' => ['subtotal','descuento','total'],
            'pedidos_d' => ['precio_unit','importe_linea'],
        ];
        if (isset($moneyMap[$table])) $this->alterDecimals($conn, $schema, $table, $moneyMap[$table], $p, $s);

        // FKs mínimas
        if ($withFks) {
            if ($table === 'colores_por_articulo') {
                $this->ensureForeignKey($conn, $schema, 'colores_por_articulo', 'fk_cpa_art',
                    ['cod_articulo'], "{$schema}.articulos", ['cod_articulo']);
            }
            if ($table === 'pedidos_d') {
                $this->ensureForeignKey($conn, $schema, 'pedidos_d', 'fk_pd_pc',
                    ['empresa','nro_pedido'], "{$schema}.pedidos_c", ['empresa','nro_pedido']);
            }
        }

        // Índices recomendados
        if ($withIndexes) {
            $indexMap = [
                'articulos' => [
                    ['idx_art_cod', ['cod_articulo']],
                ],
                'clientes' => [
                    ['idx_cli_cod',  ['cod_cli']],
                    ['idx_cli_cuit', ['cuit']],
                ],
                'colores_por_articulo' => [
                    ['idx_cpa_art', ['cod_articulo']],
                ],
                'pedidos_c' => [
                    ['idx_pc_cliente_fecha', ['cliente','fecha']],
                ],
                'pedidos_d' => [
                    ['idx_pd_join',     ['empresa','nro_pedido']],
                    ['idx_pd_articulo', ['cod_articulo']],
                ],
            ];
            foreach ($indexMap[$table] ?? [] as [$name,$cols]) {
                $this->ensureIndex($conn, $schema, $table, $name, $cols);
            }
        }
    }

    private function convertTableCollation($conn, string $schema, string $table, string $charset, string $collation): void
    {
        $conn->statement("ALTER TABLE `{$schema}`.`{$table}` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
    }
     private function ensureUnique($conn, string $schema, string $table, string $name, array $cols): void
    {
        if (!$this->constraintExists($conn, $schema, $table, $name)) {
            $colList = '`'.implode('`,`',$cols).'`';
            $conn->statement("ALTER TABLE `{$schema}`.`{$table}` ADD CONSTRAINT `{$name}` UNIQUE ({$colList})");
        }
    }

    private function ensurePrimaryKey($conn, string $schema, string $table, array $cols): void
    {
        $hasPk = $conn->selectOne("
            SELECT COUNT(*) c FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_TYPE='PRIMARY KEY' AND TABLE_SCHEMA=? AND TABLE_NAME=?", [$schema,$table]
        );
        if (intval($hasPk->c ?? 0) === 0) {
            $colList = '`'.implode('`,`',$cols).'`';
            $conn->statement("ALTER TABLE `{$schema}`.`{$table}` ADD PRIMARY KEY ({$colList})");
        }
    }

    private function alterDecimals($conn, string $schema, string $table, array $cols, int $p, int $s): void
    {
        foreach ($cols as $c) {
            $col = $conn->selectOne("
                SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=?", [$schema,$table,$c]
            );
            if ($col) {
                $conn->statement("ALTER TABLE `{$schema}`.`{$table}` MODIFY `{$c}` DECIMAL({$p},{$s}) NULL");
            }
        }
    }

    private function ensureForeignKey($conn, string $schema, string $table, string $name, array $cols, string $ref, array $refCols): void
    {
        if (!$this->constraintExists($conn, $schema, $table, $name)) {
            $colList = '`'.implode('`,`',$cols).'`';
            $refList = '`'.implode('`,`',$refCols).'`';
            $conn->statement("
                ALTER TABLE `{$schema}`.`{$table}`
                ADD CONSTRAINT `{$name}` FOREIGN KEY ({$colList}) REFERENCES {$ref} ({$refList})
            ");
        }
    }

    private function ensureIndex($conn, string $schema, string $table, string $name, array $cols): void
    {
        $exists = $conn->selectOne("
            SELECT COUNT(*) c
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND INDEX_NAME=?", [$schema,$table,$name]
        );
        if (intval($exists->c ?? 0) === 0) {
            $colList = '`'.implode('`,`',$cols).'`';
            $conn->statement("CREATE INDEX `{$name}` ON `{$schema}`.`{$table}` ({$colList})");
        }
    }

    private function constraintExists($conn, string $schema, string $table, string $name): bool
    {
        $row = $conn->selectOne("
            SELECT COUNT(*) c FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND CONSTRAINT_NAME=?", [$schema,$table,$name]
        );
        return intval($row->c ?? 0) > 0;
    }


    
}
