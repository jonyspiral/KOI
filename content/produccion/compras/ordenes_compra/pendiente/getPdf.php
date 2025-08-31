<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/compras/ordenes_compra/pendiente/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$orderBy = Funciones::get('orderBy');
$proveedor = Factory::getInstance()->getProveedor(Funciones::get('idProveedor'));
$pdf = Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Ordenes_compra_pendientes' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Ordenes de Compra Pendientes';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy(), 'Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Proveedor' => (empty($idProveedor) ? '-' : '[' . $proveedor->id . '] ' . $proveedor->razonSocial));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>