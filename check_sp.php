<?php
require_once 'includes.php';

try {
    $db = Factory::getInstance()->db();
    
    // Consultar stored procedures en el schema actual
    $sql = "SHOW PROCEDURE STATUS WHERE Db = DATABASE()";
    $procedures = $db->query($sql);
    
    echo "=== STORED PROCEDURES EN LA BASE DE DATOS ===\n";
    if (empty($procedures)) {
        echo "No se encontraron Stored Procedures creados.\n";
    } else {
        foreach ($procedures as $proc) {
            echo "- Name: " . $proc['Name'] . " | Type: " . $proc['Type'] . "\n";
        }
    }
    
} catch (Exception $ex) {
    echo "ERROR: " . $ex->getMessage() . "\n";
}
