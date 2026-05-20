<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_cajas_movimientos_caja_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_cajas_movimientos_caja_getPdf_php');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/cajas/movimientos_caja/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$idCaja = Funciones::get('caja');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::get('empresa');
$saldoInicialFinal = Funciones::get('saldoInicialFinal') == 'S';

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Movimientos_de_caja_' . $idCaja . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Movimientos de caja';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'), 'Cod. caja' => $idCaja);
	$html2pdf->orientacion = Html2pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

