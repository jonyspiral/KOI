<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos_propios/borrar/')) { ?>
<?php

$idBanco = Funciones::post('idBanco');
$idSucursal = Funciones::post('idSucursal');

try {
	$bancoPropio = Factory::getInstance()->getBancoPropio($idBanco, $idSucursal);
	$bancoPropio->borrar()->notificar('abm/bancos_propios/borrar/');
	Html::jsonSuccess('El banco propio fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El banco propio que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar el banco propio');
}

?>
<?php } ?>