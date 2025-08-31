<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/resumen_bancario/buscar/')) { ?>
<?php

$idCaja = Funciones::get('caja');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::get('empresa');
$saldoInicialFinal = Funciones::get('saldoInicialFinal') == 'S';

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Resumen_Bancario_' . $idCaja . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Resumen Bancario';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'), 'Cod. caja' => $idCaja);
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>