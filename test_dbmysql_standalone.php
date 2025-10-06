<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔹 Carga mínima: solo lo necesario para probar DB
require_once __DIR__.'/factory/Config.php';
require_once __DIR__.'/factory/drivers/DbMysql.php';

// ⚙️ Instancia directa (misma config que usa Factory)
$db = new DbMysql([
    'host'    => Config::mysql_host,
    'port'    => Config::mysql_port,
    'name'    => Config::mysql_db,
    'user'    => Config::mysql_user,
    'pass'    => Config::mysql_pass,
    'charset' => Config::mysql_charset,
    'timeout' => 5,
]);

try {
    echo "1) Test conexión...\n";
    $v = $db->query("SELECT VERSION() AS v");
    echo "   ✅ MySQL versión: " . $v[0]['v'] . "\n";

    echo "2) Test shim TOP/ISNULL...\n";
    // Cambiá 'clientes' por una tabla que exista en tu BD:
    $r = $db->query("SELECT TOP 1 ISNULL('nombre', 'N/A') AS n FROM Clientes");
    echo "   ✅ Resultado: " . json_encode($r) . "\n";

    echo "3) Test DATEDIFF...\n";
    $d = $db->query("SELECT DATEDIFF(day, '2025-01-01', '2025-01-10') AS d");
    echo "   ✅ Días: " . $d[0]['d'] . "\n";

    echo "4) Test lastInsertId...\n";
    // Asegurate de tener esta tabla:
    // CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50));
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
