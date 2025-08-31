<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('sistema/usuarios/abm/borrar/')) { ?>
<?php
$idUsuario = Funciones::post('idUsuario');
try {
	$usuario = Factory::getInstance()->getUsuario($idUsuario);
	foreach ($usuario->roles as $rol){
		Factory::getInstance()->marcarParaBorrar($rol);
	}
	Factory::getInstance()->marcarParaBorrar($usuario);
	Factory::getInstance()->persistir($usuario);
	Html::jsonSuccess('El usuario fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El usuario que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el usuario');
}
?>
<?php } ?>