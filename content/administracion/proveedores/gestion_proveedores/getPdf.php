<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/gestion_proveedores/buscar/')) { ?>
<?php

$saldoFechaHasta = Funciones::get('saldoFechaHasta');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Gestion_proveedores';
	$html2pdf->tituloReporte = 'Gestión proveedores';
	$html2pdf->datosCabecera = array('Saldo a la fecha' => (isset($saldoFechaHasta) ? $saldoFechaHasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>