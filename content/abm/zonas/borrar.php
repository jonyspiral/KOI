<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/zonas/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$zona = Factory::getInstance()->getZona($id);
	Factory::getInstance()->marcarParaBorrar($zona);
	$zona->borrar()->notificar('abm/zonas/borrar/');
	Html::jsonSuccess('La zona fue borrada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La zona que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la zona');
}
?>
<?php } ?>