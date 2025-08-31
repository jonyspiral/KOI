<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/ecommerce/reporte_ventas/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$customer = Funciones::get('customer');
$usergroup = Funciones::get('usergroup');
$modo = Funciones::get('modo');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Ventas_Ecommerce' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Ventas Ecommerce';
	$html2xls->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta'=>(isset($hasta) ? $hasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>