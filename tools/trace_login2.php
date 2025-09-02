<?php
ini_set('display_errors',1); error_reporting(E_ALL); ini_set('log_errors',1);
header('Content-Type: text/plain; charset=utf-8');

$LOG = '/tmp/trace_login2.log';
$T = function($m){ file_put_contents($GLOBALS['LOG'], date('H:i:s ').$m."\n", FILE_APPEND); };

register_shutdown_function(function() use ($T){
  $e = error_get_last(); if ($e) $T("SHUTDOWN: [{$e['type']}] {$e['message']} @ {$e['file']}:{$e['line']}");
});

echo "TRACE2 start\n"; $T("== start ==");

$T("require includes.php"); require __DIR__.'/../includes.php';
$T("includes OK");

$T("session_start"); session_start(); $T("sid=".session_id());
echo "sid=".session_id()."\n";

$user = isset($_POST['user']) ? $_POST['user'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
$empresa = isset($_POST['empresa']) ? $_POST['empresa'] : null;
echo "_POST: ".json_encode($_POST)."\n"; $T("_POST: ".json_encode($_POST));

try {
  // Paso 1: DB directa contra la vista
  $db = Factory::getInstance()->db();
  $row = $db->queryOne("SELECT id, usuario, password, anulado, tipo FROM UsuarioLogin WHERE id=?", [$user]);
  echo "DB.row: ".json_encode($row)."\n"; $T("DB.row OK");
  $hashIn = Funciones::toSHA1($pass);
  echo "cmp(db, in): ".(isset($row['password']) && $row['password']===$hashIn ? "EQ":"NE")."\n";
  $T("cmp=". (isset($row['password']) && $row['password']===$hashIn ? "EQ":"NE"));

  // Paso 2: flujo normal del framework
  $T("UsuarioLogin::login ENTER");
  UsuarioLogin::login(); // usa $_POST
  $T("UsuarioLogin::login RETURN");

  $u = Usuario::logueado();
  echo $u ? "OK {$u->id}\n" : "SIN_USUARIO\n";
  $T("logueado=".($u?$u->id:'NULL'));

  echo "SESSION_KEYS: ".implode(',', array_keys($_SESSION))."\n";
  $T("SESSION_KEYS: ".implode(',', array_keys($_SESSION)));
} catch (Exception $e) {
  echo "EX: ".$e->getMessage()."\n";
  $T("EX: ".$e->getMessage());
}
if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
// … (usar $_SESSION lo necesario) …
if (session_status() === PHP_SESSION_ACTIVE) { @session_write_close(); }
