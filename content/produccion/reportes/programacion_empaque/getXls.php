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
	$html2xls = new Html2Xls();
	$html2xls->html = Html2Xls::getHtmlFromPhp('buscar.php');
	$fechaDesdeEmpaque = Funciones::get('fechaDesdeEmpaque');
	$fechaHastaEmpaque = Funciones::get('fechaHastaEmpaque');
	$html2xls->fileName = 'Reporte_Programación_Empaque' . (isset($fechaDesdeEmpaque) ? '_' . Funciones::formatearFecha($fechaDesdeEmpaque, 'd-m-Y') : '') . (isset($fechaHastaEmpaque) ? '_' . Funciones::formatearFecha($fechaHastaEmpaque, 'd-m-Y') : '');
	$html2xls->tituloReporte = 'Reporte Programación Empaque';
	$html2xls->datosCabecera = array('Desde E.' => (isset($fechaDesdeEmpaque) ? $fechaDesdeEmpaque : '-'), 'Hasta E.' => (isset($fechaHastaEmpaque) ? $fechaHastaEmpaque : '-'),  'Anulado' => $anulado, 'Cumplido Paso' => $cumplidoPaso, 'Tipo Tarea' => $tipoTarea, 'Situacion' => $situacion, 'Artículo' => (isset($articulo) ? $articulo : '-'));
	$html2xls->download();
	$html2xls->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>