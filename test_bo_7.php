<?php
require_once('premaster.php');
echo "HELLO BO TEST 7\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = Factory::getInstance()->db();
    
    // Let's query roles_por_usuario
    $rows = $db->query("SELECT * FROM roles_por_usuario LIMIT 20");
    echo "Roles assigned to users:\n";
    print_r($rows);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
