<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/stock/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$nameArticulo = Funciones::get('nameArticulo');
$nameColor = Funciones::get('nameColor');
Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Stock_' . Funciones::hoy('d-m-Y');
	$html2pdf->tituloReporte = 'Stock menos pendientes';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy('d-m-Y'), 'Art' => (isset($idArticulo) ? $idArticulo . '-' . $nameArticulo : '-'),  'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>