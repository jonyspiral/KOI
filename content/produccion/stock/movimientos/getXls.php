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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Movimientos_de_stock' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Movimientos de stock';
	$html2xls->datosCabecera = array(
		'Desde' => (isset($desde) ? $desde : '-'),
		'Hasta' => (isset($hasta) ? $hasta : '-'),
		'Almacén' => (isset($idAlmacen) ? $idAlmacen : '-'),
		'Artículo' => (isset($idArticulo) ? $idArticulo : '-'),
		'Color' => (isset($idColorArticulo) ? $idColorArticulo : '-')
	);
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>