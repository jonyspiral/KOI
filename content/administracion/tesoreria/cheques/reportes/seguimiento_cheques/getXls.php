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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2xls->fileName = 'Seguimiento_cheques' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Seguimiento de cheques';
	$html2xls->datosCabecera = array(
		'Fecha' => Funciones::hoy()
	);
	($fechaDesde) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($fechaHasta) && ($html2xls->datosCabecera['Hasta'] = $fechaHasta);
	($empresa != 0) && ($html2xls->datosCabecera['E'] = $empresa);
	($idCliente) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($diasDesde) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($diasHasta) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($importeDesde) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($importeHasta) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($idCuentaBancaria) && ($html2xls->datosCabecera['Desde'] = $fechaDesde);
	($idCaja) && ($html2xls->datosCabecera['Nº Caja'] = $idCaja);
	($tipo != '0') && ($html2xls->datosCabecera['Tipo'] = ($tipo == '1' ? 'Propio' : 'De terceros'));
	($numero) && ($html2xls->datosCabecera['Numero'] = $numero);
	($rechazado != '0') && ($html2xls->datosCabecera['Rechazados'] = ($rechazado == '1' ? 'S' : 'N'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>