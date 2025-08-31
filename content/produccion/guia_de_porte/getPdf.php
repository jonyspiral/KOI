<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('produccion/guia_de_porte/buscar/')) { ?>
<?php

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

function obternerNombreIva($condicionIVA){
	$condicionIVA = Factory::getInstance()->getCondicionIva($condicionIVA);
	return $condicionIVA->nombre;
}


try {
	$formulario = new FormularioGuiaDePorte();
	//Lleno todas las variables del formulario
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
	$formulario->condicionIVA = obternerNombreIva($condicionIVA);
	$formulario->transportistaSenor = $transportistaSenor;
	$formulario->transportistaDomicilio = $transportistaDomicilio;
	$formulario->transportistaCUIT = $transportistaCUIT;
	$formulario->transportistaDNI = $transportistaDNI;

	//Mando a armar el array de remitos incluidos y el detalle
	$formulario->detalle = $detalle;
	$formulario->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>