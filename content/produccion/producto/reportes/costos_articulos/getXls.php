<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/reportes/costos_articulos/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$tipoReporte = Funciones::get('tipoReporte');

try {
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$html2xls->fileName = 'Reporte_Costo_Articulos_' . ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado') . (isset($idArticulo) ? '_' . $idArticulo : '') . (isset($idColor) ? '_' . $idColor : '');
	$html2xls->tituloReporte = 'Reporte Costo Artículos';
	$html2xls->datosCabecera = array('Tipo Reporte' => ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado'), 'Artículo' => (isset($idArticulo) ? $idArticulo : '-'), 'Color' => (isset($idColor) ? $idColor : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>