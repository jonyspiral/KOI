<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/notificaciones/usuarios_notificados/editar/')) { ?>
<?php

$idTipoNotificacion = Funciones::post('idTipoNotificacion');
$uxtn = Funciones::post('uxtn');
$rxtn = Funciones::post('rxtn');

try {
	$tipoNotificacion = Factory::getInstance()->getTipoNotificacion($idTipoNotificacion);

	$tipoNotificacion->roles = array();
 	foreach ($rxtn as $o){
		try {
			$rolPorTipoNotificacion = Factory::getInstance()->getRolPorTipoNotificacion();
			$rolPorTipoNotificacion->id = $o['idRol'];
			$rolPorTipoNotificacion->tipoNotificacion = $tipoNotificacion;
			$rolPorTipoNotificacion->eliminable = ($o['eliminable'] == '1' ? 'S' : 'N');
			$tipoNotificacion->addRol($rolPorTipoNotificacion);
		} catch (Exception $ex) {
			continue;
		}
	}

	$tipoNotificacion->usuarios = array();
	foreach ($uxtn as $o){
		try {
			$usuarioPorTipoNotificacion = Factory::getInstance()->getUsuarioPorTipoNotificacion();
			$usuarioPorTipoNotificacion->id = $o['idUsuario'];
			$usuarioPorTipoNotificacion->tipoNotificacion = $tipoNotificacion;
			$usuarioPorTipoNotificacion->eliminable = ($o['eliminable'] == '1' ? 'S' : 'N');
			$tipoNotificacion->addUsuario($usuarioPorTipoNotificacion);
		} catch (Exception $ex) {
			continue;
		}
	}

	Factory::getInstance()->persistir($tipoNotificacion);

	Html::jsonSuccess('Los usuarios/roles notificados fue actualizados correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El tipo de notificacion no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el usuario');
}
?>
<?php } ?>