<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cuentas_bancarias/agregar/')) { ?>
<?php

$idBanco = Funciones::post('idBanco');
$idSucursal = Funciones::post('idSucursal');
$nombreCuenta = Funciones::post('nombreCuenta');
$numeroCuenta = Funciones::post('numeroCuenta');
$idProveedor = Funciones::post('idProveedor');
$idCaja = Funciones::post('idCaja');
$idImputacion = Funciones::post('idImputacion');

try {
	$cuentaBancaria = Factory::getInstance()->getCuentaBancaria();
	$cuentaBancaria->banco = Factory::getInstance()->getBanco($idBanco);
	$cuentaBancaria->sucursal = Factory::getInstance()->getBancoPropio($idBanco, $idSucursal);

	$cuentaBancaria->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$cuentaBancaria->caja = Factory::getInstance()->getCaja($idCaja);
	$cuentaBancaria->imputacion = Factory::getInstance()->getImputacion($idImputacion);

	$cuentaBancaria->nombreCuenta = $nombreCuenta;
	$cuentaBancaria->numeroCuenta = $numeroCuenta;

	$cuentaBancaria->guardar()->notificar('abm/cuentas_bancarias/agregar/');
	Html::jsonSuccess('La cuenta bancaria fue guardada correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar la cuenta bancaria');
}

?>
<?php } ?>
