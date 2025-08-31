<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/panel_de_control/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$numeroOrdenFabricacion = Funciones::get('numeroOrdenFabricacion');
$numeroTarea = Funciones::get('numeroTarea');
$one = Funciones::get('one') == '1';
$orden = Funciones::get('orden');
$esPdf = Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Confirmacion_stock' . $idCaja . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Confirmación stock';
	$html2pdf->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>