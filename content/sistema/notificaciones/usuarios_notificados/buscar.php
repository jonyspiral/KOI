<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/usuarios_notificados/buscar/')) { ?>
<?php

$idTipoNotificacion = Funciones::get('idTipoNotificacion');

try {
	$tipoNotificacion = Factory::getInstance()->getTipoNotificacion($idTipoNotificacion);
	$rxtn = $tipoNotificacion->roles;
	$uxtn = $tipoNotificacion->usuarios;
	$echoArr = array('roles' => $rxtn, 'usuarios' => $uxtn);
	Html::jsonEncode('', $echoArr);
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>