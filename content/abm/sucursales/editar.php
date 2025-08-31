<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/sucursales/editar/')) { ?>
<?php

$idSucursal = Funciones::post('idSucursal');
$idCliente = Funciones::post('idCliente');
$nombre = Funciones::post('nombre');
$telefono1 = Funciones::post('telefono1');
$telefono2 = Funciones::post('telefono2');
$celular = Funciones::post('celular');
$puntoVenta = Funciones::post('puntoVenta');
$email1 = Funciones::post('email1');
$fax =  Funciones::post('fax');
$calle = Funciones::post('calle');
$numero = Funciones::post('numero');
$piso = Funciones::post('piso');
$dpto = Funciones::post('dpto');
$codPostal = Funciones::post('codPostal');
$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$idLocalidad = Funciones::post('idLocalidad');
$reparto = Funciones::post('reparto');
$vendedor = Funciones::post('vendedor');
$observaciones = Funciones::post('observaciones');
$idSucursalEntrega = Funciones::post('idEntrega');
$horarioAtencion = Funciones::post('horarioAtencion');
$transporte = Funciones::post('transporte');
$zonaTransporte = Funciones::post('zonaTransporte');
$latitud = Funciones::post('latitud');
$longitud = Funciones::post('longitud');
$horarioEntrega1 = Funciones::post('horarioEntrega1');
$horarioEntrega2 = Funciones::post('horarioEntrega2');

try {
	if (!isset($idCliente) || !isset($idSucursal))
		throw new FactoryExceptionRegistroNoExistente();
	$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
	
	$sucursal->nombre = $nombre;
	$sucursal->telefono1 = $telefono1;
	$sucursal->telefono2 = $telefono2;
	$sucursal->celular = $celular;
	$sucursal->email = $email1;
	$sucursal->esPuntoDeVenta = $puntoVenta;
	$sucursal->fax = $fax;
	$sucursal->direccionCalle = $calle;
	$sucursal->direccionNumero = $numero;
	$sucursal->direccionPiso = $piso;
	$sucursal->direccionDepartamento = $dpto;
	$sucursal->direccionCodigoPostal = $codPostal;
	$sucursal->direccionPais = Factory::getInstance()->getPais($idPais);
	$sucursal->direccionProvincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	$sucursal->direccionLocalidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);
	$sucursal->reparto = $reparto;
	$sucursal->vendedor = Factory::getInstance()->getVendedor($vendedor);
	$sucursal->observaciones = $observaciones;
	$sucursal->sucursalEntrega = Factory::getInstance()->getSucursal($idCliente, $idSucursalEntrega);
	$sucursal->horarioAtencion = $horarioAtencion;
	$sucursal->transporte = Factory::getInstance()->getTransporte($transporte);
	$sucursal->zonaTransporte = Factory::getInstance()->getZonaTransporte($zonaTransporte);
	$sucursal->direccionLatitud = $latitud;
	$sucursal->direccionLongitud = $longitud;
	$sucursal->horarioEntrega1 = $horarioEntrega1;
	$sucursal->horarioEntrega2 = $horarioEntrega2;

	$sucursal->guardar()->notificar('abm/sucursales/editar/');
	Html::jsonSuccess('La sucursal fue guardada correctamente');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La sucursal que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la sucursal');
}
?>
<?php } ?>