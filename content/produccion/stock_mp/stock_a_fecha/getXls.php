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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Stock_a_fecha_mp_' . Funciones::formatearFecha($fecha, 'd-m-Y');
	$html2xls->tituloReporte = 'Stock a fecha MP';
	$html2xls->datosCabecera = array('Fecha' => Funciones::formatearFecha($fecha, 'd-m-Y'), 'Alm' => (isset($idAlmacen) ? $idAlmacen . '-' . $nameAlmacen : '-'), 'Mat' => (isset($idMaterial) ? $idMaterial . '-' . $nameMaterial : '-'), 'Color' => (isset($idColor) ? $idColor . '-' . $nameColor : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>