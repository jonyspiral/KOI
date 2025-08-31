<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/gestion_proveedores/buscar/')) { ?>
<?php

$saldoFechaHasta = Funciones::get('saldoFechaHasta');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Gestion_proveedores';
	$html2xls->tituloReporte = 'Gestion proveedores';
	$html2xls->datosCabecera = array('Saldo a la fecha' => (isset($saldoFechaHasta) ? $saldoFechaHasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>