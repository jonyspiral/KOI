<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_stock_mp_movimientos() {
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

register_shutdown_function('produccionPdfFatalHandler_stock_mp_movimientos');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/stock_mp/movimientos/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$desde = Funciones::get('fechaDesde');
$hasta = Funciones::get('fechaHasta');
$tipoMovimiento = Funciones::get('tipoMovimiento');
$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$orden = Funciones::get('orden');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Movimientos_de_stock_mp' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Movimientos de stock MP';
	$html2pdf->datosCabecera = array(
		'Desde' => (isset($desde) ? $desde : '-'),
		'Hasta' => (isset($hasta) ? $hasta : '-'),
		'Almacï¿½n' => (isset($idAlmacen) ? $idAlmacen : '-'),
		'Material' => (isset($idMaterial) ? $idMaterial : '-'),
		'Color' => (isset($idColor) ? $idColor : '-')
	);
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
