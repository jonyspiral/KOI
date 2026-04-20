<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/saldo_cajas/buscar/')) { ?>
<?php

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'saldos_de_caja_' . Usuario::logueado()->id;
	$html2xls->tituloReporte = 'Saldos de caja';
	$html2xls->datosCabecera = array('Usuario' => Usuario::logueado()->id);
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>