<?php
ini_set('display_errors',1); error_reporting(E_ALL);
session_start();

$_SESSION['usuario_id']     = 1;
$_SESSION['usuario']        = 'dev';
$_SESSION['usuario_nombre'] = 'DEV';
$_SESSION['empresa']        = 'KOI';
$_SESSION['isAdmin']        = true;

if (!isset($_SESSION['permisos'])) $_SESSION['permisos'] = [];
$keys = [
  'comercial/reportes/predespachos',
  'comercial/reportes/predespachos/',
  'comercial/reportes/predespachos/index',
  'comercial/reportes/predespachos/index/',
  'comercial/reportes/predespachos/buscar',
  'comercial/reportes/predespachos/buscar/',
  'comercial/reportes/predespachos/getPdf',
  'comercial/reportes/predespachos/getPdf/',
  'comercial/reportes/predespachos/getXls',
  'comercial/reportes/predespachos/getXls/',
];
foreach ($keys as $k) { $_SESSION['permisos'][$k] = true; }

header('Content-Type: text/plain; charset=utf-8');
echo "DEV login OK\n";
print_r(array_keys($_SESSION));
