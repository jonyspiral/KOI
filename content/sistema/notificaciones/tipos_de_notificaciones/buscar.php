<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/tipos_de_notificaciones/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$tiposNotificacion = Factory::getInstance()->getTipoNotificacion($id);
	Html::jsonEncode('', $tiposNotificacion->expand());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>