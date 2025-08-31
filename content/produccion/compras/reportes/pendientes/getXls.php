<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/reportes/pendientes/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$idMaterial = Funciones::get('idMaterial');
$idColor = Funciones::get('idColor');
$orden = Funciones::get('orden');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2xls->fileName = 'Reporte_pendientes' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Reporte pendientes';
	$html2xls->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Proveedor' => (isset($idProveedor) ? $idProveedor : '-'), 'Material' => (isset($idMaterial) ? $idMaterial : '-'), 'Color' => (isset($idColor) ? $idColor : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>