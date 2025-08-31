<?php
ini_set('display_errors',1); error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../includes.php';
session_start();

try {
  $obj   = Factory::getInstance()->getUsuarioLogin('jony');
  $hIn   = Funciones::toSHA1('Route667');

  echo "id: ", (isset($obj->id)?$obj->id:'(null)'), "\n";
  echo "db.pwd.len: ", (isset($obj->password)?strlen((string)$obj->password):0), "\n";
  echo "db.pwd.10 : ", substr((string)$obj->password,0,10), "\n";
  echo "in.hash.10: ", substr($hIn,0,10), "\n";
  echo "cmp(===)  : ", (isset($obj->password) && $obj->password === $hIn ? "EQ" : "NE"), "\n";
} catch (Exception $e) {
  echo "EX: ", $e->getMessage(), "\n";
}
