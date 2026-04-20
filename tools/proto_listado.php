<?php header("Content-Type: text/plain; charset=utf-8"); ?>
<?php
require __DIR__.'/../factory/Factory.php';
$db = Factory::getInstance()->db();

$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $tabla)) {
  http_response_code(400); die('tabla invĂˇlida');
}
$schema = (new ReflectionClass('Config'))->getConstant('mysql_db');

// Validar existencia segura
$exists = $db->value("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", [$schema, $tabla]);
if (!$exists) { http_response_code(404); die('tabla no existe'); }

// Traer 10 filas
$rows = $db->query("SELECT * FROM `{$tabla}` LIMIT 10");
header('Content-Type: text/plain; charset=utf-8');
echo "Tabla: {$schema}.{$tabla}\n";
foreach ($rows as $i => $r) {
  echo str_repeat('-', 20)." #".($i+1)." ".str_repeat('-', 20)."\n";
  foreach ($r as $k=>$v) { echo $k.": ".(is_null($v)?'NULL':$v)."\n"; }
}
