<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ob_implicit_flush(true);

$LOG = '/tmp/trace_login.log';
function T($msg){ file_put_contents($GLOBALS['LOG'], date('H:i:s.u ').$msg."\n", FILE_APPEND); }

header('Content-Type: text/plain; charset=utf-8');
echo "TRACE_LOGIN start\n"; T("== start ==");

T("require includes.php ...");
require __DIR__.'/../includes.php';
T("includes OK");

T("session_start ...");
session_start();
T("session_start OK sid=".session_id());
echo "sid=".session_id()."\n";

T("_POST: ".json_encode($_POST));
echo "_POST: ".json_encode($_POST)."\n";

try {
  T("UsuarioLogin::login() enter");
  UsuarioLogin::login();                    // usa $_POST si viene
  T("UsuarioLogin::login() return");

  $u = Usuario::logueado();
  T("Usuario::logueado=".($u? $u->id : 'NULL'));
  echo $u ? "OK {$u->id}\n" : "SIN_USUARIO\n";

  echo "SESSION_KEYS: ".implode(',', array_keys($_SESSION))."\n";
  T("SESSION_KEYS: ".implode(',', array_keys($_SESSION)));
} catch (Exception $e) {
  T("EX: ".$e->getMessage());
  echo "EX: ".$e->getMessage()."\n";
}

register_shutdown_function(function(){
  $e = error_get_last();
  if ($e) T("SHUTDOWN: [{$e['type']}] {$e['message']} @ {$e['file']}:{$e['line']}");
});
