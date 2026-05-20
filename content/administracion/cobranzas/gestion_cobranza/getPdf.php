<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_cobranzas_gestion_cobranza_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_cobranzas_gestion_cobranza_getPdf_php');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/cobranzas/gestion_cobranza/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Gestion_cobranza' . (isset($idVendedor) ? '_' . $idVendedor : '') . (isset($idCliente) ? '_' . $idCliente : '');
	$html2pdf->tituloReporte = 'GestiÃƒÂ³n cobranza';
	$html2pdf->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Cliente' => (isset($idCliente) ? $idCliente : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

