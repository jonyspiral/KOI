<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_contabilidad_asientos_contables_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_contabilidad_asientos_contables_getPdf_php');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/contabilidad/asientos_contables/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$numeroDesde = Funciones::get('numeroDesde');
$numeroHasta = Funciones::get('numeroHasta');
$numeroHasta = Funciones::get('empresa');
Funciones::get('pdf', '1');
Funciones::get('confirmar', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Reporte_asientos_contables' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Asientos contables';
	$html2pdf->datosCabecera = array('F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'F. vto. desde' => (isset($fechaVtoDesde) ? $fechaVtoDesde : '-'), 'F. vto. hasta' => (isset($fechaVtoHasta) ? $fechaVtoHasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

