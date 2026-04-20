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
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Reporte_sumas_saldos' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Sumas y saldos';
	$html2pdf->datosCabecera = array('F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>