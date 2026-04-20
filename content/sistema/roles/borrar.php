<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/roles/borrar/')) { ?>
<?php
$idRol = Funciones::post('idRol');
try {
	$rol = Factory::getInstance()->getRol($idRol);
	foreach ($rol->funcionalidades as $fun){
		Factory::getInstance()->marcarParaBorrar($fun);
	}
	Factory::getInstance()->marcarParaBorrar($rol);
	Factory::getInstance()->persistir($rol);
	Html::jsonSuccess('El rol fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El rol que intentó borrar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el rol');
}
?>
<?php } ?>