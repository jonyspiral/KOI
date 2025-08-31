<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/articulo/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2xls->fileName = 'Reporte_Clientes' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Reporte Clientes';
	$html2xls->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'), 'Empresa' => ($empresa != 0 ? $empresa : 'Todas') );
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>