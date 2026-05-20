<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_reportes_programacion_empaque() {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => array(
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        )
    ));
}

register_shutdown_function('produccionPdfFatalHandler_reportes_programacion_empaque');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/reportes/programacion_empaque/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


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
	$html2pdf->tituloReporte = 'Reporte Programaciï¿½n Empaque';
	$html2pdf->datosCabecera = array('Desde E.' => (isset($fechaDesdeEmpaque) ? $fechaDesdeEmpaque : '-'), 'Hasta E.' => (isset($fechaHastaEmpaque) ? $fechaHastaEmpaque : '-'), 'Cumplido Paso' => $cumplidoPaso, 'Tipo Tarea' => $tipoTarea, 'Situacion' => $situacion);
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
