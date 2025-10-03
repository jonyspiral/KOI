<?php
error_reporting(E_ALL & ~E_NOTICE);

/* ======== CONFIG DESDE ENV ======== */
$TSQL_BIN    = getenv('TSQL_BIN')    ? getenv('TSQL_BIN')    : '/usr/bin/tsql';
$TDSVER      = getenv('TDSVER')      ? getenv('TDSVER')      : '7.1';
$MSSQL_HOST  = getenv('MSSQL_HOST')  ? getenv('MSSQL_HOST')  : '';
$MSSQL_PORT  = getenv('MSSQL_PORT')  ? getenv('MSSQL_PORT')  : '1433';
$MSSQL_USER  = getenv('MSSQL_USER')  ? getenv('MSSQL_USER')  : '';
$MSSQL_PASS  = getenv('MSSQL_PASS')  ? getenv('MSSQL_PASS')  : '';
$MSSQL_DB    = getenv('MSSQL_DB')    ? getenv('MSSQL_DB')    : '';

$MYSQL_HOST  = getenv('MYSQL_HOST')  ? getenv('MYSQL_HOST')  : '';
$MYSQL_USER  = getenv('MYSQL_USER')  ? getenv('MYSQL_USER')  : '';
$MYSQL_PASS  = getenv('MYSQL_PASS')  ? getenv('MYSQL_PASS')  : '';
$MYSQL_DB    = getenv('MYSQL_DB')    ? getenv('MYSQL_DB')    : '';

/* ======== AUX: ejecutar tsql con STDIN ======== */
function tsql_query($cmd, $env, $sql) {
  $descriptorspec = array(
    0 => array('pipe', 'w'), // stdin
    1 => array('pipe', 'r'), // stdout
    2 => array('pipe', 'r'), // stderr
  );
  $proc = proc_open($cmd, $descriptorspec, $pipes, NULL, $env);
  if (!is_resource($proc)) return array('', 'no se pudo abrir tsql', 1);
  fwrite($pipes[0], $sql);
  fclose($pipes[0]);
  $out = stream_get_contents($pipes[1]); fclose($pipes[1]);
  $err = stream_get_contents($pipes[2]); fclose($pipes[2]);
  $code = proc_close($proc);
  return array($out, $err, $code);
}

/* ======== limpiar salida de tsql/sp_helptext ======== */
function clean_tsql_output($s) {
  $s = str_replace("\r\n", "\n", $s);
  $lines = preg_split("/\n/", $s);
  $keep = array();
  foreach ($lines as $ln) {
    $t = trim($ln);
    if ($t === '') continue;
    if (preg_match('/^\(\d+\s+rows?\s+affected\)$/i', $t)) continue;
    if (preg_match('/^locale (is|charset is)|^using default charset/i', $t)) continue;
    if (preg_match('/^Default database being set to|^Setting .* default database/i', $t)) continue;
    if ($t === 'text' || preg_match('/^-{3,}$/', $t)) continue;
    if (preg_match('/^(\d+>\s*)+$/', $t)) continue;    // "1> 2> ..." prompts
    if (preg_match('/^\s*GO\s*$/i', $t)) continue;
    $keep[] = $ln;
  }
  $out = trim(implode("\n", $keep));
  return $out;
}

/* ======== T-SQL -> MySQL (transformación mínima, segura) ======== */
function tsql_to_mysql_body($view, $tsql) {
  $src = $tsql;
  // quitar encabezado CREATE VIEW dbo.<view> AS (si viniera)
  $pat = '/^\s*create\s+view\s+(?:\[?dbo\]?(?:\.|\\\.`)?\s*)?\[?'.preg_quote($view,'/').'\]?\s+as\s*/i';
  $src = preg_replace($pat, '', $src);

  // corchetes y dbo.
  $src = preg_replace('/\[(.*?)\]/', '$1', $src);
  $src = preg_replace('/\bdbo\./i', '', $src);

  // TOP n [PERCENT]  -> quitar del SELECT y agregar LIMIT n
  if (preg_match('/\bselect\s+top\s+(\d+)\s*(percent)?\s+/i', $src, $m)) {
    $n = intval($m[1]);
    $src = preg_replace('/\bselect\s+top\s+\d+\s*(percent)?\s+/i', 'SELECT ', $src);
    $src = rtrim($src, " \t\n\r;")."\nLIMIT ".$n;
  }

  // funciones básicas
  $src = preg_replace('/\bISNULL\s*\(/i', 'IFNULL(', $src);
  $src = preg_replace('/\bGETDATE\s*\(\s*\)/i', 'NOW()', $src);

  // CONVERT(VARCHAR(n), x) -> CAST(x AS CHAR(n))  (muy básico)
  $src = preg_replace('/CONVERT\s*\(\s*VARCHAR\s*\(\s*(\d+)\s*\)\s*,\s*([^)]+?)\)/i', 'CAST($2 AS CHAR($1))', $src);
  $src = preg_replace('/CONVERT\s*\(\s*VARCHAR\s*\s*,\s*([^)]+?)\)/i', 'CAST($1 AS CHAR)', $src);

  // limpiar ; duplicados
  $src = trim($src);
  $src = preg_replace('/;\s*$/', '', $src);

  return $src;
}

/* ======== aplicar en MySQL ======== */
function apply_mysql($host,$user,$pass,$db,$sql,$view){
  $mysqli = @new mysqli($host, $user, $pass, $db);
  if ($mysqli->connect_errno) {
    echo "❌ error en $view: ".$mysqli->connect_error."\n";
    return false;
  }
  $mysqli->set_charset('utf8mb4');
  if (!$mysqli->query($sql)) {
    echo "❌ error en $view: ".$mysqli->error."\n";
    $mysqli->close();
    return false;
  }
  $mysqli->close();
  echo "✅ aplicada: $view\n";
  return true;
}

/* ======== MAIN ======== */
$views = array_slice($argv, 1);
if (!$views) {
  fwrite(STDERR, "Uso: php extract_transform_apply_one.php <view1> [view2 ...]\n");
  exit(2);
}

$cmd = escapeshellcmd($TSQL_BIN)
     .' -H '.escapeshellarg($MSSQL_HOST)
     .' -p '.escapeshellarg($MSSQL_PORT)
     .' -U '.escapeshellarg($MSSQL_USER)
     .' -P '.escapeshellarg($MSSQL_PASS)
     .' -D '.escapeshellarg($MSSQL_DB);

$env = array_merge($_ENV, array('TDSVER' => $TDSVER));

$ok = 0; $err = 0;
foreach ($views as $view) {
  // 1) extraer tal cual
  $qry = "set nocount on\ngo\nexec sp_helptext 'dbo.$view'\ngo\n";
  list($out,$stderr,$code) = tsql_query($cmd, $env, $qry);
  $raw = clean_tsql_output($out."\n".$stderr);
  if (!$raw) {
    echo "❌ error en $view: no se pudo extraer definición\n";
    $err++; continue;
  }

  // 2) transformar
  $body = tsql_to_mysql_body($view, $raw);
  if (!$body) {
    echo "❌ error en $view: cuerpo vacío tras transformar\n";
    $err++; continue;
  }
  $final = "CREATE OR REPLACE VIEW `".$view."` AS\n".$body.";";
  // 3) aplicar
  if (apply_mysql($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB,$final,$view)) {
    $ok++;
  } else {
    $err++;
  }
}
echo "----\nAplicadas OK: $ok\nErrores: $err\n";