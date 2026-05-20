<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_tesoreria_cheques_reportes_cheques_cartera_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_tesoreria_cheques_reportes_cheques_cartera_getPdf_php');

require_once('../../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/tesoreria/cheques/reportes/cheques_cartera/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');
$cliente = Funciones::get('cliente');
$orderBy = Funciones::get('orderBy');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Cheques_cartera' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Cheques En Cartera';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy(), 'Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Empresa' => (($empresa != 0) ? $empresa : 'Todas') );
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

