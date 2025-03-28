<?php

try {
    $dsn = "odbc:MiSQLServer";
    $username = "Koi";
    $password = "koisys";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT TOP 1 [cod_articulo], [denom_articulo] FROM [articulos_new] WHERE [cod_articulo] = ?");
    $stmt->execute(['869']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "Éxito: " . print_r($result, true);
    } else {
        echo "No se encontraron resultados.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
