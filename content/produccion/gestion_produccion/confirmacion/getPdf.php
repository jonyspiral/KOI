<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_gestion_produccion_confirmacion() {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => array(
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        )
    ));
}

register_shutdown_function('produccionPdfFatalHandler_gestion_produccion_confirmacion');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/gestion_produccion/confirmacion/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$numeroOrdenFabricacion = Funciones::get('numeroOrdenFabricacion');
$numeroTarea = Funciones::get('numeroTarea');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$esPdf = Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Confirmacion_stock' . $idCaja . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Confirmaciï¿½n stock';
	$html2pdf->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
