<?php
require_once 'includes.php';

function describeTable($tableName) {
    echo "=== ESTRUCTURA DE LA TABLA $tableName ===\n";
    try {
        $db = Factory::getInstance()->db();
        $cols = $db->query("DESCRIBE $tableName");
        foreach ($cols as $c) {
            echo "- Field: " . $c['Field'] . " | Type: " . $c['Type'] . "\n";
        }
    } catch (Exception $ex) {
        echo "ERROR: " . $ex->getMessage() . "\n";
    }
}

describeTable("stock");
describeTable("movimientos_stock");
