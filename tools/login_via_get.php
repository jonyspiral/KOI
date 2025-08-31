<?php
require __DIR__.'/../includes.php'; session_start();
if (isset($_GET['user'], $_GET['pass'])) {
  try {
    UsuarioLogin::login($_GET['user'], Funciones::toSHA1($_GET['pass']));
    echo "OK\n";
  } catch (Exception $e) { echo "FAIL: ".$e->getMessage()."\n"; }
} else { echo "NOARGS\n"; }
