<?php require_once('../../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/reportes/seguimiento_cheques/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');
$idCliente = Funciones::get('idCliente');
$diasDesde = Funciones::get('diasDesde');
$diasHasta = Funciones::get('diasHasta');
$importeDesde = Funciones::get('importeDesde');
$importeHasta = Funciones::get('importeHasta');
$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$idCaja = Funciones::get('idCaja');
$tipo = Funciones::get('tipo');
$numero = Funciones::get('numero');
$rechazado = Funciones::get('rechazado');
$orden = Funciones::get('orden');
$pdf = Funciones::get('pdf', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2pdf->fileName = 'Seguimiento_cheques' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Seguimiento de cheques';
	$html2pdf->datosCabecera = array(
		'Fecha' => Funciones::hoy()
	);
	($fechaDesde) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($fechaHasta) && ($html2pdf->datosCabecera['Hasta'] = $fechaHasta);
	($empresa != 0) && ($html2pdf->datosCabecera['E'] = $empresa);
	($idCliente) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($diasDesde) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($diasHasta) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($importeDesde) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($importeHasta) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($idCuentaBancaria) && ($html2pdf->datosCabecera['Desde'] = $fechaDesde);
	($idCaja) && ($html2pdf->datosCabecera['Nº Caja'] = $idCaja);
	($tipo != '0') && ($html2pdf->datosCabecera['Tipo'] = ($tipo == '1' ? 'Propio' : 'De terceros'));
	($numero) && ($html2pdf->datosCabecera['Numero'] = $numero);
	($rechazado != '0') && ($html2pdf->datosCabecera['Rechazados'] = ($rechazado == '1' ? 'S' : 'N'));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>