<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/stock_a_fecha/buscar/')) { ?>
<?php

$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$nameAlmacen = Funciones::get('nameAlmacen');
$nameArticulo = Funciones::get('nameArticulo');
$nameColor = Funciones::get('nameColor');
$fecha = Funciones::get('fecha');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Stock_a_fecha_' . Funciones::formatearFecha($fecha, 'd-m-Y');
	$html2pdf->tituloReporte = 'Stock a fecha';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::formatearFecha($fecha, 'd-m-Y'), 'Alm' => (isset($idAlmacen) ? $idAlmacen . '-' . $nameAlmacen : '-'), 'Art' => (isset($idArticulo) ? $idArticulo . '-' . $nameArticulo : '-'), 'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>