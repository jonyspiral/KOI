<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "SIMPLE TEST START\n";
$changed = chdir('content/administracion/proveedores/gestion_proveedores');
echo "CWD changed: " . ($changed ? "YES" : "NO") . " - Current CWD: " . getcwd() . "\n";
require('buscar.php');
echo "\nSIMPLE TEST END\n";
