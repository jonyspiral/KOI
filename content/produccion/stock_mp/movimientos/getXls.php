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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Movimientos_de_stock_mp' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Movimientos de stock MP';
	$html2xls->datosCabecera = array(
		'Desde' => (isset($desde) ? $desde : '-'),
		'Hasta' => (isset($hasta) ? $hasta : '-'),
		'Almacén' => (isset($idAlmacen) ? $idAlmacen : '-'),
		'Material' => (isset($idMaterial) ? $idMaterial : '-'),
		'Color' => (isset($idColor) ? $idColor : '-')
	);
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>