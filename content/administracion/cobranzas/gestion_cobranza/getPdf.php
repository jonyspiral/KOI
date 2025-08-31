<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/gestion_cobranza/buscar/')) { ?>
<?php

$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Gestion_cobranza' . (isset($idVendedor) ? '_' . $idVendedor : '') . (isset($idCliente) ? '_' . $idCliente : '');
	$html2pdf->tituloReporte = 'Gestión cobranza';
	$html2pdf->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Cliente' => (isset($idCliente) ? $idCliente : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>