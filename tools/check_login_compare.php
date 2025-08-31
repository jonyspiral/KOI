<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/../includes.php';
session_start();

$user = isset($_POST['user']) ? $_POST['user'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

try {
  $obj = Factory::getInstance()->getUsuarioLogin($user); // viene de la vista UsuarioLogin
  $hashPost = Funciones::toSHA1($pass);

  header('Content-Type: text/plain; charset=utf-8');
  echo "user: $user\n";
  echo "db.hash (first10): ".substr((string)$obj->password,0,10)." len=".strlen((string)$obj->password)."\n";
  echo "in.hash (first10): ".substr($hashPost,0,10)." len=".strlen($hashPost)."\n";
  echo "cmp (===): ".(($obj->password === $hashPost)?'EQ':'NE')."\n";
  echo "anulado: ".var_export($obj->anulado,true)." tipo: ".var_export($obj->tipo,true)."\n";
} catch (Exception $e) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "EX: ".$e->getMessage()."\n";
}
