<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/finanzas/reportes/articulo/buscar/')) { ?>
<?php

$empresa = Funciones::get('empresa');
$articulo = Funciones::get('articulo');
$color = Funciones::get('color');
$cliente = Funciones::get('cliente');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesde = Funciones::get('fechaDesde');
	$fechaHasta = Funciones::get('fechaHasta');
	$html2xls->fileName = 'Reporte_Articulos' . (isset($fechaDesde) ? '_' . Funciones::formatearFecha($fechaDesde, 'd-m-Y') : '') . (isset($hasta) ? '_' . Funciones::formatearFecha($hasta, 'd-m-Y') : '') . (isset($idProveedor) ? '_' . $idProveedor : '');
	$html2xls->tituloReporte = 'Reporte Artículos';
	$html2xls->datosCabecera = array('Desde' => (isset($fechaDesde) ? $fechaDesde : '-'), 'Hasta' => (isset($fechaHasta) ? $fechaHasta : '-'),  'Cliente' => (isset($cliente) ? $cliente : '-'), 'Empresa' => ($empresa != 0 ? $empresa : 'Todas'), 'Artículo' => (isset($articulo) ? $articulo : '-'), 'Color' => (isset($color) ? $color : '-') );
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>