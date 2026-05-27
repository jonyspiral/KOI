<?php
require_once('premaster.php');
echo "HELLO BO TEST 6\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = Factory::getInstance()->db();
    $userRows = $db->query("SELECT * FROM users WHERE tipo != 'C' AND anulado = 'N' LIMIT 1");
    if (!$userRows || count($userRows) === 0) {
        // Fallback: try any active user
        $userRows = $db->query("SELECT * FROM users WHERE anulado = 'N' LIMIT 1");
    }
    if (!$userRows || count($userRows) === 0) die("No active users found");
    $userRow = $userRows[0];
    
    echo "Logging in as: " . $userRow['cod_usuario'] . " (tipo: " . $userRow['tipo'] . ")\n";
    
    UsuarioLogin::login($userRow['cod_usuario'], $userRow['password']);
    echo "Is Usuario logged in? " . (Usuario::logueado() ? "YES" : "NO") . "\n";
    
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
    echo "First 500 chars of output:\n";
    echo substr($output, 0, 500);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
