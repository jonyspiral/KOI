<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cuentas_bancarias/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$cuentaBancaria = Factory::getInstance()->getCuentaBancaria($id);
	$cuentaBancaria->borrar()->notificar('abm/cuentas_bancarias/borrar/');
	Html::jsonSuccess('La cuenta bancaria fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La cuenta bancaria que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar la cuenta bancaria');
}

?>
<?php } ?>