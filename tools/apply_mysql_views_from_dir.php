<?php
// USO:
//   MYSQL_HOST=127.0.0.1 MYSQL_USER=root MYSQL_PASS=xxx MYSQL_DB=koi1_stage \
//   php /var/www/encinitas/tools/apply_mysql_views_from_dir.php /var/www/encinitas/tools/vistas_out

$dir = isset($argv[1]) ? $argv[1] : null;
if (!$dir || !is_dir($dir)) {
  fwrite(STDERR, "uso: php apply_mysql_views_from_dir.php <directorio>\n");
  exit(2);
}

// ---- credenciales desde variables de entorno ----
$host = getenv("MYSQL_HOST"); $user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASS"); $db   = getenv("MYSQL_DB");
$coll = getenv("MYSQL_COLLATE") ? getenv("MYSQL_COLLATE") : "utf8mb4_0900_as_ci";

if (!$host || !$user || !$db) {
  fwrite(STDERR, "Faltan vars: MYSQL_HOST / MYSQL_USER / MYSQL_DB (y opcional MYSQL_PASS, MYSQL_COLLATE)\n");
  exit(3);
}

// ---- conectar mysqli (PHP 5.6) ----
$mysqli = @new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) { fwrite(STDERR, "MySQL connect error: ".$mysqli->connect_error."\n"); exit(4); }
$mysqli->set_charset("utf8mb4");
$mysqli->query("SET collation_connection='".$mysqli->real_escape_string($coll)."'");

// helper para limpiar y asegurar "CREATE OR REPLACE VIEW"
function normalize_sql($sql){
  // quitar comentarios de línea "-- ..."
  $sql = preg_replace('/--.*$/m', '', $sql);
  // quitar "GO" suelto
  $sql = preg_replace('/^\s*GO\s*$/mi', '', $sql);
  // asegurar OR REPLACE
  $sql = preg_replace('/\bCREATE\s+VIEW\b/i', 'CREATE OR REPLACE VIEW', $sql, 1);
  // sacar "dbo." si quedó
  $sql = preg_replace('/\bdbo\./i', '', $sql);
  // recortar espacios y asegurar ";" final
  $sql = trim($sql);
  if (substr($sql, -1) !== ';') $sql .= ';';
  return $sql;
}

// procesar *.mysql.sql
$files = glob(rtrim($dir,'/').'/*.mysql.sql');
sort($files);

$ok = 0; $err = 0;
foreach ($files as $f){
  $raw = @file_get_contents($f);
  if ($raw === false) { echo "⚠️ no pude leer: $f\n"; $err++; continue; }
  $sql = normalize_sql($raw);

  // ejecutar (puede traer varias sentencias cortas, pero normalmente es 1)
  if (!$mysqli->multi_query($sql)) {
    echo "❌ ERROR en ".basename($f).": ".$mysqli->error."\n";
    $err++; 
    continue;
  }
  // drenar resultados por si hay más de 1
  while ($mysqli->more_results() && $mysqli->next_result()) { /* noop */ }

  echo "✅ aplicada: ".basename($f)."\n";
  $ok++;
}

echo "----\nAplicadas OK: $ok\nErrores: $err\n";
if ($err>0) exit(1);