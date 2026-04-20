<?php
// PHP 5.6 compatible
if ($argc < 2) { fwrite(STDERR,"usage: php fix_mysql_sql_v2.php <file.sql>\n"); exit(2); }
$path = $argv[1];
if (!is_file($path)) { fwrite(STDERR,"file not found: $path\n"); exit(1); }

$sql = file_get_contents($path);
if ($sql === false) { fwrite(STDERR,"cannot read: $path\n"); exit(1); }

// normalizar
$sql = str_replace("\r\n","\n",$sql);
$sql = preg_replace('/^\xEF\xBB\xBF/','',$sql); // quitar BOM

// 0) quitar comentarios
$sql = preg_replace('/--.*$/m','',$sql);
$sql = preg_replace('/\/\*.*?\*\//s','',$sql);

// 1) keywords pegadas (ASSELECT, SELECTc, FROMt, JOINx, ONy, etc.)
$sql = preg_replace('/\b(AS|SELECT|FROM|WHERE|JOIN|INNER|LEFT|RIGHT|OUTER|ON|GROUP|ORDER|UNION)(?=[A-Za-z`(])/i', '$1 ', $sql);
$sql = preg_replace('/\bGROUP\s*BY\b/i','GROUP BY',$sql);
$sql = preg_replace('/\bORDER\s*BY\b/i','ORDER BY',$sql);
$sql = preg_replace('/\bUNION\s*ALL\b/i','UNION ALL',$sql);

// 2) quitar dbo. y corchetes
$sql = preg_replace('/\bdbo\./i','',$sql);
$sql = str_replace(array('[',']'), '`', $sql);

// 3) funciones T-SQL -> MySQL
$sql = str_ireplace('ISNULL(', 'IFNULL(', $sql);
$sql = preg_replace('/\bGETDATE\(\)/i','NOW()',$sql);

// CAST/CONVERT
$sql = preg_replace('/CAST\(([^)]+)\s+AS\s+VARCHAR\((\d+)\)\)/i', 'CAST($1 AS CHAR($2))', $sql);
$sql = preg_replace('/CAST\(([^)]+)\s+AS\s+VARCHAR\)/i',        'CAST($1 AS CHAR)', $sql);
$sql = preg_replace('/CONVERT\(\s*VARCHAR\(\d+\)\s*,\s*([^)]+)\)/i', 'CAST($1 AS CHAR)', $sql);
$sql = preg_replace('/CONVERT\(\s*VARCHAR\s*,\s*([^)]+)\)/i',        'CAST($1 AS CHAR)', $sql);
$sql = preg_replace('/CONVERT\(\s*INT\s*,\s*([^)]+)\)/i',            'CAST($1 AS SIGNED)', $sql);
$sql = preg_replace('/CONVERT\(\s*SMALLINT\s*,\s*([^)]+)\)/i',       'CAST($1 AS SIGNED)', $sql);

// 4) concatenaciones con + -> CONCAT()
$sql = preg_replace_callback(
  "/([A-Za-z0-9_`\\.]+)\\s*\\+\\s*'([^']*)'\\s*\\+\\s*([A-Za-z0-9_`\\.]+)/",
  function($m){ return "CONCAT(".$m[1].", '".$m[2]."', ".$m[3].")"; },
  $sql
);
$sql = preg_replace_callback(
  "/'([^']*)'\\s*\\+\\s*([A-Za-z0-9_`\\.]+)/",
  function($m){ return "CONCAT('".$m[1]."', ".$m[2].")"; },
  $sql
);
$sql = preg_replace_callback(
  "/([A-Za-z0-9_`\\.]+)\\s*\\+\\s*'([^']*)'/",
  function($m){ return "CONCAT(".$m[1].", '".$m[2]."')"; },
  $sql
);

// 5) SELECT TOP n -> LIMIT n
if (preg_match('/\bSELECT\s+TOP\s+(\d+)\s+/i', $sql, $m)) {
  $limit = $m[1];
  $sql = preg_replace('/\bSELECT\s+TOP\s+\d+\s+/i','SELECT ',$sql);
  $sql = rtrim($sql, " \t\r\n;");
  $sql .= " LIMIT ".$limit;
}

// 6) asegurar "CREATE OR REPLACE VIEW `nombre` AS SELECT ..."
$sql = preg_replace('/\bCREATE\s+VIEW\s+/i','CREATE OR REPLACE VIEW ', $sql);
$sql = preg_replace('/\bCREATE OR REPLACE VIEW\s+`?dbo`?\./i','CREATE OR REPLACE VIEW ', $sql);
$sql = preg_replace('/\bAS\s*SELECT\b/i','AS SELECT ', $sql); // por si quedó pegado
$sql = preg_replace('/\s+;/',';',$sql); // limpieza menor

// 7) compactar espacios múltiples
$sql = preg_replace("/[ \t]{2,}/", " ", $sql);

// guardar
if (file_put_contents($path,$sql) === false) { fwrite(STDERR,"cannot write: $path\n"); exit(1); }

echo "✅ fixed: ".$path."\n";
