<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_cobranzas_reportes_aplicaciones_pendientes_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_cobranzas_reportes_aplicaciones_pendientes_getPdf_php');

require_once('../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/cobranzas/reportes/aplicaciones_pendientes/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$idCliente = Funciones::get('idCliente');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$empresa = Funciones::get('empresa');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Reporte_aplicaciones_pendientes_clientes' . (isset($idCliente) ? '_' . $idCliente : '') . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Reporte aplicaciones pendientes clientes';
	$html2pdf->datosCabecera = array('Cliente' => (isset($idCliente) ? $idCliente : '-'), 'F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Empresa' => (isset($empresa) ? $empresa : 'Ambas'));
	$html2pdf->orientacion = Html2pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

