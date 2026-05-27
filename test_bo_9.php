<?php
require_once('premaster.php');
echo "HELLO BO TEST 9\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = Factory::getInstance()->db();
    
    $query = "
        SELECT DISTINCT u.cod_usuario, u.tipo
        FROM users u
        JOIN roles_por_usuario rpu ON u.cod_usuario = rpu.cod_usuario
        JOIN funcionalidades_por_rol fpr ON rpu.cod_rol = fpr.cod_rol
        WHERE fpr.cod_funcionalidad = 4751 AND u.anulado = 'N' AND rpu.anulado = 'N'
        LIMIT 5
    ";
    
    $rows = $db->query($query);
    echo "Users with search permission:\n";
    print_r($rows);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
