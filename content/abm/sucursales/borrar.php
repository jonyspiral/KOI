<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/sucursales/borrar/')) { ?>
<?php

$idSucursal = Funciones::post('idSucursal');
$idCliente = Funciones::post('idCliente');

try {
	$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
	$sucursal->borrar()->notificar('abm/sucursales/borrar/');

	Html::jsonSuccess('La sucursal fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La sucursal que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la sucursal');
}
?>
<?php } ?>