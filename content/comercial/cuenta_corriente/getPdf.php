<?php
if (!ob_get_level()) {
    ob_start();
}

function comercialPdfFatalHandler_cuenta_corriente() {
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

register_shutdown_function('comercialPdfFatalHandler_cuenta_corriente');

require_once('../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('comercial/cuenta_corriente/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idCliente = Funciones::get('idCliente');
$empresa = Funciones::get('empresa');
$cliente = Factory::getInstance()->getCliente($idCliente);
$razonSocial = Funciones::sacarTildes($cliente->razonSocial);
$razonSocial = str_replace(' ', '_', $razonSocial);
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	if (!isset($idCliente)) {
		throw new Exception('Debe elegir un cliente');
	}
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Cuenta_corriente_' . $cliente->id . '_' . $razonSocial . (isset($empresa) ? '_' . $empresa : '');
	$html2pdf->tituloReporte = 'Cuenta corriente';
	$html2pdf->datosCabecera = array('Cliente' => '[' . $cliente->id . '] ' . $cliente->razonSocial, 'E' => (isset($empresa) ? $empresa : '-'), 'F. desde' => (isset($desde) ? $desde : '-'), 'F. hasta' => (isset($hasta) ? $hasta : '-'));
	$html2pdf->open();
	//$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
