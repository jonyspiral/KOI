<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/reportes/costos_articulos/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$tipoReporte = Funciones::get('tipoReporte');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Reporte_Costo_Articulos_' . ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado') . (isset($idArticulo) ? '_' . $idArticulo : '') . (isset($idColor) ? '_' . $idColor : '');
	$html2pdf->tituloReporte = 'Reporte Costo Artículos';
	$html2pdf->datosCabecera = array('Tipo Reporte' => ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado'), 'Artículo' => (isset($idArticulo) ? $idArticulo : '-'), 'Color' => (isset($idColor) ? $idColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>