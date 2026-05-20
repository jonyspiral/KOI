<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_tesoreria_cheques_reportes_seguimiento_cheques_getPdf_php() {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        return;
    }
    if (ob_get_length()) {
        ob_clean();
    }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => $error,
    ));
}
register_shutdown_function('shutdown_content_administracion_tesoreria_cheques_reportes_seguimiento_cheques_getPdf_php');

require_once('../../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/tesoreria/cheques/reportes/seguimiento_cheques/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
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
	($idCaja) && ($html2pdf->datosCabecera['NÃ‚Âº Caja'] = $idCaja);
	($tipo != '0') && ($html2pdf->datosCabecera['Tipo'] = ($tipo == '1' ? 'Propio' : 'De terceros'));
	($numero) && ($html2pdf->datosCabecera['Numero'] = $numero);
	($rechazado != '0') && ($html2pdf->datosCabecera['Rechazados'] = ($rechazado == '1' ? 'S' : 'N'));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

