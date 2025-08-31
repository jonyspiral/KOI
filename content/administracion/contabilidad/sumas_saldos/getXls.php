<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/movimientos_caja/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
//$fechaVtoDesde = Funciones::get('fechaVtoDesde');
//$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$imputacionDesde = Funciones::get('imputacionDesde');
$imputacionHasta = Funciones::get('imputacionHasta');
$empresa = Funciones::session('empresa');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Reporte_sumas_saldos' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Sumas y saldos';
	$html2xls->datosCabecera = array('F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>