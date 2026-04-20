<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/abm/editar/')) { ?>
<?php
$idUsuario = Funciones::post('idUsuario');
$newPassword = Funciones::toSHA1(Funciones::post('newPassword'));
$rolesPorUsuario = Funciones::post('roles');
try {
	if (!isset($idUsuario))
		throw new FactoryExceptionRegistroNoExistente();
	$usuario = Factory::getInstance()->getUsuarioLogin($idUsuario);
	if (isset($newPassword))
		$usuario->password = $newPassword;
	$aux = array();
	foreach ($rolesPorUsuario as $r){
		try {
			$rolExistente = Factory::getInstance()->getRol(Funciones::toInt($r)); //Con esto verifico que el rol exista.
			if (($usuario->tipoUsuario == 'P' && $rolExistente->tipo == 'C') || ($usuario->tipoUsuario == 'C' && $rolExistente->tipo == 'P'))
				throw new FactoryException('Uno de los roles no puede ser asignado a ese tipo de usuario');
			$rol = Factory::getInstance()->getRolPorUsuario();
			$rol->id = Funciones::toInt($r);
			$rol->idUsuario = $idUsuario;
			$aux[] = $rol;
		} catch (Exception $eeex) {
			continue;
		}
	}
	$usuario->roles = $aux;
	Factory::getInstance()->persistir($usuario);
	Html::jsonSuccess('El usuario fue guardado correctamente');
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El usuario que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el usuario');
}
?>
<?php } ?>