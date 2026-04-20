<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==== CONFIG MYSQL (KOI2) ====
const DB_DRIVER      = 'mysql';        // 'mysql' o 'mssql' (no usado aquí)
const mysql_host     = '192.168.2.210';
const mysql_port     = 3306;
const mysql_user     = 'koiuser';
const mysql_pass     = 'Route667?';
const mysql_db       = 'koi1_stage';    // usa 'koi2_v1' si probás contra dev
const mysql_charset  = 'utf8';

// ==== ARG CSV ====
if ($argc < 2) {
    fwrite(STDERR, "Uso: php {$argv[0]} /ruta/a/table.csv\n");
    exit(1);
}
$csvPath = $argv[1];
if (!is_readable($csvPath)) {
    fwrite(STDERR, "No puedo leer el CSV: $csvPath\n");
    exit(1);
}

// ---- 1) Cargar tablas desde CSV y limpiar dbo. ----
$tablesSql = [];
if (($fh = fopen($csvPath, 'r')) !== false) {
    while (($row = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
        if (!isset($row[0])) continue;
        $name = trim($row[0]);
        if ($name === '') continue;

        // 🔑 limpiar prefijo "dbo."
        if (stripos($name, 'dbo.') === 0) {
            $name = substr($name, 4);
        }

        $tablesSql[$name] = true;
    }
    fclose($fh);
}
$tablesSql = array_keys($tablesSql);
sort($tablesSql, SORT_STRING);

// ---- 2) Obtener tablas reales de MySQL ----
$mysqli = new mysqli(mysql_host, mysql_user, mysql_pass, mysql_db, mysql_port);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "Error de conexión MySQL ({$mysqli->connect_errno}): {$mysqli->connect_error}\n");
    exit(1);
}
$mysqli->set_charset(mysql_charset);

$sql = "SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ?
        ORDER BY BINARY TABLE_NAME";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $db);
$db = mysql_db;
$stmt->execute();
$res = $stmt->get_result();

$tablesMy = [];
while ($row = $res->fetch_assoc()) {
    $tablesMy[] = $row['TABLE_NAME'];
}
$stmt->close();
$mysqli->close();

// ---- 3) Comparación exacta (case-sensitive) ----
$setSql = array_flip($tablesSql);
$setMy  = array_flip($tablesMy);

$faltantesEnMySQL = array_diff($tablesSql, $tablesMy);
$sobrantesEnMySQL = array_diff($tablesMy, $tablesSql);
$coinciden        = array_intersect($tablesSql, $tablesMy);

// ---- 4) Reporte ----
echo "== Comparación de tablas (case-sensitive, limpiando dbo.) ==\n";
echo "Base MySQL: " . mysql_db . " @ " . mysql_host . ":" . mysql_port . "\n";
echo "Total CSV (SQL Server, sin dbo): " . count($tablesSql) . "\n";
echo "Total MySQL: " . count($tablesMy) . "\n";
echo "Coinciden: " . count($coinciden) . "\n\n";

if ($faltantesEnMySQL) {
    echo "-- Faltan en MySQL --\n";
    foreach ($faltantesEnMySQL as $t) echo "$t\n";
    echo "\n";
} else {
    echo "-- Faltantes en MySQL: ninguno ✅ --\n\n";
}

if ($sobrantesEnMySQL) {
    echo "-- Sobrantes en MySQL --\n";
    foreach ($sobrantesEnMySQL as $t) echo "$t\n";
    echo "\n";
} else {
    echo "-- Sobrantes en MySQL: ninguno ✅ --\n\n";
}
