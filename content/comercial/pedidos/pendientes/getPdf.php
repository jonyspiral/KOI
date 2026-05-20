<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_pedidos_pendientes() {
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

register_shutdown_function('comercialPdfFatalHandler_pedidos_pendientes');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/pedidos/pendientes/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idCliente = Funciones::get('cliente');
$vendedor =Funciones::get('vendedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$clienteName = Funciones::get('clienteName');
$vendedorName = Funciones::get('vendedorName');
$cliente = Factory::getInstance()->getCliente($idCliente);
$razonSocial = Funciones::sacarTildes($cliente->razonSocial);
$razonSocial = str_replace(' ', '_', $razonSocial);

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Pedidos_Pendientes' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($razonSocial) ? '_' . $razonSocial : ''). (isset($vendedor) ? '_' . $vendedor : '');
	$html2pdf->tituloReporte = 'Pedidos Pendientes';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'),  'Cliente' => (isset($clienteName) ? $clienteName : '-'), 'Vendedor' => (isset($vendedorName) ? $vendedorName : '-') );
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
