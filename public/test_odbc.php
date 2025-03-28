<?php
try {
    $dsn = "MiSQLServer";
    $username = "Koi";
    $password = "koisys";
    $pdo = new PDO("odbc:$dsn", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Conexión exitosa\n";
    $stmt = $pdo->query("SELECT * FROM articulos_new WHERE cod_articulo = '869'");
    $row = $stmt->fetch();
    print_r($row);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
