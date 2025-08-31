<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/saldo_cajas/buscar/')) { ?>
<?php

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'saldos_de_caja_' . Usuario::logueado()->id;
	$html2pdf->tituloReporte = 'Saldos de caja';
	$html2pdf->datosCabecera = array('Usuario' => Usuario::logueado()->id, 'Fecha reporte' => Funciones::hoy('d/m/Y'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>