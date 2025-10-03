<?php
// /var/www/encinitas/tools/mssql_ping.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '192.168.2.100';
$db   = 'encinitas';
$user = 'Koi';
$pass = 'koisys';

// Opción A: usando la entrada de freetds.conf
//$dsn = "dblib:host=encinitas_mssql;dbname=$db;charset=UTF-8";

// Opción B: directo por IP/puerto
$dsn = "dblib:host={$host}:1433;dbname={$db};charset=UTF-8";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT            => 5,
    ]);
    echo "CONEXION OK\n";

    $ver = $pdo->query("SELECT @@VERSION AS version")->fetch();
    echo "VERSION: ".$ver['version']."\n";

    // prueba mínima a una tabla propia (si quieres)
    // $row = $pdo->query("SELECT TOP 1 * FROM dbo.AlgunaTabla")->fetch();
    // print_r($row);

} catch (Exception $e) {
    echo "ERROR: ".$e->getMessage()."\n";
    exit(1);
}

