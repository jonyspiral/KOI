<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_compras_ordenes_compra_pendiente() {
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

register_shutdown_function('produccionPdfFatalHandler_compras_ordenes_compra_pendiente');

require_once('../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/compras/ordenes_compra/pendiente/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idProveedor = Funciones::get('idProveedor');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$orderBy = Funciones::get('orderBy');
$proveedor = Factory::getInstance()->getProveedor(Funciones::get('idProveedor'));
$pdf = Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Ordenes_compra_pendientes' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Ordenes de Compra Pendientes';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy(), 'Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Proveedor' => (empty($idProveedor) ? '-' : '[' . $proveedor->id . '] ' . $proveedor->razonSocial));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
