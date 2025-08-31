<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cuentas_bancarias/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombreCuenta = Funciones::post('nombreCuenta');
$numeroCuenta = Funciones::post('numeroCuenta');
$idProveedor = Funciones::post('idProveedor');
$idCaja = Funciones::post('idCaja');
$idImputacion = Funciones::post('idImputacion');

try {
	if (!isset($id)) {
		throw new FactoryExceptionRegistroNoExistente();
	}
	$cuentaBancaria = Factory::getInstance()->getCuentaBancaria($id);
	if ($cuentaBancaria->anulado()) {
		throw new FactoryExceptionRegistroNoExistente();
	}

	$cuentaBancaria->proveedor = Factory::getInstance()->getProveedor($idProveedor);
	$cuentaBancaria->caja = Factory::getInstance()->getCaja($idCaja);
	$cuentaBancaria->imputacion = Factory::getInstance()->getImputacion($idImputacion);

	$cuentaBancaria->nombreCuenta = $nombreCuenta;
	$cuentaBancaria->numeroCuenta = $numeroCuenta;

	$cuentaBancaria->guardar()->notificar('abm/cuentas_bancarias/editar/');
	Html::jsonSuccess('La cuenta bancaria fue guardada correctamente');
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La cuenta bancaria que intentó editar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar guardar la cuenta bancaria');
}

?>
<?php } ?>