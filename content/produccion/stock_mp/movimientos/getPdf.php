<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/stock_mp/movimientos/buscar/')) { ?>
<?php

$desde = Funciones::get('fechaDesde');
$hasta = Funciones::get('fechaHasta');
$tipoMovimiento = Funciones::get('tipoMovimiento');
$idAlmacen = Funciones::get('idAlmacen');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$orden = Funciones::get('orden');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Movimientos_de_stock_mp' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Movimientos de stock MP';
	$html2pdf->datosCabecera = array(
		'Desde' => (isset($desde) ? $desde : '-'),
		'Hasta' => (isset($hasta) ? $hasta : '-'),
		'Almacén' => (isset($idAlmacen) ? $idAlmacen : '-'),
		'Material' => (isset($idMaterial) ? $idMaterial : '-'),
		'Color' => (isset($idColor) ? $idColor : '-')
	);
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>