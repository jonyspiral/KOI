<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
session_name('KOI1'); session_start();  // cambiaremos KOI1 si tu app usa otro nombre

// Semilla opcional (por si el módulo exige permiso/sesión)
$_SESSION['permisos']['comercial/reportes/predespachos/buscar'] = true;

$_GET['pagename'] = 'comercial/reportes/predespachos';
ob_start();
include __DIR__.'/../master.php';
$out = ob_get_clean();

header('Content-Type: text/html; charset=iso-8859-1');
echo $out;
