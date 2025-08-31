<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/rrhh/anomalias/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$personal = Funciones::get('personal');

try {
	$per = Factory::getInstance()->getPersonal($personal);

	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Fichajes' . (isset($personal) ? '_' . Funciones::reemplazar(' ', '_', $per->apellido) . '_' . $per->legajo . '_' : '') . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Fichajes';
	$html2pdf->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta'=>(isset($hasta) ? $hasta : '-'), 'Personal'=>(isset($personal) ? $per->legajo . ' - ' . $per->apellido : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>