<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/../includes.php';
session_start();

try {
  // Si vienen credenciales por POST, esto intenta loguear
  UsuarioLogin::login();

  $u = Usuario::logueado();

  header('Content-Type: text/plain; charset=utf-8');
  echo $u ? "OK HTTP: {$u->id}\n" : "SIN_USUARIO_HTTP\n";
  echo "SID: ".session_id()."\n";
  echo "POST: "; var_export($_POST); echo "\n";
  echo "toSHA1(pass): ", isset($_POST['pass'])?Funciones::toSHA1($_POST['pass']):'(sin pass)', "\n";
  echo "SESSION(keys): "; var_export(array_keys($_SESSION)); echo "\n";
  echo "SESSION(snapshot): "; var_export(array_intersect_key($_SESSION, array_flip([
    'empresa','usuarioLogueadoUser','usuarioLogueadoPass'
  ]))); echo "\n";
} catch (Exception $e) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "FAIL HTTP: ".$e->getMessage()."\n";
}
