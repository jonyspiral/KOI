<?php
file_put_contents('/tmp/test_agregarVarios_min.log', "✅ Script mínimo iniciado\n", FILE_APPEND);

header('Content-Type: application/json');

try {
    $favorites = isset($_POST['favorites']) ? json_decode($_POST['favorites'], true) : [];

    file_put_contents('/tmp/test_agregarVarios_min.log', "🟢 Favorites: " . print_r($favorites, true) . "\n", FILE_APPEND);

    echo json_encode(['ok' => true, 'favoritesCount' => count($favorites)]);
} catch (Exception $ex) {
    file_put_contents('/tmp/test_agregarVarios_min.log', "❌ Exception: " . $ex->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['ok' => false, 'error' => $ex->getMessage()]);
}

