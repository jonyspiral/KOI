<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/bancos/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$banco = Factory::getInstance()->getBanco($id);
	$banco->borrar()->notificar('abm/bancos/borrar/');
	Html::jsonSuccess('El banco fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El banco que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar el banco');
}

?>
<?php } ?>