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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Stock_a_fecha_' . Funciones::formatearFecha($fecha, 'd-m-Y');
	$html2xls->tituloReporte = 'Stock a fecha';
	$html2xls->datosCabecera = array('Fecha' => Funciones::formatearFecha($fecha, 'd-m-Y'), 'Alm' => (isset($idAlmacen) ? $idAlmacen . '-' . $nameAlmacen : '-'), 'Art' => (isset($idArticulo) ? $idArticulo . '-' . $nameArticulo : '-'), 'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>