<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_pedidos_estadisticas() {
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

register_shutdown_function('comercialPdfFatalHandler_pedidos_estadisticas');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/pedidos/estadisticas/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$modo = Funciones::get('modo');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');
//$tipoProducto = Funciones::get('tipoProducto');
$tipoProducto = (Funciones::get('tipoProducto') ? explode(',', Funciones::get('tipoProducto')) : array());
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Pedidos_Estadisticas' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($idCliente) ? '_' . $idCliente : '') . (isset($idVendedor) ? '_' . $idVendedor : '') . (isset($idAlmacen) ? '_' . $idAlmacen : '') . (isset($idArticulo) ? '_' . $idArticulo : '') . (isset($idColor) ? '_' . $idColor : '');
	$html2pdf->tituloReporte = 'Pedidos Estadisticas';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'),  'Cliente' => (isset($idCliente) ? $idCliente : '-'), 'Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'idAlmacen' => (isset($idAlmacen) ? $idAlmacen : '-'), 'idArticulo' => (isset($idArticulo) ? $idArticulo : '-'), 'idColor' => (isset($idColor) ? $idColor : '-') );
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
