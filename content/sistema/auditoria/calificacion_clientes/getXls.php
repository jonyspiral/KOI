<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/auditoria/calificacion_clientes/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$fechaDesde = Funciones::get('fechaDesde');
$fechaHasta = Funciones::get('fechaHasta');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Calificacion_clientes' . (isset($idCliente) ? '_' . $idCliente : '') . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($fechaHasta) ? '_' . Funciones::formatearFecha($fechaHasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Calificación clientes';
	$html2xls->datosCabecera = array('Cliente' => (isset($idCliente) ? $idCliente : '-'), 'F. desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'F. hasta' => (isset($fechaHasta) ? $fechaHasta : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>