<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_ecommerce_reporte_ventas() {
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

register_shutdown_function('comercialPdfFatalHandler_ecommerce_reporte_ventas');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/ecommerce/reporte_ventas/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$customer = Funciones::get('customer');
$usergroup = Funciones::get('usergroup');
$modo = Funciones::get('modo');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Ventas_Ecommerce' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Ventas Ecommerce';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta'=>(isset($hasta) ? $hasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
