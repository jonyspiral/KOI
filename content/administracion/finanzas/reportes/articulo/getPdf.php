<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_finanzas_reportes_articulo_getPdf_php() {
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
register_shutdown_function('shutdown_content_administracion_finanzas_reportes_articulo_getPdf_php');

require_once('../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/finanzas/reportes/articulo/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$empresa = Funciones::get('empresa');
$articulo = Funciones::get('articulo');
$color = Funciones::get('color');
$cliente = Funciones::get('cliente');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Reporte_Articulos' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($idProveedor) ? '_' . $idProveedor : '');
	$html2pdf->tituloReporte = 'Reporte ArtÃƒÂ­culos';
	$html2pdf->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'),  'Cliente' => (isset($cliente) ? $cliente : '-'), 'Empresa' => ($empresa != 0 ? $empresa : 'Todas') );
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

