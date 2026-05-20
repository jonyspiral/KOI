<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_tesoreria_reimpresion_ordenes_de_pago_getPdf_php() {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        return;
    }
    if (ob_get_length()) {
        ob_clean();
    }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => $error,
    ));
}
register_shutdown_function('shutdown_content_administracion_tesoreria_reimpresion_ordenes_de_pago_getPdf_php');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$numero = Funciones::get('numero');
$empresa = Funciones::session('empresa');

try {
	$ordenDePago = Factory::getInstance()->getOrdenDePago($numero, $empresa);
	$ordenDePago->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

