<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_stock_mp_stock_a_fecha() {
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

register_shutdown_function('produccionPdfFatalHandler_stock_mp_stock_a_fecha');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/stock_mp/stock_a_fecha/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$nameAlmacen = Funciones::get('nameAlmacen');
$nameMaterial = Funciones::get('nameMaterial');
$nameColor = Funciones::get('nameColor');
$fecha = Funciones::get('fecha');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Stock_a_fecha_mp_' . Funciones::formatearFecha($fecha, 'd-m-Y');
	$html2pdf->tituloReporte = 'Stock a fecha MP';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::formatearFecha($fecha, 'd-m-Y'), 'Alm' => (isset($idAlmacen) ? $idAlmacen . '-' . $nameAlmacen : '-'), 'Mat' => (isset($idMaterial) ? $idMaterial . '-' . $nameMaterial : '-'), 'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
