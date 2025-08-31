<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/cajas/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$caja = Factory::getInstance()->getCaja($id);
	$caja->borrar()->notificar('abm/cajas/borrar/');
	Html::jsonSuccess('La caja fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La caja que intentó borrar no existe');
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar borrar la caja');
}

?>
<?php } ?>