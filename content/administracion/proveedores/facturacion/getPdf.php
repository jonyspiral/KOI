<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/proveedores/facturacion/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
$docFAC = (Funciones::get('docFAC') == 'true') ? true : false;
$docNCR = (Funciones::get('docNCR') == 'true') ? true : false;
$docNDB = (Funciones::get('docNDB') == 'true') ? true : false;
$proveedor = Funciones::get('proveedor');
$orderBy = Funciones::get('orderBy');
$tipoReporte = Funciones::get('tipoReporte');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'iva_compras' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '') . (isset($proveedor) ? '_' . $proveedor : '');
	$html2pdf->tituloReporte = 'IVA Compras';
	$html2pdf->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'),  'Cliente' => (isset($proveedor) ? $proveedor : '-'), 'Empresa' => (($empresa != 0) ? $empresa : 'Todas') );
	$html2pdf->orientacion = Html2pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>