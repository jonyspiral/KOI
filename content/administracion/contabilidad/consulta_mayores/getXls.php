<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/consulta_mayores/buscar/')) { ?>
<?php

$idImputacion = Funciones::get('idImputacion');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$saldoInicialFinal = Funciones::get('saldoInicialFinal') == 'S';
$empresa = Funciones::session('empresa');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Reporte_consulta_mayores' . '_' . $idImputacion . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Consulta mayores';
	$html2xls->datosCabecera = array('Imputacion' => $idImputacion, 'F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'F. vto. desde' => (isset($fechaVtoDesde) ? $fechaVtoDesde : '-'), 'F. vto. hasta' => (isset($fechaVtoHasta) ? $fechaVtoHasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>