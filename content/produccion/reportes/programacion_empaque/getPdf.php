<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/reportes/programacion_empaque/buscar/')) { ?>
<?php

$anulado = Funciones::get('anulado');
$cumplidoPaso = Funciones::get('cumplidoPaso');
$tipoTarea = Funciones::get('tipoTarea');
$situacion = Funciones::get('situacion');
$articulo = Funciones::get('articulo');
$lote = Funciones::get('lote');
$tarea = Funciones::get('tarea');
$orderBy = Funciones::get('orderBy');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$fechaDesdeEmpaque = Funciones::get('fechaDesdeEmpaque');
	$fechaHastaEmpaque = Funciones::get('fechaHastaEmpaque');
	$html2pdf->fileName = 'Reporte_Programacion_Empaque' . (isset($fechaDesdeEmpaque) ? '_' . Funciones::formatearFecha($fechaDesdeEmpaque, 'd-m-Y') : '') . (isset($fechaHastaEmpaque) ? '_' . Funciones::formatearFecha($fechaHastaEmpaque, 'd-m-Y') : '');
	$html2pdf->tituloReporte = 'Reporte Programación Empaque';
	$html2pdf->datosCabecera = array('Desde E.' => (isset($fechaDesdeEmpaque) ? $fechaDesdeEmpaque : '-'), 'Hasta E.' => (isset($fechaHastaEmpaque) ? $fechaHastaEmpaque : '-'), 'Cumplido Paso' => $cumplidoPaso, 'Tipo Tarea' => $tipoTarea, 'Situacion' => $situacion);
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>