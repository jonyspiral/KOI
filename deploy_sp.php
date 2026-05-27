<?php
require_once 'includes.php';

function deployProcedure($fileBaseName) {
    echo "Procesando $fileBaseName ... ";
    $filePath = "C:/dev/encinitas/docs/migracion/sql/" . $fileBaseName;
    if (!file_exists($filePath)) {
        // Si no está en C:/dev, lo buscamos en el path relativo del contenedor
        $filePath = __DIR__ . "/docs/migracion/sql/" . $fileBaseName;
    }
    
    if (!file_exists($filePath)) {
        echo "Error: No existe el archivo SQL en $filePath\n";
        return;
    }
    
    $sqlContent = file_get_contents($filePath);
    
    // Conexión MySQL cruda
    $db = Factory::getInstance()->db();
    
    // Limpiamos los delimitadores y separamos comandos
    // 1. Quitar DROP PROCEDURE
    $dropSql = "DROP PROCEDURE IF EXISTS " . str_replace(".mysql.sql", "", $fileBaseName);
    try {
        $db->exec($dropSql);
        echo "DROP OK ... ";
    } catch (Exception $e) {
        echo "DROP FALLÓ: " . $e->getMessage() . " ... ";
    }
    
    // 2. Limpiar DELIMITER y delimitador $$ del cuerpo
    $createSql = $sqlContent;
    $createSql = preg_replace('/DROP PROCEDURE IF EXISTS.*;/i', '', $createSql);
    $createSql = preg_replace('/DELIMITER\s+\$\$/i', '', $createSql);
    $createSql = preg_replace('/DELIMITER\s+;/i', '', $createSql);
    $createSql = str_replace('$$', '', $createSql);
    $createSql = trim($createSql);
    
    try {
        $db->exec($createSql);
        echo "CREATE OK!\n";
    } catch (Exception $e) {
        echo "CREATE FALLÓ: " . $e->getMessage() . "\n";
    }
}

echo "=== DEPLOY DE PROCEDIMIENTOS ALMACENADOS ===\n";
deployProcedure("saldo_clientes_a_fecha.mysql.sql");
deployProcedure("saldo_proveedores_a_fecha.mysql.sql");
deployProcedure("sp_stock_a_fecha.mysql.sql");
deployProcedure("sp_stock_mp_a_fecha.mysql.sql");
