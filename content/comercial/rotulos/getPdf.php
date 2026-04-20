<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/rotulos/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$idSucursal = Funciones::get('idSucursal');

function llenarFormulario(&$formulario, $sucursal){
	/** @var FormularioRotulos $formulario */
	/** @var Sucursal $sucursal */
	//Lleno todas las variables del formulario
	$formulario->razonSocial = $sucursal->cliente->nombre . ' (' . $sucursal->cliente->razonSocial . ')';
	$formulario->clienteNro = $sucursal->cliente->id;
	$formulario->sucursalNro = $sucursal->id;
	$formulario->direccionEntregaCalle = $sucursal->direccionCalle;
	$formulario->direccionEntregaNumero = $sucursal->direccionNumero;
	$formulario->direccionEntregaProvincia = $sucursal->direccionProvincia->nombre;
	$formulario->direccionEntregaPiso = $sucursal->direccionPiso;
	$formulario->direccionEntregaDpto = $sucursal->direccionDepartamento;
	$formulario->direccionEntregaLocalidad = $sucursal->direccionLocalidad->nombre;
	$formulario->direccionEntregaCP = $sucursal->direccionCodigoPostal;
	$formulario->transportistaNombre = $sucursal->transporte->nombre;
	$formulario->transportistaDomicilio = $sucursal->transporte->direccionCalle . ' ' . $sucursalEntrega->transporte->direccionNumero;
	$formulario->transportistaCUIT = $sucursal->transporte->cuit;
	$formulario->horarioEntrega1 = $sucursal->horarioEntrega1;
	$formulario->horarioEntrega2 = $sucursal->horarioEntrega2;
}

try {
	$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
	$formulario = new FormularioRotulos();
	llenarFormulario($formulario, $sucursal);
	$formulario->abrir();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>