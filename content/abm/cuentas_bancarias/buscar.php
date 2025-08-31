<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cuentas_bancarias/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$cuentaBancaria = Factory::getInstance()->getCuentaBancaria($id);
	Html::jsonEncode('', $cuentaBancaria->expand());
} catch (FactoryException $ex) {
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La cuenta bancaria que intentó buscar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar buscar la cuenta bancaria');
}

?>
<?php } ?>