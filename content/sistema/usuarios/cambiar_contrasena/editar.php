<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/cambiar_contrasena/')) { ?>
<?php

$oldPassword = Funciones::toSHA1(Funciones::post('oldPassword'));
$newPassword = Funciones::toSHA1(Funciones::post('newPassword'));

try {
	$usuario = Factory::getInstance()->getUsuarioLogin(Usuario::logueado()->id);
	if (isset($newPassword)){
		if (!($usuario->password == $oldPassword))
			throw new FactoryException('El password actual ingresado es incorrecto');
		$usuario->password = $newPassword;
	}
	$aux = array();
	foreach ($usuario->roles as $r) {
		$rol = Factory::getInstance()->getRolPorUsuario();
		$rol->id = $r->id;
		$rol->idUsuario = $usuario->id;
		$aux[] = $rol;
	}
	$usuario->roles = $aux;
	$usuario->guardar()->notificar('sistema/usuarios/cambiar_contrasena/');
	Html::jsonSuccess('Se cambió la contraseña correctamente');
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El usuario que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar cambiar la contraseña');
}
?>
<?php } ?>