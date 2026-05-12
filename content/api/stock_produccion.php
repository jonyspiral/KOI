<?php
require_once('../../premaster.php');
require_once('funciones.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

$articulo = isset($_GET['articulo']) ? trim($_GET['articulo']) : '';
$color = isset($_GET['color']) ? trim($_GET['color']) : '';

if ($articulo === '' || $color === '' || strtolower($color) === 'undefined') {
    echo json_encode(array(
        'ok' => true,
        'data' => array(
            'cantidad' => 0
        ),
        'error' => false,
        'message' => 'parametros vacios'
    ));
    exit;
}

try {
    $result = getStockEnProduccion($articulo, $color);

    if (isset($result['data'])) {
        echo json_encode(array(
            'ok' => true,
            'data' => $result['data'],
            'error' => false
        ));
        exit;
    }

    echo json_encode(array(
        'ok' => true,
        'data' => array(
            'cantidad' => 0
        ),
        'error' => false,
        'message' => isset($result['error']) ? $result['error'] : 'sin stock en produccion'
    ));
    exit;

} catch (Exception $e) {
    echo json_encode(array(
        'ok' => false,
        'data' => array(
            'cantidad' => 0
        ),
        'error' => true,
        'message' => $e->getMessage()
    ));
    exit;
}
?>
