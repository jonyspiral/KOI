<?php
if (!ob_get_level()) {
	ob_start();
}

function shutdown_content_produccion_guia_de_porte_getPdf_php() {
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
register_shutdown_function('shutdown_content_produccion_guia_de_porte_getPdf_php');

require_once('../../../premaster.php');
if (ob_get_length()) {
	ob_clean();
}

$usuario = Usuario::logueado();
if (!$usuario || !$usuario->puede('produccion/guia_de_porte/buscar/')) {
	Html::jsonError('Permiso denegado o usuario no logueado');
	exit;
}

$numeroGuia = Funciones::get('numeroGuia');
$fecha = Funciones::get('fecha');
$senores = Funciones::get('senores');
$clienteNro = Funciones::get('clienteNro');
$direccionCalle = Funciones::get('direccionCalle');
$direccionNumero = Funciones::get('direccionNumero');
$direccionPiso = Funciones::get('direccionPiso');
$direccionDpto = Funciones::get('direccionDpto');
$direccionLocalidad = Funciones::get('direccionLocalidad');
$direccionCP = Funciones::get('direccionCP');
$cuit = Funciones::get('cuit');
$condicionIVA = Funciones::get('condicionIva');
$transportistaSenor = Funciones::get('transportistaSenor');
$transportistaDomicilio = Funciones::get('transportistaDomicilio');
$transportistaCUIT = Funciones::get('transportistaCuit');
$transportistaDNI = Funciones::get('transportistaDni');
$detalle = Funciones::get('detalle');

function obtenerNombreIva($condicionIVA) {
	$condicionIVA = Factory::getInstance()->getCondicionIva($condicionIVA);
	return $condicionIVA->nombre;
}

try {
	$formulario = new FormularioGuiaDePorte();
	$formulario->numeroGuia = $numeroGuia;
	$formulario->fecha = explode('/', $fecha);
	$formulario->senores = $senores;
	$formulario->clienteNro = $clienteNro;
	$formulario->direccionCalle = $direccionCalle;
	$formulario->direccionNumero = $direccionNumero;
	$formulario->direccionPiso = $direccionPiso;
	$formulario->direccionDpto = $direccionDpto;
	$formulario->direccionLocalidad = $direccionLocalidad;
	$formulario->direccionCP = $direccionCP;
	$formulario->cuit = $cuit;
	$formulario->condicionIVA = obtenerNombreIva($condicionIVA);
	$formulario->transportistaSenor = $transportistaSenor;
	$formulario->transportistaDomicilio = $transportistaDomicilio;
	$formulario->transportistaCUIT = $transportistaCUIT;
	$formulario->transportistaDNI = $transportistaDNI;
	$formulario->detalle = $detalle;
	$formulario->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}
