<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cajas/movimientos_caja/buscar/')) { ?>
<?php

$idCaja = Funciones::get('caja');
$desde = Funciones::get('desde');
$hasta = Funciones::get('hasta');
$empresa = Funciones::get('empresa');
$saldoInicialFinal = Funciones::get('saldoInicialFinal') == 'S';

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Movimientos_de_caja_' . $idCaja . (isset($desde) ? '_' . Funciones::formatearFecha($desde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Movimientos de caja';
	$html2xls->datosCabecera = array('Desde' => (isset($desde) ? $desde : '-'), 'Hasta' => (isset($hasta) ? $hasta : '-'), 'Cod. caja' => $idCaja);
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>