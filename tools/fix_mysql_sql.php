<?php
// PHP 5.6 compatible
error_reporting(E_ALL ^ E_NOTICE);

$path = isset($argv[1]) ? $argv[1] : null;
if (!$path) { fwrite(STDERR, "usage: php fix_mysql_sql.php <file>\n"); exit(2); }
if (!file_exists($path)) { fwrite(STDERR, "file not found: $path\n"); exit(3); }

$sql = file_get_contents($path);

// 0) normalizar saltos y trims
$sql = str_replace("\r\n", "\n", $sql);
$sql = trim($sql);

// 1) remover comentarios -- y /* ... */
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// 2) arreglar keywords pegados (delante y detrás)
$kw = '(SELECT|FROM|WHERE|GROUP|ORDER|BY|HAVING|JOIN|INNER|LEFT|RIGHT|FULL|OUTER|ON|AS|UNION|CREATE|VIEW|REPLACE|LIMIT)';
$sql = preg_replace('/\b'.$kw.'([A-Za-z_`(])/i', '$1 $2', $sql);     // SELECTc  -> SELECT c
$sql = preg_replace('/([A-Za-z0-9_`)])'.$kw.'\b/i', '$1 $2', $sql);   // hINNER   -> h INNER

// casos comunes adicionales
$sql = preg_replace('/\bAS\s*SELECT\b/i', 'AS SELECT', $sql);
$sql = preg_replace('/\bFROM\s*SELECT\b/i', 'FROM SELECT', $sql);

// 3) reemplazos T-SQL -> MySQL
// CAST VARCHAR -> CHAR
$sql = preg_replace('/CAST\(([^)]+)\s+AS\s+VARCHAR\((\d+)\)\)/i', 'CAST($1 AS CHAR($2))', $sql);
$sql = preg_replace('/CAST\(([^)]+)\s+AS\s+VARCHAR\)/i', 'CAST($1 AS CHAR)', $sql);
// ISNULL -> IFNULL
$sql = str_ireplace('ISNULL(', 'IFNULL(', $sql);
// GETDATE() -> NOW()
$sql = preg_replace('/\bGETDATE\(\)/i', 'NOW()', $sql);
// dbo. ->
$sql = preg_replace('/\bdbo\./i', '', $sql);

// 4) concatenación con +  -> CONCAT()
$sql = preg_replace_callback(
    "/([A-Za-z0-9_`.]+)\\s*\\+\\s*'([^']*)'\\s*\\+\\s*([A-Za-z0-9_`.]+)/",
    function($m){ return "CONCAT(".$m[1].", '".$m[2]."', ".$m[3].")"; },
    $sql
);
$sql = preg_replace_callback(
    "/'([^']*)'\\s*\\+\\s*([A-Za-z0-9_`.]+)/",
    function($m){ return "CONCAT('".$m[1]."', ".$m[2].")"; },
    $sql
);
$sql = preg_replace_callback(
    "/([A-Za-z0-9_`.]+)\\s*\\+\\s*'([^']*)'/",
    function($m){ return "CONCAT(".$m[1].", '".$m[2]."')"; },
    $sql
);

// 5) TOP n -> LIMIT n (al final)
if (preg_match('/\bSELECT\s+TOP\s+(\d+)\s+/i', $sql, $m)) {
    $limit = $m[1];
    $sql = preg_replace('/\bSELECT\s+TOP\s+\d+\s+/i', 'SELECT ', $sql, 1);
    // agregar LIMIT solo si no existe ya
    if (!preg_match('/\bLIMIT\s+\d+/i', $sql)) {
        $sql = rtrim($sql, " \t\n\r;") . " \nLIMIT " . $limit . ";";
    }
}

// 6) brackets -> backticks
$sql = str_replace(array('[',']'), '`', $sql);

// 7) normalizar encabezado CREATE VIEW
// obtener nombre desde "CREATE ... VIEW <name>"
if (preg_match('/\bCREATE\s+(OR\s+REPLACE\s+)?\s*VIEW\s+`?([A-Za-z0-9_\.]+)`?\s*/i', $sql, $mName)) {
    $viewName = $mName[2];
} else {
    // fallback: usar filename sin extension
    $base = basename($path);
    $viewName = preg_replace('/\.mysql\.sql$/i', '', $base);
    $viewName = preg_replace('/\.sql$/i', '', $viewName);
}

// remover "CREATE ... VIEW ..." previo y dejar limpio
$sql = preg_replace('/\bCREATE\s+(OR\s+REPLACE\s+)?\s*VIEW\s+`?[A-Za-z0-9_\.]+`?\s*/i', '', $sql);

// asegurar "AS" antes del SELECT
$sql = preg_replace('/^\s*AS\s*/i', '', $sql);
$sql = ltrim($sql);

// si no empieza con SELECT, forzarlo (muchos files arrancan en SELECT ya)
if (!preg_match('/^SELECT\b/i', $sql)) {
    $sql = 'SELECT ' . preg_replace('/^\s*(SELECT)?\s*/i', '', $sql);
}

// terminar con punto y coma
$sql = rtrim($sql, " \t\n\r;");
$sql = "CREATE OR REPLACE VIEW `".$viewName."` AS\n".$sql.";\n";

// limpieza final de espacios
$sql = preg_replace("/[ \t]+/", " ", $sql);
$sql = preg_replace("/\n{3,}/", "\n\n", $sql);
$sql = trim($sql) . "\n";

file_put_contents($path, $sql);
echo "✅ fixed: ".$path."\n";