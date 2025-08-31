<?php
require __DIR__.'/../includes.php';
session_start();
$_SESSION['usuarioLogueadoUser'] = 'jony';
$_SESSION['usuarioLogueadoPass'] = Funciones::toSHA1('Route667');
$_SESSION['empresa'] = '1';
echo "OK, session lista\n";
