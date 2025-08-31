<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/contactos/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$apellido = Funciones::post('apellido');
$tipo = Funciones::post('tipo');
/*
$idCliente = Funciones::post('idCliente');
$idSucursal = Funciones::post('idSucursal');
$idProveedor = Funciones::post('idProveedor');
*/
$referencia = Funciones::post('referencia');
$telefono1 = Funciones::post('telefono1');
$interno1 = Funciones::post('interno1');
$telefono2 = Funciones::post('telefono2');
$interno2 = Funciones::post('interno2');
$celular = Funciones::post('celular');
$observaciones = Funciones::post('observaciones');
$email1 = Funciones::post('email1');
$email2 = Funciones::post('email2');
$calle = Funciones::post('calle');
$numero = Funciones::post('numero');
$piso = Funciones::post('piso');
$dpto = Funciones::post('dpto');
$codPostal = Funciones::post('codPostal');
$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$idLocalidad = Funciones::post('idLocalidad');
try {
	if ($tipo == 'C') {
		$idCliente = Funciones::post('idCliente');
		$idSucursal = Funciones::post('idSucursal');
	} elseif ($tipo == 'P')
		$idProveedor = Funciones::post('idProveedor');
		
	$contacto = Factory::getInstance()->getContacto();
	$contacto->nombre = $nombre;
	$contacto->apellido = $apellido;
	if (isset($tipo)) {
		$contacto->tipo = $tipo;
		if ($contacto->tipo == 'C') {
			$contacto->cliente = Factory::getInstance()->getCliente($idCliente);
			$contacto->sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
		}
		if ($contacto->tipo == 'P')
			$contacto->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	}
	$contacto->referencia = $referencia;
	$contacto->telefono1 = $telefono1;
	$contacto->interno1 = $interno1;
	$contacto->telefono2 = $telefono2;
	$contacto->interno2 = $interno2;
	$contacto->celular = $celular;
	$contacto->observaciones = $observaciones;
	$contacto->email1 = $email1;
	$contacto->email2 = $email2;
	$contacto->direccionCalle = $calle;
	$contacto->direccionNumero = $numero;
	$contacto->direccionPiso = $piso;
	$contacto->direccionDepartamento = $dpto;
	$contacto->direccionCodigoPostal = $codPostal;
	$contacto->direccionPais = Factory::getInstance()->getPais($idPais);
	$contacto->direccionProvincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	$contacto->direccionLocalidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);

	$contacto->guardar()->notificar('abm/contactos/agregar/');
	//Factory::getInstance()->persistir($contacto);
	Html::jsonSuccess('El contacto fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el contacto');
}
?>
<?php } ?>