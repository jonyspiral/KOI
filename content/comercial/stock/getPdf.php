<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_stock() {
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

register_shutdown_function('comercialPdfFatalHandler_stock');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/stock/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$nameArticulo = Funciones::get('nameArticulo');
$nameColor = Funciones::get('nameColor');
Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Stock_' . Funciones::hoy('d-m-Y');
	$html2pdf->tituloReporte = 'Stock menos pendientes';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy('d-m-Y'), 'Art' => (isset($idArticulo) ? $idArticulo . '-' . $nameArticulo : '-'),  'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
