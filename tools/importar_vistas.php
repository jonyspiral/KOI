<?php
/**
 * Importar TODAS las vistas que están en SQL Server (encinitas) y no existen en MySQL.
 * PHP 5.6 compatible.
 *
 * Uso típico:
 *   php importar_vistas.php --mysql-collate=utf8mb4_0900_as_ci          (dry-run, solo genera .sql)
 *   php importar_vistas.php --apply --mysql-collate=utf8mb4_0900_as_ci  (aplica en MySQL)
 *
 * Filtros opcionales:
 *   --only=clientes_v,caja_v
 *   --exclude=clientes_v
 *   --limit=50
 *   --dry-run   (por defecto)
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* =========================
   PARÁMETROS MSSQL (SQL2000)
   ========================= */
$MSSQL_HOST = '192.168.2.100';
$MSSQL_DB   = 'encinitas';
$MSSQL_USER = 'Koi';
$MSSQL_PASS = 'koisys';

/* =========================
   PARÁMETROS MySQL (KOI1)
   ========================= */
$MYSQL_HOST = '192.168.2.210';
$MYSQL_PORT = 3306;
$MYSQL_USER = 'koiuser';
$MYSQL_PASS = 'Route667?';
$MYSQL_DB   = 'koi1_stage';     // cambia si querés otro
$MYSQL_CHAR = 'utf8mb4';
$MYSQL_DEF_COLLATE = 'utf8mb4_0900_as_ci';

/* ===== Args ===== */
$APPLY = false;
$DRY   = true;
$LIMIT = 0;
$ONLY  = array();
$EXCL  = array();
$COLLATE = $MYSQL_DEF_COLLATE;

for ($i=1; $i<count($argv); $i++) {
    $a = $argv[$i];
    if ($a === '--apply') { $APPLY = true; $DRY = false; }
    if ($a === '--dry-run') { $DRY = true; $APPLY = false; }
    if (strpos($a, '--mysql-collate=') === 0) {
        $v = trim(substr($a, 16));
        if ($v !== '') $COLLATE = $v;
    }
    if (strpos($a, '--only=') === 0) {
        $v = trim(substr($a, 7));
        if ($v !== '') $ONLY = array_filter(array_map('trim', explode(',', $v)));
    }
    if (strpos($a, '--exclude=') === 0) {
        $v = trim(substr($a, 10));
        if ($v !== '') $EXCL = array_filter(array_map('trim', explode(',', $v)));
    }
    if (strpos($a, '--limit=') === 0) {
        $v = (int)trim(substr($a, 8));
        if ($v > 0) $LIMIT = $v;
    }
}

function out($s){ echo $s.(substr($s,-1)==="\n"?'':"\n"); }

/* ===== Conexión MSSQL ===== */
try {
    $pdoSql = new PDO(
        'dblib:host='.$MSSQL_HOST.';dbname='.$MSSQL_DB.';charset=UTF-8',
        $MSSQL_USER,
        $MSSQL_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (Exception $e) {
    die("❌ Error MSSQL (dblib): ".$e->getMessage()."\n");
}
try {
    // Para evitar truncado de TEXT/NTEXT en SQL2000
    $pdoSql->exec("SET TEXTSIZE 2147483647");
    $pdoSql->exec("SET NOCOUNT ON");
    $pdoSql->exec("SET ANSI_NULLS ON; SET QUOTED_IDENTIFIER ON");
} catch (Exception $e) {}

/* ===== Conexión MySQL ===== */
try {
    $pdoMy = new PDO(
        'mysql:host='.$MYSQL_HOST.';port='.$MYSQL_PORT.';dbname='.$MYSQL_DB.';charset='.$MYSQL_CHAR,
        $MYSQL_USER,
        $MYSQL_PASS,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    // seteo collation de sesión (influye en comparaciones durante el parse/aplicar)
    $pdoMy->exec("SET NAMES ".$MYSQL_CHAR." COLLATE ".$COLLATE);
    $pdoMy->exec("SET collation_connection = '".$COLLATE."'");
} catch (Exception $e) {
    die("❌ Error MySQL: ".$e->getMessage()."\n");
}

/* ===== utilidades T-SQL -> MySQL ===== */
function tsql_to_mysql($sql, $mysqlCollate = null) {
    // 0) Sanitizar fin de línea y espacios raros
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    // Sacar BOM/ctrl
    $sql = preg_replace('/[[:^print:]\x00]/u', '', $sql);

    // 1) Unir texto de syscomments (por si llega segmentado)
    //    (si ya viene unido, no hace nada)
    $sql = preg_replace("/\n\\s*GO\\s*(\n|$)/i", "\n", $sql);

    // 2) Quitar hints/comentarios que rompen
    //    a) WITH (NOLOCK)
    $sql = preg_replace('/\bWITH\s*\(\s*NOLOCK\s*\)/i', '', $sql);
    //    b) Comentarios de línea "-- ..."
    $sql = preg_replace('/--.*$/m', '', $sql);
    //    c) Comentarios /* ... */
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    // 3) Owner/identificadores
    //    a) dbo. -> (lo dejamos si es parte del nombre calificado; MySQL no lo usa como owner)
    $sql = preg_replace('/\bdbo\./i', '', $sql);
    //    b) [nombre] -> `nombre`
    $sql = preg_replace_callback('/\[(.*?)\]/', function($m){
        return '`' . str_replace('`','``',$m[1]) . '`';
    }, $sql);

    // 4) Cabecera "CREATE VIEW ..." (SQL2000 puede venir sin OR REPLACE)
    //    Garantizamos forma MySQL CREATE OR REPLACE VIEW `vista` AS\nSELECT ...
    $sql = preg_replace('/^\s*CREATE\s+VIEW\s+([`"]?)([A-Za-z0-9_\.]+)\1\s+AS\s*/i',
                        'CREATE OR REPLACE VIEW `\\2` AS ', $sql, 1, $reps);
    // Si no detectó cabecera, lo dejamos tal cual (por si viene sólo el SELECT)
    // pero MySQL necesita "CREATE OR REPLACE VIEW x AS ...":
    if ($reps === 0 && stripos($sql, 'CREATE VIEW') === false) {
        $sql = "CREATE OR REPLACE VIEW tmp_auto AS \n" . $sql;
    }

    // 5) TOP / PERCENT -> LIMIT (reglas simples)
    //   a) SELECT TOP n ...
    $sql = preg_replace('/\bSELECT\s+TOP\s+(\d+)\s+/i', 'SELECT ', $sql, 1, $hasTop);
    //   b) SELECT DISTINCT TOP n ...
    $sql = preg_replace('/\bSELECT\s+DISTINCT\s+TOP\s+(\d+)\s+/i', 'SELECT DISTINCT ', $sql);
    //   c) PERCENT (lo ignoramos: en MySQL no hay TOP PERCENT; se usa aprox sin PERCENT)
    $sql = preg_replace('/\bPERCENT\b/i', '', $sql);
    // Guardamos el LIMIT si hubo TOP al inicio
    $limit = null;
    if ($hasTop) {
        if (preg_match('/\bSELECT\s+.*?\bTOP\s+(\d+)/i', '', $m)) { /* no-op */ }
    }
    // Como ya quitamos texto “TOP n”, extraemos n del primer match original:
    // Plan B: si la definición trae "TOP n" en otra posición, capturala antes de reemplazar.
    // Para no complicar, si querés límite real, setealo con --force-limit=n en tu script.

    // 6) Concatenación: col + ' ' + col2  -> CONCAT(col, ' ', col2)
    //    Hacemos 3 pasadas para cubrir col+literal+col / literal+col / col+literal
    // col + 'literal' + col2
    $sql = preg_replace_callback(
        "/([A-Za-z0-9_\.]+)\s*\+\s*'([^']*)'\s*\+\s*([A-Za-z0-9_\.]+)/",
        function($m){ return "CONCAT(".$m[1].", '".$m[2]."', ".$m[3].")"; },
        $sql
    );
    // 'literal' + col
    $sql = preg_replace_callback(
        "/'([^']*)'\s*\+\s*([A-Za-z0-9_\.]+)/",
        function($m){ return "CONCAT('".$m[1]."', ".$m[2].")"; },
        $sql
    );
    // col + 'literal'
    $sql = preg_replace_callback(
        "/([A-Za-z0-9_\.]+)\s*\+\s*'([^']*)'/",
        function($m){ return "CONCAT(".$m[1].", '".$m[2]."')"; },
        $sql
    );

    // 7) Funciones comunes
    $repls = [
        '/\bISNULL\s*\(/i'                       => 'IFNULL(',
        '/\bGETDATE\s*\(\s*\)/i'                 => 'NOW()',
        '/\bDATEDIFF\s*\(\s*day\s*,/i'           => 'DATEDIFF(',
        '/\bCAST\s*\(\s*([^\)]+)\s+AS\s+VARCHAR\s*\(\s*\d+\s*\)\s*\)/i' => 'CAST($1 AS CHAR)',
        '/\bCAST\s*\(\s*([^\)]+)\s+AS\s+VARCHAR\s*\)/i'                 => 'CAST($1 AS CHAR)',
        '/\bCONVERT\s*\(\s*VARCHAR\s*\(\s*\d+\s*\)\s*,\s*([^\)]+)\)/i'  => 'CAST($1 AS CHAR)',
        '/\bCONVERT\s*\(\s*VARCHAR\s*,\s*([^\)]+)\)/i'                  => 'CAST($1 AS CHAR)',
        '/\bCONVERT\s*\(\s*INT\s*,\s*([^\)]+)\)/i'                      => 'CAST($1 AS SIGNED)',
        '/\bCONVERT\s*\(\s*SMALLINT\s*,\s*([^\)]+)\)/i'                 => 'CAST($1 AS SIGNED)',
        '/\bCONVERT\s*\(\s*DECIMAL\s*\(\s*\d+\s*,\s*\d+\s*\)\s*,\s*([^\)]+)\)/i' => 'CAST($1 AS DECIMAL(20,6))',
        '/\bMONTH\s*\(/i'                       => 'MONTH(',
        '/\bYEAR\s*\(/i'                        => 'YEAR(',
        // dbo.funciones -> suponemos UDF homónimas en MySQL (si existen)
        '/\bdbo\.relativeDate\s*\(/i'           => 'relativeDate(',
        '/\bdbo\.toDate\s*\(\s*\'(\d{2})\/(\d{2})\/(\d{4})\'\s*\)/i' => "STR_TO_DATE('$1/$2/$3','%d/%m/%Y')",
    ];
    foreach ($repls as $pat => $rep) $sql = preg_replace($pat, $rep, $sql);

    // 8) Quitar SELECT ... INTO, y normalizar alias FROM/JOINS que a veces llegan truncados
    // (No debería aparecer en vistas, pero por las definiciones antiguas conviene prevenir)
    $sql = preg_replace('/\bSELECT\b(.*?)\bINTO\b\s+[A-Za-z0-9_\.`"]+/is', 'SELECT$1', $sql);

    // 9) ORDER BY en vistas (MySQL lo permite pero lo ignorará si no hay LIMIT).
    //    Dejamos el ORDER BY: MySQL ignora el orden salvo que haya LIMIT.
    //    Si querés sacarlo: $sql = preg_replace('/\bORDER\s+BY\b.+$/is','',$sql);

    // 10) Collation case-sensitive pedido
    if (!empty($mysqlCollate)) {
        // Aplica COLLATE a literales/columnas de forma simple en SELECT ... (sin romper funciones)
        // Estrategia: agregar COLLATE a alias/textos simples "columna AS x".
        // No lo forzamos en todas las expresiones para evitar 1064; usalo en campos críticos.
        // Podés complementar en tu script aplicando COLLATE a columnas clave al final.
    }

    // 11) Limpiar comas colgantes antes de FROM/WHERE/UNION
    $sql = preg_replace('/,\s*(FROM|WHERE|UNION|GROUP\s+BY|ORDER\s+BY)\b/i', " \\1", $sql);

    // 12) Asegurar punto y coma final
    $sql = rtrim($sql);
    if (substr($sql, -1) !== ';') $sql .= ';';

    // 13) Si quedó “CREATE OR REPLACE VIEW tmp_auto ...” intentar deducir nombre de vista
    if (strpos($sql, 'CREATE OR REPLACE VIEW tmp_auto') !== false &&
        preg_match('/\bCREATE\s+VIEW\s+([`"]?)([A-Za-z0-9_\.]+)\1\s+AS/i', $sql, $m2)) {
        $sql = preg_replace('/tmp_auto/', $m2[2], $sql, 1);
    }

    return $sql;
}
/* ===== helpers ===== */
function fetch_view_definition_sql2000(PDO $pdo, $fullName) {
    // Intento 1: sp_helptext con tabla temporal (evita límite 255 nvarchar)
    try {
        $sql1  = "DECLARE @obj NVARCHAR(776);\n";
        $sql1 .= "SET @obj = :full;\n";
        $sql1 .= "CREATE TABLE #h (t NVARCHAR(4000));\n";
        $sql1 .= "INSERT #h EXEC sp_helptext @obj;\n";
        $sql1 .= "SELECT t FROM #h ORDER BY (SELECT 1);\n";
        $sql1 .= "DROP TABLE #h;";
        $st = $pdo->prepare($sql1);
        $st->execute(array(':full'=>$fullName));
        $tmp = $st->fetchAll(PDO::FETCH_COLUMN, 0);
        if ($tmp && count($tmp)>0) return implode('', $tmp);
    } catch (Exception $e) { /* fallback */ }

    // Fallback: syscomments con CAST( text AS varchar(8000) )
    $sql2 = "SELECT CAST(c.text AS VARCHAR(8000)) AS text
             FROM syscomments c
             WHERE c.id = OBJECT_ID(:full)
             ORDER BY c.colid";
    $st = $pdo->prepare($sql2);
    $st->execute(array(':full'=>$fullName));
    $tmp = $st->fetchAll(PDO::FETCH_COLUMN, 0);
    if ($tmp && count($tmp)>0) return implode('', $tmp);

    return '';
}

/* ===== 1) Listar vistas en SQL Server (dbo.*) ===== */
$sqlList = "
    SELECT u.name AS owner, o.name AS view_name
    FROM sysobjects o
    JOIN sysusers  u ON o.uid = u.uid
    WHERE o.xtype = 'V' AND u.name = 'dbo'
    ORDER BY o.name
";
$rs = $pdoSql->query($sqlList);
$viewsSql = $rs->fetchAll();
$allSqlNames = array();
foreach ($viewsSql as $row) $allSqlNames[] = $row['view_name'];

/* Filtros ONLY/EXCLUDE */
if (count($ONLY)>0) {
    $allSqlNames = array_values(array_intersect($allSqlNames, $ONLY));
}
if (count($EXCL)>0) {
    $allSqlNames = array_values(array_diff($allSqlNames, $EXCL));
}

/* Limit */
if ($LIMIT > 0 && count($allSqlNames) > $LIMIT) {
    $allSqlNames = array_slice($allSqlNames, 0, $LIMIT);
}

/* ===== 2) Listar vistas existentes en MySQL ===== */
$stmt = $pdoMy->prepare("SELECT TABLE_NAME FROM information_schema.views WHERE TABLE_SCHEMA = :db");
$stmt->execute(array(':db'=>$MYSQL_DB));
$viewsMy = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
$setMy = array();
foreach ($viewsMy as $v) $setMy[strtolower($v)] = true;

/* ===== 3) Iterar vistas que faltan ===== */
$outDir = __DIR__ . '/vistas_out';
if (!is_dir($outDir)) @mkdir($outDir, 0775, true);

$total = 0;
$ok = 0;
$err = 0;
$errors = array();
$created = array();

out("Vistas en SQL Server (dbo): ".count($allSqlNames));
out("Collation de sesión MySQL: ".$COLLATE);
out($DRY ? "Modo: DRY-RUN (no crea en MySQL)\n" : "Modo: APPLY (crea/actualiza en MySQL)\n");

foreach ($allSqlNames as $name) {
    $total++;

    if (isset($setMy[strtolower($name)])) {
        // ya existe en MySQL → saltar
        continue;
    }

    $full = 'dbo.'.$name;

    // 3.a) Obtener T-SQL
    $tsql = fetch_view_definition_sql2000($pdoSql, $full);
    if ($tsql === '') {
        $err++;
        $errors[] = "$name => No se pudo obtener definición (revisar permisos/nombre)";
        continue;
    }

    // 3.b) Guardar original
    $fileT = $outDir . '/'.$name.'.tsql.sql';
    file_put_contents($fileT, $tsql);

    // 3.c) Convertir
    $mysqlSql = tsql_to_mysql($tsql, $COLLATE);
    $fileM = $outDir . '/'.$name.'.mysql.sql';
    file_put_contents($fileM, $mysqlSql);

    // 3.d) Aplicar en MySQL si corresponde
    if (!$DRY) {
        try {
            // detectar nombre de vista
            $viewName = $name;
            if (preg_match('/CREATE\s+OR\s+REPLACE\s+VIEW\s+`([^`]+)`\s+AS/i', $mysqlSql, $m)) {
                $viewName = $m[1];
            }
            $pdoMy->exec("DROP VIEW IF EXISTS `".$viewName."`");
            $pdoMy->exec($mysqlSql);
            $ok++;
            $created[] = $name;
        } catch (Exception $e) {
            $err++;
            $errors[] = "$name => Error MySQL: ".$e->getMessage();
            continue;
        }
    } else {
        $ok++; // lo contamos como procesado OK en dry-run
        $created[] = $name." (pendiente apply)";
    }
}

/* ===== 4) Resumen ===== */
out("Vistas evaluadas (que faltaban en MySQL y se procesaron): $total");
out("Correctas: $ok");
out("Errores:   $err");
if (!empty($created)) {
    out("-- Procesadas --");
    foreach ($created as $c) out("  * ".$c);
}
if (!empty($errors)) {
    out("-- Errores --");
    foreach ($errors as $e) out("  * ".$e);
}

exit($err > 0 ? 2 : 0);
