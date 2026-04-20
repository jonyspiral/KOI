<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos_propios/buscar/')) { ?>
<?php

$idBanco = Funciones::get('idBanco');
$idSucursal = Funciones::get('idSucursal');

try {
	$bancoPropio = Factory::getInstance()->getBancoPropio($idBanco, $idSucursal);
	Html::jsonEncode('', $bancoPropio->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El banco propio que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar el banco propio');
}

?>
<?php } ?>