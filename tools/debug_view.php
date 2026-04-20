<?php
/**
 * Debug de UNA vista SQL Server 2000 -> MySQL (PHP 5.6 compatible)
 *
 * Uso:
 *   php debug_view.php clientes_v
 *   php debug_view.php clientes_v --mysql-collate=utf8mb4_0900_as_ci
 *   php debug_view.php clientes_v --apply --mysql-collate=utf8mb4_0900_as_ci
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===== Parámetros de conexión =====
$SQL_HOST = '192.168.2.100';
$SQL_DB   = 'encinitas';
$SQL_USER = 'Koi';
$SQL_PASS = 'koisys';

$MY_HOST = '192.168.2.210';
$MY_PORT = 3306;
$MY_USER = 'koiuser';
$MY_PASS = 'Route667?';
$MY_DB   = 'koi1_stage';

// ===== Args =====
$TARGET_VIEW = (isset($argv[1]) && substr($argv[1], 0, 2) !== '--') ? $argv[1] : null;
if (!$TARGET_VIEW) {
    die("⚠ Debes indicar la vista. Ej: php debug_view.php clientes_v [--apply] [--mysql-collate=utf8mb4_0900_as_ci]\n");
}
$APPLY = false;
$MYSQL_COLLATE = 'utf8mb4_0900_as_ci';
for ($i = 2; $i < count($argv); $i++) {
    $a = $argv[$i];
    if ($a === '--apply') $APPLY = true;
    if (strpos($a, '--mysql-collate=') === 0) {
        $v = trim(substr($a, 16));
        if ($v !== '') $MYSQL_COLLATE = $v;
    }
}
function out($s){ echo $s.(substr($s,-1)==="\n"?'':"\n"); }

// ===== Conexión MSSQL (pdo_dblib) =====
try {
    $pdo = new PDO(
        'dblib:host='.$SQL_HOST.';dbname='.$SQL_DB.';charset=UTF-8',
        $SQL_USER, $SQL_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (Exception $e) {
    die("❌ Conexión MSSQL falló: ".$e->getMessage()."\n");
}
// Evitar truncado y setear flags
try {
    $pdo->exec("SET TEXTSIZE 2147483647");
    $pdo->exec("SET NOCOUNT ON");
    $pdo->exec("SET ANSI_NULLS ON; SET QUOTED_IDENTIFIER ON");
} catch (Exception $e) { /* ignore */ }

// Mostrar versión
$ver = '';
try { $r = $pdo->query("SELECT CAST(SERVERPROPERTY('ProductVersion') AS VARCHAR(50)) ver")->fetch(); $ver = $r ? $r['ver'] : ''; }
catch (Exception $e) { $r = $pdo->query("SELECT @@VERSION AS ver")->fetch(); $ver = $r ? $r['ver'] : ''; }
out("SQL Server version: ".$ver);

// Listado rápido (primeras 30 vistas)
out("Listando primeras 30 vistas (owner.name) en DB $SQL_DB ...");
try {
    $rs = $pdo->query("
        SELECT TOP 30 u.name + '.' + o.name AS fqname
        FROM sysobjects o
        JOIN sysusers  u ON o.uid = u.uid
        WHERE o.xtype = 'V'
        ORDER BY u.name, o.name
    ");
    foreach ($rs->fetchAll(PDO::FETCH_COLUMN, 0) as $fq) out(' - '.$fq);
} catch (Exception $e) { /* ignore */ }

// Forzamos dbo (en tu entorno todas son dbo.*)
$FULL_NAME = 'dbo.'.$TARGET_VIEW;
out("Usando vista: $FULL_NAME");

// ===== Obtener definición =====
$parts = array();

// Intento 1: sp_helptext con DECLARE/tabla temp (SQL2000)
try {
    $sql1  = "DECLARE @obj NVARCHAR(776);\n";
    $sql1 .= "SET @obj = :full;\n";
    $sql1 .= "CREATE TABLE #h (t NVARCHAR(4000));\n";
    $sql1 .= "INSERT #h EXEC sp_helptext @obj;\n";
    $sql1 .= "SELECT t FROM #h ORDER BY (SELECT 1);\n";
    $sql1 .= "DROP TABLE #h;";
    $st = $pdo->prepare($sql1);
    $st->execute(array(':full'=>$FULL_NAME));
    $tmp = $st->fetchAll(PDO::FETCH_COLUMN, 0);
    if ($tmp && count($tmp)>0) $parts = $tmp;
} catch (Exception $e) { /* fallback */ }

// Fallback robusto: syscomments con CAST a VARCHAR(8000)
if (count($parts)===0) {
    out("sp_helptext falló o no devolvió contenido, usando syscomments...");
    $sql2 = "SELECT CAST(c.text AS VARCHAR(8000)) AS text
             FROM syscomments c
             WHERE c.id = OBJECT_ID(:full)
             ORDER BY c.colid";
    $st = $pdo->prepare($sql2);
    $st->execute(array(':full'=>$FULL_NAME));
    $tmp = $st->fetchAll(PDO::FETCH_COLUMN, 0);
    if ($tmp && count($tmp)>0) $parts = $tmp;
}
if (count($parts)===0) die("❌ No se pudo obtener el texto de la vista.\n");

$tsql_def = implode('', $parts);
out("OK. Longitud definición T-SQL: ".strlen($tsql_def));

// Preview
$lines = explode("\n", $tsql_def);
out("----- PREVIEW T-SQL (primeras 15 líneas) -----");
for ($i=0; $i<15 && $i<count($lines); $i++) echo $lines[$i]."\n";
out("----------------------------------------------");

// ===== Conversión T-SQL -> MySQL =====
function tsql_to_mysql($def, $mysqlCollate) {
    $sql = str_replace("\r\n","\n",$def);

    // limpiar headers y GO
    $sql = preg_replace('/^\s*SET\s+ANSI_NULLS\s+(ON|OFF)\s*;?/mi','', $sql);
    $sql = preg_replace('/^\s*SET\s+QUOTED_IDENTIFIER\s+(ON|OFF)\s*;?/mi','', $sql);
    $sql = preg_replace('/^\s*GO\s*$/mi','', $sql);

    // CREATE VIEW ... AS -> CREATE OR REPLACE VIEW `name` AS
    if (preg_match('/CREATE\s+VIEW\s+(?:\[[^\]]+\]\.)?\[?([^\]\s]+)\]?\s+AS/mi', $sql, $m)) {
        $viewName = $m[1];
        $sql = preg_replace('/CREATE\s+VIEW\s+(?:\[[^\]]+\]\.)?\[?[^\]]+\]?\s+AS/mi',
            'CREATE OR REPLACE VIEW `'.$viewName.'` AS', $sql, 1);
    }

    // [] y dbo.
    $sql = preg_replace('/\[(.*?)\]/', '$1', $sql);
    $sql = preg_replace('/\bdbo\./i', '', $sql);

    // Reescrituras típicas
    $repl = array(
        '/\bISNULL\s*\(/i'                                     => 'IFNULL(',
        '/\bGETDATE\s*\(\s*\)/i'                               => 'CURRENT_TIMESTAMP',
        '/\bNVARCHAR\s*\(/i'                                   => 'VARCHAR(',
        '/\bNCHAR\s*\(/i'                                      => 'CHAR(',
        '/\bCAST\s*\((.*?)\s+AS\s+NVARCHAR\(\d+\)\)/i'         => 'CAST($1 AS CHAR)',
        '/DATEDIFF\s*\(\s*day\s*,\s*([^,]+)\s*,\s*([^)]+)\)/i' => 'DATEDIFF($2, $1)',
        '/CONVERT\s*\(\s*datetime\s*,\s*([^)]+)\)/i'           => 'CAST($1 AS DATETIME)',
        '/CONVERT\s*\(\s*varchar\s*\(\s*(\d+)\s*\)\s*,\s*([^)]+)\)/i' => 'CAST($2 AS CHAR($1))',
        '/SELECT\s+TOP\s+(\d+)\s+/i'                           => 'SELECT '
    );
    foreach ($repl as $k=>$v) $sql = preg_replace($k, $v, $sql);

    // TOP n -> LIMIT n (si corresponde)
    if (preg_match('/SELECT\s+TOP\s+(\d+)/i', $def, $mTop) && !preg_match('/\bLIMIT\s+\d+/i', $sql)) {
        $n = (int)$mTop[1];
        $sql = rtrim($sql, " \t\n\r\0\x0B;") . "\nLIMIT ".$n.";\n";
        return $sql;
    }

    // Concatenaciones con +  -> CONCAT()
    // col + 'literal' + col2
    $sql = preg_replace_callback(
        "/([A-Za-z0-9_\\.]+)\\s*\\+\\s*'([^']*)'\\s*\\+\\s*([A-Za-z0-9_\\.]+)/",
        function($m){ return "CONCAT(".$m[1].", '".$m[2]."', ".$m[3].")"; }, $sql);
    // 'literal' + col
    $sql = preg_replace_callback(
        "/'([^']*)'\\s*\\+\\s*([A-Za-z0-9_\\.]+)/",
        function($m){ return "CONCAT('".$m[1]."', ".$m[2].")"; }, $sql);
    // col + 'literal'
    $sql = preg_replace_callback(
        "/([A-Za-z0-9_\\.]+)\\s*\\+\\s*'([^']*)'/",
        function($m){ return "CONCAT(".$m[1].", '".$m[2]."')"; }, $sql);

    // No agregamos COLLATE al final (sería sintaxis inválida). Si hay COLLATE en el texto, lo reescribimos:
    if ($mysqlCollate && preg_match('/^[A-Za-z0-9_]+$/', $mysqlCollate)) {
        $sql = preg_replace('/\s+COLLATE\s+[A-Za-z0-9_]+/i', ' COLLATE '.$mysqlCollate, $sql);
    } else {
        $sql = preg_replace('/\s+COLLATE\s+[A-Za-z0-9_]+/i', '', $sql);
    }

    // Asegurar ; final
    $sql = rtrim($sql, " \t\n\r\0\x0B;");
    $sql .= ";\n";
    return $sql;
}

$mysql_sql = tsql_to_mysql($tsql_def, $MYSQL_COLLATE);

// ===== Salida a archivos =====
$outdir = __DIR__.'/vistas_out_debug';
if (!is_dir($outdir)) @mkdir($outdir, 0775, true);
$fn_tsql  = $outdir.'/'.$TARGET_VIEW.'.tsql.sql';
$fn_mysql = $outdir.'/'.$TARGET_VIEW.'.mysql.sql';
file_put_contents($fn_tsql,  $tsql_def);
file_put_contents($fn_mysql, $mysql_sql);
out("Archivos generados:");
out(" - ".$fn_tsql);
out(" - ".$fn_mysql);

// ===== Aplicar en MySQL (opcional) =====
if ($APPLY) {
    try {
        $pdoMy = new PDO(
            'mysql:host='.$MY_HOST.';port='.$MY_PORT.';dbname='.$MY_DB.';charset=utf8mb4',
            $MY_USER, $MY_PASS,
            array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
        );
        // Forzar collation de la sesión antes del CREATE VIEW
        $pdoMy->exec("SET NAMES utf8mb4 COLLATE ".$MYSQL_COLLATE);
        $pdoMy->exec("SET collation_connection = '".$MYSQL_COLLATE."'");

        // Nombre de la vista a crear
        $viewName = $TARGET_VIEW;
        if (preg_match('/CREATE\s+OR\s+REPLACE\s+VIEW\s+`([^`]+)`\s+AS/i', $mysql_sql, $m)) {
            $viewName = $m[1];
        }

        $pdoMy->exec("DROP VIEW IF EXISTS `".$viewName."`");
        $pdoMy->exec($mysql_sql);
        out("✅ Vista ".$viewName." aplicada en MySQL (DB ".$MY_DB.") con collation de sesión ".$MYSQL_COLLATE);
    } catch (Exception $e) {
        out("❌ CREATE VIEW error: ".$e->getMessage());
    }
}

out("Listo.");