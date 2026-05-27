<?php
require_once 'includes.php';

try {
    echo "=== PROBANDO LLAMADA A STORED PROCEDURES EN MYSQL ===\n";
    
    // Probar saldo_clientes_a_fecha
    echo "Probando saldo_clientes_a_fecha ... ";
    $res1 = Datos::EjecutarStoredProcedure('saldo_clientes_a_fecha', array('2026-05-26'));
    echo "OK (filas: " . count($res1) . ")\n";
    
    // Probar sp_stock_a_fecha
    echo "Probando sp_stock_a_fecha ... ";
    $res2 = Datos::EjecutarStoredProcedure('sp_stock_a_fecha', array('', '', '', '', '2026-05-26'));
    echo "OK (filas: " . count($res2) . ")\n";
    
} catch (Exception $ex) {
    echo "ERROR: " . $ex->getMessage() . "\n";
}
