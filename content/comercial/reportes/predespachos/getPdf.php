<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_reportes_predespachos() {
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

register_shutdown_function('comercialPdfFatalHandler_reportes_predespachos');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/reportes/predespachos/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$empresa = Funciones::session('empresa');
$tipo = Funciones::get('tipo');
$idCliente = Funciones::get('idCliente');
$idPedido = Funciones::get('idPedido');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$almacen = Funciones::get('almacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Predespachos_empresa_' . $empresa . ($tipo == 'C' ? '_por_cliente' : '_pedido_' . $idPedido);
	$html2pdf->tituloReporte = 'Predespachos';
	$html2pdf->datosCabecera = array('Empresa' => $empresa, 'Cliente' => (isset($idCliente) ? $idCliente : '-'), 'Pedido' => (isset($idPedido) ? $idPedido : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
