<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/tipos_de_notificaciones/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$tipoNotificacion = Factory::getInstance()->getTipoNotificacion($id);
	$tipoNotificacion->borrar()->notificar('sistema/notificaciones/tipos_de_notificaciones/borrar/');
	Html::jsonSuccess('El tipo de notificación fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de notificación que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el tipo de notificación');
}
?>
<?php } ?>