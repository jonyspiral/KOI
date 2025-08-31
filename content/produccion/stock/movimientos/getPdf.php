<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock/movimientos/buscar/')) { ?>
<?php

$desde = Funciones::get('fechaDesde');
$hasta = Funciones::get('fechaHasta');
$tipoMovimiento = Funciones::get('tipoMovimiento');
$idAlmacen = Funciones::get('idAlmacen');
$idArticulo = Funciones::get('idArticulo');
$idColorArticulo = Funciones::get('idColorArticulo');
$orden = Funciones::get('orden');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Movimientos_de_stock' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Movimientos de stock';
	$html2pdf->datosCabecera = array(
		'Desde' => (isset($desde) ? $desde : '-'),
		'Hasta' => (isset($hasta) ? $hasta : '-'),
		'Almacén' => (isset($idAlmacen) ? $idAlmacen : '-'),
		'Artículo' => (isset($idArticulo) ? $idArticulo : '-'),
		'Color' => (isset($idColorArticulo) ? $idColorArticulo : '-')
	);
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>