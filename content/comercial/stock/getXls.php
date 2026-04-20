<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/stock/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$nameArticulo = Funciones::get('nameArticulo');
$nameColor = Funciones::get('nameColor');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Stock_' . Funciones::hoy('d-m-Y');
	$html2xls->tituloReporte = 'Stock menos pendientes';
	$html2xls->datosCabecera = array('Fecha' => Funciones::hoy('d-m-Y'), 'Art' => (isset($idArticulo) ? $idArticulo . '-' . $nameArticulo : '-'),  'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>