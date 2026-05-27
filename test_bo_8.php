<?php
require_once('premaster.php');
echo "HELLO BO TEST 8\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = Factory::getInstance()->db();
    
    // Let's show all tables
    $tables = $db->query("SHOW TABLES");
    echo "Tables in database:\n";
    print_r($tables);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
