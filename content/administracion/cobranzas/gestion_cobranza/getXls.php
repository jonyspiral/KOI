<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/gestion_cobranza/buscar/')) { ?>
<?php

$idVendedor = Funciones::get('idVendedor');
$idCliente = Funciones::get('idCliente');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Gestion_cobranza' . (isset($idVendedor) ? '_' . $idVendedor : '') . (isset($idCliente) ? '_' . $idCliente : '');
	$html2xls->tituloReporte = 'Gestion cobranza';
	$html2xls->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Cliente' => (isset($idCliente) ? $idCliente : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>