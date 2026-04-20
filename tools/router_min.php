<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/../includes.php';    // carga premaster, autoload, etc.

$p = 'comercial/reportes/predespachos'; // el pagename que querĂ©s
$f = rtrim(Config::pathBase,'/').'/content/'.$p.'.php';

header('Content-Type: text/html; charset=iso-8859-1');
echo "<!-- router_min incluye: $f -->\n";
if (!file_exists($f)) { echo "NO ENCONTRADO: $f"; exit; }
include $f;
