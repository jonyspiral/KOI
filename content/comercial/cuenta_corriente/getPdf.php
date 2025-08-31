<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/cuenta_corriente/buscar/')) { ?>
<?php

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

?>
<?php } ?>