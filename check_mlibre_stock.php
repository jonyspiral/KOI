<?php

// Configuración desde .env
$access_token = "APP_USR-3974289321121032-061214-855a9bece0c22d4aa8c9544774b62537-448490530";
$user_id = "448490530";
$base_url = "https://api.mercadolibre.com";

// Función para hacer solicitudes a la API
function makeRequest($url, $headers, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    return ['code' => $http_code, 'data' => json_decode($response, true), 'error' => $error];
}

// Encabezados para las solicitudes
$headers = [
    "Authorization: Bearer $access_token",
    "Content-Type: application/json"
];

// 1. Verificar validez del token
echo "Verificando token...\n";
$url = "$base_url/users/me";
$response = makeRequest($url, $headers);
if ($response['code'] !== 200) {
    die("Error: Token inválido o expirado. Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n");
}
echo "Token válido. Usuario: {$response['data']['nickname']}\n";

// 2. Obtener todas las publicaciones del vendedor
$url = "$base_url/users/$user_id/items/search";
$response = makeRequest($url, $headers);
if ($response['code'] !== 200) {
    die("Error al obtener publicaciones: Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n");
}

$item_ids = $response['data']['results'];
echo "Publicaciones encontradas: " . count($item_ids) . "\n";

// 3. Obtener almacenes (para stock Full)
$warehouses = [];
$url = "$base_url/inventories/warehouses";
$response = makeRequest($url, $headers);
if ($response['code'] === 200 && !empty($response['data'])) {
    $warehouses = $response['data'];
    echo "Almacenes encontrados: " . count($warehouses) . "\n";
} else {
    echo "No se pudieron obtener los almacenes. Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n";
}

// 4. Consultar stock por publicación
foreach ($item_ids as $item_id) {
    // Obtener detalles de la publicación
    $url = "$base_url/items/$item_id";
    $response = makeRequest($url, $headers);
    if ($response['code'] !== 200) {
        echo "Error al consultar $item_id: Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n";
        continue;
    }

    $item = $response['data'];
    $logistic_type = $item['shipping']['logistic_type'] ?? 'unknown';
    $total_stock = $item['available_quantity'] ?? 0;

    echo "\nPublicación: $item_id\n";
    echo "Título: {$item['title']}\n";
    echo "Tipo de logística: $logistic_type\n";
    echo "Stock total: $total_stock\n";

    // Si es Full, intentar consultar stock en almacenes
    if ($logistic_type === 'fulfillment') {
        if (empty($warehouses)) {
            echo "No hay almacenes disponibles para consultar stock Full.\n";
            // Intentar consulta directa asumiendo un almacén predeterminado (puedes ajustar el warehouse_id si lo conoces)
            $warehouse_id = 'default'; // Cambia por el ID real si lo tienes
            $url = "$base_url/inventories/$warehouse_id/products/$item_id";
            $response = makeRequest($url, $headers);
            if ($response['code'] === 200) {
                $stock_full = $response['data']['available_quantity'] ?? 0;
                echo "Stock en Full (almacén $warehouse_id): $stock_full\n";
            } else {
                echo "Error al consultar stock Full para $item_id: Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n";
            }
        } else {
            foreach ($warehouses as $warehouse) {
                $warehouse_id = $warehouse['id'];
                $url = "$base_url/inventories/$warehouse_id/products/$item_id";
                $response = makeRequest($url, $headers);
                if ($response['code'] === 200) {
                    $stock_full = $response['data']['available_quantity'] ?? 0;
                    echo "Stock en Full (almacén $warehouse_id): $stock_full\n";
                } else {
                    echo "Error al consultar stock Full en almacén $warehouse_id: Código: {$response['code']}, Mensaje: " . json_encode($response['data']) . ", Error cURL: {$response['error']}\n";
                }
            }
        }
    } elseif ($logistic_type === 'xd_drop_off' || $logistic_type === 'cross_docking') {
        // Para Flex, el stock es el de la publicación
        echo "Stock en Flex: $total_stock\n";
    } else {
        echo "Logística desconocida, stock no desglosado\n";
    }
}

?>