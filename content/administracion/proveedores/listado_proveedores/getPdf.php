<?php
if (!ob_get_level()) {
    ob_start();
}

function shutdown_content_administracion_proveedores_listado_proveedores_getPdf_php() {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        return;
    }
    if (ob_get_length()) {
        ob_clean();
    }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(array(
        'status' => 500,
        'message' => 'Fatal error',
        'data' => $error,
    ));
}
register_shutdown_function('shutdown_content_administracion_proveedores_listado_proveedores_getPdf_php');

require_once('../../../../premaster.php');
if (ob_get_length()) {
    ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('administracion/proveedores/listado_proveedores/buscar/')) {
    Html::jsonError('Permiso denegado o usuario no logueado');
    exit;
}
$cuit = Funciones::get('cuit');
$idVendedor = Funciones::get('idVendedor');
$idPais = Funciones::get('idPais');
$idProvincia = Funciones::get('idProvincia');
$idLocalidad = Funciones::get('idLocalidad');
$calle = Funciones::get('calle');
$numero = Funciones::get('numero');
$orderBy = Funciones::get('orderBy');
$localidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);

try {
	$html2pdf = new Html2Pdf();
	$html2pdf->html = Html2Pdf::getHtmlFromPhp('buscar.php');
	$html2pdf->fileName = 'Listado_clientes' . (isset($idVendedor) ? '_vendedor_' . $idVendedor : '') . (isset($idPais) ? '_' . $idPais : '') . (isset($idProvincia) ? '_' . $idProvincia : '') . (isset($idLocalidad) ? '_' . $idLocalidad : '');
	$html2pdf->tituloReporte = 'Listado clientes';
	$html2pdf->datosCabecera = array('Vendedor' => (isset($idVendedor) ? $idVendedor : '-'), 'Pais' => (isset($idPais) ? $idPais : '-'), 'Provincia' => (isset($idProvincia) ? $idProvincia : '-'), 'Localidad' => (isset($idLocalidad) ? $localidad->nombre : '-'));
	$html2pdf->orientacion = Html2Pdf::PDF_LANDSCAPE;
	$html2pdf->open();
	$html2pdf->deleteFiles();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

