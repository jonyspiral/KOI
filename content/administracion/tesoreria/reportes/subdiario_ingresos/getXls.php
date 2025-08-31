<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reportes/subdiario_ingresos/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
$tipoRecibo = Funciones::get('tipoRecibo');
$idVendedor = Funciones::get('idVendedor');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Subdiario_ingreso' . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Subdiario ingreso';
	$html2xls->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>