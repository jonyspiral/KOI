<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/rrhh/fichajes/buscar/')) { ?>
<?php

$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$personal = Funciones::get('personal');

try {
	$per = Factory::getInstance()->getPersonal($personal);

	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Fichajes' . (isset($personal) ? '_' . $per->apellido . '(' . $per->legajo . ')' : '') . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Fichajes';
	$html2xls->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta'=>(isset($hasta) ? $hasta : '-'), 'Personal'=>(isset($personal) ? $per->legajo . ' - ' . $per->apellido : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
?>
<?php } ?>