<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/plan_cuentas/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$concepto = (Funciones::get('concepto') ? explode(' ', Funciones::get('concepto')) : array());
$esReporte = Funciones::get('$esReporte', '1');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Plan_de_cuentas' . (isset($desde) ? '_' . $desde : '') . (isset($hasta) ? '_' . $hasta : '');
	$html2pdf->tituloReporte = 'Plan de Cuentas';
	$html2pdf->datosCabecera = array('Cta. desde' => (isset($desde) ? $desde : '-'), 'Cta. hasta' => (isset($hasta) ? $hasta : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>