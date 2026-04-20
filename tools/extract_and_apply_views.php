<?php
// PHP 5.6 compatible (sin sintaxis modernas)
error_reporting(E_ALL & ~E_NOTICE);

function env($k, $def=""){ $v=getenv($k); return ($v===false)?$def:$v; }

$MSSQL_HOST = env("MSSQL_HOST");
$MSSQL_PORT = env("MSSQL_PORT","1433");
$MSSQL_USER = env("MSSQL_USER");
$MSSQL_PASS = env("MSSQL_PASS");
$MSSQL_DB   = env("MSSQL_DB");
$TSQL_BIN   = env("TSQL_BIN","/usr/bin/tsql");
$TDSVER     = env("TDSVER","7.1");

$OUT_DIR    = env("KOI_DIR","/var/www/encinitas/tools") . "/vistas_batch";
if (!is_dir($OUT_DIR)) mkdir($OUT_DIR, 0777, true);

// MySQL via apply_mysql_views_from_dir.php usa las envs MYSQL_*
$views = $argv;
array_shift($views); // script name

if (count($views)==0) {
  fwrite(STDERR, "Uso: php extract_and_apply_views.php <VIEW> [<VIEW>...]\n");
  exit(1);
}

$ok = 0; $err = 0;

foreach ($views as $VIEW) {
  // 1) Extraer en bruto desde SQL Server
  $cmd  = "TDSVER=".escapeshellarg($TDSVER)." ".
          escapeshellcmd($TSQL_BIN)." ".
          " -H ".escapeshellarg($MSSQL_HOST).
          " -p ".escapeshellarg($MSSQL_PORT).
          " -U ".escapeshellarg($MSSQL_USER).
          " -P ".escapeshellarg($MSSQL_PASS).
          " -D ".escapeshellarg($MSSQL_DB)." <<'SQL'\n".
          "set nocount on\n".
          "go\n".
          "SELECT c.text\n".
          "FROM sysobjects so\n".
          "JOIN sysusers  su ON su.uid = so.uid\n".
          "JOIN syscomments c ON c.id  = so.id\n".
          "WHERE so.xtype = 'V'\n".
          "  AND su.name  = 'dbo'\n".
          "  AND so.name  = '".addslashes($VIEW)."'\n".
          "ORDER BY c.colid\n".
          "go\n".
          "SQL";

  $raw = shell_exec($cmd);

  if ($raw===NULL || $raw==="") {
    echo "❌ error extrayendo $VIEW (sin salida)\n";
    $err++; continue;
  }

  // 2) Limpieza de ruido de tsql
  //    - líneas con prompts "1> 2> ..." y "locale is ..." y "(n rows affected)"
  //    - quitar GO sueltos y normalizar EOL
  $lines = preg_split("/\\r?\\n/", $raw);
  $clean = array();
  foreach($lines as $ln){
    $t = trim($ln);
    if ($t==="") continue;
    if (preg_match('/^\\d+>(\\s*\\d+>)*$/', $t)) continue; // prompts "1> 2> ..."
    if (stripos($t, "locale is ")===0) continue;
    if (stripos($t, "locale charset is ")===0) continue;
    if (stripos($t, "using default charset ")===0) continue;
    if (preg_match('/^\\(\\d+ rows? affected\\)$/i', $t)) continue;
    if (preg_match('/^GO$/i', $t)) continue;
    if (preg_match('/^Default database being set to /', $t)) continue;
    $clean[] = $ln;
  }
  $raw2 = implode("\n", $clean);

  // 3) Quitar encabezado viejo CREATE VIEW ... AS (si viniera)
  $def = preg_replace(
    '/^\\s*CREATE\\s+VIEW\\s+(\\[?dbo\\]?\\.)?\\[?'.preg_quote($VIEW,'/').'\\]?\\s+AS\\s*/i',
    '',
    $raw2
  );

  // 4) Quitar corchetes y dbo.
  $def = preg_replace('/\\[(.*?)\\]/', '$1', $def);
  $def = preg_replace('/\\bdbo\\./i', '', $def);

  // 5) Construir archivo final
  $sql = "CREATE OR REPLACE VIEW ".$VIEW." AS\n".$def;
  // asegurar ; final
  if (!preg_match('/;\\s*$/', $sql)) $sql .= ";\n";

  $file = $OUT_DIR . "/" . $VIEW . ".mysql.sql";
  file_put_contents($file, $sql);

  // 6) Aplicar SOLO este archivo
  $dir = $OUT_DIR;
  $cmdApply = "php /var/www/encinitas/tools/apply_mysql_views_from_dir.php ".
              escapeshellarg($dir)." ".escapeshellarg($VIEW.".mysql.sql");
  exec($cmdApply, $out, $rc);

  if ($rc===0) {
    echo "✅ aplicada: $VIEW\n";
    $ok++;
  } else {
    // tomar último mensaje de error de apply_
    $msg = implode("\n", $out);
    if ($msg==="") $msg = "Error aplicando (sin mensaje)\n";
    // El apply ya imprime motivo MySQL, mostramos resumen
    echo "❌ error en $VIEW\n";
    $err++;
  }
}

echo "----\nAplicadas OK: $ok\nErrores: $err\n";