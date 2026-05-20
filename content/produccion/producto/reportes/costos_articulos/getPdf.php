<?php
if (!ob_get_level()) {
    ob_start();
}

function produccionPdfFatalHandler_producto_reportes_costos_articulos() {
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

register_shutdown_function('produccionPdfFatalHandler_producto_reportes_costos_articulos');

require_once('../../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/producto/reportes/costos_articulos/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}


$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$tipoReporte = Funciones::get('tipoReporte');

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Reporte_Costo_Articulos_' . ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado') . (isset($idArticulo) ? '_' . $idArticulo : '') . (isset($idColor) ? '_' . $idColor : '');
	$html2pdf->tituloReporte = 'Reporte Costo Artï¿½culos';
	$html2pdf->datosCabecera = array('Tipo Reporte' => ($tipoReporte == 'D' ? 'Detallado' : 'Agrupado'), 'Artï¿½culo' => (isset($idArticulo) ? $idArticulo : '-'), 'Color' => (isset($idColor) ? $idColor : '-'));
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
