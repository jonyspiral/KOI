<?php
require_once('premaster.php');
echo "HELLO BO TEST 3\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Mock user login - get first admin user
    $db = Factory::getInstance()->db();
    $userRows = $db->query("SELECT * FROM usuarios LIMIT 1");
    if (!$userRows || count($userRows) === 0) die("No users found");
    $userRow = $userRows[0];
    
    UsuarioLogin::login($userRow['cod_usuario'], $userRow['password']);
    echo "Is Usuario logged in? " . (Usuario::logueado() ? "YES" : "NO") . "\n";
    echo "Is UsuarioLogin logged in? " . (UsuarioLogin::logueado() ? "YES" : "NO") . "\n";
    if (Usuario::logueado()) {
        echo "Logged user class: " . get_class(Usuario::logueado()) . "\n";
    }
    
    // Set some POST variables to simulate a search
    $_POST['saldoFechaHasta'] = '2026-05-26';
    
    echo "Current directory: " . getcwd() . "\n";
    $changed = chdir('content/administracion/proveedores/gestion_proveedores');
    echo "Changed directory? " . ($changed ? "YES" : "NO") . " - New: " . getcwd() . "\n";
    
    // Capture output of the buscar endpoint
    ob_start();
    require('buscar.php');
    $output = ob_get_clean();
    chdir('../../../..');
    
    echo "Endpoint executed successfully. Output length: " . strlen($output) . " bytes\n";
    echo "First 500 chars:\n";
    echo substr($output, 0, 500);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
