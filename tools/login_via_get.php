<?php


// Asegurar empresa en sesión (1 o 2; default 1)
if (empty($_SESSION['empresa'])) {
    $emp = isset($_GET['empresa']) && in_array($_GET['empresa'], ['1','2'], true) ? $_GET['empresa'] : '1';
    $_SESSION['empresa'] = $emp;
}

// (opcional) tipo de usuario esperado por la app
if (empty($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'cliente';
}



require __DIR__.'/../includes.php'; session_start();
if (isset($_GET['user'], $_GET['pass'])) {
  try {
    UsuarioLogin::login($_GET['user'], Funciones::toSHA1($_GET['pass']));
    echo "OK\n";
  } catch (Exception $e) { echo "FAIL: ".$e->getMessage()."\n"; }
} else { echo "NOARGS\n"; }
