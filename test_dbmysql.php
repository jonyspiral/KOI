<?php
// /var/www/encinitas/test_dbmysql.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('premaster.php'); // debe cargar Factory y DbMysql

try {
    $db = Factory::getInstance()->db();

    echo "1) Test conexión...\n";
    $v = $db->query("SELECT VERSION() AS v");
    echo "   ✅ MySQL versión: " . $v[0]['v'] . "\n";

    echo "2) Test shim TOP/ISNULL/NOLOCK...\n";
    // Ajustá 'clientes' a una tabla real de tu BD
    $r = $db->query("SELECT TOP 1 IFNULL(nombre,'N/A') AS n FROM clientes");
    echo "   ✅ Resultado: " . json_encode($r) . "\n";

    echo "3) Test shim DATEDIFF...\n";
    $val = $db->query("SELECT DATEDIFF(day, '2025-01-01', '2025-01-10') AS d");
    echo "   ✅ Días: " . $val[0]['d'] . "\n";

    // Asegurá que exista una tabla de prueba simple:
    // CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50));
    echo "4) Test lastInsertId...\n";
    $db->exec("INSERT INTO test_table (nombre) VALUES ('prueba')");
    $id = $db->lastInsertId();
    echo "   ✅ Nuevo ID: $id\n";
    $db->exec("DELETE FROM test_table WHERE id = $id");

    echo "5) Test escape...\n";
    $e = $db->escape("O'Reilly");
    echo "   ✅ Escapado: $e\n";

    echo "\n🎉 TODOS LOS TESTS PASARON\n";

} catch (Exception $ex) {
    echo "❌ Error: " . $ex->getMessage() . "\n";
}
