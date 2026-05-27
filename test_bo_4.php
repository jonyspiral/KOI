<?php
require_once('premaster.php');
echo "HELLO BO TEST 4\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = Factory::getInstance()->db();
    $userRows = $db->query("SELECT * FROM usuarios LIMIT 1");
    if (!$userRows || count($userRows) === 0) die("No users found");
    $userRow = $userRows[0];
    
    echo "Columns in usuarios:\n";
    print_r(array_keys($userRow));
    echo "\nUser data:\n";
    print_r($userRow);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
}
