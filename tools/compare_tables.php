<?php
/**
 * compare_tables.php
 *
 * Compara lista de tablas entre SQL Server (origen) y MySQL (destino).
 * - Origen puede ser un CSV o una conexión mssql_* (FreeTDS).
 * - Destino es siempre MySQL (mysqli).
 * - Soporta chequeo CASE-SENSITIVE exacto y reporta desvíos de mayúsculas/minúsculas.
 *
 * Uso:
 *   php compare_tables.php [ruta_csv_opcional]
 *   (si ORIGIN_DRIVER = 'csv', podés pasar el CSV por parámetro. Si no se pasa, usa config SQLSERVER_TABLES_CSV)
 *
 * Salida:
 *   - Consola con resumen.
 *   - Archivos en ./out: missing_in_mysql.csv, extra_in_mysql.csv, case_mismatches.csv, summary.json
 */

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

if (!is_dir(OUT_DIR)) {
    @mkdir(OUT_DIR, 0777, true);
}

/** Utilidades **/
function println($s = '') { echo $s . PHP_EOL; }

function normalizeName($name) {
    // Normaliza a lower para comparar case-insensitive
    return mb_strtolower($name, 'UTF-8');
}

function readTablesFromCsv($csvPath, $colName = null) {
    if (!file_exists($csvPath)) {
        throw new RuntimeException("CSV no encontrado: {$csvPath}");
    }

    $fh = fopen($csvPath, 'r');
    if (!$fh) throw new RuntimeException("No se pudo abrir el CSV: {$csvPath}");

    $tables = [];
    $header = fgetcsv($fh);
    if ($header === false) {
        fclose($fh);
        throw new RuntimeException("CSV vacío: {$csvPath}");
    }

    $targetColIdx = 0;
    if (!is_null($colName)) {
        $map = [];
        foreach ($header as $i => $h) {
            $map[trim($h)] = $i;
        }
        if (!isset($map[$colName])) {
            fclose($fh);
            throw new RuntimeException("Columna '{$colName}' no encontrada en CSV.");
        }
        $targetColIdx = $map[$colName];
    }

    // Si no se especifica columna, usamos la 1ra.
    if (is_null($colName)) {
        $targetColIdx = 0;
    }

    // Si la 1ra fila luce como header y contiene 'table' en alguna columna, volvemos a leer como datos desde la 2da línea.
    $hasHeaderTable = false;
    foreach ($header as $h) {
        if (preg_match('/table|tabla|name/i', $h)) { $hasHeaderTable = true; break; }
    }
    if ($hasHeaderTable) {
        // leer el resto
        while (($row = fgetcsv($fh)) !== false) {
            $val = isset($row[$targetColIdx]) ? trim($row[$targetColIdx]) : '';
            if ($val !== '') $tables[] = $val;
        }
    } else {
        // La primera fila ya contiene el primer dato
        $val = isset($header[$targetColIdx]) ? trim($header[$targetColIdx]) : '';
        if ($val !== '') $tables[] = $val;
        while (($row = fgetcsv($fh)) !== false) {
            $val = isset($row[$targetColIdx]) ? trim($row[$targetColIdx]) : '';
            if ($val !== '') $tables[] = $val;
        }
    }

    fclose($fh);
    return $tables;
}

function readTablesFromMssql() {
    if (!function_exists('mssql_connect')) {
        throw new RuntimeException("Extensión mssql_* no disponible. Instalar php5.6-sybase (dblib/freetds) o usar CSV.");
    }

    $link = @mssql_connect(MSSQL_HOST, MSSQL_USER, MSSQL_PASS);
    if (!$link) throw new RuntimeException("No se pudo conectar a MSSQL (" . MSSQL_HOST . ").");
    if (!@mssql_select_db(MSSQL_DB, $link)) {
        throw new RuntimeException("No se pudo seleccionar DB MSSQL '" . MSSQL_DB . "'.");
    }

    // Listar tablas de usuario
    $sql = "SELECT name FROM sysobjects WHERE xtype = 'U' ORDER BY name";
    $res = mssql_query($sql, $link);
    if (!$res) throw new RuntimeException("Error consultando MSSQL sysobjects.");

    $tables = [];
    while ($row = mssql_fetch_assoc($res)) {
        $tables[] = $row['name'];
    }
    mssql_free_result($res);
    mssql_close($link);
    return $tables;
}

function readTablesFromMysql() {
    $mysqli = @mysqli_init();
    $mysqli->options(MYSQLI_INIT_COMMAND, "SET NAMES '" . MYSQL_CHARSET . "'");
    $ok = @$mysqli->real_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT);
    if (!$ok) {
        throw new RuntimeException("No se pudo conectar a MySQL: " . mysqli_connect_error());
    }

    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $db);
    $db = MYSQL_DB;
    $stmt->execute();
    $res = $stmt->get_result();

    $tables = [];
    while ($row = $res->fetch_assoc()) {
        $tables[] = $row['TABLE_NAME'];
    }
    $stmt->close();
    $mysqli->close();
    return $tables;
}

function writeCsv($path, $rows, $header) {
    $fh = fopen($path, 'w');
    if (!$fh) return;
    fputcsv($fh, $header);
    foreach ($rows as $r) fputcsv($fh, $r);
    fclose($fh);
}

/** Programa principal **/

// Determinar origen (SQL Server por CSV o por conexión)
$originDriver = ORIGIN_DRIVER;
$csvArg = isset($argv[1]) ? $argv[1] : null;
if ($originDriver === 'csv') {
    $csvPath = $csvArg ? $csvArg : SQLSERVER_TABLES_CSV;
    println("Leyendo tablas de SQL Server desde CSV: {$csvPath}");
    $sqlTables = readTablesFromCsv($csvPath, CSV_TABLES_COLUMN);
} else if ($originDriver === 'mssql') {
    println("Listando tablas desde SQL Server (mssql_*)...");
    $sqlTables = readTablesFromMssql();
} else {
    throw new RuntimeException("ORIGIN_DRIVER inválido. Usá 'csv' o 'mssql'.");
}

// Quitar duplicados y vacíos
$sqlTables = array_values(array_unique(array_filter(array_map('trim', $sqlTables))));

println("Conectando a MySQL para listar tablas de '" . MYSQL_DB . "'...");
$mysqlTables = readTablesFromMysql();

// Mapas para comparación
$mysqlSetExact = array_fill_keys($mysqlTables, true);
$mysqlSetLower = [];
foreach ($mysqlTables as $t) $mysqlSetLower[normalizeName($t)] = $t;

$sqlSetExact = array_fill_keys($sqlTables, true);
$sqlSetLower  = [];
foreach ($sqlTables as $t) $sqlSetLower[normalizeName($t)] = $t;

// 1) Faltantes en MySQL
$missingInMysql = [];
foreach ($sqlTables as $t) {
    $hasExact = isset($mysqlSetExact[$t]);
    if (CASE_SENSITIVE) {
        if (!$hasExact) {
            $missingInMysql[] = [$t];
        }
    } else {
        $hasLower = isset($mysqlSetLower[normalizeName($t)]);
        if (!$hasExact && !$hasLower) {
            $missingInMysql[] = [$t];
        }
    }
}

// 2) Extras en MySQL (no están en SQL)
$extraInMysql = [];
foreach ($mysqlTables as $t) {
    $hasExact = isset($sqlSetExact[$t]);
    if (CASE_SENSITIVE) {
        if (!$hasExact) {
            $extraInMysql[] = [$t];
        }
    } else {
        $hasLower = isset($sqlSetLower[normalizeName($t)]);
        if (!$hasExact && !$hasLower) {
            $extraInMysql[] = [$t];
        }
    }
}

// 3) Mismatches por mayúsculas/minúsculas (coinciden case-insensitive pero difieren en case)
$caseMismatches = [];
if (REPORT_CASE_MISMATCH) {
    foreach ($sqlTables as $t) {
        $lower = normalizeName($t);
        if (isset($mysqlSetLower[$lower])) {
            $mysqlName = $mysqlSetLower[$lower];
            if ($mysqlName !== $t) {
                $caseMismatches[] = [$t, $mysqlName];
            }
        }
    }
}

// Escribir reportes
writeCsv(OUT_DIR . '/missing_in_mysql.csv', $missingInMysql, ['sqlserver_table_missing_in_mysql']);
writeCsv(OUT_DIR . '/extra_in_mysql.csv',   $extraInMysql,   ['mysql_table_not_in_sqlserver']);
writeCsv(OUT_DIR . '/case_mismatches.csv',  $caseMismatches, ['sqlserver_table', 'mysql_table']);

// Summary JSON
$summary = [
    'origin_driver' => $originDriver,
    'mysql_db' => MYSQL_DB,
    'counts' => [
        'sql_tables'   => count($sqlTables),
        'mysql_tables' => count($mysqlTables),
        'missing_in_mysql' => count($missingInMysql),
        'extra_in_mysql'   => count($extraInMysql),
        'case_mismatches'  => count($caseMismatches),
    ],
    'case_sensitive' => CASE_SENSITIVE,
    'report_case_mismatch' => REPORT_CASE_MISMATCH,
    'out_dir' => OUT_DIR,
];
file_put_contents(OUT_DIR . '/summary.json', json_encode($summary, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

println("==== RESUMEN ====");
println("Tablas en SQL (origen): " . count($sqlTables));
println("Tablas en MySQL (" . MYSQL_DB . "): " . count($mysqlTables));
println("Faltantes en MySQL: " . count($missingInMysql));
println("Extras en MySQL:    " . count($extraInMysql));
println("Case mismatches:     " . count($caseMismatches));
println("");
println("Reportes generados en: " . OUT_DIR);
println("- missing_in_mysql.csv");
println("- extra_in_mysql.csv");
println("- case_mismatches.csv");
println("- summary.json");

exit(0);
