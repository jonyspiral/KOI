<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_tesoreria_egresos_retiro_socios_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_tesoreria_egresos_retiro_socios_getPdf_php');

require_once('../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/tesoreria/egresos/retiro_socios/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$id = Funciones::get('idRetiro');
$empresa = Funciones::session('empresa');

try {
	$retiroSocio = Factory::getInstance()->getRetiroSocio($id, $empresa);
	if ($retiroSocio->anulado()) {
		throw new FactoryExceptionCustomException('El retiro estÃƒÂ¡ anulado o fue modificado');
	}

	$retiroSocio->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

