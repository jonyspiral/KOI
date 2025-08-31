<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/stock_a_fecha/buscar/')) { ?>
<?php

$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$nameAlmacen = Funciones::get('nameAlmacen');
$nameMaterial = Funciones::get('nameMaterial');
$nameColor = Funciones::get('nameColor');
$fecha = Funciones::get('fecha');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Stock_a_fecha_mp_' . Funciones::formatearFecha($fecha, 'd-m-Y');
	$html2pdf->tituloReporte = 'Stock a fecha MP';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::formatearFecha($fecha, 'd-m-Y'), 'Alm' => (isset($idAlmacen) ? $idAlmacen . '-' . $nameAlmacen : '-'), 'Mat' => (isset($idMaterial) ? $idMaterial . '-' . $nameMaterial : '-'), 'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>