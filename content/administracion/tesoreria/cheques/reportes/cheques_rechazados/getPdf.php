<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/cheques_rechazados/buscar/')) { ?>
<?php

$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$fechaVtoDesde = Funciones::get('fechaVtoDesde');
$fechaVtoHasta = Funciones::get('fechaVtoHasta');
$empresa = Funciones::get('empresa');
$orderBy = Funciones::get('orderBy');
$idCliente = Funciones::get('idCliente');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Cheques_rechazados' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Cheques Rechazados';
	$html2pdf->datosCabecera = array('Fecha' => Funciones::hoy(), 'Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Empresa' => (($empresa != 0) ? $empresa : 'Todas') );
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>